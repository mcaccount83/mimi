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
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function CoordinatorDetails(): HasOne
    {
        return $this->hasOne(\App\Models\CoordinatorDetails::class, 'user_id');
    }

    public function BoardDetails(): HasOne
    {
        return $this->hasOne(\App\Models\BoardDetails::class, 'user_id');
    }

    public function OutgoingDetails(): HasOne
    {
        return $this->hasOne(\App\Models\OutgoingBoardMember::class, 'user_id');
    }
}
