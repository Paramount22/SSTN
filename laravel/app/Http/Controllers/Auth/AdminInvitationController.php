<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * The link from an invitation e-mail: the invited admin chooses a password.
 * The token is single-use and expires after 7 days (auth.passwords.invites).
 */
class AdminInvitationController extends Controller
{
    private const INVALID = 'Odkaz z pozvánky je neplatný, už bol použitý alebo mu vypršala platnosť. Požiadajte administrátora o novú pozvánku.';

    public function show(Request $request, string $token): View
    {
        $user = $this->pendingUser((string) $request->query('email'));

        return view('auth.accept-invitation', [
            'token' => $token,
            'user' => $user && Password::broker('invites')->tokenExists($user, $token) ? $user : null,
            'invalid' => self::INVALID,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (! $this->pendingUser($request->string('email'))) {
            throw ValidationException::withMessages(['password' => self::INVALID]);
        }

        $status = Password::broker('invites')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                    'invitation_sent_at' => null,
                    // The link reached their inbox, so the address is confirmed.
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ])->save();

                AdminActivity::record('joined', $user, $user);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['password' => self::INVALID]);
        }

        return redirect()->route('login')->with('status', 'Heslo je nastavené. Teraz sa môžete prihlásiť.');
    }

    private function pendingUser(string $email): ?User
    {
        return User::where('email', mb_strtolower(trim($email)))
            ->where('is_admin', true)
            ->whereNotNull('invitation_sent_at')
            ->whereNull('deactivated_at')
            ->first();
    }
}
