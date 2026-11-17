<x-admin.layout :title="$article->title">
    <x-slot:subtitle>{{ $article->published_on->format('j. n. Y') }} · {{ $article->is_published ? 'Zverejnené' : 'Koncept' }}</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-slate-900 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 ring-1 ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
            <x-admin.icon name="arrow-left" class="h-4 w-4" /> Späť na články
        </a>
        <form method="post" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Naozaj zmazať článok aj s fotografiou?')">
            @csrf @method('delete')
            <button class="inline-flex items-center gap-2 rounded-full bg-white dark:bg-slate-900 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 ring-1 ring-red-200 dark:ring-red-500/30 hover:bg-red-50 dark:hover:bg-red-500/10">
                <x-admin.icon name="trash" class="h-4 w-4" /> Zmazať
            </button>
        </form>
    </x-slot:actions>

    <form method="post" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data" class="max-w-6xl space-y-6">
        @csrf @method('put')
        @include('admin.articles._form')
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-full bg-sstn-navy dark:bg-sstn-blue px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80] dark:hover:bg-[#0069ad]">Uložiť zmeny</button>
            <span class="text-xs text-slate-400 dark:text-slate-500">Naposledy upravené {{ $article->updated_at->format('j. n. Y H:i') }}</span>
        </div>
    </form>
</x-admin.layout>
