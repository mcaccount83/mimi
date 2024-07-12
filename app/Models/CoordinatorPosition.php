<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CoordinatorPosition extends Model
{
    use Notifiable;

    use HasFactory;

    // Specify the table name explicitly
    protected $table = 'coordinator_position';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [

    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [

    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }


    public function position()
    {
        return $this->belongsTo(\App\Models\CoordinatorPosition::class, 'position_id');
    }

}
