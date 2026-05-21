<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Unguarded]
class PaymentLog extends Model
{

    protected function casts(): array
    {
        return [
            'request_data' => 'array',
            'response_data' => 'array',
        ];
    }

    public function board(): HasOne
    {
        return $this->hasOne(Boards::class, 'user_id', 'customer_id');
    }
}
