@extends('layouts.app')

@section('title', 'Activity - '.$document->title)

@section('content')
<div class="mb-4">
    <a href="{{ route('documents.show', $document) }}" class="text-blue-600 hover:underline">← Kembali ke editor</a>
    <h1 class="text-2xl font-bold mt-2">Siapa Edit Apa: {{ $document->title }}</h1>
    <p class="text-sm text-slate-500">Log edit & deteksi konflik (siapa mengubah saat versi bentrok)</p>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b">
            <tr>
                <th class="text-left p-3">Waktu</th>
                <th class="text-left p-3">User</th>
                <th class="text-left p-3">Aksi</th>
                <th class="text-left p-3">Versi</th>
                <th class="text-left p-3">Konflik</th>
                <th class="text-left p-3">Cuplikan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($edits as $edit)
                <tr class="border-b border-slate-100">
                    <td class="p-3 whitespace-nowrap">{{ $edit->created_at->format('H:i:s') }}</td>
                    <td class="p-3 font-medium">{{ $edit->user->name }}</td>
                    <td class="p-3">{{ $edit->action }}</td>
                    <td class="p-3">v{{ $edit->document_version }}</td>
                    <td class="p-3">
                        @if ($edit->had_conflict)
                            <span class="text-amber-700 font-medium">Ya</span>
                            @if ($edit->conflict_with)
                                <span class="block text-xs text-slate-500">
                                    vs {{ $edit->conflict_with['user_name'] ?? 'user lain' }}
                                </span>
                            @endif
                        @else
                            <span class="text-slate-400">Tidak</span>
                        @endif
                    </td>
                    <td class="p-3 text-slate-600 max-w-xs truncate">{{ $edit->snippet }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-slate-500">Belum ada aktivitas edit.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
