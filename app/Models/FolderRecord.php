<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class FolderRecord extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'folder_records';

    protected $fillable = [
        'year',
        'conf',
        'state',
        'chapter_name',
        'folder_id',
    ];
}
