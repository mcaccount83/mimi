<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReportAwardsBadges extends Model
{

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
