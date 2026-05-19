@extends('layouts.app')

@section('title', $document->title)

@push('styles')
    @vite(['resources/css/editor.css'])
@endpush

@section('content')
<div id="editor-app"
    data-document-id="{{ $document->id }}"
    data-version="{{ $document->version }}"
    data-user-id="{{ $user->id }}"
    data-user-name="{{ $user->name }}"
    data-user-color="{{ $userColor }}"
    data-sync-url="{{ route('documents.sync', $document) }}"
    data-cursor-url="{{ route('documents.cursor', $document) }}"
>
    <script type="application/json" id="initial-content">@json($document->content)</script>
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <h1 class="text-2xl font-bold">{{ $document->title }}</h1>
        <div class="flex flex-wrap gap-2 text-sm">
            <a href="{{ route('documents.history', $document) }}" class="px-3 py-1 rounded border bg-white hover:bg-slate-50">Version History</a>
            <a href="{{ route('documents.activity', $document) }}" class="px-3 py-1 rounded border bg-white hover:bg-slate-50">Siapa Edit Apa</a>
            <span class="px-3 py-1 rounded bg-slate-200">Versi: <strong id="version-label">{{ $document->version }}</strong></span>
        </div>
    </div>

    <div id="conflict-banner" class="hidden mb-3 rounded-lg bg-amber-50 border border-amber-300 text-amber-900 px-4 py-3 text-sm"></div>

    <div class="editor-shell bg-white rounded-xl shadow overflow-hidden">
        <div id="toolbar" class="toolbar border-b border-slate-200 px-3 py-2 flex flex-wrap gap-1">
            <button type="button" data-cmd="bold" title="Bold"><b>B</b></button>
            <button type="button" data-cmd="italic" title="Italic"><i>I</i></button>
            <button type="button" data-cmd="underline" title="Underline"><u>U</u></button>
            <span class="toolbar-sep"></span>
            <button type="button" data-cmd="insertUnorderedList" title="Bullet list">• List</button>
            <button type="button" data-cmd="insertOrderedList" title="Numbered list">1. List</button>
            <span class="toolbar-sep"></span>
            <button type="button" data-cmd="formatBlock" data-value="h1" title="Heading 1">H1</button>
            <button type="button" data-cmd="formatBlock" data-value="h2" title="Heading 2">H2</button>
            <button type="button" data-cmd="formatBlock" data-value="p" title="Paragraph">P</button>
            <span class="toolbar-sep"></span>
            <button type="button" data-cmd="removeFormat" title="Clear format">Clear</button>
        </div>

        <div class="editor-body relative">
            <div id="remote-cursors"></div>
            <div id="editor" class="editor-content" contenteditable="true" spellcheck="true"></div>
        </div>
    </div>

    <div class="mt-4 grid md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="font-semibold mb-2">Online sekarang</h2>
            <ul id="presence-list" class="text-sm space-y-1 text-slate-600"></ul>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <h2 class="font-semibold mb-2">Cursor orang lain</h2>
            <ul id="cursor-list" class="text-sm space-y-1 text-slate-600"></ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/editor.js'])
@endpush
