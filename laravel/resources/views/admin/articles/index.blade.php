<x-admin.layout title="Články">
    <x-slot:subtitle>Krátke správy s fotografiou – reportáže zo seminárov, exkurzií a výstav.</x-slot:subtitle>
    <x-slot:actions>
        <form action="{{ route('admin.articles.index') }}" method="get">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Hľadať článok…"
                   class="w-56 rounded-full border-slate-200 bg-white py-2 text-sm focus:border-sstn-blue focus:ring-sstn-blue">
        </form>
        <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center gap-2 rounded-full bg-sstn-navy px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#003a80]">
            <x-admin.icon name="plus" class="h-4 w-4" /> Nový článok
        </a>
    </x-slot:actions>

    @if ($articles->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
            <x-admin.icon name="photo" class="mx-auto h-10 w-10 text-slate-300" />
            <p class="mt-3 font-medium text-slate-900">{{ request('q') ? 'Žiadny článok nezodpovedá hľadaniu.' : 'Zatiaľ žiadne články.' }}</p>
            <p class="mt-1 text-sm text-slate-500">Pridajte prvý článok s fotografiou, napríklad správu zo seminára.</p>
            <a href="{{ route('admin.articles.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-full bg-sstn-navy px-4 py-2 text-sm font-semibold text-white hover:bg-[#003a80]">
                <x-admin.icon name="plus" class="h-4 w-4" /> Nový článok
            </a>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($articles as $article)
                <article class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                    <a href="{{ route('admin.articles.edit', $article) }}" class="block aspect-[16/10] bg-slate-100">
                        @if ($article->image_url)
                            <img src="{{ $article->image_url }}" alt="{{ $article->image_alt }}" loading="lazy" class="h-full w-full object-cover">
                        @else
                            <span class="flex h-full items-center justify-center text-sm text-slate-400">
                                <x-admin.icon name="photo" class="mr-2 h-5 w-5" /> bez fotografie
                            </span>
                        @endif
                    </a>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span>{{ $article->published_on->format('j. n. Y') }}</span>
                            @unless ($article->is_published)
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 font-medium text-amber-800">Koncept</span>
                            @endunless
                        </div>
                        <h2 class="mt-2 text-lg font-semibold leading-snug text-slate-900">
                            <a href="{{ route('admin.articles.edit', $article) }}" class="hover:text-sstn-blue">{{ $article->title }}</a>
                        </h2>
                        <p class="mt-2 line-clamp-4 text-sm leading-relaxed text-slate-600">{{ $article->excerpt }}</p>
                        <div class="mt-auto flex items-center justify-end gap-1 pt-4">
                            <a href="{{ route('admin.articles.edit', $article) }}" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-sm text-slate-600 hover:bg-slate-100 hover:text-sstn-navy">
                                <x-admin.icon name="pencil" class="h-4 w-4" /> Upraviť
                            </a>
                            <form method="post" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Naozaj zmazať článok „{{ addslashes($article->title) }}“ aj s fotografiou?')">
                                @csrf @method('delete')
                                <button class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-sm text-slate-600 hover:bg-red-50 hover:text-red-600">
                                    <x-admin.icon name="trash" class="h-4 w-4" /> Zmazať
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($articles->hasPages())
            <div class="mt-6">{{ $articles->links() }}</div>
        @endif
    @endif
</x-admin.layout>
