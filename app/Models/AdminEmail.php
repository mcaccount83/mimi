<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminEmail extends Model
{
    protected $table = 'admin_email_new';

    protected $guarded = []; // ALL columns are mass-assignable
}
