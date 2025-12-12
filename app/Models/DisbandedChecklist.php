<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisbandedChecklist extends Model
{
    protected $table = 'disbanded_checklist';

    protected $primaryKey = 'chapter_id';

    protected $guarded = [ ]; // ALL columns are mass-assignable
}
