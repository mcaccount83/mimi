<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Status extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'status';

    protected $fillable = [

    ];

}
