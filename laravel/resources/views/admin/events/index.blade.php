<x-admin.layout title="Podujatia">
    <x-slot:subtitle>Semináre, konferencie, exkurzie a valné zhromaždenia s pozvánkami na stiahnutie.</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.events.create') }}" class="inline-flex items-center gap-2 rounded-full bg-sstn-navy dark:bg-sstn-blue px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:hover:bg-[#0069ad]">
            <x-admin.icon name="plus" class="h-4 w-4" /> Nové podujatie
        </a>
    </x-slot:actions>

    <div class="mb-4 flex flex-wrap items-center gap-2">
        @foreach (['' => 'Všetky', 'upcoming' => 'Pripravované', 'past' => 'Uskutočnené', 'draft' => 'Koncepty'] as $key => $label)
            <a href="{{ route('admin.events.index', array_filter(['filter' => $key, 'q' => request('q')])) }}"
               class="rounded-full px-3.5 py-1.5 text-sm font-medium {{ $filter === $key ? 'bg-sstn-navy dark:bg-sstn-blue text-white' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 ring-1 ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' }}">{{ $label }}</a>
        @endforeach
        @if (request('q'))
            <span class="ml-2 text-sm text-slate-500 dark:text-slate-400">Hľadané: „{{ request('q') }}“ · <a href="{{ route('admin.events.index', ['filter' => $filter]) }}" class="text-sstn-blue dark:text-sky-400 hover:underline">zrušiť</a></span>
        @endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="px-5 py-3">Dátum</th>
                    <th class="px-5 py-3">Podujatie</th>
                    <th class="hidden px-5 py-3 md:table-cell">Typ</th>
                    <th class="hidden px-5 py-3 lg:table-cell">Miesto</th>
                    <th class="px-5 py-3 text-center">Prílohy</th>
                    <th class="px-5 py-3">Stav</th>
                    <th class="px-5 py-3"><span class="sr-only">Akcie</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($events as $event)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/60">
                        <td class="whitespace-nowrap px-5 py-3 font-medium text-slate-700 dark:text-slate-200">{{ $event->date_label }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.events.edit', $event) }}" class="font-medium text-slate-900 dark:text-white hover:text-sstn-blue dark:hover:text-sky-400">{{ $event->title }}</a>
                            @if ($event->organizers)<p class="truncate text-xs text-slate-500 dark:text-slate-400 lg:max-w-md">{{ $event->organizers }}</p>@endif
                        </td>
                        <td class="hidden px-5 py-3 md:table-cell"><span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-xs text-slate-700 dark:text-slate-200">{{ $event->type_label }}</span></td>
                        <td class="hidden max-w-xs truncate px-5 py-3 text-slate-500 dark:text-slate-400 lg:table-cell">{{ $event->location }}</td>
                        <td class="px-5 py-3 text-center text-slate-600 dark:text-slate-300">{{ $event->attachments_count }}</td>
                        <td class="px-5 py-3">
                            @if (!$event->is_published)
                                <span class="rounded-full bg-amber-100 dark:bg-amber-500/15 px-2 py-0.5 text-xs font-medium text-amber-800 dark:text-amber-300">Koncept</span>
                            @elseif ($event->is_upcoming)
                                <span class="rounded-full bg-emerald-100 dark:bg-emerald-500/15 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:text-emerald-300">Pripravované</span>
                            @else
                                <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-xs font-medium text-slate-600 dark:text-slate-300">Uskutočnené</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-3 text-right">
                            <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-sstn-navy dark:hover:text-sky-300" title="Upraviť"><x-admin.icon name="pencil" class="h-4 w-4" /><span class="sr-only">Upraviť</span></a>
                            <form method="post" action="{{ route('admin.events.destroy', $event) }}" class="inline" data-confirm="Podujatie sa zmaže z webu spolu so všetkými prílohami." data-confirm-title="Zmazať podujatie?" data-confirm-item="{{ $event->title }}">
                                @csrf @method('delete')
                                <button class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400" title="Zmazať"><x-admin.icon name="trash" class="h-4 w-4" /><span class="sr-only">Zmazať</span></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-slate-500 dark:text-slate-400">Žiadne podujatia.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($events->hasPages())
            <div class="border-t border-slate-100 dark:border-slate-800 px-5 py-3">{{ $events->links() }}</div>
        @endif
    </div>
</x-admin.layout>
