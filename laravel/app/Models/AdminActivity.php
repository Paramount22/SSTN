<?php

namespace App\Models;

use App\Notifications\AdminAccessChanged;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

/**
 * Log of changes to who can use the administration. Changes that add or remove
 * access are also e-mailed to every active admin, so an account added by someone
 * who took over another admin's login does not go unnoticed.
 */
class AdminActivity extends Model
{
    public const UPDATED_AT = null;

    public const ACTIONS = [
        'invited' => 'Pozvánka do administrácie odoslaná',
        'invitation_resent' => 'Pozvánka odoslaná znova',
        'invitation_cancelled' => 'Pozvánka zrušená',
        'joined' => 'Pozvánka prijatá, účet aktivovaný',
        'deactivated' => 'Prístup do administrácie deaktivovaný',
        'reactivated' => 'Prístup do administrácie znova aktivovaný',
        'deleted' => 'Účet administrátora zmazaný',
        'granted' => 'Práva administrátora pridelené z príkazového riadka',
    ];

    /** Actions that add or remove admin access. */
    public const NOTIFY = ['invited', 'deactivated', 'reactivated', 'deleted', 'granted'];

    protected $fillable = ['actor_id', 'actor_name', 'subject_id', 'subject_name', 'subject_email', 'action', 'ip_address'];

    public static function record(string $action, User $subject, ?User $actor = null): self
    {
        $activity = static::create([
            'actor_id' => $actor?->getKey(),
            'actor_name' => $actor?->name,
            'subject_id' => $subject->getKey(),
            'subject_name' => $subject->name,
            'subject_email' => $subject->email,
            'action' => $action,
            'ip_address' => request()->ip(),
        ]);

        if (in_array($action, self::NOTIFY, true)) {
            Notification::send(User::activeAdmins()->get(), new AdminAccessChanged($activity));
        }

        return $activity;
    }

    public function label(): string
    {
        return self::ACTIONS[$this->action] ?? $this->action;
    }

    /** Who did it; changes made with `php artisan admin:create` have no actor. */
    public function actorLabel(): string
    {
        return $this->actor_name ?? 'príkazový riadok na serveri';
    }
}
