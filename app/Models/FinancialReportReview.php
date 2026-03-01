<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReportReview extends Model
{
    protected $table = 'financial_report_review';

    protected $primaryKey = 'chapter_id';

    protected $guarded = []; // ALL columns are mass-assignable
}
