<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;

#[Table(key: 'chapter_id')]
#[WithoutTimestamps]
#[Unguarded]
class FinancialReportLastYear extends Model
{
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function getTable(): string
    {
        $positionConditionsService = app(\App\Services\PositionConditionsService::class);
        $reportYearOptions = $positionConditionsService->getReportYearOptions();

        $reportYearStart = $reportYearOptions['reportYearStart'];

        return 'zzz_financial_report_12_'.$reportYearStart;
    }
}
