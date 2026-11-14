<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventRequest;
use App\Models\Event;
use App\Models\EventAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString();

        $events = Event::query()
            ->withCount('attachments')
            ->when($filter === 'upcoming', fn ($q) => $q->upcoming())
            ->when($filter === 'past', fn ($q) => $q->past())
            ->when($filter === 'draft', fn ($q) => $q->where('is_published', false)->orderByDesc('starts_on'))
            ->when($filter === '', fn ($q) => $q->orderByDesc('starts_on'))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->paginate(15)
            ->withQueryString();

        return view('admin.events.index', compact('events', 'filter'));
    }

    public function create(): View
    {
        return view('admin.events.create', ['event' => new Event(['type' => 'sstn', 'is_published' => true])]);
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $event = Event::create($request->validated());

        foreach ($request->file('files', []) as $file) {
            EventAttachment::fromUpload($event, $file);
        }

        return redirect()->route('admin.events.edit', $event)
            ->with('status', 'Podujatie bolo vytvorené. Môžete doplniť prílohy.');
    }

    public function edit(Event $event): View
    {
        $event->load('attachments');

        return view('admin.events.edit', compact('event'));
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $event->update($request->validated());

        return redirect()->route('admin.events.edit', $event)->with('status', 'Zmeny boli uložené.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->attachments->each->delete();   // model event removes the files
        $event->delete();

        return redirect()->route('admin.events.index')->with('status', 'Podujatie bolo zmazané.');
    }
}
