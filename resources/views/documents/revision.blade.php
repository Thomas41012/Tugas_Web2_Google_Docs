@extends('layouts.app')

@section('title', 'Versi '.$revision->version_number)

@section('content')
<div class="mb-4">
    <a href="{{ route('documents.history', $document) }}" class="text-blue-600 hover:underline">← Kembali ke history</a>
    <h1 class="text-2xl font-bold mt-2">
        {{ $document->title }} — Versi {{ $revision->version_number }}
    </h1>
    <p class="text-sm text-slate-500">
        {{ $revision->user->name }} · {{ $revision->created_at->format('d M Y H:i:s') }}
    </p>
</div>

<div class="bg-white rounded-xl shadow p-6 prose max-w-none revision-preview">
    {!! $revision->content !!}
</div>

<form method="POST" action="{{ route('documents.restore', [$document, $revision]) }}" class="mt-4">
    @csrf
    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        Pulihkan versi ini
    </button>
</form>
@endsection
