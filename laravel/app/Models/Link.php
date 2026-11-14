<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Link extends Model
{
    public const GROUPS = [
        'partner' => 'Partneri',
        'footer' => 'Odkazy v pätičke',
        'video' => 'Videá',
        'document' => 'Dokumenty na stiahnutie',
    ];

    protected $fillable = ['group', 'label', 'url', 'description', 'sort'];

    public function scopeInGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group)->orderBy('sort')->orderBy('id');
    }

    public function getGroupLabelAttribute(): string
    {
        return static::GROUPS[$this->group] ?? $this->group;
    }

    public function getIsExternalAttribute(): bool
    {
        return Str::startsWith($this->url, ['http://', 'https://']);
    }
}
