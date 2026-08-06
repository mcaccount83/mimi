<?php

namespace App\Console\Commands;

use App\Models\AdminIRS;
use App\Models\AdminYear;
use App\Models\FiscalYear;
use App\Services\PositionConditionsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetFiscalYear extends Command
{
    protected $signature = 'moms:reset-fiscal-year';
    protected $description = 'Creates the new fiscal year, admin year, and admin IRS rows.';

    public function handle(PositionConditionsService $positionConditionsService): int
    {
        $currentFiscalYearOptions = $positionConditionsService->getFiscalYearOptions();
        $currentFiscalYearId = $currentFiscalYearOptions['fiscalYearId'];

        $dateOptions = $positionConditionsService->getDateOptions();
        $fiscal_start = $dateOptions['currentYear'];
        $fiscal_end = $dateOptions['nextYear'];
        $fiscal_year = $fiscal_start.'-'.$fiscal_end;

        DB::beginTransaction();
        try {
            $fiscalYear = new FiscalYear;
            $fiscalYear->fiscal_year = $fiscal_year;
            $fiscalYear->fiscal_start = $fiscal_start;
            $fiscalYear->fiscal_end = $fiscal_end;
            $fiscalYear->save();

            $fiscalYearId = $fiscalYear->id;

            $adminYear = new AdminYear;
            $adminYear->fiscal_year_id = $fiscalYearId;
            $adminYear->save();

            $previousAdminIRS = AdminIRS::where('fiscal_year_id', $currentFiscalYearId)->first();

            $adminIRS = new AdminIRS;
            $adminIRS->fiscal_year_id = $fiscalYearId;
            $adminIRS->previous_file_date = $previousAdminIRS?->june_file_date;
            $adminIRS->save();

            DB::commit();
            $this->info('Fiscal year reset successfully.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $this->error('An error occurred while resetting the fiscal year.');

            return self::FAILURE;
        } finally {
            DB::disconnect();
        }
    }
}
