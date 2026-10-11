<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TrackKas') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-brand-50 bg-gradient-to-br from-brand-50 via-brand-50 to-brand-100 px-4 py-10">
            <div class="w-full max-w-md rounded-2xl bg-white px-8 py-9 shadow-[0_24px_60px_-24px_rgba(163,24,35,0.35)] ring-1 ring-black/[0.03]">
                <div class="flex flex-col items-center text-center">
                    <a href="/" class="flex flex-col items-center">
                        <x-brand-logo class="h-16 w-16" />

                        <span class="mt-4 text-2xl font-bold tracking-tight text-gray-800">
                            Track<span class="text-brand-600">Kas</span>
                        </span>
                    </a>

                    <p class="mt-1.5 text-sm text-gray-400">{{ __('Kelola keuangan dengan mudah') }}</p>
                </div>

                <div class="mt-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
