<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('coordinator_application', 'coordinator_id')]
#[Unguarded]
class CoordinatorApplication extends Model
{
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Coordinators::class, 'coordinator_id', 'id');  // 'coordinator_id' in coordinator_tree BelongsTo 'id' in coordinators
    }
}
