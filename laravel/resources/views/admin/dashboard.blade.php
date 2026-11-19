<x-admin.layout title="Prehľad">
    <x-slot:subtitle>Vitajte späť, {{ auth()->user()->name }}. Tu je stav obsahu webu sstn.sk.</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.events.create') }}" class="inline-flex items-center gap-2 rounded-full bg-sstn-navy dark:bg-sstn-blue px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:hover:bg-[#0069ad]">
            <x-admin.icon name="plus" class="h-4 w-4" /> Nové podujatie
        </a>
    </x-slot:actions>

    @php
        $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
        $eur = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, ',', ' '), '0'), ',');
        $size = fn (int $bytes) => $bytes >= 1048576
            ? number_format($bytes / 1048576, 1, ',', ' ').' MB'
            : number_format($bytes / 1024, 0, ',', ' ').' kB';
        $year = now()->year;

        $cards = [
            ['label' => 'Podujatia spolu', 'value' => $stats['events'], 'sub' => 'zverejnené: '.$stats['events_published'].' · koncepty: '.($stats['events'] - $stats['events_published']),
             'icon' => 'calendar', 'href' => route('admin.events.index'), 'tint' => 'bg-blue-50 text-sstn-blue dark:bg-sky-500/10 dark:text-sky-400'],
            ['label' => 'Pripravované', 'value' => $stats['upcoming'], 'sub' => $stats['next'] ? 'najbližšie: '.$stats['next']->starts_on->format('j. n. Y') : 'nič nie je naplánované',
             'icon' => 'clock', 'href' => route('admin.events.index', ['filter' => 'upcoming']), 'tint' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'],
            ['label' => 'Uskutočnené', 'value' => $stats['past'], 'sub' => 'v roku '.$year.': '.$stats['past_this_year'],
             'icon' => 'check', 'href' => route('admin.events.index', ['filter' => 'past']), 'tint' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300'],
            ['label' => 'Články', 'value' => $stats['articles'], 'sub' => 'zverejnené: '.$stats['articles_published'].' · koncepty: '.($stats['articles'] - $stats['articles_published']),
             'icon' => 'document', 'href' => route('admin.articles.index'), 'tint' => 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-300'],
            ['label' => 'Fotografie', 'value' => $stats['photos'], 'sub' => 'článkov bez fotografie: '.($stats['articles'] - $stats['photos']),
             'icon' => 'photo', 'href' => route('admin.articles.index'), 'tint' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400'],
            ['label' => 'Súbory na stiahnutie', 'value' => $stats['attachments'], 'sub' => 'spolu '.$size($stats['attachments_size']),
             'icon' => 'paperclip', 'href' => route('admin.events.index'), 'tint' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'],
            ['label' => 'Odkazy a partneri', 'value' => $stats['links'], 'sub' => 'v menu: '.$stats['menu_links'].' · partneri: '.$stats['partners'],
             'icon' => 'link', 'href' => route('admin.links.index'), 'tint' => 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400'],
            ['label' => 'Členské príspevky', 'value' => $stats['fees'], 'sub' => $stats['fees'] ? 'od '.$eur($stats['fee_min']).' do '.$eur($stats['fee_max']).' EUR' : 'zatiaľ žiadne',
             'icon' => 'users', 'href' => route('admin.membership-fees.index'), 'tint' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400'],
        ];
    @endphp

    {{-- Stat tiles: one headline number each, with a line of context --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as $card)
            <a href="{{ $card['href'] }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p>
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $card['tint'] }}">
                        <x-admin.icon :name="$card['icon']" class="h-5 w-5" />
                    </span>
                </div>
                <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $fmt($card['value']) }}</p>
                <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $card['sub'] }}</p>
            </a>
        @endforeach
    </div>

    {{-- Breakdowns: ranked bars in one hue, the figure printed beside each bar --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        @foreach (['Podujatia podľa typu' => $byType, 'Podujatia podľa roka' => $byYear] as $heading => $rows)
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <header class="flex items-baseline justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <h2 class="font-semibold text-slate-900 dark:text-white">{{ $heading }}</h2>
                    <span class="text-xs text-slate-400 dark:text-slate-500">spolu {{ $fmt($stats['events']) }}</span>
                </header>
                @if ($rows->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">Zatiaľ žiadne podujatia.</p>
                @else
                    @php $max = max($rows->max(), 1); @endphp
                    <ul class="space-y-3 px-5 py-4">
                        @foreach ($rows as $name => $count)
                            @php $share = $stats['events'] ? round($count / $stats['events'] * 100) : 0; @endphp
                            <li class="grid grid-cols-[1fr_auto] items-center gap-x-3 gap-y-1.5 sm:grid-cols-[10rem_1fr_3rem]" title="{{ $name }}: {{ $count }} ({{ $share }} %)">
                                <span class="truncate text-sm text-slate-700 dark:text-slate-200">{{ $name }}</span>
                                <span class="text-right text-sm font-semibold tabular-nums text-slate-900 sm:order-last dark:text-white">{{ $count }}</span>
                                <span class="col-span-2 h-2.5 rounded-full bg-blue-50 sm:col-span-1 dark:bg-sky-500/10" aria-hidden="true">
                                    <span class="block h-full rounded-full bg-sstn-blue" style="width: {{ max(round($count / $max * 100), 2) }}%"></span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Upcoming events --}}
        <section class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm xl:col-span-2">
            <header class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-5 py-4">
                <h2 class="font-semibold text-slate-900 dark:text-white">Pripravované podujatia</h2>
                <a href="{{ route('admin.events.index', ['filter' => 'upcoming']) }}" class="text-sm font-medium text-sstn-blue dark:text-sky-400 hover:underline">Všetky</a>
            </header>
            @forelse ($upcoming as $event)
                <a href="{{ route('admin.events.edit', $event) }}" class="flex items-center gap-4 border-b border-slate-100 dark:border-slate-800 px-5 py-3 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <div class="w-14 shrink-0 rounded-xl bg-sstn-navy dark:bg-sstn-blue py-2 text-center text-white">
                        <div class="text-lg font-bold leading-none">{{ $event->starts_on->format('j') }}</div>
                        <div class="mt-1 text-[10px] font-semibold uppercase tracking-wider opacity-80">{{ $event->starts_on->translatedFormat('M') }}</div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-medium text-slate-900 dark:text-white">{{ $event->title }}</p>
                        <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ $event->date_label }}@if($event->location) · {{ $event->location }}@endif</p>
                    </div>
                    <div class="hidden items-center gap-3 text-xs text-slate-500 dark:text-slate-400 sm:flex">
                        <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5">{{ $event->type_label }}</span>
                        <span class="inline-flex items-center gap-1"><x-admin.icon name="paperclip" class="h-3.5 w-3.5" />{{ $event->attachments_count }}</span>
                        @unless ($event->is_published)
                            <span class="rounded-full bg-amber-100 dark:bg-amber-500/15 px-2 py-0.5 text-amber-800 dark:text-amber-300">koncept</span>
                        @endunless
                    </div>
                </a>
            @empty
                <p class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">Žiadne pripravované podujatia. <a href="{{ route('admin.events.create') }}" class="font-medium text-sstn-blue dark:text-sky-400 hover:underline">Pridať prvé</a></p>
            @endforelse
        </section>

        {{-- Recently edited + quick links --}}
        <div class="space-y-6">
            <section class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <header class="border-b border-slate-100 dark:border-slate-800 px-5 py-4"><h2 class="font-semibold text-slate-900 dark:text-white">Naposledy upravené</h2></header>
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($recent as $event)
                        <li>
                            <a href="{{ route('admin.events.edit', $event) }}" class="flex items-center justify-between gap-3 px-5 py-3 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
                                <span class="truncate text-slate-800 dark:text-slate-100">{{ $event->title }}</span>
                                <span class="shrink-0 text-xs text-slate-400 dark:text-slate-500">{{ $event->updated_at->diffForHumans() }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="px-5 py-6 text-center text-sm text-slate-500 dark:text-slate-400">Zatiaľ nič.</li>
                    @endforelse
                </ul>
            </section>

            <section class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <header class="border-b border-slate-100 dark:border-slate-800 px-5 py-4"><h2 class="font-semibold text-slate-900 dark:text-white">Texty stránky</h2></header>
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($textBlocks as $block)
                        <li>
                            <a href="{{ route('admin.text-blocks.edit', $block) }}" class="flex items-center justify-between gap-3 px-5 py-3 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
                                <span class="text-slate-800 dark:text-slate-100">{{ $block->label }}</span>
                                <x-admin.icon name="pencil" class="h-4 w-4 text-slate-400 dark:text-slate-500" />
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-slate-100 dark:border-slate-800 px-5 py-3">
                    <a href="{{ route('admin.text-blocks.index') }}" class="text-sm font-medium text-sstn-blue dark:text-sky-400 hover:underline">Všetky texty</a>
                </div>
            </section>
        </div>
    </div>
</x-admin.layout>
