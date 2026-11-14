@php $input = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue'; @endphp

<div class="grid gap-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="title" class="block text-sm font-medium text-slate-700">Názov podujatia <span class="text-red-500">*</span></label>
        <input id="title" name="title" type="text" value="{{ old('title', $event->title) }}" required class="{{ $input }}">
        <x-input-error :messages="$errors->get('title')" class="mt-1" />
    </div>

    <div>
        <label for="type" class="block text-sm font-medium text-slate-700">Typ <span class="text-red-500">*</span></label>
        <select id="type" name="type" class="{{ $input }}">
            @foreach (\App\Models\Event::TYPES as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $event->type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-slate-700">URL adresa</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', $event->slug) }}" placeholder="vyplní sa automaticky z názvu" class="{{ $input }}">
        <x-input-error :messages="$errors->get('slug')" class="mt-1" />
    </div>

    <div>
        <label for="starts_on" class="block text-sm font-medium text-slate-700">Dátum začiatku <span class="text-red-500">*</span></label>
        <input id="starts_on" name="starts_on" type="date" value="{{ old('starts_on', $event->starts_on?->format('Y-m-d')) }}" required class="{{ $input }}">
        <x-input-error :messages="$errors->get('starts_on')" class="mt-1" />
    </div>

    <div>
        <label for="ends_on" class="block text-sm font-medium text-slate-700">Dátum konca <span class="font-normal text-slate-400">(viacdňové)</span></label>
        <input id="ends_on" name="ends_on" type="date" value="{{ old('ends_on', $event->ends_on?->format('Y-m-d')) }}" class="{{ $input }}">
        <x-input-error :messages="$errors->get('ends_on')" class="mt-1" />
    </div>

    <div>
        <label for="date_text" class="block text-sm font-medium text-slate-700">Dátum ako text <span class="font-normal text-slate-400">(voliteľné)</span></label>
        <input id="date_text" name="date_text" type="text" value="{{ old('date_text', $event->date_text) }}" placeholder="napr. 8. a 9. apríla 2025, o 15.00" class="{{ $input }}">
        <p class="mt-1 text-xs text-slate-500">Ak je vyplnený, zobrazí sa namiesto dátumu z kalendára.</p>
    </div>

    <div>
        <label for="location" class="block text-sm font-medium text-slate-700">Miesto konania</label>
        <input id="location" name="location" type="text" value="{{ old('location', $event->location) }}" placeholder="Dom ZSVTS, Koceľova 15, Bratislava" class="{{ $input }}">
    </div>

    <div class="md:col-span-2">
        <label for="organizers" class="block text-sm font-medium text-slate-700">Organizátori</label>
        <input id="organizers" name="organizers" type="text" value="{{ old('organizers', $event->organizers) }}" placeholder="SSTN, ÚNMS SR, ČSTN" class="{{ $input }}">
    </div>

    <div class="md:col-span-2">
        <label for="intro" class="block text-sm font-medium text-slate-700">Úvodný text pozvánky</label>
        <textarea id="intro" name="intro" rows="3" class="{{ $input }}" placeholder="Vážení členovia ZSVTS a SSTN, spoločne s … si Vás dovoľujeme pozvať na seminár">{{ old('intro', $event->intro) }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label for="body" class="block text-sm font-medium text-slate-700">Ďalšie informácie</label>
        <textarea id="body" name="body" rows="6" class="{{ $input }}" placeholder="Program, organizačné pokyny, téma ročníka…">{{ old('body', $event->body) }}</textarea>
        <p class="mt-1 text-xs text-slate-500">Odseky oddeľte prázdnym riadkom.</p>
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $event->is_published)) class="rounded border-slate-300 text-sstn-blue focus:ring-sstn-blue">
            Zverejnené na webe
        </label>
    </div>
</div>
