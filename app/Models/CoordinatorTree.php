<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class CoordinatorTree extends Model
{
    use HasFactory;
    use Notifiable;

    public $timestamps = false;

    protected $table = 'coordinator_reporting_tree';

    protected $fillable = [

    ];

}
