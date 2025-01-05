<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Documents extends Model
{
    use HasFactory;
    use Notifiable;

    protected $primaryKey = 'chapter_id';

    protected $fillable = [
        'chapter_id',
        'ein_letter_path',
        'disband_letter_path',
        'financial_pdf_path',
        'roster_path',
        'irs_path',
        'statement_1_path',
        'statement_2_path',

    ];
}
