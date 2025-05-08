<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ActiveStatus extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'active_status';

    protected $primaryKey = 'id';

    protected $fillable = [

    ];
}
