<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bugs extends Model
{
    use HasFactory;

    protected $fillable = [
        'task', 'details', 'status', 'priority', 'reported_id', 'notes',
    ];

    protected $table = 'bugs';

    protected $primaryKey = 'id';
}
