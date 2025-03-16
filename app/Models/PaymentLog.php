<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'status', 'payment_type', 'order_id', 'raw_response'
    ];

    protected $casts = [
        'raw_response' => 'array'
    ];

    // is bank transfer
    public function getIsBankTransferAttribute()
    {
        return $this->payment_type == 'bank_transfer';
    }

    // is echannel
    public function getIsEchannelAttribute()
    {
        return $this->payment_type == 'echannel';
    }
}
