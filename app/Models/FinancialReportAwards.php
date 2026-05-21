<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table('financial_report_awards', 'id')]
class FinancialReportAwards extends Model
{
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
