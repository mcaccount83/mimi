<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table('documents_eoy', 'chapter_id')]
#[Unguarded]
class DocumentsEOY extends Model {}
