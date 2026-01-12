<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChapterApplication extends Model
{
    protected $table = 'chapter_application';

    protected $primaryKey = 'chapter_id';

    protected $guarded = []; // ALL columns are mass-assignable

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'coordinator_id' in coordinator_tree BelongsTo 'id' in coordinators
    }
}
