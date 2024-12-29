<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\Belongsto;


class FinancialReport extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'financial_report';

    protected $primaryKey = 'chapter_id';

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

    public function awardType1(): BelongsTo
    {
        return $this->belongsTo(FinancialReportAwards::class, 'award_1_nomination_type', 'id');  // 'award_1_nomination_type' in financial_report BelongsTo 'id' in financial_report_awards
    }

    public function awardType2(): BelongsTo
    {
        return $this->belongsTo(FinancialReportAwards::class, 'award_2_nomination_type', 'id');  // 'award_2_nomination_type' in financial_report BelongsTo 'id' in financial_report_awards
    }

    public function awardType3(): BelongsTo
    {
        return $this->belongsTo(FinancialReportAwards::class, 'award_3_nomination_type', 'id');  // 'award_3_nomination_type' in financial_report BelongsTo 'id' in financial_report_awards
    }

    public function awardType4(): BelongsTo
    {
        return $this->belongsTo(FinancialReportAwards::class, 'award_4_nomination_type', 'id');  // 'award_4_nomination_type' in financial_report BelongsTo 'id' in financial_report_awards
    }

    public function awardType5(): BelongsTo
    {
        return $this->belongsTo(FinancialReportAwards::class, 'award_5_nomination_type', 'id');  // 'award_5_nomination_type' in financial_report BelongsTo 'id' in financial_report_awards
    }

}
