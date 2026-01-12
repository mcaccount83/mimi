<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoordinatorApplication extends Model
{
    protected $table = 'coordinator_application';

    protected $primaryKey = 'coordinator_id';

    protected $guarded = []; // ALL columns are mass-assignable

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Coordinators::class, 'coordinator_id', 'id');  // 'coordinator_id' in coordinator_tree BelongsTo 'id' in coordinators
    }
}
