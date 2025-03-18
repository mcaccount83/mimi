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
        'good_standing_letter',
        'ein_letter',
        'ein_letter_path',
        'irs_notes',
        'irs_verified',
        'probation_path',
        'probation_release_path',
        'new_board_submitted',
        'new_board_active',
        'financial_report_received',
        'report_received',
        'financial_review_complete',
        'review_complete',
        'report_notes',
        'report_extension',
        'extension_notes',
        'balance',
        'financial_pdf_path',
        'roster_path',
        'irs_path',
        'statement_1_path',
        'statement_2_path',
        'award_path',
        'disband_letter'.
        'disband_letter_path',
        'final_report_received'.
        'final_financial_pdf_path',
        'created_at',
        'updated_at',
    ];

}
