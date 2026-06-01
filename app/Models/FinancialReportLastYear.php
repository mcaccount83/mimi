<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;

#[Table(key: 'chapter_id')]
#[WithoutTimestamps]
#[Unguarded]

// Note: This model has no fixed table. It dynamically resolves to the previous year's
// archived financial report table (e.g. zzz_financial_report_12_2023) via getTable().
// Used in EOY processing to carry forward post_balance to the new year's pre_balance.
// See: Chapters::financialReportLastYear() relationship and updateChapterPostBalancesLIVE()
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
