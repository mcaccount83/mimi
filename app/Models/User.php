<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'user_type', 'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function Coordinators(): HasOne
    {
        return $this->hasOne(\App\Models\Coordinators::class, 'user_id');
    }

    public function Boards(): HasOne
    {
        return $this->hasOne(\App\Models\Boards::class, 'user_id');
    }

    public function OutgoingDetails(): HasOne
    {
        return $this->hasOne(\App\Models\OutgoingBoardMember::class, 'user_id');
    }
}
