<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('coordinator_reporting_tree')]
#[WithoutTimestamps]
#[Unguarded]
class CoordinatorTree extends Model
{
    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Coordinators::class, 'coordinator_id', 'id');  // 'coordinator_id' in coordinator_tree BelongsTo 'id' in coordinators
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'coordinator_id', 'primary_coordinator_id');  // 'coordinator_id' in coordinator_tree BelongsTo 'id' in chapters
    }
}
