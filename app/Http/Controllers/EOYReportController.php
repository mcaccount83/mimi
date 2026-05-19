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
use App\Models\DocumentsIRS;
use App\Models\DocumentsReport;
use App\Models\FinancialReport;
use App\Models\FinancialReportAwards;
use App\Models\FinancialReportAwardsBadges;
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
    public function __construct(
        protected UserController $userController,
        protected BaseChapterController $baseChapterController,
        protected BaseMailDataController $baseMailDataController,
        protected FinancialReportController $financialReportController,
        protected PositionConditionsService $positionConditionsService,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * View the EOY Status list
     */
    public function showEOYStatus(Request $request): View
    {
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

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearStart = $reportYearOptions['reportYearStart'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($reportYearStart) {
                $query->where(function ($q) use ($reportYearStart) {
                    $q->where('start_year', '<', $reportYearStart)
                        ->orWhere(function ($q) use ($reportYearStart) {
                            $q->where('start_year', '=', $reportYearStart)
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
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('coordinators.eoyreports.eoystatus')->with($data);
    }

    /**
     * Edit the EOY Status Details
     */
    public function editEOYDetails(Request $request, int $id): View
    {
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
        $chIRSDocuments = $baseQuery['chIRSDocuments'];
        $chReportDocuments = $baseQuery['chReportDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $allAwards = $baseQuery['allAwards'];
        $reviewComplete = $baseQuery['reviewComplete'];
        $rrList = $baseQuery['rrList'];

        if ($chActiveId == ChapterStatusEnum::ACTIVE) {
            $baseBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
        } elseif ($chActiveId == ChapterStatusEnum::ZAPPED) {
            $baseBoardQuery = $this->baseChapterController->getDisbandedBoardDetails($id);
        } else {
            throw new \RuntimeException("Unexpected chapter status: {$chActiveId}");
        }

        $PresDetails = $baseBoardQuery['PresDetails'];

        $data = ['PresDetails' => $PresDetails,
            'coorId' => $coorId, 'confId' => $confId, 'allAwards' => $allAwards, 'chDocuments' => $chDocuments,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport,
            'reviewComplete' => $reviewComplete,  'rrList' => $rrList, 'chEOYDocuments' => $chEOYDocuments, 'chapterStatus' => $chapterStatus,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
            'chIRSDocuments' => $chIRSDocuments, 'chReportDocuments' => $chReportDocuments,
        ];

        return view('coordinators.eoyreports.view')->with($data);
    }

    /**
     * Update the EOY Status Details
     */
    public function updateEOYDetails(Request $request, int $id): RedirectResponse
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
        $extension_notes = $request->filled('extension_notes') ? $request->input('extension_notes') : $request->input('hid_extension_notes');
        $irs_notes = $request->filled('irs_notes') ? $request->input('irs_notes') : $request->input('hid_irs_notes');
        $reviewer_id = isset($input['ch_reportrev']) && ! empty($input['ch_reportrev']) ? $input['ch_reportrev'] : $coorId;

        $chapter = Chapters::find($id);
        $documentsEOY = DocumentsEOY::find($id);
        $documentsIRS = DocumentsIRS::find($id);
        $financialReport = FinancialReport::find($id);
        $financialReportReview = FinancialReportReview::find($id);

        DB::beginTransaction();
        try {
            $documentsEOY->new_board_submitted = $new_board_submitted;
            $documentsEOY->new_board_active = $new_board_active;
            $documentsEOY->financial_report_received = $financial_report_received;
            $documentsEOY->financial_review_complete = $financial_review_complete;
            $documentsEOY->report_extension = $report_extension;
            $documentsEOY->extension_notes = $extension_notes;

            // Only set timestamp if it doesn't already exist AND the status is not null
            $documentsEOY->report_received = $financial_report_received != null && $documentsEOY->report_received === null ? Carbon::now() : $documentsEOY->report_received;
            $documentsEOY->review_complete = $financial_review_complete != null && $documentsEOY->review_complete === null ? Carbon::now() : $documentsEOY->review_complete;
            $documentsEOY->save();

            $documentsIRS->irs_verified = $irs_verified;
            $documentsIRS->irs_notes = $irs_notes;
            $documentsIRS->save();

            // Only set timestamp if it doesn't already exist AND the status is not null
            $financialReport->submitted = $financial_report_received != null && $financialReport->submitted === null ? Carbon::now() : $financialReport->submitted;
            $financialReport->save();

            $financialReportReview->reviewer_id = $reviewer_id;

            if ($financial_report_received != null) {
                $financialReportReview->reviewer_id = $financialReport->reviewer_id ?? $coorId;
            }

            $financialReportReview->review_complete = $financial_review_complete != null && $financialReportReview->review_complete === null ? Carbon::now() : $financialReportReview->review_complete;
            $financialReportReview->save();

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

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearStart = $reportYearOptions['reportYearStart'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($reportYearStart) {
                $query->where(function ($q) use ($reportYearStart) {
                    $q->where('start_year', '<', $reportYearStart)
                        ->orWhere(function ($q) use ($reportYearStart) {
                            $q->where('start_year', '=', $reportYearStart)
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
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('coordinators.eoyreports.eoyboardreport')->with($data);

    }

    /**
     * Board Info Report Details
     */
    public function editBoardReport(Request $request, int $id)
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
        $chIRSDocuments = $baseQuery['chIRSDocuments'];
        $chReportDocuments = $baseQuery['chReportDocuments'];

        $PresDetails = $AVPDetails = $MVPDetails = $TRSDetails = $SECDetails = null;

        if ($chEOYDocuments->new_board_active != '1') {
            $baseIncomingBoardQuery = $this->baseChapterController->getIncomingBoardDetails($id);
            $PresDetails = $baseIncomingBoardQuery['PresDetails'];
            $AVPDetails  = $baseIncomingBoardQuery['AVPDetails'];
            $MVPDetails  = $baseIncomingBoardQuery['MVPDetails'];
            $TRSDetails  = $baseIncomingBoardQuery['TRSDetails'];
            $SECDetails  = $baseIncomingBoardQuery['SECDetails'];
        }
        if ($chEOYDocuments->new_board_active == '1') {
            $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($id);
            $PresDetails = $baseActiveBoardQuery['PresDetails'];
            $AVPDetails  = $baseActiveBoardQuery['AVPDetails'];
            $MVPDetails  = $baseActiveBoardQuery['MVPDetails'];
            $TRSDetails  = $baseActiveBoardQuery['TRSDetails'];
            $SECDetails  = $baseActiveBoardQuery['SECDetails'];
        }

        $allWebLinks = Website::all();  // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu
        $allCountries = $baseQuery['allCountries'];

        $data = [
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'allWebLinks' => $allWebLinks, 'allStates' => $allStates, 'allCountries' => $allCountries, 'confId' => $confId, 'chConfId' => $chConfId, 'chEOYDocuments' => $chEOYDocuments,
            'chIRSDocuments' => $chIRSDocuments, 'chReportDocuments' => $chReportDocuments,
        ];

        return view('coordinators.eoyreports.editboardreport')->with($data);
    }

    public function updateEOYBoardReport(Request $request, int $chapter_id): RedirectResponse
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

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearStart = $reportYearOptions['reportYearStart'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($reportYearStart) {
                $query->where(function ($q) use ($reportYearStart) {
                    $q->where('start_year', '<', $reportYearStart)
                        ->orWhere(function ($q) use ($reportYearStart) {
                            $q->where('start_year', '=', $reportYearStart)
                                ->where('start_month_id', '<', 7); // July is month 7
                        });
                });
            })
            ->get();

        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox2Status = $baseQuery[CheckboxFilterEnum::REVIEWER];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];

        $data = ['chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status, 'checkBox2Status' => $checkBox2Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc,
        ];

        return view('coordinators.eoyreports.eoyfinancialreport')->with($data);
    }

    /**
     * Financial Report for Coordinator side for Reviewing of Chapters
     */
    public function editFinancialReview(Request $request, int $id): View
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
        $chIRSDocuments = $baseQuery['chIRSDocuments'];
        $chReportDocuments = $baseQuery['chReportDocuments'];
        $allAwards = $baseQuery['allAwards'];
        // $submitted = $baseQuery['submitted'];
        $rrList = $baseQuery['rrList'];

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription, 'chFinancialReportReview' => $chFinancialReportReview,
            'chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'rrList' => $rrList, 'allAwards' => $allAwards, 'chDocuments' => $chDocuments, 'chEOYDocuments' => $chEOYDocuments,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName' => $userConfName, 'userConfDesc' => $userConfDesc, 'confId' => $confId, 'chConfId' => $chConfId,
            'chIRSDocuments' => $chIRSDocuments, 'chReportDocuments' => $chReportDocuments,
        ];

        return view('coordinators.eoyreports.editfinancialreview')->with($data);
    }

    public function saveAccordionFields(FinancialReportReview $financialReportReview, array $input)
    {
        // CHAPTER DUES
        $financialReportReview->roster_attached = $input['checkRosterAttached'] ?? null;
        $financialReportReview->renewal_seems_right = $input['checkRenewalSeemsRight'] ?? null;
        $financialReportReview->step_1_notes_log = $input['Step1_Log'] ?? null;

        // MONTHLY MEETING EXPENSES
        $financialReportReview->step_2_notes_log = $input['Step2_Log'] ?? null;

        // SERVICE PROJECTS
        $financialReportReview->minimum_service_project = $input['checkServiceProject'] ?? null;
        $financialReportReview->m2m_donation = $input['checkM2MDonation'] ?? null;
        $financialReportReview->step_3_notes_log = $input['Step3_Log'] ?? null;

        // PARTY EXPENSES
        $financialReportReview->party_percentage = $input['checkPartyPercentage'] ?? null;
        $financialReportReview->step_4_notes_log = $input['Step4_Log'] ?? null;

        // OFFICE & OPERATING EXPENSES
        $financialReportReview->step_5_notes_log = $input['Step5_Log'] ?? null;

        // INTERNATIONAL EVENTS & RE-REGISTRATION
        $financialReportReview->attended_training = $input['checkAttendedTraining'] ?? null;
        $financialReportReview->step_6_notes_log = $input['Step6_Log'] ?? null;

        // DONATIONS TO CHAPTER
        $financialReportReview->step_7_notes_log = $input['Step7_Log'] ?? null;

        // OTHER INCOME & EXPENSES
        $financialReportReview->step_8_notes_log = $input['Step8_Log'] ?? null;

        // FINANCIAL SUMMARY
        $financialReportReview->total_income_less = $input['checkTotalIncome'] ?? null;
        $financialReportReview->step_9_notes_log = $input['Step9_Log'] ?? null;

        // BANK RECONCILLIATION
        $financialReportReview->beginning_balance = $input['checkBeginningBalance'] ?? null;
        $financialReportReview->bank_statement_included = $input['checkBankStatementIncluded'] ?? null;
        $financialReportReview->bank_statement_matches = $input['checkBankStatementMatches'] ?? null;
        $financialReportReview->post_balance = isset($input['post_balance']) ? preg_replace('/[^\d.]/', '', $input['post_balance']) : null;
        $financialReportReview->step_10_notes_log = $input['Step10_Log'] ?? null;

        // 990 IRS FILING
        $financialReportReview->current_990N_included = $input['checkCurrent990NAttached'] ?? null;
        $financialReportReview->step_11_notes_log = $input['Step11_Log'] ?? null;

        // CHAPTER QUESTIONS
        $financialReportReview->purchased_pins = $input['checkPurchasedPins'] ?? null;
        $financialReportReview->purchased_mc_merch = $input['checkPurchasedMCMerch'] ?? null;
        $financialReportReview->offered_merch = $input['checkOfferedMerch'] ?? null;
        $financialReportReview->bylaws_available = $input['checkBylawsMadeAvailable'] ?? null;
        $financialReportReview->sistered_another_chapter = $input['checkSisteredAnotherChapter'] ?? null;
        $financialReportReview->step_12_notes_log = $input['Step12_Log'] ?? null;
    }

    /**
     * Save Financial Report Review
     */
    public function updateEOYFinancialReport(Request $request, int $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $userName = $user['userName'];
        $userEmail = $user['userEmail'];
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $input = $request->all();
        $reviewer_id = isset($input['AssignedReviewer']) && ! empty($input['AssignedReviewer']) ? $input['AssignedReviewer'] : $coorId;
        $reportReceived = $input['submitted'];
        $submitType = $input['submit_type'];
        $reviewer_email_message = $input['reviewer_email_message'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chIRSDocuments = $baseQuery['chIRSDocuments'];
        $chReportDocuments = $baseQuery['chReportDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $chapter = Chapters::find($id);
        $documentsEOY = DocumentsEOY::find($id);
        $financialReportReview = FinancialReportReview::find($id);
        $farthest_step_visited_coord = max((int)$input['FurthestStep'], (int)$financialReportReview->farthest_step_visited_coord);

        DB::beginTransaction();
        try {
            $this->saveAccordionFields($financialReportReview, $input);
            $financialReportReview->reviewer_id = $reviewer_id ?? $coorId;
            $financialReportReview->farthest_step_visited_coord = $farthest_step_visited_coord;

            if ($submitType == 'review_complete') {
                $financialReportReview->review_complete = Carbon::now();
            }

             $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getFinancialReportData($chFinancialReport),
                $this->baseMailDataController->getFinancialDocumentsData($chDocuments, $chEOYDocuments, $chIRSDocuments, $chReportDocuments),
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
    public function updateUnsubmit(Request $request, int $id): RedirectResponse
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
    public function updateUnsubmitFinal(Request $request, int $id): RedirectResponse
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
    public function updateClearReview(Request $request, int $id): RedirectResponse
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
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearStart = $reportYearOptions['reportYearStart'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($reportYearStart) {
                $query->where(function ($q) use ($reportYearStart) {
                    $q->where('start_year', '<', $reportYearStart)
                        ->orWhere(function ($q) use ($reportYearStart) {
                            $q->where('start_year', '=', $reportYearStart)
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
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'checkBox2Status' => $checkBox2Status];

        return view('coordinators.eoyreports.eoyattachments')->with($data);
    }

    /**
     * View the Attachments Details
     */
    public function editEOYAttachments(Request $request, int $id): View
    {
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
        $chIRSDocuments = $baseQuery['chIRSDocuments'];
        $chReportDocuments = $baseQuery['chReportDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['coorId' => $coorId, 'confId' => $confId,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport,
            'chEOYDocuments' => $chEOYDocuments, 'chapterStatus' => $chapterStatus, 'chIRSDocuments' => $chIRSDocuments, 'chReportDocuments' => $chReportDocuments,
        ];

        return view('coordinators.eoyreports.editattachments')->with($data);
    }

    /**
     * Update the Attachments Details
     */
    public function updateEOYAttachments(Request $request, int $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($id);
        $documentsIRS = DocumentsIRS::find($id);

        DB::beginTransaction();
        try {
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $documentsIRS->irs_verified = (int) $request->has('irs_verified');
            $documentsIRS->irs_notes = $request->input('irs_notes');
            $documentsIRS->save();

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
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearStart = $reportYearOptions['reportYearStart'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($reportYearStart) {
                $query->where(function ($q) use ($reportYearStart) {
                    $q->where('start_year', '<', $reportYearStart)
                        ->orWhere(function ($q) use ($reportYearStart) {
                            $q->where('start_year', '=', $reportYearStart)
                                ->where('start_month_id', '<', 7); // July is month 7
                        });
                });
            })
            ->get();

        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox2Status = $baseQuery[CheckboxFilterEnum::REVIEWER];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];
        $checkBox52Status = $baseQuery[CheckboxFilterEnum::INTERNATIONALEOY];

        if ($checkBox3Status || $checkBox51Status) {
            $chapterList = $baseQuery['query']
                ->get();
        } else {
            $chapterList = $baseQuery['query']
                ->where('boundary_issue_notes', '!=', null)
                ->get();
        }

        $data = ['chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status, 'checkBox52Status' => $checkBox52Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'checkBox2Status' => $checkBox2Status];

        return view('coordinators.eoyreports.eoyboundaries')->with($data);
    }

    /**
     * View the EOY Boundary Details
     */
    public function editEOYBoundaries(Request $request, int $id): View
    {
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
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chIRSDocuments = $baseQuery['chIRSDocuments'];
        $chReportDocuments = $baseQuery['chReportDocuments'];

        $data = ['coorId' => $coorId, 'confId' => $confId, 'chEOYDocuments' => $chEOYDocuments,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport, 'chapterStatus' => $chapterStatus,
            'chIRSDocuments' => $chIRSDocuments, 'chReportDocuments' => $chReportDocuments,
        ];

        return view('coordinators.eoyreports.editboundaries')->with($data);
    }

    /**
     * Update the EOY Boundary Details
     */
    public function updateEOYBoundaries(Request $request, int $id): RedirectResponse
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

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearStart = $reportYearOptions['reportYearStart'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($reportYearStart) {
                $query->where(function ($q) use ($reportYearStart) {
                    $q->where('start_year', '<', $reportYearStart)
                        ->orWhere(function ($q) use ($reportYearStart) {
                            $q->where('start_year', '=', $reportYearStart)
                                ->where('start_month_id', '<', 7); // July is month 7
                        });
                });
            })
            ->get();

        $checkBox1Status = $baseQuery[CheckboxFilterEnum::PC_DIRECT];
        $checkBox2Status = $baseQuery[CheckboxFilterEnum::REVIEWER];
        $checkBox3Status = $baseQuery[CheckboxFilterEnum::CONFERENCE_REGION];
        $checkBox51Status = $baseQuery[CheckboxFilterEnum::INTERNATIONAL];
        $checkBox52Status = $baseQuery[CheckboxFilterEnum::INTERNATIONALEOY];

        $allAwards = FinancialReportAwards::all();

        $hasAnyAwards = false;
        $actualMaxAwards = 0;

        foreach ($chapterList as $list) {
            if (isset($list->financialReport->chapter_awards)) {
                $awards = unserialize(base64_decode($list->financialReport->chapter_awards));
                if ($awards) {
                    $validAwards = collect($awards)->filter(fn($award) => !empty($award['awards_type']))->count();
                    if ($validAwards > 0) {
                        $hasAnyAwards = true;
                        $actualMaxAwards = max($actualMaxAwards, $validAwards);
                    }
                }
            }
        }

        if ($checkBox3Status || $checkBox51Status) {
            $chapterList = $baseQuery['query']->get();
        } else {
            $chapterList = $baseQuery['query']
                ->whereHas('financialReport', fn($q) => $q->whereNotNull('chapter_awards'))
                ->get();
        }

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status, 'checkBox2Status' => $checkBox2Status,
            'allAwards' => $allAwards, 'hasAnyAwards' => $hasAnyAwards, 'actualMaxAwards' => $actualMaxAwards, 'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'checkBox52Status' => $checkBox52Status,
            'userName' => $userName, 'userPosition' => $userPosition, 'userConfName'  => $userConfName, 'userConfDesc' => $userConfDesc
        ];

        return view('coordinators.eoyreports.eoyawards', $data);
    }

    /**
     * View the EOY Award Details
     */
    public function editEOYAwards(Request $request, int $id): View
    {
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
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chIRSDocuments = $baseQuery['chIRSDocuments'];
        $chReportDocuments = $baseQuery['chReportDocuments'];
        $allAwards = $baseQuery['allAwards'];

        $data = ['coorId' => $coorId, 'confId' => $confId, 'chapterStatus' => $chapterStatus, 'chEOYDocuments' => $chEOYDocuments,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport, 'allAwards' => $allAwards,
            'chIRSDocuments' => $chIRSDocuments, 'chReportDocuments' => $chReportDocuments,
        ];

        return view('coordinators.eoyreports.editawards')->with($data);
    }

    /**
     * Update the EOY Award Details
     */
    public function updateEOYAwards(Request $request, int $id): RedirectResponse
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
    public function viewEOYAwardsHistory(Request $request, int $id): View
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
        $chFinancialReport = $baseQuery['chFinancialReport'];
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
        $chAwards = ChapterAwardHistory::with('awardtype', 'fiscalYear')
            ->where('chapter_id', $id)
            ->orderBy('report_year_id', 'desc')
            ->orderBy('awards_type')
            ->get()
            ->groupBy('report_year_id');

        $awardBadges = FinancialReportAwardsBadges::with(['fiscalYear', 'eoyAward'])->get();
        $badgeLookup = $awardBadges->keyBy(fn($b) => $b->report_year_id . '_' . $b->eoy_award_id);

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
                'chAwards' => $chAwards, 'currentApprovedAwards' => $currentApprovedAwards, 'awardTypes' => $awardTypes, 'confId' => $confId, 'chConfId' => $chConfId,
                'chapterStatus' => $chapterStatus, 'badgeLookup' => $badgeLookup, 'chFinancialReport' => $chFinancialReport
            ];

        return view('coordinators.eoyreports.awardhistory')->with($data);
    }

    /**
     * View the 990N Filing Details
     */
    public function showIRSSubmission(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearStart = $reportYearOptions['reportYearStart'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($reportYearStart) {
                $query->where(function ($q) use ($reportYearStart) {
                    $q->where('start_year', '<', $reportYearStart)
                        ->orWhere(function ($q) use ($reportYearStart) {
                            $q->where('start_year', '=', $reportYearStart)
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
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBox1Status' => $checkBox1Status,
            'checkBox3Status' => $checkBox3Status, 'checkBox51Status' => $checkBox51Status, 'checkBox2Status' => $checkBox2Status,
        ];

        return view('coordinators.eoyreports.eoyirssubmission')->with($data);
    }

    /**
     * View the 990N Filing Details
     */
    public function editIRSSubmission(Request $request, int $id): View
    {
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
        $chIRSDocuments = $baseQuery['chIRSDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['coorId' => $coorId, 'confId' => $confId, 'chapterStatus' => $chapterStatus, 'chEOYDocuments' => $chEOYDocuments,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chActiveId' => $chActiveId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport, 'chIRSDocuments' => $chIRSDocuments
        ];

        return view('coordinators.eoyreports.editirssubmission')->with($data);
    }

    /**
     * Update the 990N Filing Details
     */
    public function updateIRSSubmission(Request $request, int $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($id);
        $documentsIRS = DocumentsIRS::find($id);

        DB::beginTransaction();
        try {
            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            // Correct way to handle checkboxes
            $documentsIRS->irs_verified = $request->filled('irs_verified') ? 1 : 0;
            $documentsIRS->irs_issues = $request->filled('irs_issues') ? 1 : 0;
            $documentsIRS->irs_wrongdate = $request->filled('irs_wrongdate') ? 1 : 0;
            $documentsIRS->irs_notfound = $request->filled('irs_notfound') ? 1 : 0;
            $documentsIRS->irs_filedwrong = $request->filled('irs_filedwrong') ? 1 : 0;
            $documentsIRS->irs_notified = $request->filled('irs_notified') ? 1 : 0;
            $documentsIRS->irs_notes = $request->input('irs_notes');
            $documentsIRS->save();

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
