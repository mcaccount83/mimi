<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EinUploads extends Model
{
    use HasFactory;

    protected $fillable = ['chapter_id', 'file_name', 'file_id'];
    //
}
