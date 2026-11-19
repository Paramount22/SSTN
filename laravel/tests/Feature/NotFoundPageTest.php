<?php

use App\Models\User;

test('unknown addresses show the branded 404 page', function () {
    $this->get('/tato-stranka-neexistuje')
        ->assertNotFound()
        ->assertSee('Táto stránka', false)
        ->assertSee('nie je v&nbsp;norme', false)
        ->assertSee('<meta name="robots" content="noindex">', false)
        ->assertSee(route('home').'#podujatia', false);
});

test('missing admin records also get the 404 page', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin/events/999999/edit')
        ->assertNotFound()
        ->assertSee('MIMO TOLERANCIE');
});
