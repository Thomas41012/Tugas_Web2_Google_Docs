<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Collaborative Docs')</title>
    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen">
    <header class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('documents.index') }}" class="font-semibold text-lg text-blue-700">Collaborative Docs</a>
            @auth
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-slate-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:underline">Logout</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-6">
        @if (session('status'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
