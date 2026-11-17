<x-admin.layout title="Nový článok">
    <x-slot:actions>
        <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-slate-900 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 ring-1 ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
            <x-admin.icon name="arrow-left" class="h-4 w-4" /> Späť na články
        </a>
    </x-slot:actions>

    <form method="post" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data" class="max-w-6xl space-y-6">
        @csrf
        @include('admin.articles._form')
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-full bg-sstn-navy dark:bg-sstn-blue px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:hover:bg-[#0069ad]">Vytvoriť článok</button>
            <a href="{{ route('admin.articles.index') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Zrušiť</a>
        </div>
    </form>
</x-admin.layout>
