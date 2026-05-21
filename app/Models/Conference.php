<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('conference')]
#[Unguarded]
class Conference extends Model
{
    // In your Conference model
    public function regions(): HasMany
    {
        return $this->hasMany(Region::class, 'conference_id', 'id');
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'conference_id', 'id');
    }
}
