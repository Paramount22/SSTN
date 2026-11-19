<?php

use App\Models\User;

test('login errors are in Slovak', function () {
    $this->from(route('login'))->post(route('login'), ['email' => '', 'password' => ''])
        ->assertSessionHasErrors([
            'email' => 'Pole e-mail je povinné.',
            'password' => 'Pole heslo je povinné.',
        ]);

    $this->from(route('login'))->post(route('login'), ['email' => 'nie-je-email', 'password' => 'x'])
        ->assertSessionHasErrors(['email' => 'Pole e-mail musí byť platná e-mailová adresa.']);
});

test('password change errors are in Slovak', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->from(route('profile.edit'))->put(route('password.update'), [
        'current_password' => 'zle-heslo',
        'password' => 'kratke',
        'password_confirmation' => 'ine',
    ])->assertSessionHasErrorsIn('updatePassword', [
        'current_password' => 'Zadané heslo nie je správne.',
        'password' => 'Potvrdenie poľa heslo sa nezhoduje.',
    ]);

    $this->actingAs($user)->from(route('profile.edit'))->put(route('password.update'), [
        'current_password' => 'password',
        'password' => 'kratke',
        'password_confirmation' => 'kratke',
    ])->assertSessionHasErrorsIn('updatePassword', ['password' => 'Pole heslo musí mať aspoň 8 znakov.']);
});

test('admin form errors use the Slovak field names', function () {
    $this->actingAs(User::factory()->admin()->create())->post(route('admin.events.store'), [
        'title' => '',
        'type' => 'sstn',
        'starts_on' => '2026-12-02',
        'ends_on' => '2026-12-01',
    ])->assertSessionHasErrors([
        'title' => 'Pole názov je povinné.',
        'ends_on' => 'Pole dátum konca nesmie byť skôr ako dátum začiatku.',
    ]);
});
