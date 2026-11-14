@props(['title' => 'Administrácia'])

@php
    $nav = [
        ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'home', 'label' => 'Prehľad'],
        ['route' => 'admin.events.index', 'match' => 'admin.events.*', 'icon' => 'calendar', 'label' => 'Podujatia', 'badge' => \App\Models\Event::upcoming()->count() ?: null],
        ['route' => 'admin.text-blocks.index', 'match' => 'admin.text-blocks.*', 'icon' => 'document', 'label' => 'Texty stránky'],
        ['route' => 'admin.membership-fees.index', 'match' => 'admin.membership-fees.*', 'icon' => 'users', 'label' => 'Členské príspevky'],
        ['route' => 'admin.links.index', 'match' => 'admin.links.*', 'icon' => 'link', 'label' => 'Odkazy a partneri'],
    ];
@endphp
<!DOCTYPE html>
<html lang="sk" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · SSTN admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-slate-700 antialiased" x-data="{ sidebar: false }">
<div class="flex min-h-full">

    {{-- Sidebar --}}
    <div x-show="sidebar" x-transition.opacity class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden" @click="sidebar = false" x-cloak></div>
    <aside class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform lg:static lg:translate-x-0"
           :class="sidebar && 'translate-x-0'">
        <div class="flex h-16 items-center gap-3 border-b border-slate-200 px-5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/sstn-logo-mark.webp') }}" alt="SSTN" class="h-8 w-auto">
                <span class="text-sm font-semibold tracking-wide text-sstn-navy">Administrácia</span>
            </a>
            <button class="ml-auto rounded-md p-1.5 text-slate-500 hover:bg-slate-100 lg:hidden" @click="sidebar = false" aria-label="Zavrieť menu">
                <x-admin.icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            @foreach ($nav as $item)
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                          {{ $active ? 'bg-blue-50 text-sstn-navy' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    @if ($active)
                        <span class="absolute inset-y-2 -left-3 w-1 rounded-r bg-sstn-blue"></span>
                    @endif
                    <x-admin.icon :name="$item['icon']" class="h-5 w-5 {{ $active ? 'text-sstn-blue' : 'text-slate-400 group-hover:text-slate-600' }}" />
                    <span>{{ $item['label'] }}</span>
                    @if (!empty($item['badge']))
                        <span class="ml-auto rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="border-t border-slate-200 p-4">
            <a href="{{ url('/') }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-sm text-slate-500 hover:text-sstn-blue">
                <x-admin.icon name="external" class="h-4 w-4" /> Zobraziť web
            </a>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6 lg:px-8">
            <button class="rounded-md p-2 text-slate-500 hover:bg-slate-100 lg:hidden" @click="sidebar = true" aria-label="Otvoriť menu">
                <x-admin.icon name="menu" class="h-5 w-5" />
            </button>

            <form action="{{ route('admin.events.index') }}" method="get" class="hidden max-w-sm flex-1 sm:block">
                <label class="relative block">
                    <span class="sr-only">Hľadať podujatie</span>
                    <x-admin.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="search" name="q" value="{{ request()->routeIs('admin.events.index') ? request('q') : '' }}" placeholder="Hľadať podujatie…"
                           class="w-full rounded-full border-slate-200 bg-slate-50 py-2 pl-9 pr-4 text-sm focus:border-sstn-blue focus:ring-sstn-blue">
                </label>
            </form>

            <div class="ml-auto flex items-center gap-2">
                <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <button @click="open = !open" class="flex items-center gap-3 rounded-full py-1 pl-1 pr-3 hover:bg-slate-100">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-sstn-navy text-sm font-semibold text-white">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden text-sm font-medium text-slate-700 sm:block">{{ auth()->user()->name }}</span>
                        <x-admin.icon name="chevron-down" class="h-4 w-4 text-slate-400" />
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition x-cloak
                         class="absolute right-0 mt-2 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Môj profil</a>
                        <form method="post" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">Odhlásiť sa</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity
                     class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <x-admin.icon name="check" class="h-5 w-5 text-emerald-600" />
                    <span>{{ session('status') }}</span>
                    <button class="ml-auto text-emerald-700/70 hover:text-emerald-900" @click="show = false" aria-label="Zavrieť">✕</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-semibold">Formulár obsahuje chyby:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $title }}</h1>
                    @isset($subtitle)
                        <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
                    @endisset
                </div>
                @isset($actions)
                    <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
                @endisset
            </div>

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
