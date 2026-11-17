<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Event;
use App\Models\Link;
use App\Models\MembershipFee;
use App\Models\TextBlock;
use Illuminate\View\View;

/** The public landing page, filled with the content edited in /admin. */
class HomeController extends Controller
{
    public function __invoke(): View
    {
        $upcoming = Event::published()->upcoming()->with('attachments')->get();
        $articles = Article::published()->orderByDesc('published_on')->orderByDesc('id')->take(3)->get();
        $links = Link::orderBy('sort')->orderBy('id')->get()->groupBy('group');

        // Sections the page renders; menu anchors to a missing section are hidden.
        $sections = ['podujatia', 'o-nas', 'clenstvo', 'kontakt', ...($articles->isNotEmpty() ? ['aktuality'] : [])];
        $menu = $links->get('menu', collect())
            ->reject(fn (Link $link) => $link->is_anchor && ! in_array(ltrim($link->url, '#'), $sections, true))
            ->values();

        return view('site.home', [
            'text' => TextBlock::pluck('value', 'key'),
            'upcoming' => $upcoming,
            'pastByYear' => Event::published()->past()->with('attachments')->get()
                ->groupBy(fn (Event $event) => $event->starts_on->year),
            'articles' => $articles,
            'fees' => MembershipFee::orderBy('sort')->orderBy('id')->get(),
            'links' => $links,
            'menu' => $menu,
            'jsonLd' => $this->jsonLd($upcoming),
        ]);
    }

    /** schema.org Organization plus the upcoming events, for search engines. */
    private function jsonLd($upcoming): array
    {
        $org = [
            '@type' => 'Organization',
            '@id' => url('/').'#org',
            'name' => 'Slovenská spoločnosť pre technickú normalizáciu',
            'alternateName' => 'SSTN',
            'url' => url('/'),
            'email' => TextBlock::get('contact.email'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Koceľova 15',
                'postalCode' => '815 94',
                'addressLocality' => 'Bratislava',
                'addressCountry' => 'SK',
            ],
            'memberOf' => ['@type' => 'Organization', 'name' => 'Zväz slovenských vedeckotechnických spoločností', 'url' => 'https://www.zsvts.sk/'],
        ];

        $events = $upcoming->map(fn (Event $event) => array_filter([
            '@type' => 'Event',
            'name' => $event->title,
            'startDate' => $event->starts_on->toDateString(),
            'endDate' => $event->ends_on?->toDateString(),
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'location' => $event->location ? ['@type' => 'Place', 'name' => $event->location, 'address' => $event->location] : null,
            'organizer' => ['@id' => url('/').'#org'],
        ]));

        return ['@context' => 'https://schema.org', '@graph' => [$org, ...$events]];
    }
}
