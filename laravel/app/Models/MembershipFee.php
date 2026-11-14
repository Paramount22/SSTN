<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipFee extends Model
{
    protected $fillable = ['kind', 'price', 'votes', 'is_featured', 'sort'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function getPriceLabelAttribute(): string
    {
        $price = (float) $this->price;

        return (floor($price) == $price ? number_format($price, 0) : number_format($price, 2, ',', ' ')).' EUR';
    }
}
