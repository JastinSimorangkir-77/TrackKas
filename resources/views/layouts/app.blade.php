<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-sidebar="expanded">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TrackKas') }}</title>

        {{-- Remembered before the first paint so a collapsed sidebar never flashes open. --}}
        <script>
            try {
                if (localStorage.getItem('trackkas-sidebar') === 'collapsed') {
                    document.documentElement.setAttribute('data-sidebar', 'collapsed');
                }
            } catch (error) {}
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gray-50 lg:flex">
            @include('layouts.sidebar')

            <div class="min-w-0 flex-1">
                @isset($header)
                    <header class="border-b border-gray-100 bg-white">
                        <div class="px-6 py-4 lg:px-9">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="px-6 py-8 lg:px-9">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
