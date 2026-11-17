<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Storage;

test('is_admin cannot be mass assigned', function () {
    $user = User::create([
        'name' => 'Útočník', 'email' => 'x@example.com', 'password' => 'password', 'is_admin' => true,
    ]);

    expect($user->fresh()->is_admin)->toBeFalse();
});

test('a user cannot make themselves admin through the profile form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patch('/profile', [
        'name' => 'Útočník', 'email' => $user->email, 'is_admin' => '1',
    ])->assertSessionHasNoErrors();

    expect($user->fresh()->is_admin)->toBeFalse();
});

test('the seeder still creates an administrator', function () {
    Storage::fake('public');
    $this->seed(DatabaseSeeder::class);

    expect(User::firstWhere('email', 'admin@sstn.test')->is_admin)->toBeTrue();
});

test('the only admin cannot delete their account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/profile')
        ->assertSee('jediný administrátorský účet')
        ->assertDontSee('confirm-user-deletion');

    $this->actingAs($admin)->from('/profile')->delete('/profile', ['password' => 'password'])
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    expect($admin->fresh())->not->toBeNull();
    $this->assertAuthenticatedAs($admin);
});

test('an admin can delete their account when another admin remains', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->admin()->create();

    $this->actingAs($admin)->get('/profile')->assertSee('confirm-user-deletion');

    $this->actingAs($admin)->delete('/profile', ['password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    expect($admin->fresh())->toBeNull();
});
