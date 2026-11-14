<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    public const TYPES = [
        'sstn' => 'SSTN',
        'zsvts' => 'ZSVTS',
        'cstn' => 'Spoločne s ČSTN',
        'sdn' => 'Svetový deň normalizácie',
        'forum' => 'Fórum normalizátorov',
        'vz' => 'Valné zhromaždenie',
    ];

    protected $fillable = [
        'title', 'slug', 'type', 'starts_on', 'ends_on', 'date_text',
        'location', 'organizers', 'intro', 'body', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Event $event) {
            if (blank($event->slug)) {
                $event->slug = static::uniqueSlug($event->title, $event->id);
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'podujatie';
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(EventAttachment::class)->orderBy('sort')->orderBy('id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate(
            \DB::raw('COALESCE(ends_on, starts_on)'), '>=', today()
        )->orderBy('starts_on');
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->whereDate(
            \DB::raw('COALESCE(ends_on, starts_on)'), '<', today()
        )->orderByDesc('starts_on');
    }

    public function getTypeLabelAttribute(): string
    {
        return static::TYPES[$this->type] ?? $this->type;
    }

    public function getIsUpcomingAttribute(): bool
    {
        return ($this->ends_on ?? $this->starts_on)->isToday()
            || ($this->ends_on ?? $this->starts_on)->isFuture();
    }

    /** Human date: the free-text override, or "20. 11. 2026" / "8. – 9. 4. 2025". */
    public function getDateLabelAttribute(): string
    {
        if (filled($this->date_text)) {
            return $this->date_text;
        }
        if ($this->ends_on && ! $this->ends_on->isSameDay($this->starts_on)) {
            return $this->starts_on->format('j. n.').' – '.$this->ends_on->format('j. n. Y');
        }

        return $this->starts_on->format('j. n. Y');
    }
}
