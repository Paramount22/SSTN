<?php

use App\Models\AdminActivity;
use App\Models\User;
use App\Notifications\AdminAccessChanged;
use App\Notifications\AdminInvitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

beforeEach(function () {
    Notification::fake();
    $this->admin = User::factory()->admin()->create(['name' => 'Jana Hlavná']);
});

/** Acting as the admin with a fresh password confirmation. */
function confirmedAdmin(): TestCase
{
    return test()->actingAs(test()->admin)->withSession(['auth.password_confirmed_at' => time()]);
}

function invite(string $email = 'novy@sstn.sk'): User
{
    confirmedAdmin()->post(route('admin.users.store'), ['name' => 'Nový Kolega', 'email' => $email])
        ->assertSessionHasNoErrors();

    // The invitation link is opened by the invited person, not the logged-in admin.
    app('auth')->forgetGuards();

    return User::firstWhere('email', mb_strtolower($email));
}

function invitationToken(User $user): string
{
    $token = null;
    Notification::assertSentTo($user, AdminInvitation::class, function ($n) use (&$token) {
        $token = $n->token;

        return true;
    });

    return $token;
}

test('the admin list needs an admin and a fresh password confirmation', function () {
    $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create())->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($this->admin)->get(route('admin.users.index'))->assertRedirect(route('password.confirm'));

    confirmedAdmin()->get(route('admin.users.index'))->assertOk()->assertSee($this->admin->email);
});

test('an invitation creates a pending admin, mails them and notifies all admins', function () {
    $other = User::factory()->admin()->create();
    $user = invite('Novy@SSTN.sk');

    expect($user->is_admin)->toBeTrue()
        ->and($user->isInvitationPending())->toBeTrue()
        ->and($user->name)->toBe('Nový Kolega');

    Notification::assertSentTo($user, AdminInvitation::class);
    Notification::assertSentTo([$this->admin, $other], AdminAccessChanged::class);
    Notification::assertNotSentTo($user, AdminAccessChanged::class);

    expect(AdminActivity::sole())
        ->action->toBe('invited')
        ->actor_id->toBe($this->admin->id)
        ->subject_email->toBe('novy@sstn.sk');
});

test('an e-mail that already has an account cannot be invited', function () {
    confirmedAdmin()->post(route('admin.users.store'), ['name' => 'X', 'email' => strtoupper($this->admin->email)])
        ->assertSessionHasErrors(['email' => 'Účet s týmto e-mailom už existuje.']);

    expect(User::count())->toBe(1);
});

test('the invited admin sets a password once and can then log in', function () {
    $user = invite();
    $token = invitationToken($user);

    $this->get(route('invitation.show', ['token' => $token, 'email' => $user->email]))
        ->assertOk()->assertSee('Vitajte v administrácii');

    $this->post(route('invitation.store'), [
        'token' => $token, 'email' => $user->email,
        'password' => 'Nove-heslo-2026', 'password_confirmation' => 'Nove-heslo-2026',
    ])->assertRedirect(route('login'));

    $user->refresh();
    expect($user->isInvitationPending())->toBeFalse()
        ->and(Hash::check('Nove-heslo-2026', $user->password))->toBeTrue()
        ->and(AdminActivity::where('action', 'joined')->exists())->toBeTrue();

    // Single use
    $this->get(route('invitation.show', ['token' => $token, 'email' => $user->email]))->assertSee('Pozvánka neplatí');

    $this->post(route('login'), ['email' => $user->email, 'password' => 'Nove-heslo-2026']);
    $this->assertAuthenticatedAs($user);
});

test('an expired or wrong invitation link is rejected', function () {
    $user = invite();
    $token = invitationToken($user);
    $payload = ['email' => $user->email, 'password' => 'Nove-heslo-2026', 'password_confirmation' => 'Nove-heslo-2026'];

    $this->get(route('invitation.show', ['token' => 'zly-token', 'email' => $user->email]))->assertSee('Pozvánka neplatí');
    $this->post(route('invitation.store'), $payload + ['token' => 'zly-token'])->assertSessionHasErrors('password');

    $this->travel(8)->days();
    $this->post(route('invitation.store'), $payload + ['token' => $token])->assertSessionHasErrors('password');

    expect($user->fresh()->isInvitationPending())->toBeTrue();
});

