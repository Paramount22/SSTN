@php $input = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue'; @endphp
<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="group" class="block text-sm font-medium text-slate-700">Skupina <span class="text-red-500">*</span></label>
        <select id="group" name="group" class="{{ $input }}">
            @foreach (\App\Models\Link::GROUPS as $value => $label)
                <option value="{{ $value }}" @selected(old('group', $link->group) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="sort" class="block text-sm font-medium text-slate-700">Poradie</label>
        <input id="sort" name="sort" type="number" min="0" value="{{ old('sort', $link->sort) }}" class="{{ $input }}">
    </div>
    <div class="sm:col-span-2">
        <label for="label" class="block text-sm font-medium text-slate-700">Názov <span class="text-red-500">*</span></label>
        <input id="label" name="label" type="text" value="{{ old('label', $link->label) }}" required class="{{ $input }}">
        <x-input-error :messages="$errors->get('label')" class="mt-1" />
    </div>
    <div class="sm:col-span-2">
        <label for="url" class="block text-sm font-medium text-slate-700">Adresa <span class="text-red-500">*</span></label>
        <input id="url" name="url" type="text" value="{{ old('url', $link->url) }}" required placeholder="https://… alebo /storage/…" class="{{ $input }}">
        <x-input-error :messages="$errors->get('url')" class="mt-1" />
    </div>
    <div class="sm:col-span-2">
        <label for="description" class="block text-sm font-medium text-slate-700">Popis <span class="font-normal text-slate-400">(pri partneroch sa zobrazí pod názvom)</span></label>
        <input id="description" name="description" type="text" value="{{ old('description', $link->description) }}" class="{{ $input }}">
    </div>
</div>
