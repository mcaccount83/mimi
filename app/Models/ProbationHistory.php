<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('probation_history', 'id')]
class ProbationHistory extends Model
{
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'chapter_id' in probation_history BelongsTo 'id' in chapters
    }
}
