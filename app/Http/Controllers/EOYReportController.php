<?php

namespace App\Http\Controllers;

use App\Enums\BoardPosition;
use App\Enums\CheckboxFilterEnum;
use App\Enums\ChapterStatusEnum;
use App\Mail\EOYReviewrAssigned;
use App\Models\BoardsIncoming;
use App\Models\Chapters;
use App\Models\ChapterAwardHistory;
use App\Models\Coordinators;
use App\Models\DisbandedChecklist;
use App\Models\DocumentsEOY;
use App\Models\FinancialReport;
use App\Models\FinancialReportAwards;
use App\Models\FinancialReportFinal;
use App\Models\FinancialReportReview;
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

        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox2Status = $baseQuery[CheckboxFilterEnum::REVIEWER];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status, 'checkBox2Status' => $checkBox2Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'title' => $title, 'breadcrumb' => $breadcrumb,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('coordinators.eoyreports.eoystatus')->with($data);
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
        $chapterStatus = $baseQuery['chapterStatus'];
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
            'reviewComplete' => $reviewComplete,  'rrList' => $rrList, 'chEOYDocuments' => $chEOYDocuments, 'chapterStatus' => $chapterStatus,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('coordinators.eoyreports.view')->with($data);
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

        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox2Status = $baseQuery[CheckboxFilterEnum::REVIEWER];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

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
                return redirect()->to('/eoyreports/boardreport')->with('info', 'No Incoming Board Members for Activation');
            } elseif (count($successfulActivations) == count($activationStatuses)) {
                return redirect()->to('/eoyreports/boardreport')->with('success', 'All Board Info has been successfully activated');
            } elseif (count($successfulActivations) > 0) {
                $successCount = count($successfulActivations);
                $totalCount = count($activationStatuses);

                return redirect()->to('/eoyreports/boardreport')->with('warning', "Board activation completed: {$successCount}/{$totalCount} successful");
            } else {
                return redirect()->to('/eoyreports/boardreport')->with('fail', 'Board activation failed for all chapters');
            }
        }

        $countList = count($chapterList);
        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'countList' => $countList, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status, 'checkBox2Status' => $checkBox2Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('coordinators.eoyreports.eoyboardreport')->with($data);

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
        $chEOYDocuments = $baseQuery['chEOYDocuments'];

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
            'allWebLinks' => $allWebLinks, 'allStates' => $allStates, 'allCountries' => $allCountries, 'confId' => $confId, 'chConfId' => $chConfId, 'chEOYDocuments' => $chEOYDocuments
        ];

        return view('coordinators.eoyreports.editboardreport')->with($data);
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

        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox2Status = $baseQuery[CheckboxFilterEnum::REVIEWER];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status, 'checkBox2Status' => $checkBox2Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('coordinators.eoyreports.eoyfinancialreport')->with($data);
    }

    /**
     * Financial Report for Coordinator side for Reviewing of Chapters
     */
    public function editFinancialReview(Request $request, $id): View
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
        $chFinancialReportReview = $baseQuery['chFinancialReportReview'];
        $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $allAwards = $baseQuery['allAwards'];
        // $submitted = $baseQuery['submitted'];
        $rrList = $baseQuery['rrList'];

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription, 'chFinancialReportReview' => $chFinancialReportReview,
            'chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'rrList' => $rrList, 'allAwards' => $allAwards, 'chDocuments' => $chDocuments, 'chEOYDocuments' => $chEOYDocuments,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc, 'confId' => $confId, 'chConfId' => $chConfId,
        ];

        return view('coordinators.eoyreports.editfinancialreview')->with($data);
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
        // $farthest_step_visited_coord = $input['FurthestStep'];
        $reviewer_id = isset($input['AssignedReviewer']) && ! empty($input['AssignedReviewer']) ? $input['AssignedReviewer'] : $coorId;
        $reportReceived = $input['submitted'];
        $submitType = $input['submit_type'];
        $reviewer_email_message = $input['reviewer_email_message'];

        // Step 1 - Dues
        $roster_attached = isset($input['checkRosterAttached']) ? $input['checkRosterAttached'] : null;
        $renewal_seems_right = isset($input['checkRenewalSeemsRight']) ? $input['checkRenewalSeemsRight'] : null;
        $step_1_notes_log = $input['Step1_Log'];

        // Step 2 - Meetings
        $step_2_notes_log = $input['Step2_Log'];

        // Step 3 - Service
        $minimum_service_project = isset($input['checkServiceProject']) ? $input['checkServiceProject'] : null;
        $m2m_donation = isset($input['checkM2MDonation']) ? $input['checkM2MDonation'] : null;
        $step_3_notes_log = $input['Step3_Log'];

        // Step 4 - Parties
        $party_percentage = isset($input['party_percentage']) ? $input['party_percentage'] : null;
        $step_4_notes_log = $input['Step4_Log'];

        // Step 5 - Office & Operating
        $step_5_notes_log = $input['Step5_Log'];

        // Step 6 - International
        $attended_training = isset($input['checkAttendedTraining']) ? $input['checkAttendedTraining'] : null;
        $step_6_notes_log = $input['Step6_Log'];

        // Step 7 - Donations
        $step_7_notes_log = $input['Step7_Log'];

        // Step 8 - Other
        $step_8_notes_log = $input['Step8_Log'];

        // Step 9 - Financial Summary
        $total_income_less = isset($input['checkTotalIncome']) ? $input['checkTotalIncome'] : null;
        $step_9_notes_log = $input['Step9_Log'];

        // Step 10 - Reconciliation
        $beginning_balance = isset($input['checkBeginningBalance']) ? $input['checkBeginningBalance'] : null;
        $bank_statement_included = isset($input['checkBankStatementIncluded']) ? $input['checkBankStatementIncluded'] : null;
        $bank_statement_matches = isset($input['checkBankStatementMatches']) ? $input['checkBankStatementMatches'] : null;
        $post_balance = isset($input['post_balance']) ? preg_replace('/[^\d.]/', '', $input['post_balance']) : null;
        $step_10_notes_log = $input['Step10_Log'];

        // Step 11 - 990N
         $current_990N_included = isset($input['checkCurrent990NAttached']) ? $input['checkCurrent990NAttached'] : null;
         $step_11_notes_log = $input['Step11_Log'];

        // Step 12 - Questions
        $purchased_pins = isset($input['checkPurchasedPins']) ? $input['checkPurchasedPins'] : null;
        $purchased_mc_merch = isset($input['checkPurchasedMCMerch']) ? $input['checkPurchasedMCMerch'] : null;
        $offered_merch = isset($input['checkOfferedMerch']) ? $input['checkOfferedMerch'] : null;
        $bylaws_available = isset($input['checkBylawsMadeAvailable']) ? $input['checkBylawsMadeAvailable'] : null;
        $sistered_another_chapter = isset($input['checkSisteredAnotherChapter']) ? $input['checkSisteredAnotherChapter'] : null;
        $step_12_notes_log = $input['Step12_Log'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $chapter = Chapters::find($id);
        $documentsEOY = DocumentsEOY::find($id);
        $financialReportReview = FinancialReportReview::find($id);
        $farthest_step_visited_coord = max((int)$input['FurthestStep'], (int)$financialReportReview->farthest_step_visited_coord);

        DB::beginTransaction();
        try {
            $financialReportReview->reviewer_id = $reviewer_id ?? $coorId;
            $financialReportReview->step_1_notes_log = $step_1_notes_log;
            $financialReportReview->step_2_notes_log = $step_2_notes_log;
            $financialReportReview->step_3_notes_log = $step_3_notes_log;
            $financialReportReview->step_4_notes_log = $step_4_notes_log;
            $financialReportReview->step_5_notes_log = $step_5_notes_log;
            $financialReportReview->step_6_notes_log = $step_6_notes_log;
            $financialReportReview->step_7_notes_log = $step_7_notes_log;
            $financialReportReview->step_8_notes_log = $step_8_notes_log;
            $financialReportReview->step_9_notes_log = $step_9_notes_log;
            $financialReportReview->step_10_notes_log = $step_10_notes_log;
            $financialReportReview->step_11_notes_log = $step_11_notes_log;
            $financialReportReview->step_12_notes_log = $step_12_notes_log;
            $financialReportReview->roster_attached = $roster_attached;
            $financialReportReview->renewal_seems_right = $renewal_seems_right;
            $financialReportReview->minimum_service_project = $minimum_service_project;
            $financialReportReview->m2m_donation = $m2m_donation;
            $financialReportReview->party_percentage = $party_percentage;
            $financialReportReview->attended_training = $attended_training;
            $financialReportReview->beginning_balance = $beginning_balance;
            $financialReportReview->bank_statement_matches = $bank_statement_matches;
            $financialReportReview->bank_statement_included = $bank_statement_included;
            $financialReportReview->beginning_balance = $beginning_balance;
            $financialReportReview->post_balance = $post_balance;
            $financialReportReview->purchased_pins = $purchased_pins;
            $financialReportReview->purchased_mc_merch = $purchased_mc_merch;
            $financialReportReview->offered_merch = $offered_merch;
            $financialReportReview->bylaws_available = $bylaws_available;
            $financialReportReview->current_990N_included = $current_990N_included;
            $financialReportReview->total_income_less = $total_income_less;
            $financialReportReview->sistered_another_chapter = $sistered_another_chapter;
            $financialReportReview->farthest_step_visited_coord = $farthest_step_visited_coord;
            if ($submitType == 'review_complete') {
                $financialReportReview->review_complete = Carbon::now();
            }

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getFinancialReportData($chFinancialReport),
                $this->baseMailDataController->getFinancialDocumentsData($chEOYDocuments),
                $this->baseMailDataController->getFinancialReportReviewData($reviewer_email_message),
            );

            if ($financialReportReview->isDirty('reviewer_id')) {
                $newReviewerId = $financialReportReview->reviewer_id;
                $newReviewer = Coordinators::find($newReviewerId);
                $newReviewerEmail = $newReviewer->email;
                $to_email = $newReviewerEmail;
                Mail::to($to_email)
                    ->queue(new EOYReviewrAssigned($mailData));
            }

            $financialReportReview->save();

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
        $financialReportReview = FinancialReportReview::find($id);

        DB::beginTransaction();
        try {
            $documentsEOY->financial_report_received = '1';
            $documentsEOY->financial_review_complete = null;
            $documentsEOY->review_complete = null;
            $documentsEOY->save();

            $financialReportReview->review_complete = null;
            $financialReportReview->save();

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

        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox2Status = $baseQuery[CheckboxFilterEnum::REVIEWER];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $countList = count($chapterList);
        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'countList' => $countList, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'checkBox2Status' => $checkBox2Status];

        return view('coordinators.eoyreports.eoyattachments')->with($data);
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
        $chapterStatus = $baseQuery['chapterStatus'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'coorId' => $coorId, 'confId' => $confId,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport,
            'chEOYDocuments' => $chEOYDocuments, 'chapterStatus' => $chapterStatus
        ];

        return view('coordinators.eoyreports.editattachments')->with($data);
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

            return redirect()->to('/eoyreports/attachments')->with('success', 'Report attachments successfully updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/eoyreports/attachments')->with('fail', 'Something went wrong, Please try again.');
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

        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox2Status = $baseQuery[CheckboxFilterEnum::REVIEWER];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'checkBox2Status' => $checkBox2Status];

        return view('coordinators.eoyreports.eoyboundaries')->with($data);
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
        $chapterStatus = $baseQuery['chapterStatus'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'coorId' => $coorId, 'confId' => $confId,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport, 'chapterStatus' => $chapterStatus
        ];

        return view('coordinators.eoyreports.editboundaries')->with($data);
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

        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox2Status = $baseQuery[CheckboxFilterEnum::REVIEWER];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

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
        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'countList' => $countList, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status, 'checkBox2Status' => $checkBox2Status,
            'allAwards' => $allAwards, 'maxAwards' => $maxAwards, 'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status,
        ];

        return view('coordinators.eoyreports.eoyawards', $data);
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
        $chapterStatus = $baseQuery['chapterStatus'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $allAwards = $baseQuery['allAwards'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'coorId' => $coorId, 'confId' => $confId, 'chapterStatus' => $chapterStatus,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport, 'allAwards' => $allAwards,
        ];

        return view('coordinators.eoyreports.editawards')->with($data);
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
            $ChapterAwards[$i]['awards_type']     = $input['ChapterAwardsType'.$i] ?? null;
            $ChapterAwards[$i]['awards_desc']     = $input['ChapterAwardsDesc'.$i] ?? null;
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

            return to_route('eoyreports.editawards', ['id' => $id])
                ->with('success', 'EOY Information successfully updated.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return to_route('eoyreports.editawards', ['id' => $id])
                ->with('fail', 'Something went wrong, Please try again.');
        } finally {
            DB::disconnect();
        }
    }

    /**
     * View chapter award history
     */
    public function viewEOYAwardsHistory(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];

        $awardTypes = FinancialReportAwards::all()->keyBy('id');

        // Current year from the blob
        $financialReport = FinancialReport::find($id);
        $currentAwards = $financialReport->chapter_awards
            ? unserialize(base64_decode($financialReport->chapter_awards))
            : [];

        // Filter to only approved ones for display
        $currentApprovedAwards = array_filter($currentAwards, fn($a) => !empty($a['awards_approved']));

        // Historical from the history table (exclude current year)
        $chAwards = ChapterAwardHistory::with('awardtype')
            ->where('chapter_id', $id)
            ->orderBy('award_year', 'desc')
            ->orderBy('awards_type')
            ->get()
            ->groupBy('award_year');

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
                'chAwards' => $chAwards, 'currentApprovedAwards' => $currentApprovedAwards, 'awardTypes' => $awardTypes, 'confId' => $confId, 'chConfId' => $chConfId,
                'chapterStatus' => $chapterStatus
            ];

        return view('coordinators.eoyreports.awardhistory')->with($data);
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

        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox2Status = $baseQuery[CheckboxFilterEnum::REVIEWER];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $countList = count($chapterList);
        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'countList' => $countList, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'checkBox2Status' => $checkBox2Status,
        ];

        return view('coordinators.eoyreports.eoyirssubmission')->with($data);
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
        $chapterStatus = $baseQuery['chapterStatus'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'coorId' => $coorId, 'confId' => $confId, 'chapterStatus' => $chapterStatus,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport, 'chEOYDocuments' => $chEOYDocuments
        ];

        return view('coordinators.eoyreports.editirssubmission')->with($data);
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
            // return redirect()->to('/eoyreports/irssubmission')->with('success', 'Report attachments successfully updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('eoyreports.editirssubmission', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
            // return redirect()->to('/eoyreports/irssubmission')->with('success', 'Report attachments successfully updated');
        }
    }
}
