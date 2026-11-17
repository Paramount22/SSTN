<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

/** One cookie decision of a visitor; every change is a new row. */
class CookieConsent extends Model
{
    use MassPrunable;

    public const UPDATED_AT = null;

    protected $fillable = ['consent_id', 'categories', 'version'];

    protected function casts(): array
    {
        return ['categories' => 'array'];
    }

    /** Records older than the retention period are deleted by `php artisan model:prune`. */
    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays((int) config('cookies.log_retention_days')));
    }
}
