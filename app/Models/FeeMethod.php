<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'price',
        'percent',
        'is_published',
    ];

    public function scopeIsPublished($query)
    {
        return $query->where('is_published', 1);
    }
}
