<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Month extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'month';

    protected $primaryKey = 'id';

    protected $fillable = [

    ];
}
