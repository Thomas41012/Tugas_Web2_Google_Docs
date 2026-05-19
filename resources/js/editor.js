import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const appEl = document.getElementById('editor-app');
if (!appEl) {
    // not on editor page
} else {
    const config = {
        documentId: Number(appEl.dataset.documentId),
        version: Number(appEl.dataset.version),
        userId: Number(appEl.dataset.userId),
        userName: appEl.dataset.userName,
        userColor: appEl.dataset.userColor,
        syncUrl: appEl.dataset.syncUrl,
        cursorUrl: appEl.dataset.cursorUrl,
    };

    const initialJson = document.getElementById('initial-content')?.textContent ?? '""';
    const initialContent = JSON.parse(initialJson);

    const editor = document.getElementById('editor');
    const versionLabel = document.getElementById('version-label');
    const conflictBanner = document.getElementById('conflict-banner');
    const presenceList = document.getElementById('presence-list');
    const cursorList = document.getElementById('cursor-list');
    const remoteCursorsLayer = document.getElementById('remote-cursors');

    let currentVersion = config.version;
    let isApplyingRemote = false;
    let saveTimer = null;
    let cursorTimer = null;
    const remoteUsers = new Map();

    editor.innerHTML = initialContent || '<p></p>';

    // Toolbar
    document.querySelectorAll('#toolbar button').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const cmd = btn.dataset.cmd;
            const value = btn.dataset.value || null;
            editor.focus();
            document.execCommand(cmd, false, value);
            scheduleSave('format');
        });
    });

  function getCaretOffset(element) {
        const selection = window.getSelection();
        if (!selection || selection.rangeCount === 0) return 0;
        const range = selection.getRangeAt(0);
        const pre = range.cloneRange();
        pre.selectNodeContents(element);
        pre.setEnd(range.endContainer, range.endOffset);
        return pre.toString().length;
    }

    function showConflict(data) {
        if (!data.had_conflict) {
            conflictBanner.classList.add('hidden');
            return;
        }
        const who = data.conflict_with?.user_name ?? 'user lain';
        conflictBanner.textContent = `Konflik terdeteksi: ${who} juga mengedit. Perubahan Anda tetap disimpan (versi digabung). Cek Activity untuk detail.`;
        conflictBanner.classList.remove('hidden');
    }

    async function saveAndBroadcast(action = 'sync') {
        if (isApplyingRemote) return;

        const content = editor.innerHTML;
        const caret = getCaretOffset(editor);

        try {
            const res = await fetch(config.syncUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    content,
                    version: currentVersion,
                    caret_position: caret,
                    action,
                }),
            });

            if (!res.ok) return;

            const data = await res.json();
            currentVersion = data.version;
            versionLabel.textContent = String(currentVersion);
            showConflict(data);
        } catch (e) {
            console.error('Sync failed', e);
        }
    }

    function scheduleSave(action = 'sync') {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(() => saveAndBroadcast(action), 450);
    }

    async function sendCursor() {
        const caret = getCaretOffset(editor);
        try {
            await fetch(config.cursorUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ caret_position: caret }),
            });
        } catch (e) {
            // ignore
        }
    }

    function scheduleCursor() {
        clearTimeout(cursorTimer);
        cursorTimer = setTimeout(sendCursor, 120);
    }

    function applyRemoteContent(payload) {
        if (Number(payload.user_id) === config.userId) return;
        if (Number(payload.version) <= currentVersion) return;

        isApplyingRemote = true;
        editor.innerHTML = payload.content;
        currentVersion = Number(payload.version);
        versionLabel.textContent = String(currentVersion);
        isApplyingRemote = false;
    }

    function renderRemoteCursors() {
        remoteCursorsLayer.innerHTML = '';
        cursorList.innerHTML = '';

        remoteUsers.forEach((info) => {
            if (Number(info.user_id) === config.userId) return;

            const li = document.createElement('li');
            li.innerHTML = `<span style="color:${info.color}">●</span> ${info.user_name} — posisi karakter ${info.caret_position}`;
            cursorList.appendChild(li);

            const marker = document.createElement('div');
            marker.className = 'remote-cursor-marker';
            marker.style.borderColor = info.color;
            marker.style.top = `${Math.min(90, (info.caret_position % 50) * 1.2)}%`;
            marker.innerHTML = `<span style="background:${info.color}">${info.user_name}</span>`;
            remoteCursorsLayer.appendChild(marker);
        });
    }

    function updatePresence(members) {
        presenceList.innerHTML = '';
        Object.values(members).forEach((member) => {
            const li = document.createElement('li');
            const isMe = Number(member.id) === config.userId ? ' (Anda)' : '';
            li.textContent = `${member.name}${isMe}`;
            presenceList.appendChild(li);
        });
    }

    editor.addEventListener('input', () => {
        if (isApplyingRemote) return;
        scheduleSave('insert');
    });

    editor.addEventListener('keyup', scheduleCursor);
    editor.addEventListener('click', scheduleCursor);

    // Laravel Echo + Reverb
    const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
    const reverbHost = import.meta.env.VITE_REVERB_HOST ?? window.location.hostname;
    const reverbPort = import.meta.env.VITE_REVERB_PORT ?? 8080;
    const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: reverbScheme === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
    });

    window.Echo.join(`document.${config.documentId}`)
        .here((users) => {
            updatePresence(Object.fromEntries(users.map((u) => [u.id, u])));
        })
        .joining((user) => {
            // refreshed on here
        })
        .leaving(() => {
            // refreshed on here
        })
        .listen('.content.updated', (payload) => {
            applyRemoteContent(payload);
        })
        .listen('.cursor.moved', (payload) => {
            if (Number(payload.user_id) === config.userId) return;
            remoteUsers.set(payload.user_id, payload);
            renderRemoteCursors();
        });

    // Refresh presence periodically from channel
    setInterval(() => {
        const channel = window.Echo?.connector?.channels?.[`presence-document.${config.documentId}`];
        if (channel?.members) {
            updatePresence(channel.members);
        }
    }, 3000);
}
