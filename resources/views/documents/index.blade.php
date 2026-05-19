@extends('layouts.app')

@section('title', 'Dokumen Saya')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Dokumen Kolaboratif</h1>
</div>

<form method="POST" action="{{ route('documents.store') }}" class="bg-white rounded-xl shadow p-4 mb-6 flex gap-3">
    @csrf
    <input type="text" name="title" placeholder="Judul dokumen baru..." required
        class="flex-1 rounded-lg border border-slate-300 px-3 py-2">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        + Buat Dokumen
    </button>
</form>

<div class="grid gap-4">
    @forelse ($documents as $doc)
        <div class="bg-white rounded-xl shadow p-4 flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('documents.show', $doc) }}" class="text-lg font-semibold text-blue-700 hover:underline">
                    {{ $doc->title }}
                </a>
                <p class="text-sm text-slate-500 mt-1">
                    Oleh {{ $doc->owner->name }} · Versi {{ $doc->version }} · {{ $doc->updated_at->diffForHumans() }}
                </p>
            </div>
            <div class="flex gap-2 text-sm">
                <a href="{{ route('documents.history', $doc) }}" class="px-3 py-1 rounded border hover:bg-slate-50">History</a>
                <a href="{{ route('documents.activity', $doc) }}" class="px-3 py-1 rounded border hover:bg-slate-50">Activity</a>
                <form method="POST" action="{{ route('documents.destroy', $doc) }}" onsubmit="return confirm('Hapus dokumen?')">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1 rounded border text-red-600 hover:bg-red-50">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-slate-500">Belum ada dokumen. Buat dokumen pertama Anda.</p>
    @endforelse
</div>
@endsection
