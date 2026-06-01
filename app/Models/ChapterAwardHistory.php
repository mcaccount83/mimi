<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('chapter_awards_history', 'id')]
#[Unguarded]
class ChapterAwardHistory extends Model
{
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'chapter_id' BelongsTo 'id' in chapters
    }

    public function awardtype(): BelongsTo
    {
        return $this->belongsTo(FinancialReportAwards::class, 'awards_type', 'id');  // 'awards_type' BelongsTo 'id' in FinancialReportAwards
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'report_year_id', 'id');
    }
}
