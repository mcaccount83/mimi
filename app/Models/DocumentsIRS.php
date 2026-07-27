<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table('documents_irs', 'chapter_id', incrementing: false)]
#[Unguarded]
class DocumentsIRS extends Model {}
