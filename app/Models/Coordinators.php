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
        // return $this->belongsTo(\App\Models\User::class, 'user_id');
        return $this->belongsTo(User::class, 'user_id');
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'primary_coordinator_id', 'id');
    }

    public function displayPosition()
    {
        return $this->belongsTo(CoordinatorPosition::class, 'display_position_id');
    }

    public function secondaryPosition()
    {
        return $this->belongsTo(CoordinatorPosition::class, 'sec_position_id');
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeWithinPositionRange($query, $start, $end)
    {
        return $query->whereBetween('position_id', [$start, $end]);
    }

    public function scopeForRegionOrConference($query, $regionId, $conferenceId)
    {
        return $query->where(function ($q) use ($regionId, $conferenceId) {
            $q->where('region_id', $regionId)
            ->orWhere(function ($subQ) use ($conferenceId) {
                $subQ->where('region_id', 0)
                    ->where('conference_id', $conferenceId);
            });
        });
    }

}
