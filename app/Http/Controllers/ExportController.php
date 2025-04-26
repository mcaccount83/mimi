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
     * Export Chapter List
     */
    public function indexChapter(Request $request)
    {
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
                $startMonthName = $baseQuery['startMonthName'];
                $chapterStatus = $baseQuery['chapterStatus'];
                $websiteLink = $baseQuery['websiteLink'];
                $PresDetails = $baseQuery['PresDetails'];
                $AVPDetails = $baseQuery['AVPDetails'];
                $MVPDetails = $baseQuery['MVPDetails'];
                $TRSDetails = $baseQuery['TRSDetails'];
                $SECDetails = $baseQuery['SECDetails'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'EIN' => $chDetails->ein,
                    'Chapter Email' => $chDetails->email,
                    'Chapter P.O. Box' => $chDetails->po_box,
                    'Inquiries Email' => $chDetails->inquiries_contact,
                    'Inquiries Notes' => $chDetails->inquiries_note,
                    'Status' => $chapterStatus,
                    'Notes' => $chDetails->notes,
                    'Bounraries' => $chDetails->territory,
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Email' => $PresDetails->email,
                    'Pres Phone' => $PresDetails->phone,
                    'AVP Name' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
                    'AVP Email' => $AVPDetails->email,
                    'AVP Phone' => $AVPDetails->phone,
                    'MVP Name' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
                    'MVP Email' => $MVPDetails->email,
                    'MVP Phone' => $MVPDetails->phone,
                    'Treasurer Name' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
                    'Treasurer Email' => $TRSDetails->email,
                    'Treasurer Phone' => $TRSDetails->phone,
                    'Secretary Name' => $SECDetails->first_name.' '.$SECDetails->last_name,
                    'Secretary Email' => $SECDetails->email,
                    'Secretary Phone' => $SECDetails->phone,
                    'Website' => $websiteLink,
                    'Linked Status' => $chDetails->website_status,
                    'EGroup' => $chDetails->egroup,
                    'Social Media' => $chDetails->social1.' '.$chDetails->social1.' '.$chDetails->social1,
                    'Start Month' => $startMonthName,
                    'Start Year' => $chDetails->start_year,
                    'Dues Last Paid' => $chDetails->dues_last_paid,
                    'Members paid for' => $chDetails->members_paid_for,
                    'NextRenewal' => $chDetails->next_renewal_year,
                    'Founder' => $chDetails->founders_name,
                    'Sistered By' => $chDetails->sistered_by,
                    'FormerName' => $chDetails->former_name,
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
     * Export Zapped Chapter List
     */
    public function indexZappedChapter(Request $request)
    {
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
                $startMonthName = $baseQuery['startMonthName'];
                $chapterStatus = $baseQuery['chapterStatus'];
                $websiteLink = $baseQuery['websiteLink'];
                $PresDetails = $baseQuery['PresDisbandedDetails'];
                $AVPDetails = $baseQuery['AVPDisbandedDetails'];
                $MVPDetails = $baseQuery['MVPDisbandedDetails'];
                $TRSDetails = $baseQuery['TRSDisbandedDetails'];
                $SECDetails = $baseQuery['SECDisbandedDetails'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'EIN' => $chDetails->ein,
                    'Chapter Email' => $chDetails->email,
                    'Chapter P.O. Box' => $chDetails->po_box,
                    'Inquiries Email' => $chDetails->inquiries_contact,
                    'Inquiries Notes' => $chDetails->inquiries_note,
                    'Status' => $chapterStatus,
                    'Notes' => $chDetails->notes,
                    'Bounraries' => $chDetails->territory,
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Email' => $PresDetails->email,
                    'Pres Phone' => $PresDetails->phone,
                    'AVP Name' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
                    'AVP Email' => $AVPDetails->email,
                    'AVP Phone' => $AVPDetails->phone,
                    'MVP Name' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
                    'MVP Email' => $MVPDetails->email,
                    'MVP Phone' => $MVPDetails->phone,
                    'Treasurer Name' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
                    'Treasurer Email' => $TRSDetails->email,
                    'Treasurer Phone' => $TRSDetails->phone,
                    'Secretary Name' => $SECDetails->first_name.' '.$SECDetails->last_name,
                    'Secretary Email' => $SECDetails->email,
                    'Secretary Phone' => $SECDetails->phone,
                    'Website' => $websiteLink,
                    'Linked Status' => $chDetails->website_status,
                    'EGroup' => $chDetails->egroup,
                    'Social Media' => $chDetails->social1.' '.$chDetails->social1.' '.$chDetails->social1,
                    'Start Month' => $startMonthName,
                    'Start Year' => $chDetails->start_year,
                    'Dues Last Paid' => $chDetails->dues_last_paid,
                    'Members paid for' => $chDetails->members_paid_for,
                    'NextRenewal' => $chDetails->next_renewal_year,
                    'Founder' => $chDetails->founders_name,
                    'Sistered By' => $chDetails->sistered_by,
                    'FormerName' => $chDetails->former_name,
                    'Disband Date' => $chDetails->zap_date,
                    'Disband Reason' => $chDetails->disband_reason,
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
     * Export Coordinator List
     */
    public function indexCoordinator(Request $request)
    {
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
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdLeave = $baseQuery['cdDetails']->on_leave == 1;

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secondaryPosition->long_title ?? null,
                    'Email' => $cdDetails->email,
                    'Email2' => $cdDetails->sec_email,
                    'Report To' => $ReportTo,
                    'Address' => $cdDetails->address,
                    'City' => $cdDetails->city,
                    'State' => $cdDetails->state,
                    'Zip' => $cdDetails->zip,
                    'Phone' => $cdDetails->phone,
                    'Phone2' => $cdDetails->alt_phone,
                    'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,
                    'Coordinator Start' => $cdDetails->coordinator_start_date,
                    'Last Promoted' => $cdDetails->last_promoted,
                    'Leave of Absense' => ($cdLeave == 1) ? 'YES' : 'NO',
                    'Leave Date' => $cdDetails->leave_date,
                    'Last UpdatedBy' => $cdDetails->last_updated_by,
                    'Last UpdatedDate' => $cdDetails->last_updated_date,
                ];

                $exportCoordinatorList[] = $rowData;
            }

            $callback = function () use ($exportCoordinatorList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportCoordinatorList)) {
                    fputcsv($file, array_keys($exportCoordinatorList[0]));
                }

                foreach ($exportCoordinatorList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export Retired Coordinator List
     */
    public function indexRetiredCoordinator(Request $request)
    {
        $fileName = 'coordinator_retire_export_'.date('Y-m-d').'.csv';
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
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdLeave = $baseQuery['cdLeave'];

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secondaryPosition->long_title ?? null,
                    'Email' => $cdDetails->email,
                    'Email2' => $cdDetails->sec_email,
                    'Report To' => $ReportTo,
                    'Address' => $cdDetails->address,
                    'City' => $cdDetails->city,
                    'State' => $cdDetails->state,
                    'Zip' => $cdDetails->zip,
                    'Phone' => $cdDetails->phone,
                    'Phone2' => $cdDetails->alt_phone,
                    'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,
                    'Coordinator Start' => $cdDetails->coordinator_start_date,
                    'Last Promoted' => $cdDetails->last_promoted,
                    'Leave of Absense' => ($cdLeave == 1) ? 'YES' : 'NO',
                    'Leave Date' => $cdDetails->leave_date,
                    'Retire Date' => $cdDetails->zapped_date,
                    'Reason' => $cdDetails->reason_retired,
                    'Last UpdatedBy' => $cdDetails->last_updated_by,
                    'Last UpdatedDate' => $cdDetails->last_updated_date,
                ];

                $exportCoordinatorList[] = $rowData;
            }

            $callback = function () use ($exportCoordinatorList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportCoordinatorList)) {
                    fputcsv($file, array_keys($exportCoordinatorList[0]));
                }

                foreach ($exportCoordinatorList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export Coordinator Appreciation List
     */
    public function indexAppreciation(Request $request)
    {
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
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdLeave = $baseQuery['cdLeave'];
                $necklace = $cdDetails->recognition_necklace;

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secondaryPosition->long_title ?? null,
                    'Start Date' => $cdDetails->coordinator_start_date,
                    '<1 Year' => $cdDetails->recognition_year0,
                    '1 Year' => $cdDetails->recognition_year1,
                    '2 Years' => $cdDetails->recognition_year2,
                    '3 Years' => $cdDetails->recognition_year3,
                    '4 Years' => $cdDetails->recognition_year4,
                    '5 Years' => $cdDetails->recognition_year5,
                    '6 Years' => $cdDetails->recognition_year6,
                    '7 Years' => $cdDetails->recognition_year7,
                    '8 Years' => $cdDetails->recognition_year8,
                    '9 Years' => $cdDetails->recognition_year9,
                    'Necklace' => ($necklace == 1) ? 'YES' : 'NO',
                    'Top Tier/Other' => $cdDetails->recognition_toptier,
                    'Leave of Absense' => ($cdLeave == 1) ? 'YES' : 'NO',
                    'Leave Date' => $cdDetails->last_updated_date,
                ];

                $exportCoordinatorList[] = $rowData;
            }

            $callback = function () use ($exportCoordinatorList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportCoordinatorList)) {
                    fputcsv($file, array_keys($exportCoordinatorList[0]));
                }

                foreach ($exportCoordinatorList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
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

    /**
     * Export Overdue Re-Registration List
     */
    public function indexReReg(Request $request)
    {
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

        $reChapterList = $baseQuery['query']
            ->where(function ($query) use ($currentYear, $lastMonth) {
                $query->where('next_renewal_year', '<', $currentYear)
                    ->orWhere(function ($query) use ($currentYear, $lastMonth) {
                        $query->where('next_renewal_year', '=', $currentYear)
                            ->where('start_month_id', '<=', $lastMonth);
                    });
            })
            ->orderByDesc('start_month_id')
            ->orderByDesc('next_renewal_year')
            ->get();

        if (count($reChapterList) >= 0) {
            $exportReRegList = [];

            foreach ($reChapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $pcName = $baseQuery['pcName'];
                $startMonthName = $baseQuery['startMonthName'];
                $chapterStatus = $baseQuery['chapterStatus'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'Status' => $chapterStatus,
                    'Notes' => $chDetails->notes,
                    'Month Due' => $startMonthName,
                    'Year Due' => $chDetails->next_renewal_year,
                    'Re-Reg Notes' => $chDetails->reg_notes,
                    'Dues Last Paid' => $chDetails->dues_last_paid,
                    'Members paid for' => $chDetails->members_paid_for,
                ];

                $exportReRegList[] = $rowData;
            }

            $callback = function () use ($exportReRegList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportReRegList)) {
                    fputcsv($file, array_keys($exportReRegList[0]));
                }

                foreach ($exportReRegList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export EIN Status List
     */
    public function indexEINStatus(Request $request)
    {
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
                $startMonthName = $baseQuery['startMonthName'];
                $chDocuments = $baseQuery['chDocuments'];
                $PresDetails = $baseQuery['PresDetails'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'EIN' => $chDetails->ein,
                    'EIN Letter' => ($chDocuments->ein_letter == 1) ? 'YES' : 'NO',
                    'Start Month' => $startMonthName,
                    'Start Year' => $chDetails->start_year,
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Address' => $PresDetails->street_address,
                    'Pres City' => $PresDetails->city,
                    'Pres State' => $PresDetails->state,
                    'Pres Zip' => $PresDetails->zip,
                    'Pres Phone' => $PresDetails->phone,
                    'Pres Email' => $PresDetails->email,
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
     * Export International Chapter List
     */
    public function indexInternationalChapter(Request $request)
    {
        $fileName = 'int_chapter_'.date('Y-m-d').'.csv';
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
                $startMonthName = $baseQuery['startMonthName'];
                $chapterStatus = $baseQuery['chapterStatus'];
                $websiteLink = $baseQuery['websiteLink'];
                $PresDetails = $baseQuery['PresDetails'];
                $AVPDetails = $baseQuery['AVPDetails'];
                $MVPDetails = $baseQuery['MVPDetails'];
                $TRSDetails = $baseQuery['TRSDetails'];
                $SECDetails = $baseQuery['SECDetails'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'EIN' => $chDetails->ein,
                    'Chapter Email' => $chDetails->email,
                    'Chapter P.O. Box' => $chDetails->po_box,
                    'Inquiries Email' => $chDetails->inquiries_contact,
                    'Inquiries Notes' => $chDetails->inquiries_note,
                    'Status' => $chapterStatus,
                    'Notes' => $chDetails->notes,
                    'Bounraries' => $chDetails->territory,
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Email' => $PresDetails->email,
                    'Pres Phone' => $PresDetails->phone,
                    'AVP Name' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
                    'AVP Email' => $AVPDetails->email,
                    'AVP Phone' => $AVPDetails->phone,
                    'MVP Name' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
                    'MVP Email' => $MVPDetails->email,
                    'MVP Phone' => $MVPDetails->phone,
                    'Treasurer Name' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
                    'Treasurer Email' => $TRSDetails->email,
                    'Treasurer Phone' => $TRSDetails->phone,
                    'Secretary Name' => $SECDetails->first_name.' '.$SECDetails->last_name,
                    'Secretary Email' => $SECDetails->email,
                    'Secretary Phone' => $SECDetails->phone,
                    'Website' => $websiteLink,
                    'Linked Status' => $chDetails->website_status,
                    'EGroup' => $chDetails->egroup,
                    'Social Media' => $chDetails->social1.' '.$chDetails->social1.' '.$chDetails->social1,
                    'Start Month' => $startMonthName,
                    'Start Year' => $chDetails->start_year,
                    'Dues Last Paid' => $chDetails->dues_last_paid,
                    'Members paid for' => $chDetails->members_paid_for,
                    'NextRenewal' => $chDetails->next_renewal_year,
                    'Founder' => $chDetails->founders_name,
                    'Sistered By' => $chDetails->sistered_by,
                    'FormerName' => $chDetails->former_name,
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
     * Export International Zapped Chapter List
     */
    public function indexInternationalZapChapter(Request $request)
    {
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

        $baseQuery = $this->baseChapterController->getZappedInternationalBaseQuery($coorId);
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
                $startMonthName = $baseQuery['startMonthName'];
                $chapterStatus = $baseQuery['chapterStatus'];
                $websiteLink = $baseQuery['websiteLink'];
                $PresDetails = $baseQuery['PresDisbandedDetails'];
                $AVPDetails = $baseQuery['AVPDisbandedDetails'];
                $MVPDetails = $baseQuery['MVPDisbandedDetails'];
                $TRSDetails = $baseQuery['TRSDisbandedDetails'];
                $SECDetails = $baseQuery['SECDisbandedDetails'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'EIN' => $chDetails->ein,
                    'Chapter Email' => $chDetails->email,
                    'Chapter P.O. Box' => $chDetails->po_box,
                    'Inquiries Email' => $chDetails->inquiries_contact,
                    'Inquiries Notes' => $chDetails->inquiries_note,
                    'Status' => $chapterStatus,
                    'Notes' => $chDetails->notes,
                    'Bounraries' => $chDetails->territory,
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Email' => $PresDetails->email,
                    'Pres Phone' => $PresDetails->phone,
                    'AVP Name' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
                    'AVP Email' => $AVPDetails->email,
                    'AVP Phone' => $AVPDetails->phone,
                    'MVP Name' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
                    'MVP Email' => $MVPDetails->email,
                    'MVP Phone' => $MVPDetails->phone,
                    'Treasurer Name' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
                    'Treasurer Email' => $TRSDetails->email,
                    'Treasurer Phone' => $TRSDetails->phone,
                    'Secretary Name' => $SECDetails->first_name.' '.$SECDetails->last_name,
                    'Secretary Email' => $SECDetails->email,
                    'Secretary Phone' => $SECDetails->phone,
                    'Website' => $websiteLink,
                    'Linked Status' => $chDetails->website_status,
                    'EGroup' => $chDetails->egroup,
                    'Social Media' => $chDetails->social1.' '.$chDetails->social1.' '.$chDetails->social1,
                    'Start Month' => $startMonthName,
                    'Start Year' => $chDetails->start_year,
                    'Dues Last Paid' => $chDetails->dues_last_paid,
                    'Members paid for' => $chDetails->members_paid_for,
                    'NextRenewal' => $chDetails->next_renewal_year,
                    'Founder' => $chDetails->founders_name,
                    'Sistered By' => $chDetails->sistered_by,
                    'FormerName' => $chDetails->former_name,
                    'Disband Date' => $chDetails->zap_date,
                    'Disband Reason' => $chDetails->disband_reason,
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
     * Export International Coordinator List
     */
    public function indexIntCoordinator(Request $request)
    {
        $fileName = 'int_coordinator_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseCoordinatorController->getActiveInternationalBaseQuery($coorId);
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdLeave = $baseQuery['cdDetails']->on_leave;

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secondaryPosition->long_title ?? null,
                    'Email' => $cdDetails->email,
                    'Email2' => $cdDetails->sec_email,
                    'Report To' => $ReportTo,
                    'Address' => $cdDetails->address,
                    'City' => $cdDetails->city,
                    'State' => $cdDetails->state,
                    'Zip' => $cdDetails->zip,
                    'Phone' => $cdDetails->phone,
                    'Phone2' => $cdDetails->alt_phone,
                    'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,
                    'Coordinator Start' => $cdDetails->coordinator_start_date,
                    'Last Promoted' => $cdDetails->last_promoted,
                    'Leave of Absense' => ($cdLeave == 1) ? 'YES' : 'NO',
                    'Leave Date' => $cdDetails->leave_date,
                    'Last UpdatedBy' => $cdDetails->last_updated_by,
                    'Last UpdatedDate' => $cdDetails->last_updated_date,
                ];

                $exportCoordinatorList[] = $rowData;
            }

            $callback = function () use ($exportCoordinatorList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportCoordinatorList)) {
                    fputcsv($file, array_keys($exportCoordinatorList[0]));
                }

                foreach ($exportCoordinatorList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export International Retired Coordinator List
     */
    public function indexIntRetCoordinator(Request $request)
    {
        $fileName = 'int_coordinator_ret_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseCoordinatorController->getRetiredInternationalBaseQuery($coorId);
        $coordinatorList = $baseQuery['query']->get();

        if (count($coordinatorList) > 0) {
            $exportCoordinatorList = [];

            foreach ($coordinatorList as $list) {
                $cdId = $list->id;
                $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
                $cdDetails = $baseQuery['cdDetails'];
                $cdId = $baseQuery['cdId'];
                $regionLongName = $baseQuery['regionLongName'];
                $cdConfId = $baseQuery['cdConfId'];
                $RptFName = $baseQuery['RptFName'];
                $RptLName = $baseQuery['RptLName'];
                $ReportTo = $RptFName.' '.$RptLName;
                $displayPosition = $baseQuery['displayPosition'];
                $secondaryPosition = $baseQuery['secondaryPosition'];
                $cdLeave = $baseQuery['cdDetails'];

                $rowData = [
                    'Conference' => $cdConfId,
                    'Region' => $regionLongName,
                    'Coordinator Name' => $cdDetails->first_name.' '.$cdDetails->last_name,
                    'Position' => $displayPosition->long_title,
                    'Sec Position' => $secondaryPosition->long_title ?? null,
                    'Email' => $cdDetails->email,
                    'Email2' => $cdDetails->sec_email,
                    'Report To' => $ReportTo,
                    'Address' => $cdDetails->address,
                    'City' => $cdDetails->city,
                    'State' => $cdDetails->state,
                    'Zip' => $cdDetails->zip,
                    'Phone' => $cdDetails->phone,
                    'Phone2' => $cdDetails->alt_phone,
                    'Birthday' => $cdDetails->birthday_month_id.' / '.$cdDetails->birthday_day,
                    'Coordinator Start' => $cdDetails->coordinator_start_date,
                    'Last Promoted' => $cdDetails->last_promoted,
                    'Leave of Absense' => ($cdLeave == 1) ? 'YES' : 'NO',
                    'Leave Date' => $cdDetails->leave_date,
                    'Retire Date' => $cdDetails->zapped_date,
                    'Reason' => $cdDetails->reason_retired,
                    'Last UpdatedBy' => $cdDetails->last_updated_by,
                    'Last UpdatedDate' => $cdDetails->last_updated_date,
                ];

                $exportCoordinatorList[] = $rowData;
            }

            $callback = function () use ($exportCoordinatorList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportCoordinatorList)) {
                    fputcsv($file, array_keys($exportCoordinatorList[0]));
                }

                foreach ($exportCoordinatorList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export International Overdue Re-Registration List
     */
    public function indexIntReReg(Request $request)
    {
        $fileName = 'int_rereg_export_'.date('Y-m-d').'.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $now = Carbon::now();
        $currentMonth = $now->month;
        $lastMonth = $now->copy()->subMonth()->format('m');
        $currentYear = $now->year;

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);

        $reChapterList = $baseQuery['query']
            ->where(function ($query) use ($currentYear, $lastMonth) {
                $query->where('next_renewal_year', '<', $currentYear)
                    ->orWhere(function ($query) use ($currentYear, $lastMonth) {
                        $query->where('next_renewal_year', '=', $currentYear)
                            ->where('start_month_id', '<=', $lastMonth);
                    });
            })
            ->orderByDesc('start_month_id')
            ->orderByDesc('next_renewal_year')
            ->get();

        if (count($reChapterList) >= 0) {
            $exportReRegList = [];

            foreach ($reChapterList as $list) {
                $chId = $list->id;
                $baseQuery = $this->baseChapterController->getChapterDetails($chId);
                $chDetails = $baseQuery['chDetails'];
                $chId = $baseQuery['chId'];
                $stateShortName = $baseQuery['stateShortName'];
                $regionLongName = $baseQuery['regionLongName'];
                $chConfId = $baseQuery['chConfId'];
                $pcName = $baseQuery['pcName'];
                $startMonthName = $baseQuery['startMonthName'];
                $chapterStatus = $baseQuery['chapterStatus'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'Primary Coordinator' => $pcName,
                    'Status' => $chapterStatus,
                    'Notes' => $chDetails->notes,
                    'Month Due' => $startMonthName,
                    'Year Due' => $chDetails->next_renewal_year,
                    'Re-Reg Notes' => $chDetails->reg_notes,
                    'Dues Last Paid' => $chDetails->dues_last_paid,
                    'Members paid for' => $chDetails->members_paid_for,
                ];

                $exportReRegList[] = $rowData;
            }

            $callback = function () use ($exportReRegList) {
                $file = fopen('php://output', 'w');

                if (! empty($exportReRegList)) {
                    fputcsv($file, array_keys($exportReRegList[0]));
                }

                foreach ($exportReRegList as $row) {
                    fputcsv($file, $row);
                }

                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        return redirect()->to('/home');
    }

    /**
     * Export International EIN Status List
     */
    public function indexIntEINStatus(Request $request)
    {
        $fileName = 'int_ein_status_'.date('Y-m-d').'.csv';
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
                $startMonthName = $baseQuery['startMonthName'];
                $chDocuments = $baseQuery['chDocuments'];
                $PresDetails = $baseQuery['PresDetails'];

                $rowData = [
                    'Conference' => $chConfId,
                    'Region' => $regionLongName,
                    'State' => $stateShortName,
                    'Name' => $chDetails->name,
                    'EIN' => $chDetails->ein,
                    'EIN Letter' => ($chDocuments->ein_letter == 1) ? 'YES' : 'NO',
                    'Start Month' => $startMonthName,
                    'Start Year' => $chDetails->start_year,
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Address' => $PresDetails->street_address,
                    'Pres City' => $PresDetails->city,
                    'Pres State' => $PresDetails->state,
                    'Pres Zip' => $PresDetails->zip,
                    'Pres Phone' => $PresDetails->phone,
                    'Pres Email' => $PresDetails->email,
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
                $chIsActive = $baseQuery['chIsActive'];
                if ($chIsActive == '1') {
                    $PresDetails = $baseQuery['PresDetails'];
                    $deleteColumn = null;
                }
                if ($chIsActive == '0') {
                    $PresDetails = $baseQuery['PresDisbandedDetails'];
                    $deleteColumn = 'DELETE';
                }

                $rowData = [
                    'delete' => $deleteColumn,
                    'EIN' => $chDetails->ein,
                    'Name' => $chDetails->name,
                    'Pres Name' => $PresDetails->first_name.' '.$PresDetails->last_name,
                    'Pres Address' => $PresDetails->street_address,
                    'Pres City' => $PresDetails->city,
                    'Pres State' => $PresDetails->state,
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
}
