<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'state';

    protected $primaryKey = 'id';

    protected $fillable = []; // No fillable fields


public function region()
{
    return $this->belongsTo(Region::class, 'region_id', 'id');

}

public function conference()
{
    return $this->belongsTo(Conference::class, 'conference_id', 'id');
}

}
