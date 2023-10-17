<?php

namespace App;

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
