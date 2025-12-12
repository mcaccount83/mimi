<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Region extends Model
{
    protected $table = 'region';

    protected $primaryKey = 'id';

    protected $fillable = [ ]; // No fillable fields

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');  // 'conference_id' in region BelongsTo 'id' in conference
    }
}
