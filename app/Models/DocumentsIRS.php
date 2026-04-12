<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentsIRS extends Model
{
    protected $table = 'documents_irs';

    protected $primaryKey = 'chapter_id';

    protected $guarded = []; // ALL columns are mass-assignable
}
