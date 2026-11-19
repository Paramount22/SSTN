<?php

use App\Models\Article;
use App\Models\Event;
use App\Models\User;

test('the dashboard shows content statistics and event breakdowns', function () {
    Event::factory()->create(['type' => 'sstn', 'starts_on' => today()->addMonth(), 'is_published' => true]);
    Event::factory()->create(['type' => 'cstn', 'starts_on' => today()->subMonths(2), 'is_published' => true]);
    Event::factory()->create(['type' => 'cstn', 'starts_on' => today()->subYear(), 'is_published' => false]);
    Article::factory()->create(['is_published' => true, 'image_path' => 'clanky/2026/a.jpg']);
    Article::factory()->create(['is_published' => false]);

    $this->actingAs(User::factory()->admin()->create())->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSeeInOrder(['Podujatia spolu', '3', 'zverejnené: 2 · koncepty: 1'])
        ->assertSeeInOrder(['Články', '2', 'zverejnené: 1 · koncepty: 1'])
        ->assertSee('článkov bez fotografie: 1')
        ->assertSee('najbližšie: '.today()->addMonth()->format('j. n. Y'))
        ->assertSeeInOrder(['Podujatia podľa typu', 'Spoločne s ČSTN', '2', 'SSTN', '1'])
        ->assertSeeInOrder(['Podujatia podľa roka', (string) today()->addMonth()->year]);
});

test('the dashboard works with no content', function () {
    $this->actingAs(User::factory()->admin()->create())->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('nič nie je naplánované')
        ->assertSee('Zatiaľ žiadne podujatia.');
});
