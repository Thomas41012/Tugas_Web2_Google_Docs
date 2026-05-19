git add README.mdHEAD
# Collaborative Docs — Google Docs Clone (Laravel 12)

Editor dokumen kolaboratif realtime dengan **multi-user editing**, **live cursor tracking**, **version history**, dan **conflict resolution** (siapa mengedit apa).

## Fitur

| Fitur | Status |
|-------|--------|
| Multi-user editing (dokumen sama) | ✅ |
| Live cursor tracking | ✅ |
| Version history (revisi dokumen) | ✅ |
| Conflict resolution (log siapa edit apa) | ✅ |
| Bold / Italic / Toolbar | ✅ |
| Realtime via Laravel Reverb | ✅ |
| Tidak freeze (debounce sync) | ✅ |

## Cara Menjalankan

### 1. Prasyarat

- PHP 8.2+
- Composer
- Node.js 18+
- Extension PHP: `sqlite3`, `mbstring`, `openssl`

### 2. Install (jika belum)

```bash
cd collaborative-docs
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Pastikan di `.env`:

```env
BROADCAST_CONNECTION=reverb
DB_CONNECTION=sqlite
```

### 3. Database

```bash
php artisan migrate --seed
```

Akun demo:

| Email | Password |
|-------|----------|
| alice@docs.test | password |
| bob@docs.test | password |

### 4. Jalankan (3 terminal atau 1 perintah)

**Opsi A — satu perintah (disarankan):**

```bash
composer run dev
```

**Opsi B — manual (3 terminal):**

```bash
# Terminal 1
php artisan serve

# Terminal 2
php artisan reverb:start

# Terminal 3
npm run dev
```

Buka: **http://127.0.0.1:8000**

### 5. Uji realtime

1. Login sebagai **Alice** di browser biasa
2. Login sebagai **Bob** di browser incognito / browser lain
3. Buka dokumen **"Dokumen Kolaboratif Demo"**
4. Ketik bersamaan — teks muncul realtime
5. Cek **Version History** dan **Siapa Edit Apa** untuk log & konflik

---

## Daftar File yang Dibuat / Diubah

### Database

| File | Fungsi |
|------|--------|
| `database/migrations/2025_05_20_000001_create_documents_table.php` | Tabel dokumen |
| `database/migrations/2025_05_20_000002_create_document_revisions_table.php` | Riwayat versi |
| `database/migrations/2025_05_20_000003_create_document_edits_table.php` | Log edit & konflik |
| `database/seeders/DatabaseSeeder.php` | Data demo Alice, Bob, dokumen |

### Models

| File | Fungsi |
|------|--------|
| `app/Models/Document.php` | Model dokumen |
| `app/Models/DocumentRevision.php` | Model revisi |
| `app/Models/DocumentEdit.php` | Model log edit |
| `app/Models/User.php` | + relasi `documents()` |

### Events (Broadcasting)

| File | Fungsi |
|------|--------|
| `app/Events/DocumentContentUpdated.php` | Broadcast perubahan konten |
| `app/Events/CursorMoved.php` | Broadcast posisi cursor |

### Controllers

| File | Fungsi |
|------|--------|
| `app/Http/Controllers/AuthController.php` | Login / register / logout |
| `app/Http/Controllers/DocumentController.php` | CRUD dokumen + sync + cursor |
| `app/Http/Controllers/DocumentRevisionController.php` | History & restore |
| `app/Http/Controllers/DocumentEditController.php` | Halaman activity log |

### Routes & Channels

| File | Fungsi |
|------|--------|
| `routes/web.php` | Semua route aplikasi |
| `routes/channels.php` | Presence channel `document.{id}` |

### Views (Blade)

| File | Fungsi |
|------|--------|
| `resources/views/layouts/app.blade.php` | Layout utama |
| `resources/views/auth/login.blade.php` | Halaman login |
| `resources/views/auth/register.blade.php` | Halaman daftar |
| `resources/views/documents/index.blade.php` | Daftar dokumen |
| `resources/views/documents/show.blade.php` | **Editor utama** |
| `resources/views/documents/history.blade.php` | Version history |
| `resources/views/documents/revision.blade.php` | Lihat 1 revisi |
| `resources/views/documents/activity.blade.php` | Siapa edit apa |

### Frontend (Realtime Editor)

| File | Fungsi |
|------|--------|
| `resources/js/editor.js` | Editor + Echo + sync + cursor |
| `resources/css/editor.css` | Style toolbar & editor |
| `vite.config.js` | Entry `editor.js` & `editor.css` |

### Config

| File | Fungsi |
|------|--------|
| `config/broadcasting.php` | Koneksi Reverb |
| `config/reverb.php` | Server WebSocket |
| `app/Providers/AppServiceProvider.php` | Route `/broadcasting/auth` |
| `.env` | `BROADCAST_CONNECTION=reverb` + kunci Reverb |

---

## Arsitektur Singkat

```
Browser A ──┐
            ├──► Laravel (sync API) ──► SQLite
Browser B ──┘              │
                             ▼
                    Laravel Reverb (WebSocket)
                             │
                    Broadcast ke browser lain
```

- **Sync**: debounce 450ms → `POST /documents/{id}/sync` → simpan DB + broadcast
- **Cursor**: throttle 120ms → `POST /documents/{id}/cursor` → broadcast posisi
- **Konflik**: jika `client_version < server_version` → flag `had_conflict` + simpan siapa edit terakhir
- **Revisi**: auto-save setiap 5 versi + restore manual dari history

---

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Realtime tidak jalan | Pastikan `php artisan reverb:start` berjalan |
| Error WebSocket | Cek `VITE_REVERB_*` di `.env`, jalankan `npm run dev` atau `npm run build` |
| 419 CSRF | Refresh halaman setelah login |
| Broadcast auth gagal | Pastikan sudah login, cek `BROADCAST_CONNECTION=reverb` |

---

## License

MIT
=======
# Tugas_Web2_Google_Docs
>>>>>>> fdea6b5f7890bf7a096fd5317507f315c0e131f9
