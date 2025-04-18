<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'customer_id',
        'amount',
        'status',
        'response_code',
        'response_message',
        'request_data',
        'response_data'
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    public function board(): HasOne
    {
        return $this->hasOne(Boards::class, 'user_id', 'customer_id');
    }
}
