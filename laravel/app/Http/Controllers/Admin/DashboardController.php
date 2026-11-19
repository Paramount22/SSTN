<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Event;
use App\Models\EventAttachment;
use App\Models\Link;
use App\Models\MembershipFee;
use App\Models\TextBlock;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $eventDates = Event::pluck('starts_on');

        return view('admin.dashboard', [
            'stats' => [
                'events' => $eventDates->count(),
                'events_published' => Event::published()->count(),
                'upcoming' => Event::upcoming()->count(),
                'next' => Event::published()->upcoming()->first(),
                'past' => Event::past()->count(),
                'past_this_year' => Event::past()->whereYear('starts_on', now()->year)->count(),
                'articles' => Article::count(),
                'articles_published' => Article::published()->count(),
                'photos' => Article::whereNotNull('image_path')->count(),
                'attachments' => EventAttachment::count(),
                'attachments_size' => (int) EventAttachment::sum('size'),
                'links' => Link::count(),
                'menu_links' => Link::where('group', 'menu')->count(),
                'partners' => Link::where('group', 'partner')->count(),
                'fees' => MembershipFee::count(),
                'fee_min' => MembershipFee::min('price'),
                'fee_max' => MembershipFee::max('price'),
            ],
            // Breakdowns for the bar lists: largest first by type, newest first by year.
            'byType' => Event::query()->selectRaw('type, count(*) as total')->groupBy('type')->orderByDesc('total')->pluck('total', 'type')
                ->mapWithKeys(fn ($total, $type) => [Event::TYPES[$type] ?? $type => (int) $total]),
            'byYear' => $eventDates->countBy(fn ($date) => $date->year)->sortKeysDesc(),
            'upcoming' => Event::upcoming()->withCount('attachments')->limit(5)->get(),
            'recent' => Event::latest('updated_at')->limit(5)->get(),
            'textBlocks' => TextBlock::orderBy('sort')->limit(6)->get(),
        ]);
    }
}
