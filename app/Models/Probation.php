<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('probation', 'id')]
class Probation extends Model
{
    public function percentage(): BelongsTo
    {
        return $this->belongsTo(ProbationPercentage::class, 'percentage', 'id');  // 'percentage' in probation BelongsTo 'id' in probation_percentage
    }
}
