@php $input = 'mt-1 block w-full rounded-lg border-slate-300 dark:border-slate-600 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue'; @endphp

<div class="grid gap-6 lg:grid-cols-5">
    {{-- Text --}}
    <section class="space-y-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm lg:col-span-3">
        <div>
            <label for="title" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nadpis <span class="text-red-500 dark:text-red-400">*</span></label>
            <input id="title" name="title" type="text" value="{{ old('title', $article->title) }}" required placeholder="Seminár Třinec 2024" class="{{ $input }}">
            <x-input-error :messages="$errors->get('title')" class="mt-1" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="published_on" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Dátum <span class="text-red-500 dark:text-red-400">*</span></label>
                <input id="published_on" name="published_on" type="date" value="{{ old('published_on', $article->published_on?->format('Y-m-d')) }}" required class="{{ $input }}">
                <x-input-error :messages="$errors->get('published_on')" class="mt-1" />
            </div>
            <div>
                <label for="slug" class="block text-sm font-medium text-slate-700 dark:text-slate-200">URL adresa</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $article->slug) }}" placeholder="vyplní sa automaticky z nadpisu" class="{{ $input }}">
                <x-input-error :messages="$errors->get('slug')" class="mt-1" />
            </div>
        </div>

        <div>
            <label id="body-label" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Text článku</label>
            <x-admin.rich-editor name="body" class="mt-1" :value="old('body', $article->body)" label="Text článku" aria-labelledby="body-label"
                                 placeholder="Česká společnost pro technickou normalizaci (ČSTN) a Slovenská spoločnosť pre technickú normalizáciu (SSTN) zorganizovali…" />
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Odkaz pridáte označením textu a tlačidlom s reťazou. Nadpisy H2 a H3 použite na členenie dlhšieho textu.</p>
            <x-input-error :messages="$errors->get('body')" class="mt-1" />
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $article->is_published)) class="rounded border-slate-300 dark:border-slate-600 text-sstn-blue dark:text-sky-400 focus:ring-sstn-blue">
            Zverejnené na webe
        </label>
    </section>

    {{-- Photo --}}
    <section class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm lg:col-span-2"
             x-data="{ preview: null, existing: @js($article->image_url), remove: false,
                       pick(e) { const f = e.target.files[0]; this.preview = f ? URL.createObjectURL(f) : null; if (f) this.remove = false; } }">
        <h2 class="text-sm font-medium text-slate-700 dark:text-slate-200">Fotografia</h2>

        <div class="mt-2 overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800">
            <template x-if="preview || (existing && !remove)">
                <img :src="preview || existing" alt="" class="aspect-[16/10] w-full object-cover">
            </template>
            <template x-if="!preview && (!existing || remove)">
                <label for="image" class="flex aspect-[16/10] cursor-pointer flex-col items-center justify-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                    <x-admin.icon name="photo" class="h-8 w-8 text-slate-300 dark:text-slate-600" />
                    <span>Kliknite a vyberte fotografiu</span>
                </label>
            </template>
        </div>

        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @change="pick"
               class="mt-3 block w-full text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-full file:border-0 file:bg-blue-50 dark:file:bg-sky-500/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-sstn-blue dark:file:text-sky-300 hover:file:bg-blue-100 dark:hover:file:bg-sky-500/20">
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">JPG, PNG alebo WebP, najviac 10 MB. Najlepšie na šírku – fotografia sa automaticky zmenší na {{ \App\Models\Article::IMAGE_MAX_SIDE }} px.</p>
        <x-input-error :messages="$errors->get('image')" class="mt-1" />

        @if ($article->image_path)
            <label class="mt-3 inline-flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200" x-show="!preview">
                <input type="hidden" name="remove_image" value="0">
                <input type="checkbox" name="remove_image" value="1" x-model="remove" class="rounded border-slate-300 dark:border-slate-600 text-red-600 dark:text-red-400 focus:ring-red-500">
                Odstrániť fotografiu
            </label>
            @if ($article->image_width)
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Aktuálna: {{ $article->image_width }} × {{ $article->image_height }} px</p>
            @endif
        @endif

        <div class="mt-4">
            <label for="image_alt" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Popis fotografie</label>
            <input id="image_alt" name="image_alt" type="text" value="{{ old('image_alt', $article->image_alt) }}" placeholder="Účastníci seminára v Třinci" class="{{ $input }}">
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Čo je na fotke. Pomáha nevidiacim aj vyhľadávačom.</p>
        </div>
    </section>
</div>
