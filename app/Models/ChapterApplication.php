<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('chapter_application', 'chapter_id')]
#[Unguarded]
class ChapterApplication extends Model
{

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'coordinator_id' in coordinator_tree BelongsTo 'id' in coordinators
    }
}
