<!DOCTYPE html>
<html lang="sk" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ isset($heading) ? trim(strip_tags($heading)).' · ' : '' }}SSTN admin</title>
    <script>
        // Same theme as the admin: saved choice, otherwise the system setting.
        try {
            const theme = localStorage.getItem('sstn-admin-theme');
            if (theme === 'dark' || (! theme && matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        } catch (e) {}
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-slate-700 antialiased dark:text-slate-200">
<div class="flex min-h-full">

    {{-- Brand panel: navy with the millimetre grid of the public site --}}
    <aside class="relative hidden w-[46%] max-w-2xl flex-col justify-between overflow-hidden bg-sstn-navy p-12 text-white lg:flex"
           style="background-image: linear-gradient(rgba(255,255,255,.07) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.07) 1px, transparent 1px), linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px); background-size: 64px 64px, 64px 64px, 16px 16px, 16px 16px;">
        <div class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full bg-sstn-blue/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-24 h-96 w-96 rounded-full bg-sky-400/20 blur-3xl"></div>

        <a href="{{ route('home') }}" class="relative inline-flex w-fit items-center rounded-xl bg-white px-3 py-2 shadow-lg shadow-black/10">
            <img src="{{ asset('images/sstn-logo-mark.webp') }}" alt="SSTN – úvodná stránka webu" class="h-9 w-auto">
        </a>

        <div class="relative">
            <p class="text-sm font-semibold uppercase tracking-[.2em] text-sky-300">Administrácia</p>
            <h2 class="mt-4 text-4xl font-bold leading-tight tracking-tight xl:text-5xl">Správa obsahu<br>webu SSTN</h2>
            <p class="mt-5 max-w-md text-base leading-relaxed text-slate-300">Podujatia s pozvánkami, články s fotografiami, texty stránky, členské príspevky a odkazy na jednom mieste.</p>

            {{-- Dimension line, as in the public site's hero --}}
            <div class="mt-10 flex max-w-md items-center gap-3 text-[11px] font-semibold uppercase tracking-[.3em] text-sky-300/80" aria-hidden="true">
                <span class="h-3 w-px bg-sky-300/60"></span>
                <span class="h-px flex-1 bg-sky-300/60"></span>
                <span>STN · EN · ISO</span>
                <span class="h-px flex-1 bg-sky-300/60"></span>
                <span class="h-3 w-px bg-sky-300/60"></span>
            </div>
        </div>

        <p class="relative text-sm text-slate-400">© {{ now()->year }} Slovenská spoločnosť pre technickú normalizáciu</p>
    </aside>

    {{-- Form --}}
    <main class="flex flex-1 flex-col items-center justify-center px-4 py-12 sm:px-6">
        <div class="w-full max-w-sm">
            <a href="{{ route('home') }}" class="mb-10 inline-flex lg:hidden">
                <img src="{{ asset('images/sstn-logo-mark.webp') }}" alt="SSTN – úvodná stránka webu" class="h-10 w-auto dark:rounded-md dark:bg-white dark:px-1.5 dark:py-1">
            </a>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $heading ?? 'Administrácia' }}</h1>
            @isset($intro)
                <p class="mt-2 text-sm leading-relaxed text-slate-500 dark:text-slate-400">{{ $intro }}</p>
            @endisset

            <div class="mt-8">
                {{ $slot }}
            </div>

            <a href="{{ route('home') }}" class="mt-12 inline-flex items-center gap-2 text-sm text-slate-500 hover:text-sstn-blue dark:text-slate-400 dark:hover:text-sky-400">
                <x-admin.icon name="arrow-left" class="h-4 w-4" /> Späť na web
            </a>
        </div>
    </main>
</div>
</body>
</html>
