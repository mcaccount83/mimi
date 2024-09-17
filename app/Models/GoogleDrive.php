<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleDrive extends Model
{
    use HasFactory;

    // Specify the table name explicitly
    protected $table = 'google_drive';

    // Disable auto-incrementing primary key (since there is no `id` column)
    public $incrementing = false;

    // If you don't have any primary key, set this to `null`
    protected $primaryKey = null;

    // Disable timestamps if not using them (optional)
    public $timestamps = true; // Set this to `false` if you don't need timestamps

    //
}
