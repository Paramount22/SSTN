<x-admin.layout title="Podujatia">
    <x-slot:subtitle>Semináre, konferencie, exkurzie a valné zhromaždenia s pozvánkami na stiahnutie.</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.events.create') }}" class="inline-flex items-center gap-2 rounded-full bg-sstn-navy px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80]">
            <x-admin.icon name="plus" class="h-4 w-4" /> Nové podujatie
        </a>
    </x-slot:actions>

    <div class="mb-4 flex flex-wrap items-center gap-2">
        @foreach (['' => 'Všetky', 'upcoming' => 'Pripravované', 'past' => 'Uskutočnené', 'draft' => 'Koncepty'] as $key => $label)
            <a href="{{ route('admin.events.index', array_filter(['filter' => $key, 'q' => request('q')])) }}"
               class="rounded-full px-3.5 py-1.5 text-sm font-medium {{ $filter === $key ? 'bg-sstn-navy text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-slate-50' }}">{{ $label }}</a>
        @endforeach
        @if (request('q'))
            <span class="ml-2 text-sm text-slate-500">Hľadané: „{{ request('q') }}“ · <a href="{{ route('admin.events.index', ['filter' => $filter]) }}" class="text-sstn-blue hover:underline">zrušiť</a></span>
        @endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
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
            <tbody class="divide-y divide-slate-100">
                @forelse ($events as $event)
                    <tr class="hover:bg-slate-50/60">
                        <td class="whitespace-nowrap px-5 py-3 font-medium text-slate-700">{{ $event->date_label }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.events.edit', $event) }}" class="font-medium text-slate-900 hover:text-sstn-blue">{{ $event->title }}</a>
                            @if ($event->organizers)<p class="truncate text-xs text-slate-500 lg:max-w-md">{{ $event->organizers }}</p>@endif
                        </td>
                        <td class="hidden px-5 py-3 md:table-cell"><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $event->type_label }}</span></td>
                        <td class="hidden max-w-xs truncate px-5 py-3 text-slate-500 lg:table-cell">{{ $event->location }}</td>
                        <td class="px-5 py-3 text-center text-slate-600">{{ $event->attachments_count }}</td>
                        <td class="px-5 py-3">
                            @if (!$event->is_published)
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Koncept</span>
                            @elseif ($event->is_upcoming)
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800">Pripravované</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">Uskutočnené</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-3 text-right">
                            <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-slate-600 hover:bg-slate-100 hover:text-sstn-navy" title="Upraviť"><x-admin.icon name="pencil" class="h-4 w-4" /><span class="sr-only">Upraviť</span></a>
                            <form method="post" action="{{ route('admin.events.destroy', $event) }}" class="inline" onsubmit="return confirm('Naozaj zmazať podujatie „{{ addslashes($event->title) }}“ aj s prílohami?')">
                                @csrf @method('delete')
                                <button class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-slate-600 hover:bg-red-50 hover:text-red-600" title="Zmazať"><x-admin.icon name="trash" class="h-4 w-4" /><span class="sr-only">Zmazať</span></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-slate-500">Žiadne podujatia.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($events->hasPages())
            <div class="border-t border-slate-100 px-5 py-3">{{ $events->links() }}</div>
        @endif
    </div>
</x-admin.layout>
