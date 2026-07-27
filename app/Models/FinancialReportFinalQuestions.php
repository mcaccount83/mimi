<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table('financial_report_final_questions', 'chapter_id', incrementing: false)]
#[Unguarded]
class FinancialReportFinalQuestions extends Model
{
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
