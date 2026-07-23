<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('probation_percentage', 'id')]
class ProbationPercentage extends Model
{
    public function probation(): BelongsTo
        {
            return $this->belongsTo(Probation::class, 'probation', 'id');  // 'probation' in probation_percentage BelongsTo 'id' in probation
        }
}
