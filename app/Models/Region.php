<?php

namespace App\Models;

use Faker\Core\Coordinates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Region extends Model
{
    use HasFactory;
    use Notifiable;

    // Specify the table name explicitly
    protected $table = 'region';

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

}
