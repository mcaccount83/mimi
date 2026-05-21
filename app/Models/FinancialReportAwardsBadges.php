<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialReportAwardsBadges extends Model
{

    protected $primaryKey = 'id';

    protected $guarded = []; // ALL columns are mass-assignable

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'report_year_id', 'id');
    }

    public function eoyAward(): BelongsTo
    {
        return $this->belongsTo(FinancialReportAwards::class, 'eoy_award_id', 'id');
    }
}
