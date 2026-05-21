<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table(key: 'chapter_id')]
#[Unguarded]
class Payments extends Model
{
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
