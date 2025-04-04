<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Probation extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'probation';

    protected $primaryKey = 'id';

    protected $fillable = [

    ];
}
