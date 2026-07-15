<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ARTE Y METAL') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=cinzel:400,600,700|manrope:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#050505] text-[#1e1a11] antialiased" style="font-family:'Manrope',sans-serif;">
        <div class="relative min-h-dvh overflow-hidden">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_10%_10%,rgba(212,175,85,0.16),transparent_42%),radial-gradient(circle_at_88%_76%,rgba(158,118,36,0.16),transparent_34%)]"></div>

            <div class="relative mx-auto flex min-h-dvh w-full max-w-7xl items-center justify-center px-4 py-8">
                <main class="w-full max-w-xl rounded-3xl border border-[#d9be75]/80 bg-white p-6 shadow-[0_0_45px_rgba(212,175,85,0.28)] sm:p-8 lg:p-10">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-feedback-modals />
    </body>
</html>
