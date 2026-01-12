<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';

    protected $primaryKey = 'id';

    protected $guarded = []; // ALL columns are mass-assignable
}
