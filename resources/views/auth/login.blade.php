<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ setting('site_name', config('app.name', 'Laravel')) }}</title>
    <link rel="icon" type="image/x-icon" href="{{ setting('favicon', '/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.fontawesome')
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-size: 14px; }
    </style>
</head>
<body class="min-h-screen bg-slate-200/80 text-slate-900 antialiased" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="min-h-screen flex items-center justify-center px-4 py-6">
        <section class="w-full max-w-md rounded-2xl bg-white p-5 sm:p-6 shadow-[0_20px_60px_rgba(15,23,42,0.12)]">
            <div class="mb-6 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center text-primary">
                    <x-fa-icon name="bus" class="fa-fw text-4xl" />
                </a>
                <h1 class="mt-3 text-xl font-extrabold text-slate-900">Login Panel</h1>
                <p class="mt-1 text-sm text-slate-500">Halaman ini khusus untuk akses panel admin.</p>
            </div>

            <x-auth-session-status class="mb-4 text-sm text-emerald-600" :status="session('status')" />

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-slate-900">Username</label>
                    <div class="flex items-center rounded-xl border border-slate-300 bg-slate-50 px-3">
                        <x-fa-icon name="user" class="text-slate-500" />
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Username" class="w-full border-0 bg-transparent px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0">
                    </div>
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-slate-900">Password</label>
                    <div class="flex items-center rounded-xl border border-slate-300 bg-slate-50 px-3">
                        <x-fa-icon name="lock" class="text-slate-500" />
                        <input id="password" name="password" x-bind:type="showPassword ? 'text' : 'password'" required autocomplete="current-password" placeholder="Password" class="w-full border-0 bg-transparent px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0">
                        <button type="button" @click="showPassword = !showPassword" class="-mr-1 rounded-lg p-2 text-slate-500 hover:bg-slate-200/70" aria-label="Show password">
                            <x-fa-icon name="eye" class="text-base" x-show="!showPassword" />
                            <x-fa-icon name="eye-slash" class="text-base" x-show="showPassword" x-cloak />
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-orange-500 focus:ring-orange-500">
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="mt-1 w-full rounded-xl bg-orange-300 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-orange-400">
                    Sign in
                </button>
            </form>
        </section>
    </div>
</body>
</html>
