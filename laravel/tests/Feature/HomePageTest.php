<?php

use App\Models\Article;
use App\Models\Event;
use App\Models\TextBlock;
use App\Models\User;
use Database\Seeders\LinkSeeder;
use Database\Seeders\MembershipFeeSeeder;
use Database\Seeders\TextBlockSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->seed([TextBlockSeeder::class, MembershipFeeSeeder::class, LinkSeeder::class]);
});

test('home page shows texts, fees and links from the admin', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Spájame ľudí, ktorí')
        ->assertSee('SK42 0900 0000 0000 1148 4047')
        ->assertSee('Kolektívne nad 100 zamestnancov')
        ->assertSee('Ing. Pavol Radič')
        ->assertSee('Víťazné video 2016');
});

test('text edits in the admin appear on the home page', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->put(route('admin.text-blocks.update', TextBlock::firstWhere('key', 'hero.lead')), [
            'value' => 'Nový podnadpis zo správy obsahu.',
        ]);

    $this->get(route('home'))->assertSee('Nový podnadpis zo správy obsahu.');
});

test('published events are split into upcoming and past', function () {
    Event::factory()->create(['title' => 'Budúci seminár', 'starts_on' => today()->addMonth()]);
    Event::factory()->create(['title' => 'Minulý seminár', 'starts_on' => today()->subYear()]);
    Event::factory()->create(['title' => 'Skrytý koncept', 'starts_on' => today()->addWeek(), 'is_published' => false]);

    $this->get(route('home'))
        ->assertSeeInOrder(['Pripravujeme', 'Budúci seminár', 'Uskutočnené podujatia', 'Minulý seminár'])
        ->assertDontSee('Skrytý koncept');
});

test('latest published articles appear with their photo', function () {
    Article::factory()->create(['title' => 'Seminár Třinec 2024', 'published_on' => '2024-06-13', 'image_path' => 'clanky/2024/trinec.jpg', 'image_alt' => 'Účastníci']);
    Article::factory()->create(['title' => 'Neverejný článok', 'is_published' => false]);

    $this->get(route('home'))
        ->assertSee('href="#aktuality"', false)
        ->assertSee('Seminár Třinec 2024')
        ->assertSee('13. júna 2024')
        ->assertSee('/storage/clanky/2024/trinec.jpg')
        ->assertDontSee('Neverejný článok');
});

test('articles section and menu item are hidden when there are no articles', function () {
    $this->get(route('home'))->assertDontSee('href="#aktuality"', false);
});

test('home page carries valid structured data', function () {
    Event::factory()->create(['title' => 'Budúci seminár', 'starts_on' => today()->addMonth()]);

    preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $this->get(route('home'))->getContent(), $m);
    $graph = json_decode($m[1], true, flags: JSON_THROW_ON_ERROR)['@graph'];

    expect($graph[0]['@type'])->toBe('Organization')
        ->and($graph[1]['name'])->toBe('Budúci seminár');
});

test('event dates read naturally in Slovak', function () {
    expect(Event::factory()->make(['starts_on' => '2026-11-20', 'ends_on' => null])->date_long)->toBe('20. novembra 2026')
        ->and(Event::factory()->make(['starts_on' => '2025-03-20', 'ends_on' => '2025-03-21'])->date_long)->toBe('20. – 21. marca 2025')
        ->and(Event::factory()->make(['starts_on' => '2025-03-31', 'ends_on' => '2025-04-01'])->date_long)->toBe('31. marca – 1. apríla 2025');
});
