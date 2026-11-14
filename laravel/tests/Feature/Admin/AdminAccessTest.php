<?php

use App\Models\User;

test('guests are redirected to login', function () {
    $this->get('/admin')->assertRedirect('/login');
});

test('non-admin users get 403', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertForbidden();
});

test('admins see the dashboard', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('Prehľad');
});

test('login redirects to the admin dashboard', function () {
    $user = User::factory()->admin()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('dashboard', absolute: false));

    $this->get('/dashboard')->assertRedirect(route('admin.dashboard', absolute: false));
});

test('public registration is disabled', function () {
    $this->get('/register')->assertNotFound();
});
