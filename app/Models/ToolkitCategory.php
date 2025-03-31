<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ToolkitCategory extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'toolkit_category';

    protected $fillable = [

    ];
}