test('an ordinary password reset token does not work as an invitation', function () {
    $user = invite();
    $resetToken = Password::broker('users')->createToken($user);

    $this->post(route('invitation.store'), [
        'token' => $resetToken, 'email' => $user->email,
        'password' => 'Nove-heslo-2026', 'password_confirmation' => 'Nove-heslo-2026',
    ])->assertSessionHasErrors('password');

    expect($user->fresh()->isInvitationPending())->toBeTrue();
});

test('a pending invitation can be resent and cancelled', function () {
    $user = invite();

    confirmedAdmin()->post(route('admin.users.invitation.resend', $user))->assertSessionHasNoErrors();
    Notification::assertSentToTimes($user, AdminInvitation::class, 2);

    confirmedAdmin()->delete(route('admin.users.invitation.cancel', $user))->assertSessionHasNoErrors();
    expect($user->fresh())->toBeNull()
        ->and(DB::table('admin_invitation_tokens')->count())->toBe(0);

    // Only pending invitations can be cancelled
    confirmedAdmin()->delete(route('admin.users.invitation.cancel', $this->admin))->assertNotFound();
    expect($this->admin->fresh())->not->toBeNull();
});

test('a deactivated admin is logged out, cannot log in and can be reactivated', function () {
    $other = User::factory()->admin()->create();

    confirmedAdmin()->patch(route('admin.users.deactivate', $other))->assertSessionHasNoErrors();
    expect($other->fresh()->isActive())->toBeFalse();
    Notification::assertSentTo($this->admin, AdminAccessChanged::class, fn ($n) => $n->activity->action === 'deactivated');
    Notification::assertNotSentTo($other, AdminAccessChanged::class);

    // An open session ends on the next request
    $this->actingAs($other->fresh())->get(route('admin.dashboard'))->assertRedirect(route('login'));
    $this->assertGuest();

    $this->post(route('login'), ['email' => $other->email, 'password' => 'password'])->assertSessionHasErrors('email');
    $this->assertGuest();

    confirmedAdmin()->patch(route('admin.users.reactivate', $other))->assertSessionHasNoErrors();
    expect($other->fresh()->isActive())->toBeTrue();
});

test('an admin cannot deactivate their own account', function () {
    confirmedAdmin()->patch(route('admin.users.deactivate', $this->admin))->assertSessionHasErrors('user');

    expect($this->admin->fresh()->isActive())->toBeTrue();
});

test('pending and deactivated admins do not count when deleting the last real admin', function () {
    invite();
    User::factory()->admin()->create()->forceFill(['deactivated_at' => now()])->save();

    expect($this->admin->isLastAdmin())->toBeTrue();
    $this->actingAs($this->admin)->delete(route('profile.destroy'), ['password' => 'password'])
        ->assertSessionHasErrorsIn('userDeletion', 'password');
});

test('deleting your own admin account is logged and announced', function () {
    $other = User::factory()->admin()->create();

    $this->actingAs($this->admin)->delete(route('profile.destroy'), ['password' => 'password'])->assertRedirect('/');

    expect(AdminActivity::sole()->action)->toBe('deleted');
    Notification::assertSentTo($other, AdminAccessChanged::class);
});

test('admin:create is logged, announced and reactivates a deactivated admin', function () {
    $user = User::factory()->admin()->create(['email' => 'clen@sstn.sk']);
    $user->forceFill(['deactivated_at' => now()])->save();

    $this->artisan('admin:create', ['email' => 'clen@sstn.sk'])
        ->expectsConfirmation('Účet clen@sstn.sk už existuje. Nastaviť mu nové heslo a práva administrátora?', 'yes')
        ->expectsQuestion('Heslo', 'Nove-heslo-2026')
        ->expectsQuestion('Heslo znova', 'Nove-heslo-2026')
        ->assertSuccessful();

    expect($user->fresh()->isActive())->toBeTrue()
        ->and(AdminActivity::sole())->action->toBe('granted')->actor_id->toBeNull();
    Notification::assertSentTo([$this->admin, $user], AdminAccessChanged::class);
});

test('the admin change e-mail says who did what', function () {
    invite();
    $mail = (new AdminAccessChanged(AdminActivity::sole()))->toMail($this->admin)->render();

    expect((string) $mail)->toContain('Pozvánka do administrácie odoslaná')
        ->toContain('Nový Kolega (novy@sstn.sk)')
        ->toContain('Jana Hlavná');
});
