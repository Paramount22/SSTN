<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InviteAdminRequest;
use App\Models\AdminActivity;
use App\Models\User;
use App\Notifications\AdminInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Who can use the administration. New admins are invited by e-mail and choose
 * their own password; admins are deactivated rather than deleted. Every change is
 * logged and e-mailed to all active admins (AdminActivity::record()).
 */
class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::where('is_admin', true)
                ->orderByRaw('deactivated_at is not null')
                ->orderBy('name')
                ->get(),
            'activities' => AdminActivity::latest('id')->limit(15)->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(InviteAdminRequest $request): RedirectResponse
    {
        // Rolled back when the e-mail cannot be sent, so no half-invited account is left.
        $user = DB::transaction(function () use ($request) {
            $user = new User($request->safe()->only(['name', 'email']));
            // Nobody knows this password; the invited admin sets their own.
            $user->password = Str::password(40);
            $user->forceFill(['is_admin' => true, 'invitation_sent_at' => now()])->save();

            $this->sendInvitation($user, $request->user());
            AdminActivity::record('invited', $user, $request->user());

            return $user;
        });

        return redirect()->route('admin.users.index')->with('status', "Pozvánka pre {$user->email} bola odoslaná.");
    }

    public function resendInvitation(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->is_admin && $user->isInvitationPending() && $user->isActive(), 404);

        DB::transaction(function () use ($request, $user) {
            $user->forceFill(['invitation_sent_at' => now()])->save();
            $this->sendInvitation($user, $request->user());
            AdminActivity::record('invitation_resent', $user, $request->user());
        });

        return back()->with('status', "Pozvánka pre {$user->email} bola odoslaná znova.");
    }

    public function cancelInvitation(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->is_admin && $user->isInvitationPending(), 404);

        // The account was never used, so it can go entirely.
        AdminActivity::record('invitation_cancelled', $user, $request->user());
        Password::broker('invites')->deleteToken($user);
        $user->delete();

        return back()->with('status', "Pozvánka pre {$user->email} bola zrušená.");
    }

    public function deactivate(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->is_admin && ! $user->isInvitationPending() && $user->isActive(), 404);

        // Also guarantees an active admin always remains: the one making the change.
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Vlastný účet nemôžete deaktivovať.']);
        }

        // A new remember token invalidates "remember me" cookies; open sessions end in EnsureAccountIsActive.
        $user->forceFill(['deactivated_at' => now(), 'remember_token' => Str::random(60)])->save();
        AdminActivity::record('deactivated', $user, $request->user());

        return back()->with('status', "Prístup pre {$user->email} bol deaktivovaný.");
    }

    public function reactivate(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->is_admin && ! $user->isActive(), 404);

        $user->forceFill(['deactivated_at' => null])->save();
        AdminActivity::record('reactivated', $user, $request->user());

        return back()->with('status', "Prístup pre {$user->email} bol znova aktivovaný.");
    }

    private function sendInvitation(User $user, User $inviter): void
    {
        $user->notify(new AdminInvitation(Password::broker('invites')->createToken($user), $inviter->name));
    }
}
