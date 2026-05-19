<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    protected $table = 'fiscal_year';

    protected $guarded = [];

    // Relationships
    public function adminYear()
    {
        return $this->hasOne(AdminYear::class);
    }

    public function irsYear()
    {
        return $this->hasOne(AdminIRS::class);
    }

    public function reportYear()
    {
        return $this->hasOne(AdminReport::class, 'report_year_id');
    }

    public function awardBadges()
    {
        return $this->hasMany(FinancialReportAwardsBadges::class, 'report_year_id', 'id');
    }
}
