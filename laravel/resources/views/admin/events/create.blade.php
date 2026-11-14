<x-admin.layout title="Nové podujatie">
    <x-slot:actions>
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
            <x-admin.icon name="arrow-left" class="h-4 w-4" /> Späť na zoznam
        </a>
    </x-slot:actions>

    <form method="post" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @include('admin.events._form')
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-slate-900">Prílohy</h2>
            <p class="mt-1 text-sm text-slate-500">Pozvánka, program, prihláška… PDF, DOC, DOCX, RTF, XLS(X), PPT(X) alebo ZIP, max. 20 MB na súbor. Názvy príloh upravíte po uložení.</p>
            <input type="file" name="files[]" multiple accept=".pdf,.doc,.docx,.rtf,.odt,.xls,.xlsx,.ppt,.pptx,.zip"
                   class="mt-4 block w-full text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-sstn-blue hover:file:bg-blue-100">
            <x-input-error :messages="$errors->get('files.*')" class="mt-1" />
        </section>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-full bg-sstn-navy px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80]">Vytvoriť podujatie</button>
            <a href="{{ route('admin.events.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Zrušiť</a>
        </div>
    </form>
</x-admin.layout>
