<?php

namespace App\Http\Controllers;

use App\Mail\EOYElectionReportReminder;
use App\Mail\EOYFinancialReportReminder;
use App\Mail\EOYLateReportReminder;
use App\Mail\EOYReviewrAssigned;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\Documents;
use App\Models\FinancialReport;
use App\Models\FinancialReportAwards;
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

class EOYReportController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
    }

    /**
     * Active Chapter List Base Query
     */
    public function getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    {
        $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);
        if ($conditions['coordinatorCondition']) {
            $coordinatorData = $this->userController->loadReportingTree($cdId);
            $inQryArr = $coordinatorData['inQryArr'];
        }

        $baseQuery = Chapters::with(['state', 'conference', 'region', 'president', 'documents', 'financialReport', 'reportReviewer', 'primaryCoordinator'])
            ->where('is_active', 1)
            ->where(function ($query) {
                $query->where('created_at', '<=', date('Y-06-30'))
                    ->orWhereNull('created_at');
            });

        if ($conditions['founderCondition']) {
        } elseif ($conditions['assistConferenceCoordinatorCondition']) {
            $baseQuery->where('conference_id', '=', $cdConfId);
        } elseif ($conditions['regionalCoordinatorCondition']) {
            $baseQuery->where('region_id', '=', $cdRegId);
        } else {
            $baseQuery->whereIn('primary_coordinator_id', $inQryArr);
        }

        if (isset($_GET['check']) && $_GET['check'] == 'yes') {
            $checkBoxStatus = 'checked';
            $baseQuery->where('primary_coordinator_id', '=', $cdId);
        } else {
            $checkBoxStatus = '';
        }

        if (isset($_GET['check2']) && $_GET['check2'] == 'yes') {
            $checkBox2Status = 'checked';
            $baseQuery->whereHas('financialReport', function ($query) use ($cdId) {
                $query->where('reviewer_id', '=', $cdId);
            });
        } else {
            $checkBox2Status = '';
        }

        $baseQuery->orderBy(State::select('state_short_name')
            ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name');

        return ['query' => $baseQuery, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

    }

    /**
     * Active Chapter Details Base Query
     */
    public function getChapterDetails($id)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'documents', 'financialReport', 'reportReviewer', 'boards'])->find($id);
        $chId = $chDetails->id;
        $chIsActive = $chDetails->is_active;
        $stateShortName = $chDetails->state->state_short_name;
        $regionLongName = $chDetails->region->long_name;
        $conferenceDescription = $chDetails->conference->conference_description;
        $chConfId = $chDetails->conference_id;
        $chRegId = $chDetails->region_id;
        $chPcId = $chDetails->primary_coordinator_id;

        $chDocuments = $chDetails->documents;
        $submitted = $chDetails->documents->financial_report_received;
        $reviewComplete = $chDetails->documents->review_complete;
        $chFinancialReport = $chDetails->financialReport;

        $allAwards = FinancialReportAwards::all();  // Full List for Dropdown Menu

        $boards = $chDetails->boards()->with('stateName')->get();
        $bdDetails = $boards->groupBy('board_position_id');
        $defaultBoardMember = (object) ['id' => null, 'first_name' => '', 'last_name' => '', 'email' => '', 'street_address' => '', 'city' => '', 'zip' => '', 'phone' => '', 'state' => '', 'user_id' => ''];

        // Fetch board details or fallback to default
        $PresDetails = $bdDetails->get(1, collect([$defaultBoardMember]))->first(); // President
        $AVPDetails = $bdDetails->get(2, collect([$defaultBoardMember]))->first(); // AVP
        $MVPDetails = $bdDetails->get(3, collect([$defaultBoardMember]))->first(); // MVP
        $TRSDetails = $bdDetails->get(4, collect([$defaultBoardMember]))->first(); // Treasurer
        $SECDetails = $bdDetails->get(5, collect([$defaultBoardMember]))->first(); // Secretary

        // Load Board and Coordinators for Sending Email
        $emailData = $this->userController->loadEmailDetails($chId);
        $emailListChap = $emailData['emailListChap'];
        $emailListCoord = $emailData['emailListCoord'];

        // Load Report Reviewer Coordinator Dropdown List
        $rrDetails = $this->userController->loadReviewerList($chRegId, $chConfId);

        return ['chDetails' => $chDetails, 'chIsActive' => $chIsActive, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'chConfId' => $chConfId, 'chRegId' => $chRegId, 'chPcId' => $chPcId,
            'chDocuments' => $chDocuments, 'reviewComplete' => $reviewComplete, 'chFinancialReport' => $chFinancialReport, 'allAwards' => $allAwards,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord, 'rrDetails' => $rrDetails, 'submitted' => $submitted,
        ];

    }

    /**
     * View the EOY Status list
     */
    public function showEOYStatus(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox2Status = $baseQuery['checkBox2Status'];

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

        return view('eoyreports.eoystatus')->with($data);
    }

    /**
     * Auto Send EOY Report Status Reminder
     */
    public function sendEOYStatusReminder(Request $request): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
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
                $emailData = $this->userController->loadEmailDetails($chapter->id);
                $emailListChap = $emailData['emailListChap'];
                $emailListCoord = $emailData['emailListCoord'];

                $chapterEmails[$chapter->name] = $emailListChap;
                $coordinatorEmails[$chapter->name] = $emailListCoord;
            }

            $stateShortName = $chapter->state->state_short_name;
            $chDocuments = $chapter->documents;

            $mailData[$chapter->name] = [
                'chapterName' => $chapter->name,
                'chapterState' => $stateShortName,
                'boardElectionReportReceived' => $chDocuments->new_board_submitted,
                'financialReportReceived' => $chDocuments->financial_report_received,
                '990NSubmissionReceived' => $chDocuments->financial_report_received,
                'einLetterCopyReceived' => $chDocuments->ein_letter_path,
            ];
        }

        foreach ($mailData as $chapterName => $data) {
            $to_email = $chapterEmails[$chapterName] ?? [];
            $cc_email = $coordinatorEmails[$chapterName] ?? [];

            if (! empty($to_email)) {
                Mail::to($to_email)
                    ->cc($cc_email)
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->getChapterDetails($id);
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
        $rrDetails = $baseQuery['rrDetails'];

        $data = ['cdId' => $cdId, 'cdPositionid' => $cdPositionid, 'cdConfId' => $cdConfId, 'allAwards' => $allAwards, 'chDocuments' => $chDocuments,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chIsActive' => $chIsActive, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chFinancialReport' => $chFinancialReport,
            'reviewComplete' => $reviewComplete,  'rrDetails' => $rrDetails,
        ];

        return view('eoyreports.view')->with($data);
    }

    /**
     * Update the EOY Status Details
     */
    public function updateEOYDetails(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName = $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

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
            $chapter->last_updated_date = date('Y-m-d H:i:s');
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
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox2Status = $baseQuery['checkBox2Status'];

        $row_count = count($chapterList);

        if (isset($_GET['board'])) {
            $status = '';
            if ($row_count > 0) {
                for ($i = 0; $i < $row_count; $i++) {
                    if ($chapterList[$i]->new_board_submitted && ! $chapterList[$i]->new_board_active) {
                        $status = $this->activateBoard($chapterList[$i]->id, $lastUpdatedBy);
                    }
                }
            }

            if ($status == 'success') {
                return redirect()->to('/eoy/boardreport')->with('success', 'All Board Info has been successfully activated');
            } elseif ($status == 'fail') {
                return redirect()->to('/eoy/boardreport')->with('fail', 'Something went wrong, Please try again.');
            } else {
                return redirect()->to('/eoy/boardreport')->with('info', 'No Incoming Board Members for Activation');
            }
            exit;
        }

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

        return view('eoyreports.eoyboardreport')->with($data);
    }

    /**
     * Board Election Report Reminder Auto Send
     */
    public function sendEOYBoardReportReminder(Request $request): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
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
                $emailData = $this->userController->loadEmailDetails($chapter->id);
                $emailListChap = $emailData['emailListChap'];
                $emailListCoord = $emailData['emailListCoord'];

                $chapterEmails[$chapter->name] = $emailListChap;
                $coordinatorEmails[$chapter->name] = $emailListCoord;
            }

            $stateShortName = $chapter->state->state_short_name;
            $chDocuments = $chapter->documents;

            $mailData[$chapter->name] = [
                'chapterName' => $chapter->name,
                'chapterState' => $stateShortName,
            ];
        }

        foreach ($mailData as $chapterName => $data) {
            $to_email = $chapterEmails[$chapterName] ?? [];
            $cc_email = $coordinatorEmails[$chapterName] ?? [];

            if (! empty($to_email)) {
                Mail::to($to_email)
                    ->cc($cc_email)
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
     * Activate Board Function
     */
    public function activateBoard($chapter_id, $lastUpdatedBy)
    {
        // Fetching New Board Info from Incoming Board Members
        $incomingBoardDetails = DB::table('incoming_board_member')
            ->select('*')
            ->where('chapter_id', '=', $chapter_id)
            ->orderBy('board_position_id')
            ->get();
        $countIncomingBoardDetails = count($incomingBoardDetails);

        if ($countIncomingBoardDetails > 0) {
            DB::beginTransaction();
            try {
                // Fetching Existing Board Members from Board Details
                $boardDetails = DB::table('boards')
                    ->select('*')
                    ->where('chapter_id', '=', $chapter_id)
                    ->get();
                $countBoardDetails = count($boardDetails);

                if ($countBoardDetails > 0) {
                    // Mark ALL existing board members as outgoing
                    foreach ($boardDetails as $record) {
                        DB::table('users')
                            ->where('id', $record->user_id)
                            ->update([
                                'user_type' => 'outgoing',
                                'updated_at' => now(),
                            ]);
                    }

                    // Delete all board members associated with the chapter from boards table
                    DB::table('boards')
                        ->where('chapter_id', $chapter_id)
                        ->delete();
                }

                // Create & Activate Details of Board members from Incoming Board Members
                foreach ($incomingBoardDetails as $incomingRecord) {
                    // Check if user already exists
                    $existingUser = DB::table('users')->where('email', $incomingRecord->email)->first();

                    if ($existingUser) {
                        // If the user exists, update all necessary fields, including is_active and user_type
                        DB::table('users')
                            ->where('id', $existingUser->id)
                            ->update([
                                'first_name' => $incomingRecord->first_name,
                                'last_name' => $incomingRecord->last_name,
                                'email' => $incomingRecord->email,
                                'is_active' => 1,
                                'user_type' => 'board',
                                'updated_at' => now(),
                            ]);

                        $userId = $existingUser->id;
                    } else {
                        // Insert new user
                        $userId = DB::table('users')->insertGetId([
                            'first_name' => $incomingRecord->first_name,
                            'last_name' => $incomingRecord->last_name,
                            'email' => $incomingRecord->email,
                            'password' => Hash::make('TempPass4You'),
                            'user_type' => 'board',
                            'is_active' => 1,
                            'updated_at' => now(),
                        ]);
                    }

                    // Prepare board details data
                    $boardId = [
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
                        'last_updated_date' => now(),
                        'is_active' => 1,
                    ];

                    // Upsert board details (update if the user and chapter already exist)
                    DB::table('boards')->upsert(
                        [$boardId], // The values to insert or update
                        ['user_id', 'chapter_id'], // The unique constraints for upsert
                        array_keys($boardId) // The columns to update if a conflict occurs
                    );

                }

                // Update Chapter after Board Active
                DB::update('UPDATE chapters SET new_board_active = ? WHERE id = ?', [1, $chapter_id]);

                // Delete all board members associated with the chapter from incoming_boards table
                DB::table('incoming_board_member')
                    ->where('chapter_id', $chapter_id)
                    ->delete();

                DB::commit();
                $status = 'success'; // Set status to success if everything goes well
            } catch (\Exception $e) {
                DB::rollback();
                // Log the exception or print it out for debugging
                Log::error('Error activating board: '.$e->getMessage());
                $status = 'fail'; // Set status to fail if an exception occurs
            }
        }

        return $status;
    }

    /**
     * Board Info Report Details
     */
    public function editBoardReport(Request $request, $id): View
    {
        Log::debug('Received ID: '.$id); // Add this line

        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $allWebLinks = Website::all();  // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
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
        $user = $request->user();
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;

        if ($request->input('submit_type') == 'activate_board') {
            $status = $this->activateBoard($chapter_id, $lastUpdatedBy);
            if ($status == 'success') {
                return redirect()->to('/eoy/boardreport')->with('success', 'Board Info has been successfully activated');
            } elseif ($status == 'fail') {
                return redirect()->to('/eoy/boardreport')->with('fail', 'Something went wrong, Please try again.');
            }
        }

        $chapter = Chapters::find($chapter_id);
        $boundaryStatus = $request->input('BoundaryStatus');
        $issue_note = $request->input('BoundaryIssue');
        //Boundary Issues Correct 0 | Not Correct 1
        if ($boundaryStatus == 0) {
            $issue_note = '';
        }

        $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        if (empty(trim($ch_webstatus))) {
            $ch_webstatus = 0; // Set it to 0 if it's blank
        }

        $website = $request->input('ch_website');
        // Ensure it starts with "http://" or "https://"
        if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
            $website = 'http://'.$website;
        }

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
            $chapter->new_board_submitted = 1;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            //President Info
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $PREDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '1')
                    ->get();
                $id = $request->input('presID');
                if (count($PREDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
                        ->update(['first_name' => $request->input('ch_pre_fname'),
                            'last_name' => $request->input('ch_pre_lname'),
                            'email' => $request->input('ch_pre_email'),
                            'street_address' => $request->input('ch_pre_street'),
                            'city' => $request->input('ch_pre_city'),
                            'state' => $request->input('ch_pre_state'),
                            'zip' => $request->input('ch_pre_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_pre_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                } else {
                    $board = DB::table('incoming_board_member')->insert(
                        ['first_name' => $request->input('ch_pre_fname'),
                            'last_name' => $request->input('ch_pre_lname'),
                            'email' => $request->input('ch_pre_email'),
                            'board_position_id' => 1,
                            'chapter_id' => $chapter_id,
                            'street_address' => $request->input('ch_pre_street'),
                            'city' => $request->input('ch_pre_city'),
                            'state' => $request->input('ch_pre_state'),
                            'zip' => $request->input('ch_pre_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_pre_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            //AVP Info
            if ($request->input('AVPVacant') == 'on') {
                $id = $request->input('avpID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->input('ch_avp_fname') != '' && $request->input('ch_avp_lname') != '' && $request->input('ch_avp_email') != '') {
                $AVPDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '2')
                    ->get();
                $id = $request->input('avpID');
                if (count($AVPDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
                        ->update(['first_name' => $request->input('ch_avp_fname'),
                            'last_name' => $request->input('ch_avp_lname'),
                            'email' => $request->input('ch_avp_email'),
                            'street_address' => $request->input('ch_avp_street'),
                            'city' => $request->input('ch_avp_city'),
                            'state' => $request->input('ch_avp_state'),
                            'zip' => $request->input('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                } else {

                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->input('ch_avp_fname'),
                            'last_name' => $request->input('ch_avp_lname'),
                            'email' => $request->input('ch_avp_email'),
                            'board_position_id' => 2,
                            'chapter_id' => $chapter_id,
                            'street_address' => $request->input('ch_avp_street'),
                            'city' => $request->input('ch_avp_city'),
                            'state' => $request->input('ch_avp_state'),
                            'zip' => $request->input('ch_avp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            //MVP Info
            if ($request->input('MVPVacant') == 'on') {
                $id = $request->input('mvpID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->input('ch_mvp_fname') != '' && $request->input('ch_mvp_lname') != '' && $request->input('ch_mvp_email') != '') {
                $MVPDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '3')
                    ->get();
                $id = $request->input('mvpID');
                if (count($MVPDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
                        ->update(['first_name' => $request->input('ch_mvp_fname'),
                            'last_name' => $request->input('ch_mvp_lname'),
                            'email' => $request->input('ch_mvp_email'),
                            'street_address' => $request->input('ch_mvp_street'),
                            'city' => $request->input('ch_mvp_city'),
                            'state' => $request->input('ch_mvp_state'),
                            'zip' => $request->input('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);
                } else {

                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->input('ch_mvp_fname'),
                            'last_name' => $request->input('ch_mvp_lname'),
                            'email' => $request->input('ch_mvp_email'),
                            'board_position_id' => 3,
                            'chapter_id' => $chapter_id,
                            'street_address' => $request->input('ch_mvp_street'),
                            'city' => $request->input('ch_mvp_city'),
                            'state' => $request->input('ch_mvp_state'),
                            'zip' => $request->input('ch_mvp_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            //TRS Info
            if ($request->input('TreasVacant') == 'on') {
                $id = $request->input('trsID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->input('ch_trs_fname') != '' && $request->input('ch_trs_lname') != '' && $request->input('ch_trs_email') != '') {
                $TRSDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '4')
                    ->get();
                $id = $request->input('trsID');
                if (count($TRSDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
                        ->update(['first_name' => $request->input('ch_trs_fname'),
                            'last_name' => $request->input('ch_trs_lname'),
                            'email' => $request->input('ch_trs_email'),
                            'street_address' => $request->input('ch_trs_street'),
                            'city' => $request->input('ch_trs_city'),
                            'state' => $request->input('ch_trs_state'),
                            'zip' => $request->input('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);

                } else {

                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->input('ch_trs_fname'),
                            'last_name' => $request->input('ch_trs_lname'),
                            'email' => $request->input('ch_trs_email'),
                            'board_position_id' => 4,
                            'chapter_id' => $chapter_id,
                            'street_address' => $request->input('ch_trs_street'),
                            'city' => $request->input('ch_trs_city'),
                            'state' => $request->input('ch_trs_state'),
                            'zip' => $request->input('ch_trs_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }
            //SEC Info
            if ($request->input('SecVacant') == 'on') {
                $id = $request->input('secID');
                DB::table('incoming_board_member')
                    ->where('id', $id)
                    ->delete();
            }
            if ($request->input('ch_sec_fname') != '' && $request->input('ch_sec_lname') != '' && $request->input('ch_sec_email') != '') {
                $SECDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $chapter_id)
                    ->where('board_position_id', '=', '5')
                    ->get();
                $id = $request->input('secID');
                if (count($SECDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $id)
                        ->update(['first_name' => $request->input('ch_sec_fname'),
                            'last_name' => $request->input('ch_sec_lname'),
                            'email' => $request->input('ch_sec_email'),
                            'street_address' => $request->input('ch_sec_street'),
                            'city' => $request->input('ch_sec_city'),
                            'state' => $request->input('ch_sec_state'),
                            'zip' => $request->input('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s')]);

                } else {
                    $board = DB::table('incoming_board_member')->insert(
                        [
                            'first_name' => $request->input('ch_sec_fname'),
                            'last_name' => $request->input('ch_sec_lname'),
                            'email' => $request->input('ch_sec_email'),
                            'board_position_id' => 5,
                            'chapter_id' => $chapter_id,
                            'street_address' => $request->input('ch_sec_street'),
                            'city' => $request->input('ch_sec_city'),
                            'state' => $request->input('ch_sec_state'),
                            'zip' => $request->input('ch_sec_zip'),
                            'country' => 'USA',
                            'phone' => $request->input('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => date('Y-m-d H:i:s'),
                        ]
                    );
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox2Status = $baseQuery['checkBox2Status'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

        return view('eoyreports.eoyfinancialreport')->with($data);
    }

    /**
     * Financial Report Reminder Auto Send
     */
    public function sendEOYFinancialReportReminder(Request $request): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
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
                $emailData = $this->userController->loadEmailDetails($chapter->id);
                $emailListChap = $emailData['emailListChap'];
                $emailListCoord = $emailData['emailListCoord'];

                $chapterEmails[$chapter->name] = $emailListChap;
                $coordinatorEmails[$chapter->name] = $emailListCoord;
            }

            $stateShortName = $chapter->state->state_short_name;
            $chDocuments = $chapter->documents;

            $mailData[$chapter->name] = [
                'chapterName' => $chapter->name,
                'chapterState' => $stateShortName,
            ];
        }

        foreach ($mailData as $chapterName => $data) {
            $to_email = $chapterEmails[$chapterName] ?? [];
            $cc_email = $coordinatorEmails[$chapterName] ?? [];

            if (! empty($to_email)) {
                Mail::to($to_email)
                    ->cc($cc_email)
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
    public function reviewFinancialReport(Request $request, $id)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $loggedInName = $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $chDocuments = $baseQuery['chDocuments'];
        $allAwards = $baseQuery['allAwards'];
        $submitted = $baseQuery['submitted'];
        $rrDetails = $baseQuery['rrDetails'];

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
            'chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'rrDetails' => $rrDetails, 'submitted' => $submitted, 'allAwards' => $allAwards,
            'chDocuments' => $chDocuments,
        ];

        return view('eoyreports.reviewfinancialreport')->with($data);
    }

    /**
     * Save Financial Report Review
     */
    public function updateEOYFinancialReport(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName = $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

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
        $step_13_notes_log = $input['Step13_Log'];

        $reviewer_email_message = $input['reviewer_email_message'];

        // Step 1 - Dues
        $check_roster_attached = isset($input['checkRosterAttached']) ? $input['checkRosterAttached'] : null;
        $check_renewal_seems_right = isset($input['checkRenewalSeemsRight']) ? $input['checkRenewalSeemsRight'] : null;

        // Step 3 - Service
        $check_minimum_service_project = isset($input['checkServiceProject']) ? $input['checkServiceProject'] : null;
        $check_m2m_donation = isset($input['checkM2MDonation']) ? $input['checkM2MDonation'] : null;

        // Step 4 - Parties
        $check_party_percentage = isset($input['check_party_percentage']) ? $input['check_party_percentage'] : null;

        //Step - Financials
        $check_total_income_less = isset($input['checkTotalIncome']) ? $input['checkTotalIncome'] : null;

        //Step 8 - Reconciliation
        $check_beginning_balance = isset($input['check_beginning_balance']) ? $input['check_beginning_balance'] : null;
        $check_bank_statement_included = isset($input['checkBankStatementIncluded']) ? $input['checkBankStatementIncluded'] : null;
        $check_bank_statement_matches = isset($input['checkBankStatementMatches']) ? $input['checkBankStatementMatches'] : null;

        $post_balance = $input['post_balance'];
        $post_balance = str_replace(',', '', $post_balance);
        $post_balance = $post_balance === '' ? null : $post_balance;

        //Step 9 - Questions
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

        $baseQuery = $this->getChapterDetails($id);
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
            $financialReport->step_13_notes_log = $step_13_notes_log;
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
            // $financialReport->check_award_1_approved = $check_award_1_approved;
            // $financialReport->check_award_2_approved = $check_award_2_approved;
            // $financialReport->check_award_3_approved = $check_award_3_approved;
            // $financialReport->check_award_4_approved = $check_award_4_approved;
            // $financialReport->check_award_5_approved = $check_award_5_approved;
            $financialReport->farthest_step_visited_coord = $farthest_step_visited_coord;
            if ($submitType == 'review_complete') {
                $financialReport->review_complete = date('Y-m-d H:i:s');
            }

            $mailData = [
                'chapterid' => $id,
                'chapter_name' => $chDetails->name,
                'chapter_state' => $stateShortName,
                'completed_name' => $completed_name,
                'completed_email' => $completed_email,
                'roster_path' => $roster_path,
                'file_irs_path' => $file_irs_path,
                'bank_statement_included_path' => $statement_1_path,
                'bank_statement_2_included_path' => $statement_2_path,
                'reviewer_email_message' => $reviewer_email_message,
                'userName' => $userName,
            ];

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
                $documents->review_complete = date('Y-m-d H:i:s');
            }

            $documents->save();

            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

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
            $chapter->last_updated_date = date('Y-m-d H:i:s');
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

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
            $chapter->last_updated_date = date('Y-m-d H:i:s');
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox2Status = $baseQuery['checkBox2Status'];

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

        return view('eoyreports.eoyattachments')->with($data);
    }

    /**
     * View the Attachments Details
     */
    public function editEOYAttachments(Request $request, $id)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chIsActive = $baseQuery['chIsActive'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['cdId' => $cdId, 'cdPositionid' => $cdPositionid, 'cdConfId' => $cdConfId,
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);

        DB::beginTransaction();
        try {
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox2Status = $baseQuery['checkBox2Status'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status];

        return view('eoyreports.eoyboundaries')->with($data);
    }

    /**
     * View the EOY Boundary Details
     */
    public function editEOYBoundaries(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chIsActive = $baseQuery['chIsActive'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];

        $data = ['cdId' => $cdId, 'cdPositionid' => $cdPositionid, 'cdConfId' => $cdConfId,
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
            $chapter->territory = $request->filled('ch_territory') ? $request->input('ch_territory') : $request->input('ch_old_territory');
            $chapter->boundary_issue_resolved = (int) $request->has('ch_resolved');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = now();
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->getBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
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
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox2Status' => $checkBox2Status,
            'allAwards' => $allAwards, 'maxAwards' => $maxAwards,
        ];

        return view('eoyreports.eoyawards', $data);
    }

    /**
     * View the EOY Award Details
     */
    public function editEOYAwards(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chIsActive = $baseQuery['chIsActive'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $allAwards = $baseQuery['allAwards'];

        $data = ['cdId' => $cdId, 'cdPositionid' => $cdPositionid, 'cdConfId' => $cdConfId,
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
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

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
            $chapter->last_updated_date = date('Y-m-d H:i:s');
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
