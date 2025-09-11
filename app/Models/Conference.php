<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Conference extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'conference';

    protected $fillable = ['id', 'conference_name', 'short_name', 'conference_description', 'short_description',
    ];

    // In your Conference model
    public function regions(): HasMany
    {
        return $this->hasMany(Region::class, 'conference_id', 'id');
    }
}
