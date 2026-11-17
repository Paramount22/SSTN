<x-admin.layout :title="$event->title">
    <x-slot:subtitle>{{ $event->type_label }} · {{ $event->date_label }}@if($event->location) · {{ $event->location }}@endif</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-slate-900 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 ring-1 ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
            <x-admin.icon name="arrow-left" class="h-4 w-4" /> Späť na zoznam
        </a>
        <form method="post" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Naozaj zmazať podujatie aj s prílohami?')">
            @csrf @method('delete')
            <button class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-slate-900 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 ring-1 ring-red-200 dark:ring-red-500/30 hover:bg-red-50 dark:hover:bg-red-500/10">
                <x-admin.icon name="trash" class="h-4 w-4" /> Zmazať
            </button>
        </form>
    </x-slot:actions>

    <div class="grid gap-6 xl:grid-cols-3">
        <form method="post" action="{{ route('admin.events.update', $event) }}" class="space-y-6 xl:col-span-2">
            @csrf @method('put')
            <section class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
                @include('admin.events._form')
            </section>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-full bg-sstn-navy dark:bg-sstn-blue px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:hover:bg-[#0069ad]">Uložiť zmeny</button>
                <span class="text-xs text-slate-400 dark:text-slate-500">Naposledy upravené {{ $event->updated_at->format('j. n. Y H:i') }}</span>
            </div>
        </form>

        {{-- Attachments --}}
        <aside class="space-y-4">
            <section class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <header class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-5 py-4">
                    <h2 class="font-semibold text-slate-900 dark:text-white">Prílohy</h2>
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $event->attachments->count() }}</span>
                </header>

                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($event->attachments as $attachment)
                        <li class="px-5 py-3">
                            <form method="post" action="{{ route('admin.attachments.update', $attachment) }}" class="flex items-start gap-2">
                                @csrf @method('patch')
                                <span class="mt-1 grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-50 dark:bg-sky-500/10 text-[10px] font-bold text-sstn-blue dark:text-sky-400">{{ $attachment->extension }}</span>
                                <div class="min-w-0 flex-1">
                                    <input type="text" name="label" value="{{ $attachment->label }}" class="block w-full rounded-lg border-slate-300 dark:border-slate-600 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue" aria-label="Názov prílohy">
                                    <p class="mt-1 flex flex-wrap items-center gap-x-2 text-xs text-slate-500 dark:text-slate-400">
                                        <a href="{{ $attachment->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-sstn-blue dark:text-sky-400 hover:underline"><x-admin.icon name="download" class="h-3.5 w-3.5" />{{ $attachment->original_name }}</a>
                                        <span>· {{ $attachment->size_label }}</span>
                                    </p>
                                </div>
                                <input type="number" name="sort" value="{{ $attachment->sort }}" min="0" class="w-14 rounded-lg border-slate-300 dark:border-slate-600 text-center text-sm" title="Poradie" aria-label="Poradie">
                                <button type="submit" class="rounded-lg p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-sstn-navy dark:hover:text-sky-300" title="Uložiť názov"><x-admin.icon name="check" class="h-4 w-4" /></button>
                            </form>
                            <form method="post" action="{{ route('admin.attachments.destroy', $attachment) }}" class="mt-1 text-right" onsubmit="return confirm('Zmazať prílohu „{{ addslashes($attachment->label) }}“?')">
                                @csrf @method('delete')
                                <button class="text-xs text-red-600 dark:text-red-400 hover:underline">Zmazať prílohu</button>
                            </form>
                        </li>
                    @empty
                        <li class="px-5 py-6 text-center text-sm text-slate-500 dark:text-slate-400">Zatiaľ bez príloh.</li>
                    @endforelse
                </ul>

                <form method="post" action="{{ route('admin.events.attachments.store', $event) }}" enctype="multipart/form-data" class="space-y-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/60 px-5 py-4">
                    @csrf
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Pridať prílohu</p>
                    <input type="text" name="label" value="{{ old('label') }}" placeholder="Názov, napr. Pozvánka a program" class="block w-full rounded-lg border-slate-300 dark:border-slate-600 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue">
                    <input type="file" name="file" required accept=".pdf,.doc,.docx,.rtf,.odt,.xls,.xlsx,.ppt,.pptx,.zip"
                           class="block w-full text-sm text-slate-600 dark:text-slate-300 file:mr-3 file:rounded-full file:border-0 file:bg-white dark:file:bg-slate-800 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-sstn-blue dark:file:text-sky-300 file:ring-1 file:ring-slate-200 dark:file:ring-slate-700">
                    <x-input-error :messages="$errors->get('file')" />
                    <button type="submit" class="rounded-full bg-sstn-blue px-4 py-2 text-sm font-semibold text-white hover:bg-[#0069ad]">Nahrať</button>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Súbor sa uloží do <code class="rounded bg-white dark:bg-slate-900 px-1">/storage/akcie/{{ $event->starts_on->year }}/</code> – rovnaká adresa ako na pôvodnom webe.</p>
                </form>
            </section>
        </aside>
    </div>
</x-admin.layout>
