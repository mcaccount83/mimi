<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Month extends Model
{
    use HasFactory;
    use Notifiable;

    // Specify the table name explicitly
    protected $table = 'month';

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
