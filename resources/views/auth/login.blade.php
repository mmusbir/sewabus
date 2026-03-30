<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ setting('site_name', config('app.name', 'Laravel')) }}</title>
    <link rel="icon" type="image/x-icon" href="{{ setting('favicon', '/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-size: 14px; }
    </style>
</head>
<body class="min-h-screen bg-slate-200/80 text-slate-900 antialiased" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="min-h-screen flex items-center justify-center px-4 py-6 md:px-8">
        <section class="w-full max-w-4xl rounded-2xl bg-white p-4 md:p-5 shadow-[0_20px_60px_rgba(15,23,42,0.12)]">
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div class="relative hidden overflow-hidden rounded-2xl lg:block min-h-[380px]">
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ setting('hero_image_1', setting('hero_image', '/stitch_img_hero.jpg')) }}')"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-orange-500/70 via-orange-400/20 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-8 px-6">
                        <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                            {{ setting('site_name', 'CahayaBone') }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="w-full max-w-sm mx-auto">
                        <h2 class="mb-5 text-lg font-extrabold text-slate-900">Sign in</h2>

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
                                    <svg class="h-5 w-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 1 1 15 0A17.933 17.933 0 0 1 12 21.75a17.933 17.933 0 0 1-7.5-1.632Z" />
                                    </svg>
                                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Username" class="w-full border-0 bg-transparent px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0">
                                </div>
                            </div>

                            <div>
                                <label for="password" class="mb-2 block text-sm font-bold text-slate-900">Password</label>
                                <div class="flex items-center rounded-xl border border-slate-300 bg-slate-50 px-3">
                                    <svg class="h-5 w-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-1.5 0h12a1.5 1.5 0 0 1 1.5 1.5v7.5a1.5 1.5 0 0 1-1.5 1.5H6a1.5 1.5 0 0 1-1.5-1.5V12a1.5 1.5 0 0 1 1.5-1.5Z" />
                                    </svg>
                                    <input id="password" name="password" x-bind:type="showPassword ? 'text' : 'password'" required autocomplete="current-password" placeholder="Password" class="w-full border-0 bg-transparent px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0">
                                    <button type="button" @click="showPassword = !showPassword" class="-mr-1 rounded-lg p-2 text-slate-500 hover:bg-slate-200/70" aria-label="Show password">
                                        <svg x-show="!showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5.25 12 5.25S20.268 7.943 21.542 12c-1.274 4.057-5.065 6.75-9.542 6.75S3.732 16.057 2.458 12Z" />
                                            <circle cx="12" cy="12" r="3" stroke-width="2"></circle>
                                        </svg>
                                        <svg x-show="showPassword" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 3 18 18M10.585 10.586A2 2 0 0 0 13.414 13.414M9.363 5.365A9.97 9.97 0 0 1 12 5.25c4.477 0 8.268 2.693 9.542 6.75a11.12 11.12 0 0 1-4.03 5.568M6.228 6.228A11.2 11.2 0 0 0 2.458 12c1.274 4.057 5.065 6.75 9.542 6.75a9.96 9.96 0 0 0 5.305-1.522" />
                                        </svg>
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
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
