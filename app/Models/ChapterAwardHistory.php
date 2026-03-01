<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChapterAwardHistory extends Model
{
    protected $table = 'chapter_awards_history';

    protected $guarded = []; // ALL columns are mass-assignable

    public function chapter()
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'chapter_id' BelongsTo 'id' in chapters
    }

    public function awardtype(): BelongsTo
    {
        return $this->belongsTo(FinancialReportAwards::class, 'awards_type', 'id');  // 'awards_type' BelongsTo 'id' in FinancialReportAwards
    }
}
