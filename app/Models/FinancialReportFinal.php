<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table('financial_report_final', 'chapter_id')]
#[Unguarded]
class FinancialReportFinal extends Model
{
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
