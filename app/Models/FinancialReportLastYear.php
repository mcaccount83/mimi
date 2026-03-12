<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


class FinancialReportLastYear extends Model
{
    protected $primaryKey = 'chapter_id';

    public $timestamps = false;

    protected $guarded = []; // ALL columns are mass-assignable

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function getTable(): string
    {
        $positionConditionsService = app(\App\Services\PositionConditionsService::class);
        $getEOYOptions = $positionConditionsService->getEOYOptions();

        $lastYearEOY = $getEOYOptions['lastYearEOY'];

        return 'zzz_financial_report_12_' . $lastYearEOY;
    }
}
