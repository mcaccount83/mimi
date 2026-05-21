<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Table('financial_report_awards', 'id')]
class FinancialReportAwards extends Model
{

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
