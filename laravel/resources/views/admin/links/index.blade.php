<x-admin.layout title="Odkazy a partneri">
    <x-slot:subtitle>Hlavné menu, partneri v hlavičke, odkazy v pätičke, videá a dokumenty na stiahnutie.</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.links.create') }}" class="inline-flex items-center gap-2 rounded-full bg-sstn-navy dark:bg-sstn-blue px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:hover:bg-[#0069ad]">
            <x-admin.icon name="plus" class="h-4 w-4" /> Nový odkaz
        </a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-2">
        @foreach (\App\Models\Link::GROUPS as $group => $groupLabel)
            <section class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <header class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-5 py-4">
                    <div>
                        <h2 class="font-semibold text-slate-900 dark:text-white">{{ $groupLabel }}</h2>
                        @if ($group === 'menu')
                            <p class="text-xs text-slate-500 dark:text-slate-400">Položky v hlavičke webu, zoradené podľa poradia. Miesto je približne na 5–6 krátkych položiek.</p>
                        @endif
                    </div>
                    <a href="{{ route('admin.links.create', ['group' => $group]) }}" class="text-sm font-medium text-sstn-blue dark:text-sky-400 hover:underline">+ pridať</a>
                </header>
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($groups->get($group, collect()) as $link)
                        <li class="flex items-center gap-3 px-5 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-slate-900 dark:text-white">{{ $link->label }}</p>
                                <a href="{{ $link->is_anchor ? route('home').$link->url : $link->url }}" target="_blank" rel="noopener" class="block truncate text-xs text-slate-500 dark:text-slate-400 hover:text-sstn-blue dark:hover:text-sky-400">{{ $link->url }}</a>
                                @if ($link->description)<p class="truncate text-xs text-slate-400 dark:text-slate-500">{{ $link->description }}</p>@endif
                            </div>
                            <a href="{{ route('admin.links.edit', $link) }}" class="rounded-lg p-1.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-sstn-navy dark:hover:text-sky-300" title="Upraviť"><x-admin.icon name="pencil" class="h-4 w-4" /></a>
                            <form method="post" action="{{ route('admin.links.destroy', $link) }}" onsubmit="return confirm('Zmazať odkaz „{{ addslashes($link->label) }}“?')">
                                @csrf @method('delete')
                                <button class="rounded-lg p-1.5 text-slate-500 dark:text-slate-400 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400" title="Zmazať"><x-admin.icon name="trash" class="h-4 w-4" /></button>
                            </form>
                        </li>
                    @empty
                        <li class="px-5 py-6 text-center text-sm text-slate-500 dark:text-slate-400">Žiadne odkazy.</li>
                    @endforelse
                </ul>
            </section>
        @endforeach
    </div>
</x-admin.layout>
