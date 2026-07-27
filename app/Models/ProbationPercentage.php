<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('probation_percentage', 'id')]
class ProbationPercentage extends Model
{
    public function probation(): BelongsTo
    {
        return $this->belongsTo(Probation::class, 'probation', 'id');  // 'probation' in probation_percentage BelongsTo 'id' in probation
    }

    public function financialReport(): HasMany
    {
        return $this->hasMany(FinancialReport::class, 'party_percentage', 'id');  // 'id' in probation hasMany 'party_percentage' in financial_report
    }
}
