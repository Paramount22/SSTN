<?php

use App\Models\Article;
use App\Models\Event;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

test('delete forms ask through the styled dialog instead of confirm()', function () {
    Event::factory()->create(['title' => 'Seminár Praha']);
    Article::factory()->create(['title' => 'Exkurzia']);

    foreach (['admin.events.index', 'admin.articles.index', 'admin.links.index', 'admin.membership-fees.index'] as $route) {
        $this->actingAs($this->admin)->get(route($route))
            ->assertOk()
            ->assertSee('x-data="confirmDialog"', false)
            ->assertDontSee('return confirm(', false);
    }

    $this->actingAs($this->admin)->get(route('admin.events.index'))
        ->assertSee('data-confirm-title="Zmazať podujatie?"', false)
        ->assertSee('data-confirm-item="Seminár Praha"', false);
});

test('item names in the dialog are escaped', function () {
    Event::factory()->create(['title' => 'Seminár "Normy" <script>alert(1)</script>']);

    $this->actingAs($this->admin)->get(route('admin.events.index'))
        ->assertSee('data-confirm-item="Seminár &quot;Normy&quot; &lt;script&gt;alert(1)&lt;/script&gt;"', false)
        ->assertDontSee('<script>alert(1)</script>', false);
});
