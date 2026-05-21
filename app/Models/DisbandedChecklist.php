<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table('disbanded_checklist', 'chapter_id')]
#[Unguarded]
class DisbandedChecklist extends Model {}
