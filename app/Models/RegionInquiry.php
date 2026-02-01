<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegionInquiry extends Model
{
    protected $table = 'region_inquiries';

    protected $primaryKey = 'region_id';

    protected $guarded = []; // ALL columns are mass-assignable

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');  // 'region_id' in region_inquiries BelongsTo 'id' in region
    }

}
