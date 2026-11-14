@php $input = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue'; @endphp
<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="kind" class="block text-sm font-medium text-slate-700">Druh členstva <span class="text-red-500">*</span></label>
        <input id="kind" name="kind" type="text" value="{{ old('kind', $fee->kind) }}" required placeholder="Kolektívne do 50 zamestnancov" class="{{ $input }}">
        <x-input-error :messages="$errors->get('kind')" class="mt-1" />
    </div>
    <div>
        <label for="price" class="block text-sm font-medium text-slate-700">Členský príspevok (EUR) <span class="text-red-500">*</span></label>
        <input id="price" name="price" type="text" inputmode="decimal" value="{{ old('price', $fee->price) }}" required class="{{ $input }}">
        <x-input-error :messages="$errors->get('price')" class="mt-1" />
    </div>
    <div>
        <label for="votes" class="block text-sm font-medium text-slate-700">Počet hlasov a účastníkov na akciách <span class="text-red-500">*</span></label>
        <input id="votes" name="votes" type="number" min="0" value="{{ old('votes', $fee->votes) }}" required class="{{ $input }}">
        <x-input-error :messages="$errors->get('votes')" class="mt-1" />
    </div>
    <div>
        <label for="sort" class="block text-sm font-medium text-slate-700">Poradie</label>
        <input id="sort" name="sort" type="number" min="0" value="{{ old('sort', $fee->sort) }}" class="{{ $input }}">
    </div>
    <div class="flex items-end">
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $fee->is_featured)) class="rounded border-slate-300 text-sstn-blue focus:ring-sstn-blue">
            Zvýrazniť na webe (tmavá karta)
        </label>
    </div>
</div>
