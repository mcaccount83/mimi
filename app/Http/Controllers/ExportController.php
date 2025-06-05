<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseCoordinatorController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseCoordinatorController $baseCoordinatorController)
    {

        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * Format Chapter Information in Gropus
     */
     private function formatChapterEINInfo($chapterData)
    {
        $chDetails = $chapterData['chDetails'];
        $chDocuments = $chapterData['chDocuments'] ?? null;
        $startMonthName = $chapterData['startMonthName'] ?? '';

        return [
            'EIN' => $chDetails->ein,
            'EIN Letter' => ($chDocuments && $chDocuments->ein_letter == 1) ? 'YES' : 'NO',
        ];
    }

    private function formatChapterLocationInfo($chapterData)
    {
        $stateShortName = $chapterData['stateShortName'];
        $regionLongName = $chapterData['regionLongName'];
        $chConfId = $chapterData['chConfId'];

        return [
            'Conference' => $chConfId,
            'Region' => $regionLongName,
            'State' => $stateShortName,
        ];
    }

    private function formatChapterNameInfo($chapterData)
    {
        $chDetails = $chapterData['chDetails'];

        return [
            'Name' => $chDetails->name,
        ];
    }

    private function formatChapterStatusInfo($chapterData)
    {
        $chDetails = $chapterData['chDetails'];
        $chapterStatus = $chapterData['chapterStatus'] ?? '';

        return [
            'Bounraries' => $chDetails->territory,
            'Status' => $chapterStatus,
            'Notes' => $chDetails->notes,
        ];
    }

    private function formatChapterPCInfo($chapterData)
    {
        $pcName = $chapterData['pcName'] ?? '';

        return [
            'Primary Coordinator' => $pcName,
        ];
    }

    private function formatChapterContactInfo($chapterData)
    {
        $chDetails = $chapterData['chDetails'];

        return [
            'Inquiries Email' => $chDetails->inquiries_contact,
            'Inquiries Notes' => $chDetails->inquiries_note,
            'Chapter Email' => $chDetails->email,
            'Chapter P.O. Box' => $chDetails->po_box,
        ];
    }

    private function formatWebsiteInfo($chapterData)
    {
        $chDetails = $chapterData['chDetails'];
        $websiteLink = $chapterData['websiteLink'] ?? '';

        return [
            'Website' => $websiteLink,
            'Linked Status' => $chDetails->website_status,
            'EGroup' => $chDetails->egroup,
            'Social Media' => trim(($chDetails->social1 ?? '').' '.($chDetails->social2 ?? '').' '.($chDetails->social3 ?? '')),
        ];
    }

    private function formatPaymentInfo($chapterData)
    {
        $chDetails = $chapterData['chDetails'];
        $chPayments = $chapterData['chPayments'] ?? null;

        return [
            'Next Renewal' => $chDetails->next_renewal_year,
            'Dues Last Paid' => $chPayments->rereg_date,
            'Members paid for' => $chPayments->rereg_members,
            'Re-Reg Notes' => $chPayments->rereg_notes,
        ];
    }

    private function formatChapterStartInfo($chapterData)
    {
        $chDetails = $chapterData['chDetails'];
        $startMonthName = $chapterData['startMonthName'] ?? '';

        return [
            'Start Month' => $startMonthName,
            'Start Year' => $chDetails->start_year,
        ];
    }

    private function formatChapterHistoryInfo($chapterData)
    {
        $chDetails = $chapterData['chDetails'];

        return [
            'Founder' => $chDetails->founders_name,
            'Sistered By' => $chDetails->sistered_by,
            'FormerName' => $chDetails->former_name,
        ];
    }


    private function formatEOYInfo($chapterData)
    {
      $chDocuments = $chapterData['chDocuments'];

        return [
            'Board Report Received' => ($chDocuments->new_board_submitted == 1) ? 'YES' : 'NO',
            'Board Report Activated' => ($chDocuments->new_board_active == 1) ? 'YES' : 'NO',
            'Financial Report Received' => ($chDocuments->financial_report_received == 1) ? 'YES' : 'NO',
            'Financial Review Complete' => ($chDocuments->financial_review_complete == 1) ? 'YES' : 'NO',
            'Report Notes' => $chDocuments->report_notes,
            'Extension Given' => ($chDocuments->report_extension == 1) ? 'YES' : 'NO',
            'Extension Notes' => $chDocuments->extension_notes,
        ];
    }

    private function formatPresidentInfo($chapterData)
    {
        $PresDetails = $chapterData['PresDetails'];

        return [
            'Pres Name' => ($PresDetails && $PresDetails->first_name) ? $PresDetails->first_name.' '.$PresDetails->last_name : '',
            'Pres Address' => $PresDetails->street_address ?? '',
            'Pres City' => $PresDetails->city ?? '',
            'Pres State' => $PresDetails->state->state_short_name ?? '',
            'Pres Zip' => $PresDetails->zip ?? '',
            'Pres Phone' => $PresDetails->phone ?? '',
            'Pres Email' => $PresDetails->email ?? '',
        ];
    }

    private function formatDisbandedInfo($chapterData)
    {
        $chDetails = $chapterData['chDetails'];

        return [
            'Disband Date' => $chDetails->zap_date,
            'Disband Reason' => $chDetails->disband_reason,
        ];
    }

    private function formatBoardMemberInfo($chapterData)
    {
        $PresDetails = $chapterData['PresDetails'];
        $AVPDetails = $chapterData['AVPDetails'];
        $MVPDetails = $chapterData['MVPDetails'];
        $TRSDetails = $chapterData['TRSDetails'];
        $SECDetails = $chapterData['SECDetails'];

        return [
            'President Name' => ($PresDetails && $PresDetails->first_name) ? $PresDetails->first_name.' '.$PresDetails->last_name : '',
            'President Email' => $PresDetails->email ?? '',
            'President Phone' => $PresDetails->phone ?? '',
            'AVP Name' => ($AVPDetails && $AVPDetails->first_name) ? $AVPDetails->first_name.' '.$AVPDetails->last_name : '',
            'AVP Email' => $AVPDetails->email ?? '',
            'AVP Phone' => $AVPDetails->phone ?? '',
            'MVP Name' => ($MVPDetails && $MVPDetails->first_name) ? $MVPDetails->first_name.' '.$MVPDetails->last_name : '',
            'MVP Email' => $MVPDetails->email ?? '',
            'MVP Phone' => $MVPDetails->phone ?? '',
            'Treasurer Name' => ($TRSDetails && $TRSDetails->first_name) ? $TRSDetails->first_name.' '.$TRSDetails->last_name : '',
            'Treasurer Email' => $TRSDetails->email ?? '',
            'Treasurer Phone' => $TRSDetails->phone ?? '',
            'Secretary Name' => ($SECDetails && $SECDetails->first_name) ? $SECDetails->first_name.' '.$SECDetails->last_name : '',
            'Secretary Email' => $SECDetails->email ?? '',
            'Secretary Phone' => $SECDetails->phone ?? '',
        ];
    }

    private function formatDisbandedBoardMemberInfo($chapterData)
    {
        $PresDetails = $chapterData['PresDisbandedDetails'];
        $AVPDetails = $chapterData['AVPDisbandedDetails'];
        $MVPDetails = $chapterData['MVPDisbandedDetails'];
        $TRSDetails = $chapterData['TRSDisbandedDetails'];
        $SECDetails = $chapterData['SECDisbandedDetails'];

        return [
            'President Name' => ($PresDetails && $PresDetails->first_name) ? $PresDetails->first_name.' '.$PresDetails->last_name : '',
            'President Email' => $PresDetails->email ?? '',
            'President Phone' => $PresDetails->phone ?? '',
            'AVP Name' => ($AVPDetails && $AVPDetails->first_name) ? $AVPDetails->first_name.' '.$AVPDetails->last_name : '',
            'AVP Email' => $AVPDetails->email ?? '',
            'AVP Phone' => $AVPDetails->phone ?? '',
            'MVP Name' => ($MVPDetails && $MVPDetails->first_name) ? $MVPDetails->first_name.' '.$MVPDetails->last_name : '',
            'MVP Email' => $MVPDetails->email ?? '',
            'MVP Phone' => $MVPDetails->phone ?? '',
            'Treasurer Name' => ($TRSDetails && $TRSDetails->first_name) ? $TRSDetails->first_name.' '.$TRSDetails->last_name : '',
            'Treasurer Email' => $TRSDetails->email ?? '',
            'Treasurer Phone' => $TRSDetails->phone ?? '',
            'Secretary Name' => ($SECDetails && $SECDetails->first_name) ? $SECDetails->first_name.' '.$SECDetails->last_name : '',
            'Secretary Email' => $SECDetails->email ?? '',
            'Secretary Phone' => $SECDetails->phone ?? '',
        ];
    }

    /**
     * Format row data for full chapter & international chapter export
     */
    private function formatFullChapterRow($chapterData)
    {
        return array_merge(
            $this->formatChapterEINInfo($chapterData),
            $this->formatChapterLocationInfo($chapterData),
            $this->formatChapterNameInfo($chapterData),
            $this->formatChapterStatusInfo($chapterData),
            $this->formatChapterPCInfo($chapterData),
            $this->formatChapterContactInfo($chapterData),
            $this->formatBoardMemberInfo($chapterData),
            $this->formatWebsiteInfo($chapterData),
            $this->formatPaymentInfo($chapterData),
            $this->formatChapterStartInfo($chapterData),
            $this->formatChapterHistoryInfo($chapterData)
        );
    }

    /**
     * Format row data for zapped & internaitonal zapped chapter export
     */
    private function formatZappedChapterRow($chapterData)
    {
        return array_merge(
            $this->formatChapterEINInfo($chapterData),
            $this->formatChapterLocationInfo($chapterData),
            $this->formatChapterNameInfo($chapterData),
            $this->formatChapterStatusInfo($chapterData),
            $this->formatChapterPCInfo($chapterData),
            $this->formatChapterContactInfo($chapterData),
            $this->formatDisbandedBoardMemberInfo($chapterData),
            $this->formatWebsiteInfo($chapterData),
            $this->formatPaymentInfo($chapterData),
            $this->formatChapterStartInfo($chapterData),
            $this->formatChapterHistoryInfo($chapterData),
            $this->formatDisbandedInfo($chapterData)
        );
    }

     /**
     * Format row data for re-registration & international re-registration export
     */
    private function formatReRegRow($chapterData)
    {
        return array_merge(
            $this->formatChapterLocationInfo($chapterData),
            $this->formatChapterNameInfo($chapterData),
            $this->formatChapterStartInfo($chapterData),
            $this->formatPaymentInfo($chapterData)
        );
    }

    /**
     * Format row data for EIN status & international EIN status export
     */
    private function formatEINStatusRow($chapterData)
    {
        return array_merge(
            $this->formatChapterLocationInfo($chapterData),
            $this->formatChapterNameInfo($chapterData),
            $this->formatChapterEINInfo($chapterData),
            $this->formatChapterStartInfo($chapterData),
            $this->formatPresidentInfo($chapterData)
        );
    }

   /**
     * Export Chapter List
     */
    public function indexChapter(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'chapter_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $chapterIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($chapterIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($chapterIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'EIN', 'EIN Letter', 'Conference', 'Region', 'State', 'Name', 'Bounraries', 'Status',
                'Notes', 'Primary Coordinator', 'Inquiries Email', 'Inquiries Notes', 'Chapter Email',
                'Chapter P.O. Box', 'President Name', 'President Email', 'President Phone',
                'AVP Name', 'AVP Email', 'AVP Phone', 'MVP Name', 'MVP Email', 'MVP Phone',
                'Treasurer Name', 'Treasurer Email', 'Treasurer Phone', 'Secretary Name',
                'Secretary Email', 'Secretary Phone', 'Website', 'Linked Status', 'EGroup',
                'Social Media',  'Next Renewal', 'Dues Last Paid','Members paid for','Re-Reg Notes',
                'Start Month', 'Start Year', 'Founder', 'Sistered By', 'FormerName',
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($chapterIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $chId) {
                    // Use your existing base controller method
                    $chapterData = $this->baseChapterController->getChapterDetails($chId);
                    $boardData = $this->baseChapterController->getActiveBoardDetails($chId);
                    // Merge the data arrays
                    $combinedData = array_merge($chapterData, $boardData);

                    $rowData = $this->formatFullChapterRow($combinedData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($chId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Zapped Chapter List
     */
    public function indexZappedChapter(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'chapter_zap_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getZappedBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $chapterIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($chapterIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($chapterIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'EIN', 'EIN Letter', 'Conference', 'Region', 'State', 'Name', 'Bounraries', 'Status',
                'Notes', 'Primary Coordinator', 'Inquiries Email', 'Inquiries Notes', 'Chapter Email',
                'Chapter P.O. Box', 'President Name', 'President Email', 'President Phone',
                'AVP Name', 'AVP Email', 'AVP Phone', 'MVP Name', 'MVP Email', 'MVP Phone',
                'Treasurer Name', 'Treasurer Email', 'Treasurer Phone', 'Secretary Name',
                'Secretary Email', 'Secretary Phone', 'Website', 'Linked Status', 'EGroup',
                'Social Media',  'Next Renewal', 'Dues Last Paid','Members paid for','Re-Reg Notes',
                'Start Month', 'Start Year', 'Founder', 'Sistered By', 'FormerName',
                'Disband Date', 'Disband Reason'
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($chapterIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $chId) {
                    // Use your existing base controller method
                    $chapterData = $this->baseChapterController->getChapterDetails($chId);
                    $boardData = $this->baseChapterController->getDisbandedBoardDetails($chId);
                    // Merge the data arrays
                    $combinedData = array_merge($chapterData, $boardData);

                    $rowData = $this->formatZappedChapterRow($combinedData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($chId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export International Chapter List
     */
    public function indexInternationalChapter(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'int_chapter_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $chapterIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($chapterIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($chapterIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'EIN', 'EIN Letter', 'Conference', 'Region', 'State', 'Name', 'Bounraries', 'Status',
                'Notes', 'Primary Coordinator', 'Inquiries Email', 'Inquiries Notes', 'Chapter Email',
                'Chapter P.O. Box', 'President Name', 'President Email', 'President Phone',
                'AVP Name', 'AVP Email', 'AVP Phone', 'MVP Name', 'MVP Email', 'MVP Phone',
                'Treasurer Name', 'Treasurer Email', 'Treasurer Phone', 'Secretary Name',
                'Secretary Email', 'Secretary Phone', 'Website', 'Linked Status', 'EGroup',
                'Social Media',  'Next Renewal', 'Dues Last Paid','Members paid for','Re-Reg Notes',
                'Start Month', 'Start Year', 'Founder', 'Sistered By', 'FormerName',
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($chapterIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $chId) {
                    // Use your existing base controller method
                    $chapterData = $this->baseChapterController->getChapterDetails($chId);
                    $boardData = $this->baseChapterController->getActiveBoardDetails($chId);
                    // Merge the data arrays
                    $combinedData = array_merge($chapterData, $boardData);

                    $rowData = $this->formatFullChapterRow($combinedData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($chId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export International Zapped Chapter List
     */
    public function indexInternationalZapChapter(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512');
        set_time_limit(600); // 10 minutes

        $fileName = 'int_chapter_zap_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getZappedInternationalBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $chapterIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($chapterIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($chapterIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'EIN', 'EIN Letter', 'Conference', 'Region', 'State', 'Name', 'Bounraries', 'Status',
                'Notes', 'Primary Coordinator', 'Inquiries Email', 'Inquiries Notes', 'Chapter Email',
                'Chapter P.O. Box', 'President Name', 'President Email', 'President Phone',
                'AVP Name', 'AVP Email', 'AVP Phone', 'MVP Name', 'MVP Email', 'MVP Phone',
                'Treasurer Name', 'Treasurer Email', 'Treasurer Phone', 'Secretary Name',
                'Secretary Email', 'Secretary Phone', 'Website', 'Linked Status', 'EGroup',
                'Social Media',  'Next Renewal', 'Dues Last Paid','Members paid for','Re-Reg Notes',
                'Start Month', 'Start Year', 'Founder', 'Sistered By', 'FormerName',
                'Disband Date', 'Disband Reason'
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($chapterIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $chId) {
                    // Use your existing base controller method
                    $chapterData = $this->baseChapterController->getChapterDetails($chId);
                    $boardData = $this->baseChapterController->getDisbandedBoardDetails($chId);
                    // Merge the data arrays
                    $combinedData = array_merge($chapterData, $boardData);

                    $rowData = $this->formatZappedChapterRow($combinedData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($chId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }






   /**
 * Export Overdue Re-Registration List - Optimized
 */
public function indexReReg(Request $request)
{
    // Increase memory limit and execution time
    ini_set('memory_limit', '512M');
    set_time_limit(300);

    $fileName = 'rereg_export_'.date('Y-m-d').'.csv';
    $headers = [
        'Content-type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=$fileName",
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0',
    ];

    $user = $this->userController->loadUserInformation($request);
    $coorId = $user['user_coorId'];
    $confId = $user['user_confId'];
    $regId = $user['user_regId'];
    $positionId = $user['user_positionId'];
    $secPositionId = $user['user_secPositionId'];

    $now = Carbon::now();
    $currentMonth = $now->month;
    $lastMonth = $now->copy()->subMonth()->format('m');
    $currentYear = $now->year;

    $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

    $reChapterIds = $baseQuery['query']
        ->where(function ($query) use ($currentYear, $lastMonth) {
            $query->where('next_renewal_year', '<', $currentYear)
                ->orWhere(function ($query) use ($currentYear, $lastMonth) {
                    $query->where('next_renewal_year', '=', $currentYear)
                        ->where('start_month_id', '<=', $lastMonth);
                });
        })
        ->orderByDesc('start_month_id')
        ->orderByDesc('next_renewal_year')
        ->pluck('id')
        ->toArray();

    if (empty($reChapterIds)) {
        return redirect()->to('/home');
    }

    $callback = function () use ($reChapterIds) {
        $file = fopen('php://output', 'w');

        // Write headers based on what formatReRegRow returns
        $headers = [
            'Conference', 'Region', 'State', 'Name',
            'Start Month', 'Start Year', 'Next Renewal Year', 'Dues Last Paid',
            'Members paid for', 'Re-Reg Notes',
        ];
        fputcsv($file, $headers);

        // Process in chunks
        $chunkSize = 50;
        $chunks = array_chunk($reChapterIds, $chunkSize);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $chId) {
                $chapterData = $this->baseChapterController->getChapterDetails($chId);
                $rowData = $this->formatReRegRow($chapterData);
                fputcsv($file, $rowData);
            }

            // Memory cleanup
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}

 /**
 * Export International Overdue Re-Registration List - Optimized
 */
public function indexIntReReg(Request $request)
{
    // Increase memory limit and execution time
    ini_set('memory_limit', '512M');
    set_time_limit(300);

    $fileName = 'rereg_export_'.date('Y-m-d').'.csv';
    $headers = [
        'Content-type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=$fileName",
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0',
    ];

    $user = $this->userController->loadUserInformation($request);
    $coorId = $user['user_coorId'];
    $confId = $user['user_confId'];
    $regId = $user['user_regId'];
    $positionId = $user['user_positionId'];
    $secPositionId = $user['user_secPositionId'];

    $now = Carbon::now();
    $currentMonth = $now->month;
    $lastMonth = $now->copy()->subMonth()->format('m');
    $currentYear = $now->year;

    $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

    $reChapterIds = $baseQuery['query']
        ->where(function ($query) use ($currentYear, $lastMonth) {
            $query->where('next_renewal_year', '<', $currentYear)
                ->orWhere(function ($query) use ($currentYear, $lastMonth) {
                    $query->where('next_renewal_year', '=', $currentYear)
                        ->where('start_month_id', '<=', $lastMonth);
                });
        })
        ->orderByDesc('start_month_id')
        ->orderByDesc('next_renewal_year')
        ->pluck('id')
        ->toArray();

    if (empty($reChapterIds)) {
        return redirect()->to('/home');
    }

    $callback = function () use ($reChapterIds) {
        $file = fopen('php://output', 'w');

        // Write headers based on what formatReRegRow returns
        $headers = [
            'Conference', 'Region', 'State', 'Name',
            'Start Month', 'Start Year', 'Next Renewal Year', 'Dues Last Paid',
            'Members paid for', 'Re-Reg Notes',
        ];
        fputcsv($file, $headers);

        // Process in chunks
        $chunkSize = 50;
        $chunks = array_chunk($reChapterIds, $chunkSize);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $chId) {
                $chapterData = $this->baseChapterController->getChapterDetails($chId);
                $rowData = $this->formatReRegRow($chapterData);
                fputcsv($file, $rowData);
            }

            // Memory cleanup
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}

/**
 * Export EIN Status List - Optimized
 */
public function indexEINStatus(Request $request)
{
    // Increase memory limit and execution time
    ini_set('memory_limit', '512M');
    set_time_limit(300);

    $fileName = 'ein_status_'.date('Y-m-d').'.csv';
    $headers = [
        'Content-type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=$fileName",
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0',
    ];

    $user = $this->userController->loadUserInformation($request);
    $coorId = $user['user_coorId'];
    $confId = $user['user_confId'];
    $regId = $user['user_regId'];
    $positionId = $user['user_positionId'];
    $secPositionId = $user['user_secPositionId'];

    $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
    $chapterIds = $baseQuery['query']->pluck('id')->toArray();

    if (empty($chapterIds)) {
        return redirect()->to('/home');
    }

    $callback = function () use ($chapterIds) {
        $file = fopen('php://output', 'w');

        // Write headers based on what formatEINStatusRow returns
        $headers = [
            'Conference', 'Region', 'State', 'Name', 'EIN', 'EIN Letter', 'Start Month', 'Start Year',
            'Pres Name', 'Pres Address', 'Pres City', 'Pres State', 'Pres Zip', 'Pres Phone', 'Pres Email'
        ];
        fputcsv($file, $headers);

        // Process in chunks
        $chunkSize = 50;
        $chunks = array_chunk($chapterIds, $chunkSize);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $chId) {
                $chapterData = $this->baseChapterController->getChapterDetails($chId);
                $boardData = $this->baseChapterController->getActiveBoardDetails($chId);
                // Merge the data arrays
                $combinedData = array_merge($chapterData, $boardData);

                $rowData = $this->formatEINStatusRow($combinedData);
                fputcsv($file, $rowData);
            }

            // Memory cleanup
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}

/**
 * Export International EIN Status List - Optimized
 */
public function indexIntEINStatus(Request $request)
{
    // Increase memory limit and execution time
    ini_set('memory_limit', '512M');
    set_time_limit(300);

    $fileName = 'ein_status_'.date('Y-m-d').'.csv';
    $headers = [
        'Content-type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=$fileName",
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0',
    ];

    $user = $this->userController->loadUserInformation($request);
    $coorId = $user['user_coorId'];
    $confId = $user['user_confId'];
    $regId = $user['user_regId'];
    $positionId = $user['user_positionId'];
    $secPositionId = $user['user_secPositionId'];

    $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
    $chapterIds = $baseQuery['query']->pluck('id')->toArray();

    if (empty($chapterIds)) {
        return redirect()->to('/home');
    }

    $callback = function () use ($chapterIds) {
        $file = fopen('php://output', 'w');

        // Write headers based on what formatEINStatusRow returns
        $headers = [
            'Conference', 'Region', 'State', 'Name', 'EIN', 'EIN Letter', 'Start Month', 'Start Year',
            'Pres Name', 'Pres Address', 'Pres City', 'Pres State', 'Pres Zip', 'Pres Phone', 'Pres Email'
        ];
        fputcsv($file, $headers);

        // Process in chunks
        $chunkSize = 50;
        $chunks = array_chunk($chapterIds, $chunkSize);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $chId) {
                $chapterData = $this->baseChapterController->getChapterDetails($chId);
                $boardData = $this->baseChapterController->getActiveBoardDetails($chId);
                // Merge the data arrays
                $combinedData = array_merge($chapterData, $boardData);

                $rowData = $this->formatEINStatusRow($combinedData);
                fputcsv($file, $rowData);
            }

            // Memory cleanup
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}

    /**
     * Export EOY Reports Status List
     */
    public function indexEOYStatus(Request $request)
    {
        $fileName = 'eoy_status_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        if (count($chapterList) > 0) {
            $exportChapterList = [];

            foreach ($chapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $pcName = $baseQuery['pcName'];
                $chDocuments = $baseQuery['chDocuments'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'Board Report Received' => ($chDocuments->new_board_submitted == 1) ? 'YES' : 'NO',
                    'Board Report Activated' => ($chDocuments->new_board_active == 1) ? 'YES' : 'NO',
                    'Financial Report Received' => ($chDocuments->financial_report_received == 1) ? 'YES' : 'NO',
                    'Financial Review Complete' => ($chDocuments->financial_review_complete == 1) ? 'YES' : 'NO',
                    'Report Notes' => $chDocuments->report_notes,
                    'Extension Given' => ($chDocuments->report_extension == 1) ? 'YES' : 'NO',
                    'Extension Notes' => $chDocuments->extension_notes,
                ];

                $exportChapterList[] = $rowData;
            }

            $callback = function () use ($exportChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportChapterList)) {
                    fputcsv($file, array_keys($exportChapterList[0]));
                }

                foreach ($exportChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }


    /**
     * Export International EOY Reports Status List
     */
    public function indexIntEOYStatus(Request $request)
    {
        $fileName = 'int_eoy_status_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $chapterList = $baseQuery['query']->get();

        if (count($chapterList) > 0) {
            $exportChapterList = [];

            foreach ($chapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $pcName = $baseQuery['pcName'];
                $chDocuments = $baseQuery['chDocuments'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'Board Report Received' => ($chDocuments->new_board_submitted == 1) ? 'YES' : 'NO',
                    'Board Report Activated' => ($chDocuments->new_board_active == 1) ? 'YES' : 'NO',
                    'Financial Report Received' => ($chDocuments->financial_report_received == 1) ? 'YES' : 'NO',
                    'Financial Review Complete' => ($chDocuments->financial_review_complete == 1) ? 'YES' : 'NO',
                    'Report Notes' => $chDocuments->report_notes,
                    'Extension Given' => ($chDocuments->report_extension == 1) ? 'YES' : 'NO',
                    'Extension Notes' => $chDocuments->extension_notes,
                ];

                $exportChapterList[] = $rowData;
            }

            $callback = function () use ($exportChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportChapterList)) {
                    fputcsv($file, array_keys($exportChapterList[0]));
                }

                foreach ($exportChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }


    /**
     * Export International Chapter List
     */
    public function indexInternationalIRSFiling(Request $request)
    {
        $fileName = 'int_subordinate_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        // Get January 1st of the previous year
        $previousYear = Carbon::now()->subYear()->startOfYear();

        $baseQueryActive = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $baseQueryZapped = $this->baseChapterController->getZappedInternationalBaseQuery($coorId);

        $activeSubquery = $baseQueryActive['query']
            ->select('chapters.*');

        $zappedSubquery = $baseQueryZapped['query']
            ->where('chapters.zap_date', '>', $previousYear)
            ->select('chapters.*');

        $irsChapterList = DB::table(DB::raw("({$activeSubquery->toSql()}) as active_chapters"))
            ->mergeBindings($activeSubquery->getQuery())
            ->union(
                DB::table(DB::raw("({$zappedSubquery->toSql()}) as zapped_chapters"))
                    ->mergeBindings($zappedSubquery->getQuery())
            )
            ->orderBy('ein')
            ->get();

        if (count($irsChapterList) > 0) {
            $exportChapterList = [];

            foreach ($irsChapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $chActiveId = $baseQuery['chActiveId'];

                $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($chId);
                $baseDisbandedBoardQuery = $this->baseChapterController->getDisbandedBoardDetails($chId);
                $basePendingBoardQuery = $this->baseChapterController->getPendingBoardDetails($chId);

                if ($chActiveId == '1') {
                    $PresDetails = $baseActiveBoardQuery['PresDetails'];
                    $deleteColumn = null;
                }
                if ($chActiveId == '0') {
                    $PresDetails = $baseDisbandedBoardQuery['PresDisbandedDetails'];
                    $deleteColumn = 'DELETE';
                }
                if ($chActiveId == '2') {
                    $PresDetails = $basePendingBoardQuery['PresPendingDetails'];
                    $deleteColumn = 'DELETE';
                }

                $rowData = [
                    'delete' => $deleteColumn,
                    'EIN' => $chDetails->ein,
                    'Name' => $chDetails->name,
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Address' => $PresDetails->street_address,
                    'Pres City' => $PresDetails->city,
                    'Pres State' => $PresDetails->state_id,
                    'Pres Zip' => $PresDetails->zip,
                ];

                $exportChapterList[] = $rowData;
            }

            $callback = function () use ($exportChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportChapterList)) {
                    fputcsv($file, array_keys($exportChapterList[0]));
                }

                foreach ($exportChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }



    /**
     * Format Coordinator Information in Gropus
     */
    private function formatCoordinatorLocationInfo($coordData)
    {
        $cdDetails = $coordData['cdDetails'];
        $regionLongName = $coordData['regionLongName'];
        $regionLongName = $coordData['regionLongName'];
        $cdConfId = $coordData['cdConfId'];

        return [
            'Conference' => $cdConfId,
            'Region' => $regionLongName,
            'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
        ];
    }

    private function formatCoordinatorPositionInfo($coordData)
    {
        $displayPosition = $coordData['displayPosition'];
        $secondaryPosition = $coordData['secondaryPosition'];

        $secPositionValue = null;

        // Check what type of data $secondaryPosition is
        if ($secondaryPosition) {
            if (is_object($secondaryPosition)) {
                // If it's a single object
                $secPositionValue = $secondaryPosition->long_title ?? null;
            } elseif (is_array($secondaryPosition) || $secondaryPosition instanceof \Traversable) {
                // If it's a collection or array
                $secondaryPositionTitles = [];
                foreach ($secondaryPosition as $position) {
                    if (is_object($position)) {
                        $secondaryPositionTitles[] = $position->long_title ?? '';
                    } elseif (is_string($position)) {
                        $secondaryPositionTitles[] = $position;
                    }
                }
                $secPositionValue = ! empty($secondaryPositionTitles) ? implode(', ', $secondaryPositionTitles) : null;
            } elseif (is_string($secondaryPosition)) {
                // If it's already a string
                $secPositionValue = $secondaryPosition;
            }
        }

        return [
            'Position' => $displayPosition->long_title,
            'Sec Position' => $secPositionValue,
        ];
    }

    private function formatCoordinatorPositionExtraInfo($coordData)
    {
        $RptFName = $coordData['RptFName'];
        $RptLName = $coordData['RptLName'];
        $ReportTo = $RptFName.' '.$RptLName;
        $cdAdminRole = $coordData['cdAdminRole'];

        return [
            'Admin' => $cdAdminRole->admin_role,
            'Report To' => $ReportTo,
        ];
    }

    private function formatCoordinatorContactInfo($coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Email' => $cdDetails->email,
            'Email2' => $cdDetails->sec_email,
            'Phone' => $cdDetails->phone,
            'Phone2' => $cdDetails->alt_phone,
        ];
    }

    private function formatCoordinatorAddressInfo($coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Address' => $cdDetails->address,
            'City' => $cdDetails->city,
            'State' => $cdDetails->state->state_short_name,
            'Zip' => $cdDetails->zip,
        ];
    }

    private function formatCoordinatorBirthdayInfo($coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,

        ];
    }

    private function formatCoordinatorStartInfo($coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Coordinator Start' => $cdDetails->coordinator_start_date,
        ];
    }

    private function formatCoordinatorHistoryInfo($coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Last Promoted' => $cdDetails->last_promoted,
            'Leave of Absense' => ($cdDetails->on_leave == 1) ? 'YES' : 'NO',
            'Leave Date' => $cdDetails->leave_date,
        ];
    }

    private function formatCoordinatorAppreciationInfo($coordData)
    {
        $cdDetails = $coordData['cdDetails'];
        $necklace = $cdDetails->recognition->recognition_necklace;

        return [
            '<1 Year' => $cdDetails->recognition->recognitionGift0?->recognition_gift,
            '1 Year' => $cdDetails->recognition->recognitionGift1?->recognition_gift,
            '2 Years' => $cdDetails->recognition->recognitionGift2?->recognition_gift,
            '3 Years' => $cdDetails->recognition->recognitionGift3?->recognition_gift,
            '4 Years' => $cdDetails->recognition->recognitionGift4?->recognition_gift,
            '5 Years' => $cdDetails->recognition->recognitionGift5?->recognition_gift,
            '6 Years' => $cdDetails->recognition->recognitionGift6?->recognition_gift,
            '7 Years' => $cdDetails->recognition->recognitionGift7?->recognition_gift,
            '8 Years' => $cdDetails->recognition->recognitionGift8?->recognition_gift,
            '9 Years' => $cdDetails->recognition->recognitionGift9?->recognition_gift,
            'Necklace' => ($necklace == 1) ? 'YES' : 'NO',
            'Top Tier/Other' => $cdDetails->recognition->recognition_toptier
        ];
    }

    private function formatCoordinatorRetireInfo($coordData)
    {
        $cdDetails = $coordData['cdDetails'];

        return [
            'Retire Date' => $cdDetails->zapped_date,
            'Retire Reason' => $cdDetails->reason_retired,
        ];
    }

    /**
     * Format row data for full coordinator & international coordinator export
     */
    private function formatFullCoordinatorRow($coordData)
    {
        return array_merge(
            $this->formatCoordinatorLocationInfo($coordData),
            $this->formatCoordinatorPositionInfo($coordData),
            $this->formatCoordinatorPositionExtraInfo($coordData),
            $this->formatCoordinatorContactInfo($coordData),
            $this->formatCoordinatorAddressInfo($coordData),
            $this->formatCoordinatorBirthdayInfo($coordData),
            $this->formatCoordinatorStartInfo($coordData),
            $this->formatCoordinatorHistoryInfo($coordData)
        );
    }

    /**
     * Format row data for full retired coordinator & international retired coordinator export
     */
    private function formatRetiredCoordinatorRow($coordData)
    {
        return array_merge(
            $this->formatCoordinatorLocationInfo($coordData),
            $this->formatCoordinatorPositionInfo($coordData),
            $this->formatCoordinatorPositionExtraInfo($coordData),
            $this->formatCoordinatorContactInfo($coordData),
            $this->formatCoordinatorAddressInfo($coordData),
            $this->formatCoordinatorBirthdayInfo($coordData),
            $this->formatCoordinatorHistoryInfo($coordData),
            $this->formatCoordinatorStartInfo($coordData),
            $this->formatCoordinatorRetireInfo($coordData)
        );
    }

    /**
     * Format row data for coordinator appreciation export
     */
    private function formatCoordinatorAppreciationRow($coordData)
    {
        return array_merge(
            $this->formatCoordinatorLocationInfo($coordData),
            $this->formatCoordinatorPositionInfo($coordData),
            $this->formatCoordinatorStartInfo($coordData),
            $this->formatCoordinatorAppreciationInfo($coordData)
        );
    }




    /**
     * Export Coordinator List
     */
    public function indexCoordinator(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'coordinator_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $coordIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($coordIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($coordIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'Conference', 'Region', 'Coordinator Name', 'Position', 'Sec Position', 'Admin',
                'Report To', 'Email', 'Email2', 'Phone', 'Phone2', 'Address', 'City', 'State', 'Zip',
                'Birthday', 'Coordinator Start', 'Last Promoted', 'Leave of Absense', 'Leave Date',
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($coordIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $cdId) {
                    // Use your existing base controller method
                    $coordData = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                    $rowData = $this->formatFullCoordinatorRow($coordData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($cdId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export International Coordinator List
     */
    public function indexIntCoordinator(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'coordinator_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getActiveInternationalBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $coordIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($coordIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($coordIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'Conference', 'Region', 'Coordinator Name', 'Position', 'Sec Position', 'Admin',
                'Report To', 'Email', 'Email2', 'Phone', 'Phone2', 'Address', 'City', 'State', 'Zip',
                'Birthday', 'Coordinator Start', 'Last Promoted', 'Leave of Absense', 'Leave Date',
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($coordIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $cdId) {
                    // Use your existing base controller method
                    $coordData = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                    $rowData = $this->formatFullCoordinatorRow($coordData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($cdId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Retired Coordinator List
     */
    public function indexRetiredCoordinator(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'coordinator_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getRetiredBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $coordIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($coordIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($coordIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'Conference', 'Region', 'Coordinator Name', 'Position', 'Sec Position', 'Admin',
                'Report To', 'Email', 'Email2', 'Phone', 'Phone2', 'Address', 'City', 'State', 'Zip',
                'Birthday', 'Coordinator Start', 'Last Promoted', 'Leave of Absense', 'Leave Date',
                'Retire Date', 'Retire Reason'
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($coordIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $cdId) {
                    // Use your existing base controller method
                    $coordData = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                    $rowData = $this->formatRetiredCoordinatorRow($coordData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($cdId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export International Retired Coordinator List
     */
    public function indexIntRetCoordinator(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'coordinator_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getRetiredInternationalBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $coordIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($coordIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($coordIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'Conference', 'Region', 'Coordinator Name', 'Position', 'Sec Position', 'Admin',
                'Report To', 'Email', 'Email2', 'Phone', 'Phone2', 'Address', 'City', 'State', 'Zip',
                'Birthday', 'Coordinator Start', 'Last Promoted', 'Leave of Absense', 'Leave Date',
                'Retire Date', 'Retire Reason'
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($coordIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $cdId) {
                    // Use your existing base controller method
                    $coordData = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                    $rowData = $this->formatRetiredCoordinatorRow($coordData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($cdId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Coordinator Appreciation List
     */
    public function indexAppreciation(Request $request)
    {
        // Increase memory limit and execution time for large exports
        ini_set('memory_limit', '512M');
        set_time_limit(300); // 5 minutes

        $fileName = 'coordinator_appreciation_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);

        // Get all chapter IDs first
        $coordIds = $baseQuery['query']->pluck('id')->toArray();

        if (empty($coordIds)) {
            return redirect()->to('/home');
        }

        $callback = function () use ($coordIds) {
            $file = fopen('php://output', 'w');

            // Write headers
            $headers = [
                'Conference', 'Region', 'Coordinator Name', 'Position', 'Sec Position',
                'Start Date', '<1 Year', '1 Year', '2 Year', '3 Year', '4 Year', '5 Year', '6 Year',
                '7 Year', '8 Year', '9 Year', 'Necklace', 'Top Tier/Other',
            ];
            fputcsv($file, $headers);

            // Process chapters in chunks to manage memory
            $chunkSize = 50; // Smaller chunks since each getChapterDetails call is heavy
            $chunks = array_chunk($coordIds, $chunkSize);

            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $cdId) {
                    // Use your existing base controller method
                    $coordData = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                    $rowData = $this->formatCoordinatorAppreciationRow($coordData);
                    fputcsv($file, $rowData);

                    // Clear memory periodically within chunks
                    if (($chunkIndex * $chunkSize + array_search($cdId, $chunk)) % 10 === 0) {
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }

                // Force garbage collection after each chunk
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Clear memory after each chunk
                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }




    /**
     * Export Chapter Coordinator List
     */
    public function indexChapterCoordinator(Request $request)
    {
        $fileName = 'chapter_coordinator_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        if ($chapterList->isEmpty()) {
            return redirect()->to('/home');
        }

        $positionCodes = ['CC', 'ACC', 'RC', 'ARC', 'SC', 'AC', 'BS'];

        if (count($chapterList) > 0) {
            $exportChapterList = [];

            foreach ($chapterList as $chapter) {
                $chId = $chapter->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];

                // Get coordinator reporting tree
                $reportingList = DB::table('coordinator_reporting_tree')
                    ->where('coordinator_id', $chapter->primary_coordinator_id)
                    ->first();

                // Filter and reverse reporting list
                $coordinatorIds = collect((array) $reportingList)
                    ->except(['id', 'layer0'])
                    ->reverse()
                    ->values();

                // Get coordinator details
                $coordinators = DB::table('coordinators as cd')
                    ->select('cd.id', 'cd.first_name', 'cd.last_name', 'cp.short_title as position')
                    ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                    ->whereIn('cd.id', $coordinatorIds)
                    ->get()
                    ->keyBy('position');

                // Initialize row data with basic chapter info
                $rowData = [
                    'Conference' => $chDetails->conference_id,
                    'Region' => $baseQuery['regionLongName'],
                    'State' => $baseQuery['stateShortName'],
                    'Chapter Name' => $chDetails->name,
                ];

                // Add coordinator positions to row data
                foreach ($positionCodes as $position) {
                    $coordinator = $coordinators->first(function ($coord) use ($position) {
                        return $coord->position === $position;
                    });

                    $rowData[$position] = $coordinator
                        ? "{$coordinator->first_name} {$coordinator->last_name}"
                        : '';
                }

                $exportChapterList[] = $rowData;
            }

            $callback = function () use ($exportChapterList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportChapterList)) {
                    fputcsv($file, array_keys($exportChapterList[0]));
                }

                foreach ($exportChapterList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }


}
