<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coordinators extends Model
{
    use HasFactory;

    //
    public $timestamps = false;

    protected $fillable = [
        'name', 'email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function position()
    {
        return $this->belongsTo(CoordinatorPosition::class, 'display_position_id');
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }



    // public function chapter()
    // {
    //     return $this->belongsTo(Chapter::class, 'primary_coordinator_id', 'id');
    // }

    // public function displayPosition()
    // {
    //     return $this->belongsTo(CoordinatorPosition::class, 'display_position_id');
    // }

    // public function secondaryPosition()
    // {
    //     return $this->belongsTo(CoordinatorPosition::class, 'sec_position_id');
    // }

}
