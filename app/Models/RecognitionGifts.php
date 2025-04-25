<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class RecognitionGifts extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'recognition_gifts';

    protected $primaryKey = 'id';

    protected $fillable = [

    ];
}
