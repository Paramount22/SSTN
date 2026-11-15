<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $articles = Article::query()
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->orderByDesc('published_on')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.articles.index', compact('articles'));
    }

    public function create(): View
    {
        return view('admin.articles.create', [
            'article' => new Article(['published_on' => today(), 'is_published' => true]),
        ]);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        $article = Article::create($request->articleData());

        if ($request->hasFile('image')) {
            $article->attachImage($request->file('image'));
        }

        return redirect()->route('admin.articles.edit', $article)->with('status', 'Článok bol vytvorený.');
    }

    public function edit(Article $article): View
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $article->update($request->articleData());

        if ($request->hasFile('image')) {
            $article->attachImage($request->file('image'));
        } elseif ($request->boolean('remove_image')) {
            $article->removeImage();
        }

        return redirect()->route('admin.articles.edit', $article)->with('status', 'Zmeny boli uložené.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();   // model event removes the photo

        return redirect()->route('admin.articles.index')->with('status', 'Článok bol zmazaný.');
    }
}
