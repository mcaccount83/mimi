<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documents extends Model
{
    use HasFactory;

    //
    // public $timestamps = false;

    protected $fillable = [
        'chapter_id',  // Add this if not already present
        'ein_letter_path',
        'ein_notes',
        'disband_letter_path',
        'financial_pdf_path',
        'roster_path',
        'irs_path',
        'statement_1_path',
        'statement_2_path',

    ];
    protected $table = 'documents';

    protected $primaryKey = 'chapter_id';


}
