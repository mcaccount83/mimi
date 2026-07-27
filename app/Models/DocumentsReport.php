<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table('documents_report', 'chapter_id', incrementing: false)]
#[Unguarded]
class DocumentsReport extends Model {}
