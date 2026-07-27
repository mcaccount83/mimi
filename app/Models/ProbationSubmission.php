<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table('probation_submission', 'chapter_id', incrementing: false)]
#[Unguarded]
class ProbationSubmission extends Model {}
