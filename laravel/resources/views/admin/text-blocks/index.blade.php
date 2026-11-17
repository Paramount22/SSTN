<x-admin.layout title="Texty stránky">
    <x-slot:subtitle>Texty, ktoré sa zobrazujú na webe: úvod, poslanie, členstvo, kontakt. Bloky sú pevne dané, mení sa len ich obsah.</x-slot:subtitle>

    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <ul class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach ($blocks as $block)
                <li>
                    <a href="{{ route('admin.text-blocks.edit', $block) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-800">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-slate-900 dark:text-white">{{ $block->label }}</p>
                            <p class="mt-0.5 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit(strip_tags((string) $block->value), 160) ?: '— prázdne —' }}</p>
                        </div>
                        <span class="hidden rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-xs text-slate-600 dark:text-slate-300 sm:inline">{{ \App\Models\TextBlock::TYPES[$block->type] ?? $block->type }}</span>
                        <x-admin.icon name="pencil" class="h-4 w-4 shrink-0 text-slate-400 dark:text-slate-500" />
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</x-admin.layout>
