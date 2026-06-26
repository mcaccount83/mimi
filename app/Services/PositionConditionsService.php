<?php

namespace App\Services;

use App\Enums\CoordinatorPosition;
use App\Enums\UserTypeEnum;
use App\Models\AdminEmail;
use App\Models\AdminIRS;
use App\Models\AdminReport;
use App\Models\AdminYear;
use App\Models\FiscalYear;
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
    public function getConditionsForUser(?int $positionId, $secPositionId = [], $coorId = null)
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
            'canEditFiles' => $this->hasPosition(CoordinatorPosition::IT, $positionId, $secPositionId),
        ];
    }

    /**
     * Check if user has a position (primary or secondary) // Helper function for getConditionsForUser
     */
    private function hasPosition(string $position, int $primaryPositionId, array $secondaryPositionIds): bool
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
    public function getFiscalYearOptions(): array
    {
        $fiscalYear = FiscalYear::orderByDesc('created_at') // newest created row first
            ->first();
        $adminYear = AdminYear::with('fiscalYear')
            ->orderByDesc('created_at') // newest created row first
            ->first();
        $irsYear = AdminIRS::with('fiscalYear')
            ->orderByDesc('created_at') // newest created row first
            ->first();

        // Fiscal year values directly from table
        $fiscalYearId = $fiscalYear->id;
        $fiscalYearRange = $fiscalYear->fiscal_year; // "2025-2026"
        $fiscalYearStart = $fiscalYear->fiscal_start; // "2025"
        $fiscalYearEnd = $fiscalYear->fiscal_end; // "2026"
        $fiscalYearStartDate = $fiscalYearStart.'-07-01'; // "2025-07-01"
        $fiscalMonthStart = '7';
        $fiscalMonthEnd = '6';
        $fiscalYearEndDate = $fiscalYearEnd.'-06-30';

        return [
            'fiscalYear' => $fiscalYear,
            'adminYear' => $adminYear,
            'irsYear' => $irsYear,
            'fiscalYearId' => $fiscalYearId,
            'fiscalYearRange' => $fiscalYearRange,
            'fiscalYearStart' => $fiscalYearStart,
            'fiscalYearEnd' => $fiscalYearEnd,
            'fiscalYearStartDate' => $fiscalYearStartDate,
            'fiscalMonthStart' => $fiscalMonthStart,
            'fiscalMonthEnd' => $fiscalMonthEnd,
            'fiscalYearEndDate' => $fiscalYearEndDate,
        ];
    }

    public function getReportYearOptions(): array
    {
        $reportYear = AdminReport::with('fiscalYear')
            ->orderByDesc('created_at') // newest created row first
            ->first();

        $reportYearId = $reportYear->fiscalYear->id;
        $reportYearRange = $reportYear->fiscalYear->report_year; // "2024-2025"
        $reportYearStart = $reportYear->fiscalYear->report_start; // "2024"
        $reportYearEnd = $reportYear->fiscalYear->report_end; // "2025"

        $boardReportStart = $reportYearEnd; // "2025"
        $boardReportEnd = (int) $reportYearEnd + 1; // 2026
        $boardReportRange = $boardReportStart.'-'.$boardReportEnd;

        // Optional display names
        $yearColumnName = $reportYearEnd.'_financial_pdf_path';
        $boardReportName = $boardReportRange.' Board Report';
        $financialReportName = $reportYearRange.' Financial Report';
        $financialPDFName = $reportYearEnd.' Financial PDF';
        $irsFilingName = $reportYearStart.' 990N IRS Filing';

        // Display Options
        $display_testing = ($reportYear->display_testing == 1);
        $display_live = ($reportYear->display_live == 1);
        $currentMonth = $this->getDateOptions()['currentMonth'];

        return [
            'reportYear' => $reportYear,
            'boardReportRange' => $boardReportRange,
            'boardReportStart' => $boardReportStart,
            'boardReportEnd' => $boardReportEnd,
            'reportYearId' => $reportYearId,
            'reportYearRange' => $reportYearRange,
            'reportYearStart' => $reportYearStart,
            'reportYearEnd' => $reportYearEnd,
            'yearColumnName' => $yearColumnName,
            'boardReportName' => $boardReportName,
            'financialReportName' => $financialReportName,
            'financialPDFName' => $financialPDFName,
            'irsFilingName' => $irsFilingName,
            'displayEOYTESTING' => ($display_testing && ! $display_live),
            'displayEOYLIVE' => ($display_live && $currentMonth >= 5 && $currentMonth <= 12),
            'displayBoardRptLIVE' => ($display_live && $currentMonth >= 5 && $currentMonth <= 9),
            'displayFinancialRptLIVE' => ($display_live && $currentMonth >= 6 && $currentMonth <= 12),
            'displayEINInstructionsLIVE' => ($display_live && $currentMonth >= 7 && $currentMonth <= 12),
            'disableBoardEdits' => ($currentMonth >= 4 && $currentMonth <= 9),
            'display_testing' => $display_testing,
            'display_live' => $display_live,
            'activateBoard' => ($display_live && $currentMonth >= 7 && $currentMonth <= 12),
        ];
    }

    /**
     * Get Admin Email Addresses  // Called manually when needed
     */
    public function getAdminEmail(): array
    {
        // Get all admin emails and key by name
        $emails = AdminEmail::pluck('email', 'name');

        return [
            'list_admin' => $emails['list_admin'] ?? '',
            'payments_admin' => $emails['payments_admin'] ?? '',
            'ein_admin' => $emails['ein_admin'] ?? '',
            'gsuite_admin' => $emails['gsuite_admin'] ?? '',
            'mimi_admin' => $emails['mimi_admin'] ?? '',
            'grant_admin' => $emails['grant_admin'] ?? '',
        ];
    }

    public function getViewAs(int $userTypeId, object $PresDetails): array
    {
        $viewingAs = session('viewing_as', 'board');
        $presTypeId = $PresDetails?->user?->type_id ?? null;

        if ($userTypeId == UserTypeEnum::COORD && $viewingAs == 'coord') {
            return [
                'bdPositionId' => '1',
                'bdDetails' => $PresDetails,
                'bdTypeId' => $presTypeId,
            ];
        }

        return [
            'bdPositionId' => $PresDetails?->position_id ?? null,
            'bdDetails' => $PresDetails,
            'bdTypeId' => $presTypeId,
        ];
    }
}
