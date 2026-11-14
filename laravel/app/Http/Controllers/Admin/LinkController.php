<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LinkRequest;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(): View
    {
        return view('admin.links.index', [
            'groups' => Link::orderBy('sort')->orderBy('id')->get()->groupBy('group'),
        ]);
    }

    public function create(Request $request): View
    {
        $group = array_key_exists($request->string('group')->toString(), Link::GROUPS)
            ? $request->string('group')->toString()
            : 'footer';

        return view('admin.links.create', [
            'link' => new Link(['group' => $group, 'sort' => (Link::where('group', $group)->max('sort') ?? 0) + 1]),
        ]);
    }

    public function store(LinkRequest $request): RedirectResponse
    {
        Link::create($request->validated());

        return redirect()->route('admin.links.index')->with('status', 'Odkaz bol pridaný.');
    }

    public function edit(Link $link): View
    {
        return view('admin.links.edit', compact('link'));
    }

    public function update(LinkRequest $request, Link $link): RedirectResponse
    {
        $link->update($request->validated());

        return redirect()->route('admin.links.index')->with('status', 'Odkaz bol uložený.');
    }

    public function destroy(Link $link): RedirectResponse
    {
        $link->delete();

        return redirect()->route('admin.links.index')->with('status', 'Odkaz bol zmazaný.');
    }
}
