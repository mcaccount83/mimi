<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conference extends Model
{
    protected $table = 'conference';

    protected $guarded = []; // ALL columns are mass-assignable

    // In your Conference model
    public function regions(): HasMany
    {
        return $this->hasMany(Region::class, 'conference_id', 'id');
    }
}
