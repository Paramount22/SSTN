<x-admin.layout title="Členské príspevky">
    <x-slot:subtitle>Druhy členstva, výška príspevku a počet hlasov. Zobrazujú sa v sekcii Členstvo.</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.membership-fees.create') }}" class="inline-flex items-center gap-2 rounded-full bg-sstn-navy px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80]">
            <x-admin.icon name="plus" class="h-4 w-4" /> Pridať druh členstva
        </a>
    </x-slot:actions>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3 w-16">Poradie</th>
                    <th class="px-5 py-3">Druh členstva</th>
                    <th class="px-5 py-3 text-right">Členský príspevok</th>
                    <th class="px-5 py-3 text-center">Hlasy / účastníci</th>
                    <th class="px-5 py-3">Zvýraznené</th>
                    <th class="px-5 py-3"><span class="sr-only">Akcie</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($fees as $fee)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-3 text-slate-400">{{ $fee->sort }}</td>
                        <td class="px-5 py-3 font-medium text-slate-900">{{ $fee->kind }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-900">{{ $fee->price_label }}</td>
                        <td class="px-5 py-3 text-center">{{ $fee->votes }}</td>
                        <td class="px-5 py-3">@if ($fee->is_featured)<span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-sstn-blue">áno</span>@else<span class="text-slate-400">—</span>@endif</td>
                        <td class="whitespace-nowrap px-5 py-3 text-right">
                            <a href="{{ route('admin.membership-fees.edit', $fee) }}" class="inline-flex rounded-lg px-2 py-1 text-slate-600 hover:bg-slate-100 hover:text-sstn-navy" title="Upraviť"><x-admin.icon name="pencil" class="h-4 w-4" /></a>
                            <form method="post" action="{{ route('admin.membership-fees.destroy', $fee) }}" class="inline" onsubmit="return confirm('Zmazať „{{ addslashes($fee->kind) }}“?')">
                                @csrf @method('delete')
                                <button class="inline-flex rounded-lg px-2 py-1 text-slate-600 hover:bg-red-50 hover:text-red-600" title="Zmazať"><x-admin.icon name="trash" class="h-4 w-4" /></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">Žiadne položky.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin.layout>
