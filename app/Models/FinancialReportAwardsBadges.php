<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialReportAwardsBadges extends Model
{
    protected $table = 'financial_report_awards_badges';

    protected $primaryKey = 'id';

    protected $guarded = []; // ALL columns are mass-assignable

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'report_year_id', 'id');
    }

    public function eoyAward()
    {
        return $this->belongsTo(FinancialReportAwards::class, 'eoy_award_id', 'id');
    }
}


