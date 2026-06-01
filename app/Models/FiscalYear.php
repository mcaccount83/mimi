<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Table('fiscal_year', 'id')]
#[Unguarded]
class FiscalYear extends Model
{
    // Relationships
    public function adminYear(): HasOne
    {
        return $this->hasOne(AdminYear::class);
    }

    public function irsYear(): HasOne
    {
        return $this->hasOne(AdminIRS::class);
    }

    public function reportYear(): HasOne
    {
        return $this->hasOne(AdminReport::class, 'report_year_id');
    }

    public function awardBadges(): HasMany
    {
        return $this->hasMany(FinancialReportAwardsBadges::class, 'report_year_id', 'id');
    }
}
