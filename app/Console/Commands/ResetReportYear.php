<?php

namespace App\Console\Commands;

use App\Models\AdminReport;
use App\Models\FiscalYear;
use App\Services\PositionConditionsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetReportYear extends Command
{
    protected $signature = 'moms:reset-report-year';
    protected $description = 'Creates the new report year.';

    public function handle(PositionConditionsService $positionConditionsService): int
    {
        $fiscalYearOptions = $positionConditionsService->getFiscalYearOptions();
        $fiscalYearId = $fiscalYearOptions['fiscalYearId'];
        $report_start = $fiscalYearOptions['reportYearStart'];
        $report_end = $fiscalYearOptions['reportYearEnd'];
        $report_year = $fiscalYearOptions['reportYearEnd'];

        DB::beginTransaction();
        try {
            $fiscalYear = FiscalYear::findOrFail($fiscalYearId);
            $fiscalYear->fiscal_year = $report_year;
            $fiscalYear->fiscal_start = $report_start;
            $fiscalYear->fiscal_end = $report_end;
            $fiscalYear->save();

            $adminReport = new AdminReport;
            $adminReport->report_year_id = $fiscalYearId;
            $adminReport->reset_report_year = '1';
            $adminReport->save();

            DB::commit();
            $this->info('Report year reset successfully.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->error('An error occurred while resetting the report year.');

            return self::FAILURE;
        } finally {
            DB::disconnect();
        }
    }
}
