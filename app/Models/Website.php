<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Website extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'website';

    protected $primaryKey = 'id';

    protected $fillable = [

    ];
}
