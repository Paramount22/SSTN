<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;

class EventAttachmentController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['nullable', 'string', 'max:120'],
            'file' => ['required', File::types(EventAttachment::ALLOWED_EXTENSIONS)->max(20 * 1024)],
        ], [], ['file' => 'súbor', 'label' => 'názov']);

        EventAttachment::fromUpload($event, $data['file'], $data['label'] ?? null);

        return back()->with('status', 'Príloha bola nahraná.');
    }

    public function update(Request $request, EventAttachment $attachment): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:999'],
        ], [], ['label' => 'názov', 'sort' => 'poradie']);

        $attachment->update($data);

        return back()->with('status', 'Príloha bola upravená.');
    }

    public function destroy(EventAttachment $attachment): RedirectResponse
    {
        $attachment->delete();

        return back()->with('status', 'Príloha bola zmazaná.');
    }
}
