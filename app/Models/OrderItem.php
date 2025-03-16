<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'productable_id',
        'productable_type',
        'metadata',
        'price',
        'addon_price',
        'qty',
        'total',
    ];

    protected $casts = [
        'metadata' => 'object',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function productable(): MorphTo
    {
        return $this->morphTo();
    }
}
