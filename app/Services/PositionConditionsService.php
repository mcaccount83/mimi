<?php

namespace App\Services;

use App\Enums\CoordinatorPosition;
use App\Models\Admin;
use App\Models\AdminEmail;
use Illuminate\Support\Facades\Request;

class PositionConditionsService
{
    public function __construct(
        private ReportingService $reportingService
    ) {}

    /**
     * Check if route is active
     */
    public function isActiveRoute(array $routes): string
    {
        foreach ($routes as $route) {
            if (Request::is($route)) {
                return 'active';
            }
        }

        return '';
    }

    /**
     * Get all position-based conditions for a user  // Loaded automatically for blades in ViewServiceProvider
     */
    public function getConditionsForUser($positionId, $secPositionId = [], $coorId = null)
    {
        // Handle null values gracefully
        $positionId = (int) ($positionId ?? 0);
        $secPositionId = array_map('intval', is_array($secPositionId) ? $secPositionId : [$secPositionId]);

        $hasChapterReports = $coorId ? $this->reportingService->hasAnyChapterReports($coorId) : false;
        $hasCoordinatorReports = $coorId ? $this->reportingService->hasAnyCoordinatorReports($coorId) : false;

        return [
            'coordinatorCondition' => $hasChapterReports,
            'supervisingCoordinatorCondition' => $hasCoordinatorReports,
            'founderCondition' => ($positionId == CoordinatorPosition::FOUNDER),
            'conferenceCoordinatorCondition' => ($positionId >= CoordinatorPosition::CC && $positionId <= CoordinatorPosition::FOUNDER),
            'assistConferenceCoordinatorCondition' => ($positionId >= CoordinatorPosition::ACC && $positionId <= CoordinatorPosition::FOUNDER),
            'regionalCoordinatorCondition' => ($positionId >= CoordinatorPosition::RC && $positionId <= CoordinatorPosition::FOUNDER),
            'assistRegionalCoordinatorCondition' => ($positionId >= CoordinatorPosition::ARC && $positionId <= CoordinatorPosition::FOUNDER),
            'areaCoordinatorCondition' => ($positionId >= CoordinatorPosition::AC && $positionId <= CoordinatorPosition::FOUNDER),
            'bigSisterCondition' => ($positionId >= CoordinatorPosition::BS && $positionId <= CoordinatorPosition::FOUNDER),
            'eoyTestCondition' => ($positionId >= CoordinatorPosition::ACC && $positionId <= CoordinatorPosition::FOUNDER) || $this->hasPosition(CoordinatorPosition::ART, $positionId, $secPositionId),
            'eoyReportCondition' => ($positionId >= CoordinatorPosition::BS && $positionId <= CoordinatorPosition::FOUNDER) || $this->hasPosition(CoordinatorPosition::ART, $positionId, $secPositionId) || $this->hasPosition(CoordinatorPosition::ARR, $positionId, $secPositionId),
            'eoyReportConditionDISABLED' => $this->hasPosition(CoordinatorPosition::IT, $positionId, $secPositionId),
            'inquiriesCondition' => $this->hasPosition(CoordinatorPosition::IC, $positionId, $secPositionId) || $this->hasPosition(CoordinatorPosition::IIC, $positionId, $secPositionId),
            'inquiriesInternationalCondition' => $this->hasPosition(CoordinatorPosition::IIC, $positionId, $secPositionId),
            'inquiriesConferenceCondition' => $this->hasPosition(CoordinatorPosition::IC, $positionId, $secPositionId),
            'webReviewCondition' => $this->hasPosition(CoordinatorPosition::WR, $positionId, $secPositionId),
            'einCondition' => $this->hasPosition(CoordinatorPosition::EIN, $positionId, $secPositionId),
            'm2mCondition' => $this->hasPosition(CoordinatorPosition::M2M, $positionId, $secPositionId) || $this->hasPosition(CoordinatorPosition::M2M2, $positionId, $secPositionId),
            'listAdminCondition' => $this->hasPosition(CoordinatorPosition::LIST, $positionId, $secPositionId),
            'ITCondition' => $this->hasPosition(CoordinatorPosition::IT, $positionId, $secPositionId),
        ];
    }

     /**
     * Check if user has a position (primary or secondary) // Helper function for getConditionsForUser
     */
    private function hasPosition(int $position, int $primaryPositionId, array $secondaryPositionIds): bool
    {
        return $primaryPositionId == $position || in_array($position, $secondaryPositionIds);
    }

