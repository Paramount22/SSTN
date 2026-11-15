<x-admin.layout :title="$article->title">
    <x-slot:subtitle>{{ $article->published_on->format('j. n. Y') }} · {{ $article->is_published ? 'Zverejnené' : 'Koncept' }}</x-slot:subtitle>
    <x-slot:actions>
        <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
            <x-admin.icon name="arrow-left" class="h-4 w-4" /> Späť na články
        </a>
        <form method="post" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Naozaj zmazať článok aj s fotografiou?')">
            @csrf @method('delete')
            <button class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-medium text-red-600 ring-1 ring-red-200 hover:bg-red-50">
                <x-admin.icon name="trash" class="h-4 w-4" /> Zmazať
            </button>
        </form>
    </x-slot:actions>

    <form method="post" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data" class="max-w-6xl space-y-6">
        @csrf @method('put')
        @include('admin.articles._form')
        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-full bg-sstn-navy px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80]">Uložiť zmeny</button>
            <span class="text-xs text-slate-400">Naposledy upravené {{ $article->updated_at->format('j. n. Y H:i') }}</span>
        </div>
    </form>
</x-admin.layout>
