<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleDrive extends Model
{
    protected $table = 'google_drive_new';

    protected $guarded = []; // ALL columns are mass-assignable
}
