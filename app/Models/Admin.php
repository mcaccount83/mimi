<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'task', 'details', 'status', 'notes', 'priority', 'reported_id',
    ];

    protected $table = 'admin';

    protected $primaryKey = 'id';
}
