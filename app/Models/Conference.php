<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Conference extends Model
{
    use HasFactory;
    use Notifiable;

    // Specify the table name explicitly
    protected $table = 'conference';

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

    // public function coordinators(): BelongsTo
    // {
    //     return $this->belongsTo(Coordinators::class, 'conference');
    // }

    // public function chapter(): BelongsTo
    // {
    //     return $this->belongsTo(Chapter::class, 'conference');
    // }
}
