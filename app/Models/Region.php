<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Table('region', 'id')]
#[Unguarded]
class Region extends Model
{
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');  // 'conference_id' in region BelongsTo 'id' in conference
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'region_id', 'id');
    }

    public function inquiries(): HasOne
    {
        return $this->hasOne(RegionInquiry::class, 'region_id', 'id');
    }
}
