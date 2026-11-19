<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

// phpunit.xml sets ADMIN_PATH=sprava-test, so the whole suite runs under a secret path.

test('the admin, login and password pages live under the secret path', function () {
    expect(config('admin.path'))->toBe('sprava-test')
        ->and(route('login', absolute: false))->toBe('/sprava-test/login')
        ->and(route('password.request', absolute: false))->toBe('/sprava-test/forgot-password')
        ->and(route('admin.dashboard', absolute: false))->toBe('/sprava-test')
        ->and(route('profile.edit', absolute: false))->toBe('/sprava-test/profile');

    $this->get('/sprava-test/login')->assertOk()->assertSee('Prihlásenie');
    $this->get('/sprava-test/forgot-password')->assertOk()->assertSee('Zabudnuté heslo');
});

test('the usual admin and login addresses return the 404 page', function (string $path) {
    $this->get($path)->assertNotFound()->assertSee('MIMO TOLERANCIE');
})->with(['/admin', '/admin/login', '/login', '/forgot-password', '/reset-password/abc', '/dashboard', '/profile', '/register']);

test('guests on admin pages are sent to the secret login', function () {
    $this->get('/sprava-test/events')->assertRedirect('/sprava-test/login');
});

test('the password reset e-mail links to the secret path', function () {
    Notification::fake();
    $user = User::factory()->admin()->create();

    $this->post('/sprava-test/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
        return str_contains($notification->toMail($user)->actionUrl, '/sprava-test/reset-password/');
    });
});
