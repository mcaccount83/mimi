<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProbationSubmission extends Model
{
    protected $primaryKey = 'chapter_id';

    protected $table = 'probation_submission';

    protected $guarded = [ ]; // ALL columns are mass-assignable

}
