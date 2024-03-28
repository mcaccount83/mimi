<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'conf',
        'state',
        'chapter_name',
        'folder_id',
    ];
}
