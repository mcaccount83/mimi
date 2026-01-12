<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReportFinal extends Model
{
    protected $table = 'financial_report_final';

    protected $primaryKey = 'chapter_id';

    protected $guarded = []; // ALL columns are mass-assignable
}
