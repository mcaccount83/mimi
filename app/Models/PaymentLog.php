<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PaymentLog extends Model
{
    protected $guarded = []; // ALL columns are mass-assignable

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    public function board(): HasOne
    {
        return $this->hasOne(Boards::class, 'user_id', 'customer_id');
    }
}
