<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table('financial_report', 'chapter_id', incrementing: false)]
#[Unguarded]
class FinancialReport extends Model
{
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
