<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Mail\EOYElectionReportReminder;
use App\Mail\EOYFinancialReportReminder;
use App\Mail\EOYLateReportReminder;
use App\Mail\EOYReviewrAssigned;
use App\Models\Boards;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\Documents;
use App\Models\FinancialReport;
use App\Models\FinancialReportAwards;
use App\Models\IncomingBoard;
use App\Models\State;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EOYReportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseMailDataController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseMailDataController $baseMailDataController)
    {
        

        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseMailDataController = $baseMailDataController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /* /Custom Helpers/ */
    // $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);
    // $displayEOY = getEOYDisplay();

    /* / Base Chapter Controller / */
    //  $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    //  $this->baseChapterController->getChapterDetails($chId)

    /**
     * View the EOY Report Title
     */
    public function getPageTitle(Request $request)
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $conditions = getPositionConditions($positionId, $secPositionId);
        $adminReportCondition = $conditions['adminReportCondition'];
        $eoyTestCondition = $conditions['eoyTestCondition'];

        $displayEOY = getEOYDisplay();
        $displayTESTING = $displayEOY['displayTESTING'];
        $displayLIVE = $displayEOY['displayLIVE'];

        $titles = [
            'eoy_reports' => 'End of Year Reports',
            'eoy_details' => 'EOY Details',
        ];

        if ($adminReportCondition && ! $displayTESTING && ! $displayLIVE) {
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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox2Status = $baseQuery['checkBox2Status'];

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status,
            'title' => $title, 'breadcrumb' => $breadcrumb,
        ];

        return view('eoyreports.eoystatus')->with($data);
    }

    /**
     * Auto Send EOY Report Status Reminder
     */
    public function sendEOYStatusReminder(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->whereHas('documents', function ($query) {
                $query->where('report_extension', '0')
                    ->orWhereNull('report_extension');
            })
            ->whereHas('documents', function ($query) {
                $query->where('new_board_submitted', '0')
                    ->orWhereNull('new_board_submitted')
                    ->orWhere('financial_report_received', '0')
                    ->orWhereNull('financial_report_received');
            })
            ->get();

        if ($chapterList->isEmpty()) {
            return redirect()->back()->with('info', 'There are no Chapters with Reports Due.');
        }

        $chapterIds = [];
        $chapterEmails = [];
        $coordinatorEmails = [];
        $mailData = [];

        foreach ($chapterList as $chapter) {
            $chapterIds[] = $chapter->id;

            if ($chapter->name) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chDocuments = $emailDetails['chDocuments'];
                $chFinancialReport = $emailDetails['chFinancialReport'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            $mailData[$chDetails->name] = array_merge(
                $this->baseMailDataController->getChapterBasicData($chDetails, $stateShortName),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport),
            );

        }

        foreach ($mailData as $chapterName => $data) {
            if (! empty($chapterName)) {
                Mail::to($chapterEmails[$chapterName] ?? [])
                    ->cc($coordinatorEmails[$chapterName] ?? [])
                    ->queue(new EOYLateReportReminder($data));
            }
        }

        try {
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/eoy/status')->with('success', 'EOY Late Notices have been successfully sent.');

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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chIsActive = $baseQuery['chIsActive'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $allAwards = $baseQuery['allAwards'];
        $reviewComplete = $baseQuery['reviewComplete'];
        $rrList = $baseQuery['rrList'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb,
            'coorId' => $coorId, 'confId' => $confId, 'allAwards' => $allAwards, 'chDocuments' => $chDocuments,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chIsActive' => $chIsActive, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport,
            'reviewComplete' => $reviewComplete,  'rrList' => $rrList,
        ];

        return view('eoyreports.view')->with($data);
    }

    /**
     * Update the EOY Status Details
     */
    public function updateEOYDetails(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $input = $request->all();
        $new_board_submitted = ! isset($input['new_board_submitted']) ? null : ($input['new_board_submitted'] === 'on' ? 1 : 0);
        $new_board_active = ! isset($input['new_board_active']) ? null : ($input['new_board_active'] === 'on' ? 1 : 0);
        $financial_report_received = ! isset($input['financial_report_received']) ? null : ($input['financial_report_received'] === 'on' ? 1 : 0);
        $financial_review_complete = ! isset($input['financial_review_complete']) ? null : ($input['financial_review_complete'] === 'on' ? 1 : 0);
        $report_extension = ! isset($input['report_extension']) ? null : ($input['report_extension'] === 'on' ? 1 : 0);
        $irs_verified = ! isset($input['irs_verified']) ? null : ($input['irs_verified'] === 'on' ? 1 : 0);
        $extension_notes = $input['extension_notes'];
        $irs_notes = $input['irs_notes'];
        $reviewer_id = isset($input['ch_reportrev']) && ! empty($input['ch_reportrev']) ? $input['ch_reportrev'] : $userId;

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);
        $financialReport = FinancialReport::find($id);

        DB::beginTransaction();
        try {
            $documents->new_board_submitted = $new_board_submitted;
            $documents->new_board_active = $new_board_active;
            $documents->financial_report_received = $financial_report_received;
            $documents->financial_review_complete = $financial_review_complete;
            $documents->report_extension = $report_extension;
            $documents->extension_notes = $extension_notes;
            $documents->irs_verified = $irs_verified;
            $documents->irs_notes = $irs_notes;
            $documents->report_received = $financial_report_received != null ? date('Y-m-d H:i:s') : null;
            $documents->review_complete = $financial_review_complete != null ? date('Y-m-d H:i:s') : null;
            $documents->save();

            $financialReport->reviewer_id = $reviewer_id;
            $financialReport->submitted = $financial_report_received != null ? date('Y-m-d H:i:s') : null;
            if ($financial_report_received != null) {
                $financialReport->reviewer_id = $financialReport->reviewer_id ?? $userId;
            }
            $financialReport->review_complete = $financial_review_complete != null ? date('Y-m-d H:i:s') : null;
            $financialReport->save();

            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('eoyreports.view', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        }

        return to_route('eoyreports.view', ['id' => $id])->with('success', 'EOY Information successfully updated.');
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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];
        $lastUpdatedBy = $user['user_name'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox2Status = $baseQuery['checkBox2Status'];

        $activationStatuses = [];

        if (isset($_GET['board'])) {
            foreach ($chapterList as $chapter) {
                if ($chapter->documents->new_board_submitted && ! $chapter->documents->new_board_active) {
                    $status = $this->activateBoard($chapter->id, $lastUpdatedBy);
                    $activationStatuses[$chapter->id] = $status;
                }
            }

            $successfulActivations = array_filter($activationStatuses, function ($status) {
                return $status === 'success';
            });

            if (count($successfulActivations) == count($activationStatuses)) {
                return redirect()->to('/eoy/boardreport')->with('success', 'All Board Info has been successfully activated');
            } elseif (count($successfulActivations) > 0) {
                return redirect()->to('/eoy/boardreport')->with('warning', 'Some boards were activated, some failed');
            } elseif (count($activationStatuses) == 0) {
                return redirect()->to('/eoy/boardreport')->with('info', 'No Incoming Board Members for Activation');
            } else {
                return redirect()->to('/eoy/boardreport')->with('fail', 'Board activation failed');
            }
            exit;
        }

        $countList = count($chapterList);
        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

        return view('eoyreports.eoyboardreport')->with($data);
    }

    /**
     * Board Election Report Reminder Auto Send
     */
    public function sendEOYBoardReportReminder(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->whereHas('documents', function ($query) {
                $query->where('report_extension', '0')
                    ->orWhereNull('report_extension');
            })
            ->whereHas('documents', function ($query) {
                $query->where('new_board_submitted', '0')
                    ->orWhereNull('new_board_submitted');
            })
            ->get();

        if ($chapterList->isEmpty()) {
            return redirect()->back()->with('info', 'There are no Chapters with Board Reports Due.');
        }

        $chapterIds = [];
        $chapterEmails = [];
        $coordinatorEmails = [];
        $mailData = [];

        foreach ($chapterList as $chapter) {
            $chapterIds[] = $chapter->id;

            if ($chapter->name) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chDocuments = $emailDetails['chDocuments'];
                $chFinancialReport = $emailDetails['chFinancialReport'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            $mailData[$chDetails->name] = array_merge(
                $this->baseMailDataController->getChapterBasicData($chDetails, $stateShortName),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport),
            );

        }

        foreach ($mailData as $chapterName => $data) {
            if (! empty($chapterName)) {
                Mail::to($chapterEmails[$chapterName] ?? [])
                    ->cc($coordinatorEmails[$chapterName] ?? [])
                    ->queue(new EOYElectionReportReminder($data));
            }
        }

        try {
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/eoy/boardreport')->with('success', 'Board Election Reminders have been successfully sent.');

    }

    /**
     * Activate New Board Members
     */
    public function activateBoard(Request $request, $chapter_id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $status = 'fail'; // Default to 'fail'

        $incomingBoardDetails = IncomingBoard::where('chapter_id', $chapter_id)->get();

        if ($incomingBoardDetails && count($incomingBoardDetails) > 0) {
            DB::beginTransaction();
            try {
                $boardDetails = Boards::where('chapter_id', $chapter_id)->get();

                if ($boardDetails && count($boardDetails) > 0) {
                    $borDetails = Boards::with('user')->where('chapter_id', $chapter_id)->get();
                    foreach ($borDetails as $record) {
                        $user_id = $record->user_id;
                        $userDetails = User::find($user_id);

                        $userDetails->user_type = 'outgoing';
                        $userDetails->updated_at = now();
                        $userDetails->save();
                    }

                    Boards::where('chapter_id', $chapter_id)->delete();
                }

                foreach ($incomingBoardDetails as $incomingRecord) {
                    $existingUser = User::where('email', $incomingRecord->email)->first();
                    if ($existingUser) {
                        $existingUser->first_name = $incomingRecord->first_name;
                        $existingUser->last_name = $incomingRecord->last_name;
                        $existingUser->email = $incomingRecord->email;
                        $existingUser->user_type = 'board';
                        $existingUser->updated_at = now();
                        $existingUser->save();
                        $userId = $existingUser->id;

                    } else {
                        $newUser = User::create([  // Create user details if new
                            'first_name' => $incomingRecord->first_name,
                            'last_name' => $incomingRecord->last_name,
                            'email' => $incomingRecord->email,
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1,
                        ]);
                        $userId = $newUser->id;

                    }

                    Boards::create([  // Create board details if new
                        'user_id' => $userId,
                        'first_name' => $incomingRecord->first_name,
                        'last_name' => $incomingRecord->last_name,
                        'email' => $incomingRecord->email,
                        'board_position_id' => $incomingRecord->board_position_id,
                        'chapter_id' => $chapter_id,
                        'street_address' => $incomingRecord->street_address,
                        'city' => $incomingRecord->city,
                        'state' => $incomingRecord->state,
                        'zip' => $incomingRecord->zip,
                        'country' => 'USA',
                        'phone' => $incomingRecord->phone,
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
                        'is_active' => 1,
                    ]);
                }

                $documents = Documents::find($chapter_id);
                $documents->new_board_active = 1;
                $documents->save();

                IncomingBoard::where('chapter_id', $chapter_id)->delete();

                DB::commit();
                $status = 'success'; // Set status to success if everything goes well
            } catch (\Exception $e) {
                DB::rollback();
                $status = 'fail'; // Set status to fail if an exception occurs
            }
        }

        return redirect()->back()->with('status', $status);
    }

    /**
     * Board Info Report Details
     */
    public function editBoardReport(Request $request, $id)
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];

        // $boards = IncomingBoard::find($id);
        $boards = IncomingBoard::where('chapter_id', $id)->get();
        if ($boards && $boards->count() > 0) {
            $bdDetails = $boards->groupBy('board_position_id');
        } else {
            $bdDetails = collect(); // Empty collection
        }

        // $bdDetails = $boards->groupBy('board_position_id');
        $defaultBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state' => '', 'user_id' => ''];

        $PresDetails = $bdDetails->get(1, collect([$defaultBoardMember]))->first(); // President
        $AVPDetails = $bdDetails->get(2, collect([$defaultBoardMember]))->first(); // AVP
        $MVPDetails = $bdDetails->get(3, collect([$defaultBoardMember]))->first(); // MVP
        $TRSDetails = $bdDetails->get(4, collect([$defaultBoardMember]))->first(); // Treasurer
        $SECDetails = $bdDetails->get(5, collect([$defaultBoardMember]))->first(); // Secretary

        $allWebLinks = Website::all();  // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu

        // Check if the board activation button was clicked
        if ($request->has('board') && $request->input('board') === 'active') {
            $activationStatuses = [];

            // Only activate if the board is submitted and not yet active
            if ($chDetails->documents->new_board_submitted && ! $chDetails->documents->new_board_active) {
                $status = $this->activateBoard($chDetails->id, $lastUpdatedBy);
                $activationStatuses[$chDetails->id] = $status;
            }

            $successfulActivations = array_filter($activationStatuses, function ($status) {
                return $status === 'success';
            });

            if (count($successfulActivations) > 0) {
                return redirect()->to('/eoy/boardreport')->with('success', 'Board activated successfully');
            } else {
                return redirect()->to('/eoy/boardreport')->with('fail', 'Board activation failed');
            }
        }

        $data = [
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'allWebLinks' => $allWebLinks, 'allStates' => $allStates,
        ];

        return view('eoyreports.editboardreport')->with($data);
    }

    /**
     * Update Board Report (store)
     */
    public function updateEOYBoardReport(Request $request, $chapter_id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        if ($request->input('submit_type') == 'activate_board') {
            $status = $this->activateBoard($chapter_id, $lastUpdatedBy);
            if ($status == 'success') {
                return redirect()->to('/eoy/boardreport')->with('success', 'Board Info has been successfully activated');
            } elseif ($status == 'fail') {
                return redirect()->to('/eoy/boardreport')->with('fail', 'Something went wrong, Please try again.');
            }
        }

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

        $documents = Documents::find($chapter_id);

        DB::beginTransaction();
        try {
            $chapter->inquiries_contact = $request->input('InquiriesContact');
            $chapter->boundary_issues = $request->input('BoundaryStatus');
            $chapter->boundary_issue_notes = $issue_note;
            $chapter->website_url = $website;
            $chapter->website_status = $ch_webstatus;
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            $documents->new_board_submitted = 1;
            $documents->save();

            // President Info
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $PREDetails = IncomingBoard::where('chapter_id', $chId)
                    ->where('board_position_id', '1')
                    ->get();
                $presId = $request->input('presID');
                if (count($PREDetails) != 0) {
                    IncomingBoard::where('id', $presId)
                        ->update([   // Update board details
                            'first_name' => $request->input('ch_pre_fname'),
                            'last_name' => $request->input('ch_pre_lname'),
                            'email' => $request->input('ch_pre_email'),
                            'street_address' => $request->input('ch_pre_street'),
                            'city' => $request->input('ch_pre_city'),
                            'state' => $request->input('ch_pre_state'),
                            'zip' => $request->input('ch_pre_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_pre_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                } else {
                    IncomingBoard::create([  // Create board details if new
                        'chapter_id' => $chId,
                        'board_position_id' => '1',
                        'first_name' => $request->input('ch_pre_fname'),
                        'last_name' => $request->input('ch_pre_lname'),
                        'email' => $request->input('ch_pre_email'),
                        'street_address' => $request->input('ch_pre_street'),
                        'city' => $request->input('ch_pre_city'),
                        'state' => $request->input('ch_pre_state'),
                        'zip' => $request->input('ch_pre_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_pre_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
                    ]);
                }
            }

            // AVP Info
            $AVPDetails = IncomingBoard::where('chapter_id', $chId)
                ->where('board_position_id', '2')
                ->get();

            if (count($AVPDetails) > 0) {
                if ($request->input('AVPVacant') == 'on') {
                    IncomingBoard::where('chapter_id', $chId)
                        ->where('board_position_id', '2')
                        ->delete();  // Delete board member if now Vacant
                } else {
                    $AVPId = $request->input('avpID');
                    IncomingBoard::where('id', $AVPId)
                        ->update([   // Update board details if already exists
                            'first_name' => $request->input('ch_avp_fname'),
                            'last_name' => $request->input('ch_avp_lname'),
                            'email' => $request->input('ch_avp_email'),
                            'street_address' => $request->input('ch_avp_street'),
                            'city' => $request->input('ch_avp_city'),
                            'state' => $request->input('ch_avp_state'),
                            'zip' => $request->input('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                }
            } else {
                if ($request->input('AVPVacant') != 'on') {
                    IncomingBoard::create([  // Create board details if new
                        'chapter_id' => $chId,
                        'board_position_id' => '2',
                        'first_name' => $request->input('ch_avp_fname'),
                        'last_name' => $request->input('ch_avp_lname'),
                        'email' => $request->input('ch_avp_email'),
                        'street_address' => $request->input('ch_avp_street'),
                        'city' => $request->input('ch_avp_city'),
                        'state' => $request->input('ch_avp_state'),
                        'zip' => $request->input('ch_avp_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_avp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
                    ]);
                }
            }

            // MVP Info
            $MVPDetails = IncomingBoard::where('chapter_id', $chId)
                ->where('board_position_id', '3')
                ->get();

            if (count($MVPDetails) > 0) {
                if ($request->input('MVPVacant') == 'on') {
                    IncomingBoard::where('chapter_id', $chId)
                        ->where('board_position_id', '3')
                        ->delete();  // Delete board member if now Vacant
                } else {
                    $MVPId = $request->input('mvpID');
                    IncomingBoard::where('id', $MVPId)
                        ->update([   // Update board details if already exists
                            'first_name' => $request->input('ch_mvp_fname'),
                            'last_name' => $request->input('ch_mvp_lname'),
                            'email' => $request->input('ch_mvp_email'),
                            'street_address' => $request->input('ch_mvp_street'),
                            'city' => $request->input('ch_mvp_city'),
                            'state' => $request->input('ch_mvp_state'),
                            'zip' => $request->input('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                }
            } else {
                if ($request->input('MVPVacant') != 'on') {
                    IncomingBoard::create([  // Create board details if new
                        'chapter_id' => $chId,
                        'board_position_id' => '3',
                        'first_name' => $request->input('ch_mvp_fname'),
                        'last_name' => $request->input('ch_mvp_lname'),
                        'email' => $request->input('ch_mvp_email'),
                        'street_address' => $request->input('ch_mvp_street'),
                        'city' => $request->input('ch_mvp_city'),
                        'state' => $request->input('ch_mvp_state'),
                        'zip' => $request->input('ch_mvp_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_mvp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
                    ]);
                }
            }

            // TRS Info
            $TRSDetails = IncomingBoard::where('chapter_id', $chId)
                ->where('board_position_id', '4')
                ->get();

            if (count($TRSDetails) > 0) {
                if ($request->input('TreasVacant') == 'on') {
                    IncomingBoard::where('chapter_id', $chId)
                        ->where('board_position_id', '4')
                        ->delete();  // Delete board member if now Vacant
                } else {
                    $TRSId = $request->input('trsID');
                    IncomingBoard::where('id', $TRSId)
                        ->update([   // Update board details if already exists
                            'first_name' => $request->input('ch_trs_fname'),
                            'last_name' => $request->input('ch_trs_lname'),
                            'email' => $request->input('ch_trs_email'),
                            'street_address' => $request->input('ch_trs_street'),
                            'city' => $request->input('ch_trs_city'),
                            'state' => $request->input('ch_trs_state'),
                            'zip' => $request->input('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                }
            } else {
                if ($request->input('TreasVacant') != 'on') {
                    IncomingBoard::create([  // Create board details if new
                        'chapter_id' => $chId,
                        'board_position_id' => '4',
                        'first_name' => $request->input('ch_trs_fname'),
                        'last_name' => $request->input('ch_trs_lname'),
                        'email' => $request->input('ch_trs_email'),
                        'street_address' => $request->input('ch_trs_street'),
                        'city' => $request->input('ch_trs_city'),
                        'state' => $request->input('ch_trs_state'),
                        'zip' => $request->input('ch_trs_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_trs_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
                    ]);
                }
            }

            // SEC Info
            $SECDetails = IncomingBoard::where('chapter_id', $chId)
                ->where('board_position_id', '5')
                ->get();

            if (count($SECDetails) > 0) {
                if ($request->input('SecVacant') == 'on') {
                    IncomingBoard::where('chapter_id', $chId)
                        ->where('board_position_id', '5')
                        ->delete();  // Delete board member if now Vacant
                } else {
                    $SECId = $request->input('secID');
                    IncomingBoard::where('id', $SECId)
                        ->update([   // Update board details if already exists
                            'first_name' => $request->input('ch_sec_fname'),
                            'last_name' => $request->input('ch_sec_lname'),
                            'email' => $request->input('ch_sec_email'),
                            'street_address' => $request->input('ch_sec_street'),
                            'city' => $request->input('ch_sec_city'),
                            'state' => $request->input('ch_sec_state'),
                            'zip' => $request->input('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                }
            } else {
                if ($request->input('SecVacant') != 'on') {
                    IncomingBoard::create([  // Create board details if new
                        'chapter_id' => $chId,
                        'board_position_id' => '5',
                        'first_name' => $request->input('ch_sec_fname'),
                        'last_name' => $request->input('ch_sec_lname'),
                        'email' => $request->input('ch_sec_email'),
                        'street_address' => $request->input('ch_sec_street'),
                        'city' => $request->input('ch_sec_city'),
                        'state' => $request->input('ch_sec_state'),
                        'zip' => $request->input('ch_sec_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_sec_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->back()->with('success', 'Board Info has been Saved');
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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox2Status = $baseQuery['checkBox2Status'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

        return view('eoyreports.eoyfinancialreport')->with($data);
    }

    /**
     * Financial Report Reminder Auto Send
     */
    public function sendEOYFinancialReportReminder(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->whereHas('documents', function ($query) {
                $query->where('report_extension', '0')
                    ->orWhereNull('report_extension');
            })
            ->whereHas('documents', function ($query) {
                $query->where('financial_report_received', '0')
                    ->orWhereNull('financial_report_received');
            })
            ->get();

        if ($chapterList->isEmpty()) {
            return redirect()->back()->with('info', 'There are no Chapters with Financial Reports Due.');
        }

        $chapterIds = [];
        $chapterEmails = [];
        $coordinatorEmails = [];
        $mailData = [];

        foreach ($chapterList as $chapter) {
            $chapterIds[] = $chapter->id;

            if ($chapter->name) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chDocuments = $emailDetails['chDocuments'];
                $chFinancialReport = $emailDetails['chFinancialReport'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            $mailData[$chDetails->name] = array_merge(
                $this->baseMailDataController->getChapterBasicData($chDetails, $stateShortName),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport),
            );

        }

        foreach ($mailData as $chapterName => $data) {
            if (! empty($chapterName)) {
                Mail::to($chapterEmails[$chapterName] ?? [])
                    ->cc($coordinatorEmails[$chapterName] ?? [])
                    ->queue(new EOYFinancialReportReminder($data));
            }
        }
        try {
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/eoy/financialreport')->with('success', 'Financial Report Reminders have been successfully sent.');

    }

    /**
     * Financial Report for Coordinator side for Reviewing of Chapters
     */
    public function reviewFinancialReport(Request $request, $id): View
    {
        $user = $this->userController->loadUserInformation($request);
        $loggedInName = $user['user_name'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $chDocuments = $baseQuery['chDocuments'];
        $allAwards = $baseQuery['allAwards'];
        $submitted = $baseQuery['submitted'];
        $rrList = $baseQuery['rrList'];

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'rrList' => $rrList, 'submitted' => $submitted, 'allAwards' => $allAwards,
            'chDocuments' => $chDocuments,
        ];

        return view('eoyreports.reviewfinancialreport')->with($data);
    }

    /**
     * Save Financial Report Review
     */
    public function updateEOYFinancialReport(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userName = $user['user_name'];
        $lastUpdatedBy = $userName;
        $lastupdatedDate = date('Y-m-d H:i:s');

        $input = $request->all();
        $farthest_step_visited_coord = $input['FurthestStep'];
        $reviewer_id = isset($input['AssignedReviewer']) ? $input['AssignedReviewer'] : null;
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

        $post_balance = $input['post_balance'];
        $post_balance = str_replace(',', '', $post_balance);
        $post_balance = $post_balance === '' ? null : $post_balance;

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
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $roster_path = $chDocuments->roster_path;
        $file_irs_path = $chDocuments->irs_path;
        $statement_1_path = $chDocuments->statement_1_path;
        $statement_2_path = $chDocuments->statement_2_path;
        $completed_name = $chFinancialReport->completed_name;
        $completed_email = $chFinancialReport->completed_email;
        $reviewerEmail = $chDetails->reportReviewer->email;

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);
        $financialReport = FinancialReport::find($id);

        DB::beginTransaction();
        try {
            $financialReport->reviewer_id = $reviewer_id;
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
                $financialReport->review_complete = $lastupdatedDate;
            }

            $mailData = array_merge(
                $this->baseMailDataController->getChapterBasicData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport),
            );

            // $mailData = [
            //     'chapterid' => $id,
            //     'chapter_name' => $chDetails->name,
            //     'chapter_state' => $stateShortName,
            //     'completed_name' => $completed_name,
            //     'completed_email' => $completed_email,
            //     'roster_path' => $roster_path,
            //     'file_irs_path' => $file_irs_path,
            //     'bank_statement_included_path' => $statement_1_path,
            //     'bank_statement_2_included_path' => $statement_2_path,
            //     'reviewer_email_message' => $reviewer_email_message,
            //     'userName' => $userName,
            // ];

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
                $documents->financial_report_complete = 1;
                $documents->review_complete = $lastupdatedDate;
            }

            $documents->save();

            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            DB::commit();
            if ($submitType == 'review_complete') {
                return redirect()->back()->with('success', 'Report has been successfully Marked as Review Complete');
            } else {
                return redirect()->back()->with('success', 'Report has been successfully Updated');
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);  // Log the error
        }

        return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
    }

    /**
     * Unsubmit Report
     */
    public function updateUnsubmit(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);
        $financialReport = FinancialReport::find($id);

        DB::beginTransaction();
        try {
            $documents->financial_report_received = null;
            $documents->report_received = null;
            $documents->save();

            $financialReport->submitted = null;
            $financialReport->save();

            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->back()->with('success', 'Report has been successfully Unsubmitted.');
    }

    /**
     * Clear Report Review
     */
    public function updateClearReview(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);
        $financialReport = FinancialReport::find($id);

        DB::beginTransaction();
        try {
            $documents->financial_report_received = '1';
            $documents->financial_report_complete = null;
            $documents->review_complete = null;
            $documents->save();

            $financialReport->review_complete = null;
            $financialReport->save();

            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->back()->with('success', 'Review Complete has been successfully Cleared.');
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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox2Status = $baseQuery['checkBox2Status'];

        $countList = count($chapterList);
        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chIsActive = $baseQuery['chIsActive'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'coorId' => $coorId, 'confId' => $confId,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chIsActive' => $chIsActive, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport,
        ];

        return view('eoyreports.editattachments')->with($data);
    }

    /**
     * Update the Attachments Details
     */
    public function updateEOYAttachments(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);

        DB::beginTransaction();
        try {
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            $documents->irs_verified = (int) $request->has('irs_verified');
            $documents->irs_notes = $request->input('irs_notes');
            $documents->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/eoy/attachments')->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/eoy/attachments')->with('success', 'Report attachments successfully updated');
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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox2Status = $baseQuery['checkBox2Status'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chIsActive = $baseQuery['chIsActive'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'coorId' => $coorId, 'confId' => $confId,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chIsActive' => $chIsActive, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport,
        ];

        return view('eoyreports.editboundaries')->with($data);
    }

    /**
     * Update the EOY Boundary Details
     */
    public function updateEOYBoundaries(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
            $chapter->territory = $request->filled('ch_territory') ? $request->input('ch_territory') : $request->input('ch_old_territory');
            $chapter->boundary_issue_resolved = (int) $request->has('ch_resolved');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('eoyreports.editboundaries', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        }

        return to_route('eoyreports.editboundaries', ['id' => $id])->with('success', 'EOY Information successfully updated.');
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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];
        $lastUpdatedBy = $user['user_name'];

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox2Status = $baseQuery['checkBox2Status'];

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
            'allAwards' => $allAwards, 'maxAwards' => $maxAwards,
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
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chIsActive = $baseQuery['chIsActive'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $allAwards = $baseQuery['allAwards'];

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'coorId' => $coorId, 'confId' => $confId,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chIsActive' => $chIsActive, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport, 'allAwards' => $allAwards,
        ];

        return view('eoyreports.editawards')->with($data);
    }

    /**
     * Update the EOY Award Details
     */
    public function updateEOYAwards(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

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
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            $financialReport->chapter_awards = $chapter_awards;
            $financialReport->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('eoyreports.editawards', ['id' => $id])->with('fail', 'Something went wrong, Please try again.');
        }

        return to_route('eoyreports.editawards', ['id' => $id])->with('success', 'EOY Information successfully updated.');
    }
}
