<x-admin.layout :title="$block->label">
    <x-slot:subtitle>{{ $block->hint ?: \App\Models\TextBlock::TYPES[$block->type] }}</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.text-blocks.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
            <x-admin.icon name="arrow-left" class="h-4 w-4" /> Všetky texty
        </a>
    </x-slot:actions>

    <form method="post" action="{{ route('admin.text-blocks.update', $block) }}" class="max-w-3xl space-y-4">
        @csrf @method('put')
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <label for="value" class="block text-sm font-medium text-slate-700">
                @if ($block->type === 'list') Položky – každý riadok je jedna položka
                @elseif ($block->type === 'html') HTML kód
                @else Text @endif
            </label>
            <textarea id="value" name="value" rows="{{ $block->type === 'text' && mb_strlen((string) $block->value) < 200 ? 4 : 14 }}"
                      class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue {{ $block->type === 'html' ? 'font-mono' : '' }}">{{ old('value', $block->value) }}</textarea>
            <x-input-error :messages="$errors->get('value')" class="mt-1" />
            <p class="mt-2 text-xs text-slate-500">Kľúč bloku: <code class="rounded bg-slate-100 px-1">{{ $block->key }}</code></p>
        </section>
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-full bg-sstn-navy px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80]">Uložiť</button>
            <a href="{{ route('admin.text-blocks.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Zrušiť</a>
        </div>
    </form>
</x-admin.layout>
