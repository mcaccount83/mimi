<?php

namespace App\Http\Controllers;

use App\Enums\BoardPosition;
use App\Enums\ChapterCheckbox;
use App\Enums\ChapterStatusEnum;
use App\Mail\EOYReviewrAssigned;
use App\Models\BoardsIncoming;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\DisbandedChecklist;
use App\Models\DocumentsEOY;
use App\Models\FinancialReport;
use App\Models\FinancialReportAwards;
use App\Models\FinancialReportFinal;
use App\Models\State;
use App\Models\Website;
use App\Services\PositionConditionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EOYReportController extends Controller implements HasMiddleware
{
    protected $positionConditionsService;

    protected $userController;

    protected $baseChapterController;

    protected $baseMailDataController;

    protected $financialReportController;

    public function __construct(PositionConditionsService $positionConditionsService, UserController $userController, BaseChapterController $baseChapterController, BaseMailDataController $baseMailDataController,
        FinancialReportController $financialReportController)
    {
        $this->positionConditionsService = $positionConditionsService;
        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseMailDataController = $baseMailDataController;
        $this->financialReportController = $financialReportController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * View the EOY Report Title
     */
    public function getPageTitle(Request $request)
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $userAdmin = $user['userAdmin'];
        $admin = ($userAdmin == '1' || $userAdmin == '2');

        $conditions = $this->positionConditionsService->getConditionsForUser($positionId, $secPositionId);
        $eoyTestCondition = $conditions['eoyTestCondition'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $displayTESTING = $EOYOptions['displayTESTING'];
        $displayLIVE = $EOYOptions['displayLIVE'];

        $titles = [
            'eoy_reports' => 'End of Year Reports',
            'eoy_details' => 'EOY Details',
        ];

        if ($admin && ! $displayTESTING && ! $displayLIVE) {
            $titles['eoy_reports'] .= ' *ADMIN*';
            $titles['eoy_details'] .= ' *ADMIN*';
        }

        if ($eoyTestCondition && $displayTESTING) {
            $titles['eoy_reports'] .= ' *TESTING*';
            $titles['eoy_details'] .= ' *TESTING*';
        }

        return $titles;
    }

    /**
     * View the EOY Status list
     */
    public function showEOYStatus(Request $request): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_reports'];
        $breadcrumb = 'EOY Status Report';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $thisYear = $EOYOptions['thisYear'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($thisYear) {
                $query->where(function ($q) use ($thisYear) {
                    $q->where('start_year', '<', $thisYear)
                        ->orWhere(function ($q) use ($thisYear) {
                            $q->where('start_year', '=', $thisYear)
                                ->where('start_month_id', '<', 7); // July is month 7
                        });
                });
            })
            ->get();

        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox2Status = $baseQuery[ChapterCheckbox::CHECK_REVIEWER];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status, 'title' => $title, 'breadcrumb' => $breadcrumb,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('eoyreports.eoystatus')->with($data);
    }

    /**
     * Edit the EOY Status Details
     */
    public function editEOYDetails(Request $request, $id): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_details'];
        $breadcrumb = 'EOY Details';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chActiveId = $baseQuery['chActiveId'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $allAwards = $baseQuery['allAwards'];
        $reviewComplete = $baseQuery['reviewComplete'];
        $rrList = $baseQuery['rrList'];

        if ($chActiveId == ChapterStatusEnum::ACTIVE) {
            $baseBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
        } elseif ($chActiveId == ChapterStatusEnum::ZAPPED) {
            $baseBoardQuery = $this->baseChapterController->getDisbandedBoardDetails($id);
        }

        $PresDetails = $baseBoardQuery['PresDetails'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'PresDetails' => $PresDetails,
            'coorId' => $coorId, 'confId' => $confId, 'allAwards' => $allAwards, 'chDocuments' => $chDocuments,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport,
            'reviewComplete' => $reviewComplete,  'rrList' => $rrList, 'chEOYDocuments' => $chEOYDocuments,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('eoyreports.view')->with($data);
    }

    /**
     * Update the EOY Status Details
     */
    public function updateEOYDetails(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $input = $request->all();
        $new_board_submitted = ! isset($input['new_board_submitted']) ? null : ($input['new_board_submitted'] == 'on' ? 1 : 0);
        $new_board_active = ! isset($input['new_board_active']) ? null : ($input['new_board_active'] == 'on' ? 1 : 0);
        $financial_report_received = ! isset($input['financial_report_received']) ? null : ($input['financial_report_received'] == 'on' ? 1 : 0);
        $financial_review_complete = ! isset($input['financial_review_complete']) ? null : ($input['financial_review_complete'] == 'on' ? 1 : 0);
        $report_extension = ! isset($input['report_extension']) ? null : ($input['report_extension'] == 'on' ? 1 : 0);
        $irs_verified = ! isset($input['irs_verified']) ? null : ($input['irs_verified'] == 'on' ? 1 : 0);
        // $extension_notes = $input['extension_notes'];
        $extension_notes = $request->filled('extension_notes') ? $request->input('extension_notes') : $request->input('hid_extension_notes');
        // $irs_notes = $input['irs_notes'];
        $irs_notes = $request->filled('irs_notes') ? $request->input('irs_notes') : $request->input('hid_irs_notes');
        $reviewer_id = isset($input['ch_reportrev']) && ! empty($input['ch_reportrev']) ? $input['ch_reportrev'] : $coorId;

        $chapter = Chapters::find($id);
        $documentsEOY = DocumentsEOY::find($id);
        $financialReport = FinancialReport::find($id);

        DB::beginTransaction();
        try {
            $documentsEOY->new_board_submitted = $new_board_submitted;
            $documentsEOY->new_board_active = $new_board_active;
            $documentsEOY->financial_report_received = $financial_report_received;
            $documentsEOY->financial_review_complete = $financial_review_complete;
            $documentsEOY->report_extension = $report_extension;
            $documentsEOY->extension_notes = $extension_notes;
            $documentsEOY->irs_verified = $irs_verified;
            $documentsEOY->irs_notes = $irs_notes;

            // Only set timestamp if it doesn't already exist AND the status is not null
            $documentsEOY->report_received = $financial_report_received != null && $documentsEOY->report_received === null ? Carbon::now() : $documentsEOY->report_received;
            $documentsEOY->review_complete = $financial_review_complete != null && $documentsEOY->review_complete === null ? Carbon::now() : $documentsEOY->review_complete;
            $documentsEOY->save();

            $financialReport->reviewer_id = $reviewer_id;

            // Only set timestamp if it doesn't already exist AND the status is not null
            $financialReport->submitted = $financial_report_received != null && $financialReport->submitted === null ? Carbon::now() : $financialReport->submitted;

            if ($financial_report_received != null) {
                $financialReport->reviewer_id = $financialReport->reviewer_id ?? $coorId;
            }

            $financialReport->review_complete = $financial_review_complete != null && $financialReport->review_complete === null ? Carbon::now() : $financialReport->review_complete;
            $financialReport->save();

            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            DB::commit();

            return to_route('eoyreports.view', ['id' => $id])->with('success', 'EOY Information successfully updated.');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('eoyreports.view', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * View the Board Info Received list
     */
    public function showEOYBoardReport(Request $request)
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_reports'];
        $breadcrumb = 'Board Election Report';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $updatedBy = $user['userName'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $thisYear = $EOYOptions['thisYear'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($thisYear) {
                $query->where(function ($q) use ($thisYear) {
                    $q->where('start_year', '<', $thisYear)
                        ->orWhere(function ($q) use ($thisYear) {
                            $q->where('start_year', '=', $thisYear)
                                ->where('start_month_id', '<', 7); // July is month 7
                        });
                });
            })
            ->get();

        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox2Status = $baseQuery[ChapterCheckbox::CHECK_REVIEWER];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $activationStatuses = [];

        // Check if the board activation button was clicked
        if ($request->has('board') && $request->input('board') == 'active') {
            foreach ($chapterList as $chapter) {
                // Check if chapter has incoming board members before attempting activation
                $BoardsIncomingDetails = BoardsIncoming::where('chapter_id', $chapter->id)->get();

                if ($BoardsIncomingDetails && count($BoardsIncomingDetails) > 0) {
                    // Each chapter gets its own transaction
                    DB::beginTransaction();
                    try {
                        $activationResult = $this->financialReportController->activateSingleBoard($request, $chapter->id);

                        if ($activationResult == 'success') {
                            DB::commit();
                            $activationStatuses[$chapter->id] = 'success';
                        } else {
                            DB::rollback();
                            $activationStatuses[$chapter->id] = 'fail';
                        }
                    } catch (\Exception $e) {
                        DB::rollback();
                        $activationStatuses[$chapter->id] = 'fail';
                        Log::error("Board activation unnsucessful for chapter {$chapter->id}: ".$e->getMessage());
                    } finally {
                        // This ensures DB connections are released even if exceptions occur
                        DB::disconnect();
                    }
                }
            }

            // Process results after all activations are attempted
            $successfulActivations = array_filter($activationStatuses, function ($status) {
                return $status == 'success';
            });

            if (count($activationStatuses) == 0) {
                return redirect()->to('/eoy/boardreport')->with('info', 'No Incoming Board Members for Activation');
            } elseif (count($successfulActivations) == count($activationStatuses)) {
                return redirect()->to('/eoy/boardreport')->with('success', 'All Board Info has been successfully activated');
            } elseif (count($successfulActivations) > 0) {
                $successCount = count($successfulActivations);
                $totalCount = count($activationStatuses);

                return redirect()->to('/eoy/boardreport')->with('warning', "Board activation completed: {$successCount}/{$totalCount} successful");
            } else {
                return redirect()->to('/eoy/boardreport')->with('fail', 'Board activation failed for all chapters');
            }
        }

        $countList = count($chapterList);
        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status, 'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('eoyreports.eoyboardreport')->with($data);

    }

    /**
     * Board Info Report Details
     */
    public function editBoardReport(Request $request, $id)
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $updatedBy = $user['userName'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];

        $baseIncomingBoardQuery = $this->baseChapterController->getIncomingBoardDetails($id);
        $PresDetails = $baseIncomingBoardQuery['PresDetails'];
        $AVPDetails = $baseIncomingBoardQuery['AVPDetails'];
        $MVPDetails = $baseIncomingBoardQuery['MVPDetails'];
        $TRSDetails = $baseIncomingBoardQuery['TRSDetails'];
        $SECDetails = $baseIncomingBoardQuery['SECDetails'];

        $allWebLinks = Website::all();  // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu
        $allCountries = $baseQuery['allCountries'];

        // Check if the board activation button was clicked
        if ($request->has('board') && $request->input('board') == 'active') {
            DB::beginTransaction();
            try {
                $status = $this->financialReportController->activateSingleBoard($request, $id);

                if ($status == 'success') {
                    DB::commit();

                    return redirect()->back()->with('success', 'Board activation successful');
                } else {
                    DB::rollback();

                    return redirect()->back()->with('fail', 'Board activation failed');
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Board activation error: '.$e->getMessage());

                return redirect()->back()->with('fail', 'Board activation failed');
            } finally {
                // This ensures DB connections are released even if exceptions occur
                DB::disconnect();
            }
        }

        $data = [
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'allWebLinks' => $allWebLinks, 'allStates' => $allStates, 'allCountries' => $allCountries, 'confId' => $confId, 'chConfId' => $chConfId,
        ];

        return view('eoyreports.editboardreport')->with($data);
    }

    public function updateEOYBoardReport(Request $request, $chapter_id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($chapter_id);
        $chId = $chapter_id;

        $boundaryStatus = $request->input('BoundaryStatus');
        $issue_note = $request->input('BoundaryIssue');
        // Boundary Issues Correct 0 | Not Correct 1
        if ($boundaryStatus == 0) {
            $issue_note = '';
        }

        // Handle web status - allow null values
        $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        // Only convert to 0 if the website is not null but status is empty
        if (! is_null($request->input('ch_website')) && empty(trim($ch_webstatus))) {
            $ch_webstatus = 0;
        }

        // Handle website URL
        $website = $request->input('ch_website');
        // Only add http:// if the website field is not null or empty
        if (! is_null($website) && ! empty(trim($website))) {
            if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
                $website = 'http://'.$website;
            }
        }

        $documentsEOY = DocumentsEOY::find($chapter_id);

        DB::beginTransaction();
        try {
            $chapter->email = $request->input('ch_inqemailcontact');
            $chapter->inquiries_contact = $request->input('ch_email') ?? null;
            $chapter->boundary_issues = $request->input('BoundaryStatus');
            $chapter->boundary_issue_notes = $issue_note;
            $chapter->website_url = $website;
            $chapter->website_status = $ch_webstatus;
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $documentsEOY->new_board_submitted = 1;
            $documentsEOY->save();

            // President Info - Handle separately since it's required
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $PREDetails = BoardsIncoming::where('chapter_id', $chId)
                    ->where('board_position_id', BoardPosition::PRES)
                    ->get();
                $presId = $request->input('presID');

                if (count($PREDetails) != 0) {
                    BoardsIncoming::where('id', $presId)
                        ->update($this->financialReportController->getBoardMemberData($request, 'ch_pre_', $updatedBy, $userId));
                } else {
                    BoardsIncoming::create(array_merge(
                        ['chapter_id' => $chId, 'board_position_id' => BoardPosition::PRES],
                        $this->financialReportController->getBoardMemberData($request, 'ch_pre_', $updatedBy, $userId)
                    ));
                }
            }

            // Handle other board positions
            $this->financialReportController->updateIncomingBoardMember($chId, BoardPosition::AVP, 'ch_avp_', 'AVPVacant', 'avpID', $request, $updatedBy, $userId);
            $this->financialReportController->updateIncomingBoardMember($chId, BoardPosition::MVP, 'ch_mvp_', 'MVPVacant', 'mvpID', $request, $updatedBy, $userId);
            $this->financialReportController->updateIncomingBoardMember($chId, BoardPosition::TRS, 'ch_trs_', 'TreasVacant', 'trsID', $request, $updatedBy, $userId);
            $this->financialReportController->updateIncomingBoardMember($chId, BoardPosition::SEC, 'ch_sec_', 'SecVacant', 'secID', $request, $updatedBy, $userId);

            DB::commit();

            return redirect()->back()->with('success', 'Board Info has been Saved');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        } finally {
            DB::disconnect();
        }
    }

    /**
     * View the Financial Reports List
     */
    public function showEOYFinancialReport(Request $request): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_reports'];
        $breadcrumb = 'Financial Reports';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $thisYear = $EOYOptions['thisYear'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($thisYear) {
                $query->where(function ($q) use ($thisYear) {
                    $q->where('start_year', '<', $thisYear)
                        ->orWhere(function ($q) use ($thisYear) {
                            $q->where('start_year', '=', $thisYear)
                                ->where('start_month_id', '<', 7); // July is month 7
                        });
                });
            })
            ->get();

        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox2Status = $baseQuery[ChapterCheckbox::CHECK_REVIEWER];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status, 'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('eoyreports.eoyfinancialreport')->with($data);
    }

    /**
     * Financial Report for Coordinator side for Reviewing of Chapters
     */
    public function reviewFinancialReport(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $loggedInName = $user['userName'];
        $updatedBy = $user['userName'];
        $userName = $user['userName'];
        $userPosition = $user['cdPosition'];
        $userConfName = $user['confName'];
        $userConfDesc = $user['confDesc'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chConfId = $baseQuery['chConfId'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $allAwards = $baseQuery['allAwards'];
        // $submitted = $baseQuery['submitted'];
        $rrList = $baseQuery['rrList'];

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'rrList' => $rrList, 'allAwards' => $allAwards, 'chDocuments' => $chDocuments, 'chEOYDocuments' => $chEOYDocuments,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc, 'confId' => $confId, 'chConfId' => $chConfId,
        ];

        return view('eoyreports.reviewfinancialreport')->with($data);
    }

    /**
     * Save Financial Report Review
     */
    public function updateEOYFinancialReport(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $userName = $user['userName'];
        $updatedId = $user['userId'];
        $updatedBy = $userName;

        $input = $request->all();
        $farthest_step_visited_coord = $input['FurthestStep'];
        $reviewer_id = isset($input['AssignedReviewer']) && ! empty($input['AssignedReviewer']) ? $input['AssignedReviewer'] : $coorId;
        $reportReceived = $input['submitted'];
        $submitType = $input['submit_type'];
        $step_1_notes_log = $input['Step1_Log'];
        $step_2_notes_log = $input['Step2_Log'];
        $step_3_notes_log = $input['Step3_Log'];
        $step_4_notes_log = $input['Step4_Log'];
        $step_5_notes_log = $input['Step5_Log'];
        $step_6_notes_log = $input['Step6_Log'];
        $step_7_notes_log = $input['Step7_Log'];
        $step_8_notes_log = $input['Step8_Log'];
        $step_9_notes_log = $input['Step9_Log'];
        $step_10_notes_log = $input['Step10_Log'];
        $step_11_notes_log = $input['Step11_Log'];
        $step_12_notes_log = $input['Step12_Log'];
        // $step_13_notes_log = $input['Step13_Log'];

        $reviewer_email_message = $input['reviewer_email_message'];

        // Step 1 - Dues
        $check_roster_attached = isset($input['checkRosterAttached']) ? $input['checkRosterAttached'] : null;
        $check_renewal_seems_right = isset($input['checkRenewalSeemsRight']) ? $input['checkRenewalSeemsRight'] : null;

        // Step 3 - Service
        $check_minimum_service_project = isset($input['checkServiceProject']) ? $input['checkServiceProject'] : null;
        $check_m2m_donation = isset($input['checkM2MDonation']) ? $input['checkM2MDonation'] : null;

        // Step 4 - Parties
        $check_party_percentage = isset($input['check_party_percentage']) ? $input['check_party_percentage'] : null;

        // Step - Financials
        $check_total_income_less = isset($input['checkTotalIncome']) ? $input['checkTotalIncome'] : null;

        // Step 8 - Reconciliation
        $check_beginning_balance = isset($input['check_beginning_balance']) ? $input['check_beginning_balance'] : null;
        $check_bank_statement_included = isset($input['checkBankStatementIncluded']) ? $input['checkBankStatementIncluded'] : null;
        $check_bank_statement_matches = isset($input['checkBankStatementMatches']) ? $input['checkBankStatementMatches'] : null;

        $post_balance = isset($input['post_balance']) ? preg_replace('/[^\d.]/', '', $input['post_balance']) : null;
        // $post_balance = $input['post_balance'];
        // $post_balance = str_replace(',', '', $post_balance);
        // $post_balance = $post_balance == '' ? null : $post_balance;

        // Step 9 - Questions
        $check_purchased_pins = isset($input['checkPurchasedPins']) ? $input['checkPurchasedPins'] : null;
        $check_purchased_mc_merch = isset($input['checkPurchasedMCMerch']) ? $input['checkPurchasedMCMerch'] : null;
        $check_offered_merch = isset($input['checkOfferedMerch']) ? $input['checkOfferedMerch'] : null;
        $check_bylaws_available = isset($input['checkBylawsMadeAvailable']) ? $input['checkBylawsMadeAvailable'] : null;
        $check_sistered_another_chapter = isset($input['checkSisteredAnotherChapter']) ? $input['checkSisteredAnotherChapter'] : null;
        $check_attended_training = isset($input['checkAttendedTraining']) ? $input['checkAttendedTraining'] : null;
        $check_current_990N_included = isset($input['checkCurrent990NAttached']) ? $input['checkCurrent990NAttached'] : null;

        // Step 10 - Awards
        $check_award_1_approved = isset($input['checkAward1Approved']) ? $input['checkAward1Approved'] : null;
        $check_award_2_approved = isset($input['checkAward2Approved']) ? $input['checkAward2Approved'] : null;
        $check_award_3_approved = isset($input['checkAward3Approved']) ? $input['checkAward3Approved'] : null;
        $check_award_4_approved = isset($input['checkAward4Approved']) ? $input['checkAward4Approved'] : null;
        $check_award_5_approved = isset($input['checkAward5Approved']) ? $input['checkAward5Approved'] : null;

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $roster_path = $chEOYDocuments->roster_path;
        $file_irs_path = $chEOYDocuments->irs_path;
        $statement_1_path = $chEOYDocuments->statement_1_path;
        $statement_2_path = $chEOYDocuments->statement_2_path;
        $completed_name = $chFinancialReport->completed_name;
        $completed_email = $chFinancialReport->completed_email;
        $reviewerEmail = $chDetails->reportReviewer->email;

        $chapter = Chapters::find($id);
        $documentsEOY = DocumentsEOY::find($id);
        $financialReport = FinancialReport::find($id);

        DB::beginTransaction();
        try {
            $financialReport->reviewer_id = $reviewer_id ?? $coorId;
            $financialReport->step_1_notes_log = $step_1_notes_log;
            $financialReport->step_2_notes_log = $step_2_notes_log;
            $financialReport->step_3_notes_log = $step_3_notes_log;
            $financialReport->step_4_notes_log = $step_4_notes_log;
            $financialReport->step_5_notes_log = $step_5_notes_log;
            $financialReport->step_6_notes_log = $step_6_notes_log;
            $financialReport->step_7_notes_log = $step_7_notes_log;
            $financialReport->step_8_notes_log = $step_8_notes_log;
            $financialReport->step_9_notes_log = $step_9_notes_log;
            $financialReport->step_10_notes_log = $step_10_notes_log;
            $financialReport->step_11_notes_log = $step_11_notes_log;
            $financialReport->step_12_notes_log = $step_12_notes_log;
            // $financialReport->step_13_notes_log = $step_13_notes_log;
            $financialReport->check_roster_attached = $check_roster_attached;
            $financialReport->check_renewal_seems_right = $check_renewal_seems_right;
            $financialReport->check_minimum_service_project = $check_minimum_service_project;
            $financialReport->check_m2m_donation = $check_m2m_donation;
            $financialReport->check_party_percentage = $check_party_percentage;
            $financialReport->check_attended_training = $check_attended_training;
            $financialReport->check_bank_statement_matches = $check_bank_statement_matches;
            $financialReport->check_bank_statement_included = $check_bank_statement_included;
            $financialReport->check_beginning_balance = $check_beginning_balance;
            $financialReport->post_balance = $post_balance;
            $financialReport->check_purchased_pins = $check_purchased_pins;
            $financialReport->check_purchased_mc_merch = $check_purchased_mc_merch;
            $financialReport->check_offered_merch = $check_offered_merch;
            $financialReport->check_bylaws_available = $check_bylaws_available;
            $financialReport->check_current_990N_included = $check_current_990N_included;
            $financialReport->check_total_income_less = $check_total_income_less;
            $financialReport->check_sistered_another_chapter = $check_sistered_another_chapter;
            $financialReport->farthest_step_visited_coord = $farthest_step_visited_coord;
            if ($submitType == 'review_complete') {
                $financialReport->review_complete = Carbon::now();
            }

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getFinancialReportData($chEOYDocuments, $chFinancialReport, $reviewer_email_message),
            );

            if ($financialReport->isDirty('reviewer_id')) {
                $newReviewerId = $financialReport->reviewer_id;
                $newReviewer = Coordinators::find($newReviewerId);
                $newReviewerEmail = $newReviewer->email;
                $to_email = $newReviewerEmail;
                Mail::to($to_email)
                    ->queue(new EOYReviewrAssigned($mailData));
            }

            $financialReport->save();

            if ($submitType == 'review_complete') {
                $documentsEOY->financial_review_complete = 1;
                $documentsEOY->review_complete = Carbon::now();
            }

            $documentsEOY->save();

            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            DB::commit();
            if ($submitType == 'review_complete') {
                return redirect()->back()->with('success', 'Report has been successfully Marked as Review Complete');
            } else {
                return redirect()->back()->with('success', 'Report has been successfully Updated');
            }
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Unsubmit Report
     */
    public function updateUnsubmit(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($id);
        $documentsEOY = DocumentsEOY::find($id);
        $financialReport = FinancialReport::find($id);

        DB::beginTransaction();
        try {
            $documentsEOY->financial_report_received = null;
            $documentsEOY->report_received = null;
            $documentsEOY->report_extension = '1';
            $documentsEOY->save();

            $financialReport->submitted = null;
            $financialReport->save();

            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            DB::commit();

            return redirect()->back()->with('success', 'Report has been successfully Unsubmitted.');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Unsubmit Final Report
     */
    public function updateUnsubmitFinal(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($id);
        $documentsEOY = DocumentsEOY::find($id);
        $financialReport = FinancialReportFinal::find($id);
        $disbandChecklist = DisbandedChecklist::find($id);

        DB::beginTransaction();
        try {
            $documentsEOY->final_report_received = null;
            $documentsEOY->report_received = null;
            $documentsEOY->report_extension = '1';
            $documentsEOY->save();

            $financialReport->submitted = null;
            $financialReport->save();

            $disbandChecklist->file_financial = null;
            $disbandChecklist->save();

            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            DB::commit();

            return redirect()->back()->with('success', 'Final Report has been successfully Unsubmitted.');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Clear Report Review
     */
    public function updateClearReview(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($id);
        $documentsEOY = DocumentsEOY::find($id);
        $financialReport = FinancialReport::find($id);

        DB::beginTransaction();
        try {
            $documentsEOY->financial_report_received = '1';
            $documentsEOY->financial_review_complete = null;
            $documentsEOY->review_complete = null;
            $documentsEOY->save();

            $financialReport->review_complete = null;
            $financialReport->save();

            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            DB::commit();

            return redirect()->back()->with('success', 'Review Complete has been successfully Cleared.');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * View the EOY Attachments list
     */
    public function showEOYAttachments(Request $request): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_reports'];
        $breadcrumb = 'Financial Report Attacchments';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $thisYear = $EOYOptions['thisYear'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($thisYear) {
                $query->where(function ($q) use ($thisYear) {
                    $q->where('start_year', '<', $thisYear)
                        ->orWhere(function ($q) use ($thisYear) {
                            $q->where('start_year', '=', $thisYear)
                                ->where('start_month_id', '<', 7); // July is month 7
                        });
                });
            })
            ->get();

        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox2Status = $baseQuery[ChapterCheckbox::CHECK_REVIEWER];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $countList = count($chapterList);
        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status, 'checkBox2Status' => $checkBox2Status];

        return view('eoyreports.eoyattachments')->with($data);
    }

    /**
     * View the Attachments Details
     */
    public function editEOYAttachments(Request $request, $id): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_details'];
        $breadcrumb = 'EOY Attachments';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chActiveId = $baseQuery['chActiveId'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'coorId' => $coorId, 'confId' => $confId,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport,
        ];

        return view('eoyreports.editattachments')->with($data);
    }

    /**
     * Update the Attachments Details
     */
    public function updateEOYAttachments(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($id);
        $documentsEOY = DocumentsEOY::find($id);

        DB::beginTransaction();
        try {
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $documentsEOY->irs_verified = (int) $request->has('irs_verified');
            $documentsEOY->irs_notes = $request->input('irs_notes');
            $documentsEOY->save();

            DB::commit();

            return redirect()->to('/eoy/attachments')->with('success', 'Report attachments successfully updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/eoy/attachments')->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * View EOY Boundary Issues List
     */
    public function showEOYBoundaries(Request $request): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_reports'];
        $breadcrumb = 'Boundray Issues Report';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $thisYear = $EOYOptions['thisYear'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($thisYear) {
                $query->where(function ($q) use ($thisYear) {
                    $q->where('start_year', '<', $thisYear)
                        ->orWhere(function ($q) use ($thisYear) {
                            $q->where('start_year', '=', $thisYear)
                                ->where('start_month_id', '<', 7); // July is month 7
                        });
                });
            })
            ->get();

        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox2Status = $baseQuery[ChapterCheckbox::CHECK_REVIEWER];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status, 'checkBox2Status' => $checkBox2Status];

        return view('eoyreports.eoyboundaries')->with($data);
    }

    /**
     * View the EOY Boundary Details
     */
    public function editEOYBoundaries(Request $request, $id): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_details'];
        $breadcrumb = 'EOY Boundaries';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chActiveId = $baseQuery['chActiveId'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'coorId' => $coorId, 'confId' => $confId,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport,
        ];

        return view('eoyreports.editboundaries')->with($data);
    }

    /**
     * Update the EOY Boundary Details
     */
    public function updateEOYBoundaries(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
            $chapter->territory = $request->filled('ch_territory') ? $request->input('ch_territory') : $request->input('ch_old_territory');
            $chapter->boundary_issue_resolved = (int) $request->has('ch_resolved');
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            DB::commit();

            return to_route('eoyreports.editboundaries', ['id' => $id])->with('success', 'EOY Information successfully updated.');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('eoyreports.editboundaries', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * List of Chapter Awards
     */
    public function showEOYAwards(Request $request): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_reports'];
        $breadcrumb = 'Chapter Awards Report';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $updatedBy = $user['userName'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $thisYear = $EOYOptions['thisYear'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($thisYear) {
                $query->where(function ($q) use ($thisYear) {
                    $q->where('start_year', '<', $thisYear)
                        ->orWhere(function ($q) use ($thisYear) {
                            $q->where('start_year', '=', $thisYear)
                                ->where('start_month_id', '<', 7); // July is month 7
                        });
                });
            })
            ->get();

        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox2Status = $baseQuery[ChapterCheckbox::CHECK_REVIEWER];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $allAwards = FinancialReportAwards::all();

        $maxAwards = 0;
        foreach ($chapterList as $list) {
            if (isset($list->financialReport->chapter_awards)) {
                $awards = unserialize(base64_decode($list->financialReport->chapter_awards));
                if ($awards) {
                    $maxAwards = max($maxAwards, count($awards));
                }
            }
        }

        $countList = count($chapterList);
        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status,
            'allAwards' => $allAwards, 'maxAwards' => $maxAwards, 'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status,
        ];

        return view('eoyreports.eoyawards', $data);
    }

    /**
     * View the EOY Award Details
     */
    public function editEOYAwards(Request $request, $id): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_details'];
        $breadcrumb = 'EOY Awards';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chActiveId = $baseQuery['chActiveId'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $allAwards = $baseQuery['allAwards'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'coorId' => $coorId, 'confId' => $confId,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport, 'allAwards' => $allAwards,
        ];

        return view('eoyreports.editawards')->with($data);
    }

    /**
     * Update the EOY Award Details
     */
    public function updateEOYAwards(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $input = $request->all();
        $ChapterAwards = null;
        $FieldCount = $input['ChapterAwardsRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $ChapterAwards[$i]['awards_type'] = $input['ChapterAwardsType'.$i] ?? null;
            $ChapterAwards[$i]['awards_desc'] = $input['ChapterAwardsDesc'.$i] ?? null;
            $ChapterAwards[$i]['awards_approved'] = $input['ChapterAwardsApproved'.$i] ?? null;
        }
        $chapter_awards = base64_encode(serialize($ChapterAwards));

        $chapter = Chapters::find($id);
        $financialReport = FinancialReport::find($id);

        DB::beginTransaction();
        try {
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $financialReport->chapter_awards = $chapter_awards;
            $financialReport->save();

            DB::commit();

            return to_route('eoyreports.editawards', ['id' => $id])->with('success', 'EOY Information successfully updated.');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('eoyreports.editawards', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * View the 990N Filing Details
     */
    public function showIRSSubmission(Request $request): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_reports'];
        $breadcrumb = 'Financial Report Attacchments';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $EOYOptions = $this->positionConditionsService->getEOYOptions();
        $thisYear = $EOYOptions['thisYear'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($thisYear) {
                $query->where(function ($q) use ($thisYear) {
                    $q->where('start_year', '<', $thisYear)
                        ->orWhere(function ($q) use ($thisYear) {
                            $q->where('start_year', '=', $thisYear)
                                ->where('start_month_id', '<', 7); // July is month 7
                        });
                });
            })
            ->get();

        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox2Status = $baseQuery[ChapterCheckbox::CHECK_REVIEWER];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $countList = count($chapterList);
        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status, 'checkBox2Status' => $checkBox2Status,
        ];

        return view('eoyreports.eoyirssubmission')->with($data);
    }

    /**
     * View the 990N Filing Details
     */
    public function editIRSSubmission(Request $request, $id): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['eoy_details'];
        $breadcrumb = 'EOY Attachments';

        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chActiveId = $baseQuery['chActiveId'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'coorId' => $coorId, 'confId' => $confId,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport,
        ];

        return view('eoyreports.editirssubmission')->with($data);
    }

    /**
     * Update the 990N Filing Details
     */
    public function updateIRSSubmission(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($id);
        $documentsEOY = DocumentsEOY::find($id);

        DB::beginTransaction();
        try {
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            // Correct way to handle checkboxes
            $documentsEOY->irs_verified = $request->filled('irs_verified') ? 1 : 0;
            $documentsEOY->irs_issues = $request->filled('irs_issues') ? 1 : 0;
            $documentsEOY->irs_wrongdate = $request->filled('irs_wrongdate') ? 1 : 0;
            $documentsEOY->irs_notfound = $request->filled('irs_notfound') ? 1 : 0;
            $documentsEOY->irs_filedwrong = $request->filled('irs_filedwrong') ? 1 : 0;
            $documentsEOY->irs_notified = $request->filled('irs_notified') ? 1 : 0;
            $documentsEOY->irs_notes = $request->input('irs_notes');
            $documentsEOY->save();

            DB::commit();

            return to_route('eoyreports.editirssubmission', ['id' => $id])->with('success', 'Report attachments successfully updated');
            // return redirect()->to('/eoy/irssubmission')->with('success', 'Report attachments successfully updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('eoyreports.editirssubmission', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
            // return redirect()->to('/eoy/irssubmission')->with('success', 'Report attachments successfully updated');
        }
    }
}
