<x-admin.layout :title="$event->title">
    <x-slot:subtitle>{{ $event->type_label }} · {{ $event->date_label }}@if($event->location) · {{ $event->location }}@endif</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
            <x-admin.icon name="arrow-left" class="h-4 w-4" /> Späť na zoznam
        </a>
        <form method="post" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Naozaj zmazať podujatie aj s prílohami?')">
            @csrf @method('delete')
            <button class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-medium text-red-600 ring-1 ring-red-200 hover:bg-red-50">
                <x-admin.icon name="trash" class="h-4 w-4" /> Zmazať
            </button>
        </form>
    </x-slot:actions>

    <div class="grid gap-6 xl:grid-cols-3">
        <form method="post" action="{{ route('admin.events.update', $event) }}" class="space-y-6 xl:col-span-2">
            @csrf @method('put')
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                @include('admin.events._form')
            </section>
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-full bg-sstn-navy px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80]">Uložiť zmeny</button>
                <span class="text-xs text-slate-400">Naposledy upravené {{ $event->updated_at->format('j. n. Y H:i') }}</span>
            </div>
        </form>

        {{-- Attachments --}}
        <aside class="space-y-4">
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h2 class="font-semibold text-slate-900">Prílohy</h2>
                    <span class="text-xs text-slate-500">{{ $event->attachments->count() }}</span>
                </header>

                <ul class="divide-y divide-slate-100">
                    @forelse ($event->attachments as $attachment)
                        <li class="px-5 py-3">
                            <form method="post" action="{{ route('admin.attachments.update', $attachment) }}" class="flex items-start gap-2">
                                @csrf @method('patch')
                                <span class="mt-1 grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-blue-50 text-[10px] font-bold text-sstn-blue">{{ $attachment->extension }}</span>
                                <div class="min-w-0 flex-1">
                                    <input type="text" name="label" value="{{ $attachment->label }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue" aria-label="Názov prílohy">
                                    <p class="mt-1 flex flex-wrap items-center gap-x-2 text-xs text-slate-500">
                                        <a href="{{ $attachment->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-sstn-blue hover:underline"><x-admin.icon name="download" class="h-3.5 w-3.5" />{{ $attachment->original_name }}</a>
                                        <span>· {{ $attachment->size_label }}</span>
                                    </p>
                                </div>
                                <input type="number" name="sort" value="{{ $attachment->sort }}" min="0" class="w-14 rounded-lg border-slate-300 text-center text-sm" title="Poradie" aria-label="Poradie">
                                <button type="submit" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-sstn-navy" title="Uložiť názov"><x-admin.icon name="check" class="h-4 w-4" /></button>
                            </form>
                            <form method="post" action="{{ route('admin.attachments.destroy', $attachment) }}" class="mt-1 text-right" onsubmit="return confirm('Zmazať prílohu „{{ addslashes($attachment->label) }}“?')">
                                @csrf @method('delete')
                                <button class="text-xs text-red-600 hover:underline">Zmazať prílohu</button>
                            </form>
                        </li>
                    @empty
                        <li class="px-5 py-6 text-center text-sm text-slate-500">Zatiaľ bez príloh.</li>
                    @endforelse
                </ul>

                <form method="post" action="{{ route('admin.events.attachments.store', $event) }}" enctype="multipart/form-data" class="space-y-3 border-t border-slate-100 bg-slate-50/60 px-5 py-4">
                    @csrf
                    <p class="text-sm font-medium text-slate-700">Pridať prílohu</p>
                    <input type="text" name="label" value="{{ old('label') }}" placeholder="Názov, napr. Pozvánka a program" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-sstn-blue focus:ring-sstn-blue">
                    <input type="file" name="file" required accept=".pdf,.doc,.docx,.rtf,.odt,.xls,.xlsx,.ppt,.pptx,.zip"
                           class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-white file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-sstn-blue file:ring-1 file:ring-slate-200">
                    <x-input-error :messages="$errors->get('file')" />
                    <button type="submit" class="rounded-full bg-sstn-blue px-4 py-2 text-sm font-semibold text-white hover:bg-[#0069ad]">Nahrať</button>
                    <p class="text-xs text-slate-500">Súbor sa uloží do <code class="rounded bg-white px-1">/storage/akcie/{{ $event->starts_on->year }}/</code> – rovnaká adresa ako na pôvodnom webe.</p>
                </form>
            </section>
        </aside>
    </div>
</x-admin.layout>
