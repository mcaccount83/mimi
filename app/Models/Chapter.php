<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    //
    public $timestamps = false;

    protected $fillable = [

        'name',
        'ein_letter_path',
        'ein_letter',

    ];
}
