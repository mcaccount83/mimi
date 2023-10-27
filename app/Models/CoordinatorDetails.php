<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoordinatorDetails extends Model
{
    //
    public $timestamps = false;

    protected $fillable = [

        'name',

    ];

    protected $table = 'coordinator_details';
}
