<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextBlock extends Model
{
    public const TYPES = [
        'text' => 'Text',
        'list' => 'Zoznam (jedna položka na riadok)',
        'html' => 'HTML',
    ];

    protected $fillable = ['key', 'label', 'type', 'value', 'hint', 'sort'];

    /** Value of a block by key, with an optional fallback. */
    public static function get(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    /** @return list<string> */
    public function getItemsAttribute(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $this->value))));
    }
}
