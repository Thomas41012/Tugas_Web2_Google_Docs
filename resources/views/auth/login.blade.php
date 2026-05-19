@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Login</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', 'alice@docs.test') }}" required
                class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" value="password" required
                class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember">
            Ingat saya
        </label>
        <button type="submit" class="w-full bg-blue-600 text-white rounded-lg py-2 font-medium hover:bg-blue-700">
            Masuk
        </button>
    </form>

    <p class="mt-4 text-sm text-slate-600">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Daftar</a>
    </p>

    <div class="mt-6 text-xs text-slate-500 border-t pt-4">
        <p>Demo: alice@docs.test / password</p>
        <p>Demo: bob@docs.test / password</p>
    </div>
</div>
@endsection
