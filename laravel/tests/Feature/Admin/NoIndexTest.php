<?php

use App\Models\User;

$noindex = '<meta name="robots" content="noindex, nofollow">';

test('admin and profile pages tell search engines not to index them', function () use ($noindex) {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee($noindex, false);
    $this->actingAs($admin)->get(route('profile.edit'))->assertOk()->assertSee($noindex, false);
});

test('the login page is not indexed either, but the public home page is', function () use ($noindex) {
    $this->get(route('login'))->assertOk()->assertSee($noindex, false);
    $this->get(route('home'))->assertOk()->assertDontSee('noindex', false);
});
