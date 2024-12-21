<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class FinancialReport extends Model
{
    use HasFactory;
    use Notifiable;

    protected $primaryKey = 'chapter_id';

    protected $table = 'financial_report';

    public $timestamps = false;

    protected $fillable = [
        'chapter_id',  // Add this if not already present
        'pre_balance',
        'amount_reserved_from_previous_year',
        'name',
        'roster_path',
        'file_irs_path',
        'bank_statement_included_path',
        'bank_statement_2_included_path',
        'award_1_files',
        'award_2_files',
        'award_3_files',
        'award_4_files',
        'award_5_files',
    ];

}
