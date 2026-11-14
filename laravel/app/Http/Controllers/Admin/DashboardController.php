<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttachment;
use App\Models\Link;
use App\Models\TextBlock;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'events' => Event::count(),
                'upcoming' => Event::upcoming()->count(),
                'attachments' => EventAttachment::count(),
                'links' => Link::count(),
            ],
            'upcoming' => Event::upcoming()->withCount('attachments')->limit(5)->get(),
            'recent' => Event::latest('updated_at')->limit(5)->get(),
            'textBlocks' => TextBlock::orderBy('sort')->limit(6)->get(),
        ]);
    }
}
