<?php

namespace App\Models;

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

    public function CoordinatorDetails()
    {
        return $this->hasOne(\App\Models\CoordinatorDetails::class);
    }

    public function BoardDetails()
    {
        return $this->hasOne(\App\Models\BoardDetails::class);
    }

    public function OutgoingDetails()
    {
        return $this->hasOne(\App\Models\OutgoingBoardMember::class);
    }
}