    /**
     * Get Date options // Loaded automatically for blades in ViewServiceProvider & Called manually when needed
     */
    public function getDateOptions(): array
    {
        $currentDate = \Carbon\Carbon::now(); // Full Current Date
        $currentDateYmd = $currentDate->format('Y-m-d');
        $currentDateWords = $currentDate->format('F j, Y'); // e.g., July 9, 2024
        $nextMonthDateWords = $currentDate->copy()->addMonth()->format('F j, Y'); // e.g., July 9, 2024
        $twoMonthsDateWords = $currentDate->copy()->addMonths(2)->format('F j, Y'); // e.g., July 9, 2024
        $threeMonthsAgo = $currentDate->copy()->subMonths(3); // Date three months ago
        $oneYearAgo = $currentDate->copy()->subYear();  // Date one year ago
        $currentYear = $currentDate->year;
        $nextYear = $currentDate->copy()->addYear()->year;
        $lastYear = $currentDate->copy()->subYear()->year;
        $currentMonth = $currentDate->format('m'); // Current Month with leading zero
        $currentMonthWords = $currentDate->format('F');  // Current Month as Full Name
        $nextMonth = $currentDate->copy()->addMonth()->format('m');  // Next Month with leading zero
        $lastMonth = $currentDate->copy()->subMonth()->format('m');  // Last Month with leading zero
        $lastMonthWords = $currentDate->copy()->subMonth()->format('F');  // Last Month as Full Name

        return [
            'currentDate' => $currentDate,
            'currentDateYmd' => $currentDateYmd,
            'currentDateWords' => $currentDateWords,
            'nextMonthDateWords' => $nextMonthDateWords,
            'twoMonthsDateWords' => $twoMonthsDateWords,
            'threeMonthsAgo' => $threeMonthsAgo,
            'oneYearAgo' => $oneYearAgo,
            'currentYear' => $currentYear,
            'nextYear' => $nextYear,
            'lastYear' => $lastYear,
            'currentMonth' => $currentMonth,
            'currentMonthWords' => $currentMonthWords,
            'nextMonth' => $nextMonth,
            'lastMonth' => $lastMonth,
            'lastMonthWords' => $lastMonthWords,
        ];
    }

     /**
     * Get EOY Date options based on fiscal year // Loaded automatically for blades in ViewServiceProvider & Called manually when needed
     */
    public function getEOYOptions(): array
    {
        $admin = Admin::orderByDesc('id')
            ->limit(1)
            ->first();
        $fiscalYear = $admin->fiscal_year;  // "2024-2025"
        $years = explode('-', $fiscalYear);  // Extract years from fiscal_year string
        $lastYear = $years[0];  // "2024"
        $thisYear = $years[1];  // "2025"
        $nextYear = $thisYear + 1;  // 2026

        $display_testing = ($admin->display_testing == 1);
        $display_live = ($admin->display_live == 1);

        $yearColumnName = $thisYear . '_financial_pdf_path'; // name for Database Column for Financial Report
        $boardReportName = $thisYear .'-'. $nextYear .' Board Report';  // Board Report Name
        $financialReportName = $lastYear .'-'. $thisYear .' Financial Report';  // Financial Report Name
        $irsFilingName = $lastYear .' 990N IRS Filing';  // IRS Filing Name

        $currentMonth = $this->getDateOptions()['currentMonth'];  // Current Month with leading zero

        return [
            'fiscalYear' => $fiscalYear,
            'thisYear' => $thisYear,
            'nextYear' => $nextYear,
            'lastYear' => $lastYear,
            'displayTESTING' => ($display_testing && !$display_live),
            'displayLIVE' => ($display_live && $currentMonth >= 5 && $currentMonth <= 12),
            'displayBoardRptLIVE' => ($display_live && $currentMonth >= 5 && $currentMonth <= 9),
            'displayFinancialRptLIVE' => ($display_live && $currentMonth >= 6 && $currentMonth <= 12),
            'displayEINInstructionsLIVE' => ($display_live && $currentMonth >= 7 && $currentMonth <= 12),
            'yearColumnName' => $yearColumnName,
            'boardReportName' => $boardReportName,
            'financialReportName' => $financialReportName,
            'irsFilingName' => $irsFilingName,
        ];
    }

    /**
     * Get Admin Email Addresses  // Called manually when needed
     */
    public function getAdminEmail(): array
    {
        $adminEmail = AdminEmail::first(); // Returns a single model instead of a collection

        $list_admin = $adminEmail->list_admin;
        $payments_admin = $adminEmail->payments_admin;
        $ein_admin = $adminEmail->ein_admin;
        $gsuite_admin = $adminEmail->gsuite_admin;
        $mimi_admin = $adminEmail->mimi_admin;

        return [
            'list_admin' => $list_admin,
            'payments_admin' => $payments_admin,
            'ein_admin' => $ein_admin,
            'gsuite_admin' => $gsuite_admin,
            'mimi_admin' => $mimi_admin,
        ];
    }
}
