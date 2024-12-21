<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Resources extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'resources';

    protected $fillable = [
        'name', 'description', 'version', 'link',
    ];
}
