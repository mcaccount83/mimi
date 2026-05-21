<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table('probation_submission', 'chapter_id')]
#[Unguarded]
class ProbationSubmission extends Model {}
