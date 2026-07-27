<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('financial_report_review', 'chapter_id', incrementing: false)]
#[Unguarded]
class FinancialReportReview extends Model
{
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function partyPercentage(): BelongsTo
    {
        return $this->belongsTo(ProbationPercentage::class, 'review_party_percentage', 'id');  // 'party_percentage' in financial_report BelongsTo 'id' in probation_percentage
    }
}
