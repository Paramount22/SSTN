<x-admin.layout :title="$block->label">
    <x-slot:subtitle>{{ $block->hint ?: \App\Models\TextBlock::TYPES[$block->type] }}</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.text-blocks.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-slate-900 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 ring-1 ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
            <x-admin.icon name="arrow-left" class="h-4 w-4" /> Všetky texty
        </a>
    </x-slot:actions>

    <form method="post" action="{{ route('admin.text-blocks.update', $block) }}" class="max-w-3xl space-y-4">
        @csrf @method('put')
        <section class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
            <label for="value" class="block text-sm font-medium text-slate-700 dark:text-slate-200">
                @if ($block->type === 'list') Položky – každý riadok je jedna položka
                @elseif ($block->type === 'html') HTML kód
                @else Text @endif
            </label>
            <textarea id="value" name="value" rows="{{ $block->type === 'text' && mb_strlen((string) $block->value) < 200 ? 4 : 14 }}"
                      class="mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue {{ $block->type === 'html' ? 'font-mono' : '' }}">{{ old('value', $block->value) }}</textarea>
            <x-input-error :messages="$errors->get('value')" class="mt-1" />
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Kľúč bloku: <code class="rounded bg-slate-100 dark:bg-slate-800 px-1">{{ $block->key }}</code></p>
        </section>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-full bg-sstn-navy dark:bg-sstn-blue px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:hover:bg-[#0069ad]">Uložiť</button>
            <a href="{{ route('admin.text-blocks.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Zrušiť</a>
        </div>
    </form>
</x-admin.layout>
