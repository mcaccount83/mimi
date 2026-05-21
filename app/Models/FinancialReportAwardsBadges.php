<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('financial_report_awards_badges', 'id')]
#[Unguarded]
class FinancialReportAwardsBadges extends Model
{
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'report_year_id', 'id');
    }

    public function eoyAward(): BelongsTo
    {
        return $this->belongsTo(FinancialReportAwards::class, 'eoy_award_id', 'id');
    }
}
