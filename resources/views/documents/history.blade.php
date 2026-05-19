@extends('layouts.app')

@section('title', 'History - '.$document->title)

@section('content')
<div class="mb-4">
    <a href="{{ route('documents.show', $document) }}" class="text-blue-600 hover:underline">← Kembali ke editor</a>
    <h1 class="text-2xl font-bold mt-2">Version History: {{ $document->title }}</h1>
</div>

<div class="space-y-3">
    @forelse ($revisions as $revision)
        <div class="bg-white rounded-xl shadow p-4 flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold">Versi {{ $revision->version_number }}</p>
                <p class="text-sm text-slate-500">
                    {{ $revision->user->name }} · {{ $revision->created_at->format('d M Y H:i') }}
                    @if ($revision->change_summary)
                        · {{ $revision->change_summary }}
                    @endif
                </p>
            </div>
            <div class="flex gap-2 text-sm">
                <a href="{{ route('documents.revision', [$document, $revision]) }}" class="px-3 py-1 border rounded hover:bg-slate-50">Lihat</a>
                <form method="POST" action="{{ route('documents.restore', [$document, $revision]) }}">
                    @csrf
                    <button class="px-3 py-1 border rounded text-blue-700 hover:bg-blue-50">Restore</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-slate-500">Belum ada revisi tersimpan.</p>
    @endforelse
</div>
@endsection
