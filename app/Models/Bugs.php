<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Bugs extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'bugs';

    protected $fillable = [
        'task', 'details', 'status', 'priority', 'reported_id', 'notes',
    ];
}
