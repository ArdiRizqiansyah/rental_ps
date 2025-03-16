<?php

namespace App\Models;

use App\Models\OrderItem;
use App\Enums\OrderStatusEnum;
use App\Helpers\GeneratingHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'code',
        'snap_token',
        'user_metadata',
        'total',
        'status',
        'settlement_at',
        'user_id',
    ];

    protected $casts = [
        'user_metadata' => 'object',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->code = GeneratingHelper::setOrderCode($order->code);
        });
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function scopeIsPending($query)
    {
        return $query->where('status', OrderStatusEnum::PENDING);
    }

    public function isSuccess(): Attribute
    {
        return Attribute::make(
            fn ($value) => $this->status == OrderStatusEnum::SUCCESS,
        );
    }
}
