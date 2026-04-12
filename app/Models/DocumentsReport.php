<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentsReport extends Model
{
    protected $table = 'documents_report';

    protected $primaryKey = 'chapter_id';

    protected $guarded = []; // ALL columns are mass-assignable
}
