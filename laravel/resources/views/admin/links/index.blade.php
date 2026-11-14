<x-admin.layout title="Odkazy a partneri">
    <x-slot:subtitle>Partneri v hlavičke, odkazy v pätičke, videá a dokumenty na stiahnutie.</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.links.create') }}" class="inline-flex items-center gap-2 rounded-full bg-sstn-navy px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80]">
            <x-admin.icon name="plus" class="h-4 w-4" /> Nový odkaz
        </a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-2">
        @foreach (\App\Models\Link::GROUPS as $group => $groupLabel)
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h2 class="font-semibold text-slate-900">{{ $groupLabel }}</h2>
                    <a href="{{ route('admin.links.create', ['group' => $group]) }}" class="text-sm font-medium text-sstn-blue hover:underline">+ pridať</a>
                </header>
                <ul class="divide-y divide-slate-100">
                    @forelse ($groups->get($group, collect()) as $link)
                        <li class="flex items-center gap-3 px-5 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-slate-900">{{ $link->label }}</p>
                                <a href="{{ $link->url }}" target="_blank" rel="noopener" class="block truncate text-xs text-slate-500 hover:text-sstn-blue">{{ $link->url }}</a>
                                @if ($link->description)<p class="truncate text-xs text-slate-400">{{ $link->description }}</p>@endif
                            </div>
                            <a href="{{ route('admin.links.edit', $link) }}" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 hover:text-sstn-navy" title="Upraviť"><x-admin.icon name="pencil" class="h-4 w-4" /></a>
                            <form method="post" action="{{ route('admin.links.destroy', $link) }}" onsubmit="return confirm('Zmazať odkaz „{{ addslashes($link->label) }}“?')">
                                @csrf @method('delete')
                                <button class="rounded-lg p-1.5 text-slate-500 hover:bg-red-50 hover:text-red-600" title="Zmazať"><x-admin.icon name="trash" class="h-4 w-4" /></button>
                            </form>
                        </li>
                    @empty
                        <li class="px-5 py-6 text-center text-sm text-slate-500">Žiadne odkazy.</li>
                    @endforelse
                </ul>
            </section>
        @endforeach
    </div>
</x-admin.layout>
