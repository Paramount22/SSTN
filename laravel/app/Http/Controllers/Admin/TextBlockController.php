<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TextBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TextBlockController extends Controller
{
    public function index(): View
    {
        return view('admin.text-blocks.index', [
            'blocks' => TextBlock::orderBy('sort')->orderBy('id')->get(),
        ]);
    }

    public function edit(TextBlock $textBlock): View
    {
        return view('admin.text-blocks.edit', ['block' => $textBlock]);
    }

    public function update(Request $request, TextBlock $textBlock): RedirectResponse
    {
        $data = $request->validate([
            'value' => ['nullable', 'string', 'max:20000'],
        ], [], ['value' => 'text']);

        $textBlock->update($data);

        return redirect()->route('admin.text-blocks.index')->with('status', "Text „{$textBlock->label}“ bol uložený.");
    }
}
