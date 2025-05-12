<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AdminEmail extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'admin_email';

    protected $fillable = [

    ];
}
