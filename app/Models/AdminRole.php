<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AdminRole extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'admin_role';

    protected $primaryKey = 'id';

    protected $fillable = [

    ];
}
