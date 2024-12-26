<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Region extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'region';

    protected $primaryKey = 'id';

    protected $fillable = [

    ];
}
