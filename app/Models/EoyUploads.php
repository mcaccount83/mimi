<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EoyUploads extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'country';

    protected $fillable = [

    ];
}
