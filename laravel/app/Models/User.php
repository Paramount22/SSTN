<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// is_admin, invitation_sent_at and deactivated_at are deliberately not fillable:
// set them only with forceFill(), never from request data.
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Admins who can log in now: not deactivated and not waiting on an invitation. */
    public function scopeActiveAdmins(Builder $query): void
    {
        $query->where('is_admin', true)->whereNull('deactivated_at')->whereNull('invitation_sent_at');
    }

    public function isActive(): bool
    {
        return $this->deactivated_at === null;
    }

    public function isInvitationPending(): bool
    {
        return $this->invitation_sent_at !== null;
    }

    /** True when no other administrator who can log in would remain if this account were gone. */
    public function isLastAdmin(): bool
    {
        return $this->is_admin
            && ! static::activeAdmins()->whereKeyNot($this->getKey())->exists();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'invitation_sent_at' => 'datetime',
            'deactivated_at' => 'datetime',
        ];
    }
}
