<x-admin.layout title="Prehľad">
    <x-slot:subtitle>Vitajte späť, {{ auth()->user()->name }}. Tu je stav obsahu webu sstn.sk.</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.events.create') }}" class="inline-flex items-center gap-2 rounded-full bg-sstn-navy dark:bg-sstn-blue px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:hover:bg-[#0069ad]">
            <x-admin.icon name="plus" class="h-4 w-4" /> Nové podujatie
        </a>
    </x-slot:actions>

    {{-- Stat cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Podujatia spolu', 'value' => $stats['events'], 'icon' => 'calendar', 'href' => route('admin.events.index'), 'tint' => 'bg-blue-50 dark:bg-sky-500/10 text-sstn-blue dark:text-sky-400'],
            ['label' => 'Pripravované', 'value' => $stats['upcoming'], 'icon' => 'clock', 'href' => route('admin.events.index', ['filter' => 'upcoming']), 'tint' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'],
            ['label' => 'Súbory na stiahnutie', 'value' => $stats['attachments'], 'icon' => 'paperclip', 'href' => route('admin.events.index'), 'tint' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400'],
            ['label' => 'Odkazy a partneri', 'value' => $stats['links'], 'icon' => 'link', 'href' => route('admin.links.index'), 'tint' => 'bg-violet-50 text-violet-600'],
        ] as $card)
            <a href="{{ $card['href'] }}" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p>
                    <span class="grid h-9 w-9 place-items-center rounded-xl {{ $card['tint'] }}">
                        <x-admin.icon :name="$card['icon']" class="h-5 w-5" />
                    </span>
                </div>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ number_format($card['value'], 0, ',', ' ') }}</p>
            </a>
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
