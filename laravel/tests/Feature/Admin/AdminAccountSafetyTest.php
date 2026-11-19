<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;
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

test('the public dev admin is never seeded outside local and testing', function () {
    Storage::fake('public');
    app()->detectEnvironment(fn () => 'production');

    $this->artisan('db:seed', ['--force' => true])
        ->expectsOutputToContain('php artisan admin:create')
        ->assertSuccessful();

    expect(User::where('email', 'admin@sstn.test')->exists())->toBeFalse()
        ->and(User::count())->toBe(0);
});

test('admin:create creates an administrator with a typed password', function () {
    $this->artisan('admin:create', ['email' => 'Tajomnik@SSTN.sk', '--name' => 'Pavol Radič'])
        ->expectsQuestion('Heslo', 'Bezpecne-heslo-2026')
        ->expectsQuestion('Heslo znova', 'Bezpecne-heslo-2026')
        ->assertSuccessful();

    $user = User::firstWhere('email', 'tajomnik@sstn.sk');
    expect($user->is_admin)->toBeTrue()
        ->and($user->name)->toBe('Pavol Radič')
        ->and(Hash::check('Bezpecne-heslo-2026', $user->password))->toBeTrue();
});

test('admin:create promotes an existing account and resets its password', function () {
    $user = User::factory()->create(['email' => 'clen@sstn.sk']);

    $this->artisan('admin:create', ['email' => 'clen@sstn.sk'])
        ->expectsConfirmation('Účet clen@sstn.sk už existuje. Nastaviť mu nové heslo a práva administrátora?', 'yes')
        ->expectsQuestion('Heslo', 'Nove-heslo-2026')
        ->expectsQuestion('Heslo znova', 'Nove-heslo-2026')
        ->assertSuccessful();

    expect($user->fresh()->is_admin)->toBeTrue()
        ->and(Hash::check('Nove-heslo-2026', $user->fresh()->password))->toBeTrue();
});

test('admin:create rejects weak or mismatched passwords', function () {
    $this->artisan('admin:create', ['email' => 'a@sstn.sk'])
        ->expectsQuestion('Heslo', 'kratke')
        ->expectsQuestion('Heslo znova', 'kratke')
        ->assertFailed();

    $this->artisan('admin:create', ['email' => 'a@sstn.sk'])
        ->expectsQuestion('Heslo', 'Bezpecne-heslo-2026')
        ->expectsQuestion('Heslo znova', 'Ine-heslo-2026')
        ->assertFailed();

    expect(User::count())->toBe(0);
});

test('the only admin cannot delete their account', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/profile')
        ->assertSee('jediný administrátorský účet')
        ->assertDontSee('Zmazať môj účet');

    $this->actingAs($admin)->from('/profile')->delete('/profile', ['password' => 'password'])
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    expect($admin->fresh())->not->toBeNull();
    $this->assertAuthenticatedAs($admin);
});

test('an admin can delete their account when another admin remains', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->admin()->create();

    $this->actingAs($admin)->get('/profile')->assertSee('Zmazať môj účet');

    $this->actingAs($admin)->delete('/profile', ['password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    expect($admin->fresh())->toBeNull();
});
