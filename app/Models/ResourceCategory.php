<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ResourceCategory extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'resource_category';

    protected $fillable = [

    ];

}
