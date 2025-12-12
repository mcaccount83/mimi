<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentsEOY extends Model
{
    protected $table = 'documents_eoy';

    protected $primaryKey = 'chapter_id';

    protected $guarded = [ ]; // ALL columns are mass-assignable
}
