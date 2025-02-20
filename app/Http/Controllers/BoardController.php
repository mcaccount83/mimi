<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckCurrentPasswordBoardRequest;
use App\Http\Requests\UpdatePasswordBoardRequest;
use App\Mail\ChapersUpdateListAdmin;
use App\Mail\ChapersUpdateListAdminMember;
use App\Mail\ChapersUpdatePrimaryCoorMember;
use App\Mail\ChaptersUpdatePrimaryCoorPresident;
use App\Mail\EOYElectionReportSubmitted;
use App\Mail\EOYElectionReportThankYou;
use App\Mail\EOYFinancialReportThankYou;
use App\Mail\EOYFinancialSubmitted;
use App\Mail\WebsiteAddNoticeChapter;
use App\Mail\WebsiteAddNoticeAdmin;
use App\Mail\WebsiteReviewNotice;
use App\Models\Admin;
use App\Models\Boards;
use App\Models\Chapters;
use App\Models\Documents;
use App\Models\FinancialReport;
use App\Models\FinancialReportAwards;
use App\Models\Resources;
use App\Models\State;
use App\Models\User;
use App\Models\Website;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BoardController extends Controller
{
    protected $userController;

    protected $pdfController;

    public function __construct(UserController $userController, PDFController $pdfController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndBoard::class);
        $this->userController = $userController;
        $this->pdfController = $pdfController;
    }

    /**
     * Reset Password
     */
    public function updatePassword(UpdatePasswordBoardRequest $request): JsonResponse
    {
        $user = $request->user();

        // Ensure the current password is correct
        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }

        // Update the user's password
        $user->password = Hash::make($request->new_password);
        $user->remember_token = null; // Reset the remember token
        $user->save();

        return response()->json(['message' => 'Password updated successfully']);
    }

    /**
     * Verify Current Passwor for Reset
     */
    public function checkCurrentPassword(CheckCurrentPasswordBoardRequest $request): JsonResponse
    {
        $user = $request->user();
        $isValid = Hash::check($request->current_password, $user->password);

        return response()->json(['isValid' => $isValid]);
    }

    /**
     * Active Chapter Details Base Query
     */
    public function getChapterDetails($id)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'startMonth', 'webLink', 'state', 'documents', 'financialReport', 'president',
            'boards', 'reportReviewer', 'primaryCoordinator'])->find($id);
        $chId = $chDetails->id;
        $chIsActive = $chDetails->is_active;
        $stateShortName = $chDetails->state->state_short_name;
        $regionLongName = $chDetails->region->long_name;
        $conferenceDescription = $chDetails->conference->conference_description;
        $chConfId = $chDetails->conference_id;
        $chRegId = $chDetails->region_id;
        $chPcId = $chDetails->primary_coordinator_id;
        $startMonthName = $chDetails->startMonth->month_long_name;
        $websiteLink = $chDetails->webLink->link_status ?? null;

        $allWebLinks = Website::all(); // Full List for Dropdown Menu
        $allStates = State::all();  // Full List for Dropdown Menu
        $allAwards = FinancialReportAwards::all();  // Full List for Dropdown Menu

        $chDocuments = $chDetails->documents;
        $submitted = $chDocuments->financial_report_received;
        $reviewComplete = $chDetails->documents->review_complete;
        $reviewerEmail = $chDetails->reportReviewer?->email;  // Could be null -- no reviewer assigned
        $chFinancialReport = $chDetails->financialReport;

        $awards = $chDetails->financialReport;

        $boards = $chDetails->boards()->with(['stateName', 'position'])->get();
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

        // PC Email for Sending Email
        $pcEmail = $chDetails->primaryCoordinator->email;

        // Load Conference Coordinators for Sending Email
        $ccEmailData = $this->userController->loadConferenceCoord($chPcId);
        $cc_id = $ccEmailData['cc_id'];
        $emailCC = $ccEmailData['cc_email'];

        return ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'cc_id' => $cc_id,
            'chFinancialReport' => $chFinancialReport, 'startMonthName' => $startMonthName, 'chDocuments' => $chDocuments, 'submitted' => $submitted,
            'PresDetails' => $PresDetails, 'AVPDetails' => $AVPDetails, 'MVPDetails' => $MVPDetails, 'TRSDetails' => $TRSDetails, 'SECDetails' => $SECDetails,
            'allWebLinks' => $allWebLinks, 'allStates' => $allStates, 'emailListChap' => $emailListChap, 'emailListCoord' => $emailListCoord, 'emailCC' => $emailCC,
            'reviewerEmail' => $reviewerEmail, 'awards' => $awards, 'allAwards' => $allAwards, 'pcEmail' => $pcEmail
        ];

    }

    /**
     * View Board Details President Login
     */
    public function showPresident(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $bdPositionid = $bdDetails->board_position_id;
        $bdIsActive = $bdDetails->is_active;
        $id = $bdDetails->chapter_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $chDocuments = $baseQuery['chDocuments'];

        $allWebLinks = $baseQuery['allWebLinks'];
        $allStates = $baseQuery['allStates'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $start_month = $chDetails->start_month_id;
        $next_renewal_year = $chDetails->next_renewal_year;
        $due_date = Carbon::create($next_renewal_year, $start_month, 1);
        // $due_date = Carbon::create($next_renewal_year, $start_month, 1)->endOfMonth();

        $admin = Admin::orderBy('id', 'desc')
        ->limit(1)
        ->first();
        $display_testing = ($admin->display_testing == 1);
        $display_live = ($admin->display_live == 1);

        $data = ['chDetails' => $chDetails, 'chFinancialReport' => $chFinancialReport, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'allWebLinks' => $allWebLinks,
            'PresDetails' => $PresDetails, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails,
            'startMonthName' => $startMonthName, 'thisMonth' => $month, 'due_date' => $due_date, 'user_type' => $user_type,
            'display_testing' => $display_testing, 'display_live' => $display_live, 'chDocuments' => $chDocuments
        ];

        return view('boards.president')->with($data);
    }

    /**
     * View Board Details President Login
     */
    public function showMember(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $bdPositionid = $bdDetails->board_position_id;
        $bdIsActive = $bdDetails->is_active;
        $id = $bdDetails->chapter_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $chDocuments = $baseQuery['chDocuments'];

        $allWebLinks = $baseQuery['allWebLinks'];
        $allStates = $baseQuery['allStates'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        if ($bdPositionid == 2) {
            $borDetails = $AVPDetails;
        } elseif ($bdPositionid == 3) {
            $borDetails = $MVPDetails;
        } elseif ($bdPositionid == 4) {
            $borDetails = $TRSDetails;
        } elseif ($bdPositionid == 5) {
            $borDetails = $SECDetails;
        }

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $start_month = $chDetails->start_month_id;
        $next_renewal_year = $chDetails->next_renewal_year;
        $due_date = Carbon::create($next_renewal_year, $start_month, 1);
        // $due_date = Carbon::create($next_renewal_year, $start_month, 1)->endOfMonth();

        $admin = Admin::orderBy('id', 'desc')
        ->limit(1)
        ->first();
        $display_testing = ($admin->display_testing == 1);
        $display_live = ($admin->display_live == 1);

        $data = ['chDetails' => $chDetails, 'chFinancialReport' => $chFinancialReport, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'allWebLinks' => $allWebLinks,
            'borDetails' => $borDetails, 'startMonthName' => $startMonthName, 'thisMonth' => $month, 'due_date' => $due_date, 'user_type' => $user_type,
            'display_testing' => $display_testing, 'display_live' => $display_live, 'chDocuments' => $chDocuments
        ];

        return view('boards.member')->with($data);
    }

    /**
     * Update Board Details President Login
     */
    public function updatePresident(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName = $user->first_name.' '.$user->last_name;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $lastUpdatedBy = $bdDetails->first_name.' '.$bdDetails->last_name;
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQueryPre = $this->getChapterDetails($id);
        $chDetailsPre = $baseQueryPre['chDetails'];
        $PresDetailsPre = $baseQueryPre['PresDetails'];
        $AVPDetailsPre = $baseQueryPre['AVPDetails'];
        $MVPDetailsPre = $baseQueryPre['MVPDetails'];
        $TRSDetailsPre = $baseQueryPre['TRSDetails'];
        $SECDetailsPre = $baseQueryPre['SECDetails'];

        $input = $request->all();
        $webStatusPre = $input['ch_hid_webstatus'];

        // $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        // if (empty(trim($ch_webstatus))) {
        //     $ch_webstatus = 0; // Set it to 0 if it's blank
        // }

        // $website = $request->input('ch_website');
        // // Ensure it starts with "http://" or "https://"
        // if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
        //     $website = 'http://'.$website;
        // }

        // Handle web status - allow null values
$ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
// Only convert to 0 if the website is not null but status is empty
if (!is_null($request->input('ch_website')) && empty(trim($ch_webstatus))) {
    $ch_webstatus = 0;
}

// Handle website URL
$website = $request->input('ch_website');
// Only add http:// if the website field is not null or empty
if (!is_null($website) && !empty(trim($website))) {
    if (!str_starts_with($website, 'http://') && !str_starts_with($website, 'https://')) {
        $website = 'http://' . $website;
    }
}

        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
            $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
            $chapter->email = $request->input('ch_email');
            $chapter->po_box = $request->input('ch_pobox');
            $chapter->website_url = $website;
            $chapter->website_status = $request->input('ch_webstatus');
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            //President Info
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $chapter = Chapters::with('president')->find($id);
                $president = $chapter->president;
                $user = $president->user;

                $user->update([   // Update user details
                    'first_name' => $request->input('ch_pre_fname'),
                    'last_name' => $request->input('ch_pre_lname'),
                    'email' => $request->input('ch_pre_email'),
                    'updated_at' => now(),
                ]);
                $president->update([   // Update board details
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
                    'last_updated_date' => now(),
                ]);
            }

            //AVP Info
            $chapter = Chapters::with('avp')->find($id);
            $avp = $chapter->avp;
            if ($avp) {
                $user = $avp->user;
                if ($request->input('AVPVacant') == 'on') {
                    $avp->delete();  // Delete board member and associated user if now Vacant
                    $user->delete();
                } else {
                    $user->update([   // Update user details if alrady exists
                        'first_name' => $request->input('ch_avp_fname'),
                        'last_name' => $request->input('ch_avp_lname'),
                        'email' => $request->input('ch_avp_email'),
                        'updated_at' => now(),
                    ]);
                    $avp->update([   // Update board details if alrady exists
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
                        'last_updated_date' => now(),
                    ]);
                }
            } else {
                if ($request->input('AVPVacant') != 'on') {
                    $user = User::create([  // Create user details if new
                        'first_name' => $request->input('ch_avp_fname'),
                        'last_name' => $request->input('ch_avp_lname'),
                        'email' => $request->input('ch_avp_email'),
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1,
                    ]);
                    $chapter->avp()->create([  // Create board details if new
                        'user_id' => $user->id,
                        'first_name' => $request->input('ch_avp_fname'),
                        'last_name' => $request->input('ch_avp_lname'),
                        'email' => $request->input('ch_avp_email'),
                        'board_position_id' => 2,
                        'street_address' => $request->input('ch_avp_street'),
                        'city' => $request->input('ch_avp_city'),
                        'state' => $request->input('ch_avp_state'),
                        'zip' => $request->input('ch_avp_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_avp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                        'is_active' => 1,
                    ]);
                }
            }

            //MVP Info
            $chapter = Chapters::with('mvp')->find($id);
            $mvp = $chapter->mvp;
            if ($mvp) {
                $user = $mvp->user;
                if ($request->input('MVPVacant') == 'on') {
                    $mvp->delete();  // Delete board member and associated user if now Vacant
                    $user->delete();
                } else {
                    $user->update([   // Update user details if alrady exists
                        'first_name' => $request->input('ch_mvp_fname'),
                        'last_name' => $request->input('ch_mvp_lname'),
                        'email' => $request->input('ch_mvp_email'),
                        'updated_at' => now(),
                    ]);
                    $mvp->update([   // Update board details if alrady exists
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
                        'last_updated_date' => now(),
                    ]);
                }
            } else {
                if ($request->input('MVPVacant') != 'on') {
                    $user = User::create([  // Create user details if new
                        'first_name' => $request->input('ch_mvp_fname'),
                        'last_name' => $request->input('ch_mvp_lname'),
                        'email' => $request->input('ch_mvp_email'),
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1,
                    ]);
                    $chapter->mvp()->create([  // Create board details if new
                        'user_id' => $user->id,
                        'first_name' => $request->input('ch_mvp_fname'),
                        'last_name' => $request->input('ch_mvp_lname'),
                        'email' => $request->input('ch_mvp_email'),
                        'board_position_id' => 3,
                        'street_address' => $request->input('ch_mvp_street'),
                        'city' => $request->input('ch_mvp_city'),
                        'state' => $request->input('ch_mvp_state'),
                        'zip' => $request->input('ch_mvp_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_mvp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                        'is_active' => 1,
                    ]);
                }
            }

            //TRS Info
            $chapter = Chapters::with('treasurer')->find($id);
            $treasurer = $chapter->treasurer;
            if ($treasurer) {
                $user = $treasurer->user;
                if ($request->input('TreasVacant') == 'on') {
                    $treasurer->delete();  // Delete board member and associated user if now Vacant
                    $user->delete();
                } else {
                    $user->update([   // Update user details if alrady exists
                        'first_name' => $request->input('ch_trs_fname'),
                        'last_name' => $request->input('ch_trs_lname'),
                        'email' => $request->input('ch_trs_email'),
                        'updated_at' => now(),
                    ]);
                    $treasurer->update([   // Update board details if alrady exists
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
                        'last_updated_date' => now(),
                    ]);
                }
            } else {
                if ($request->input('TreasVacant') != 'on') {
                    $user = User::create([  // Create user details if new
                        'first_name' => $request->input('ch_trs_fname'),
                        'last_name' => $request->input('ch_trs_lname'),
                        'email' => $request->input('ch_trs_email'),
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1,
                    ]);
                    $chapter->treasurer()->create([  // Create board details if new
                        'user_id' => $user->id,
                        'first_name' => $request->input('ch_trs_fname'),
                        'last_name' => $request->input('ch_trs_lname'),
                        'email' => $request->input('ch_trs_email'),
                        'board_position_id' => 4,
                        'street_address' => $request->input('ch_trs_street'),
                        'city' => $request->input('ch_trs_city'),
                        'state' => $request->input('ch_trs_state'),
                        'zip' => $request->input('ch_trs_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_trs_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                        'is_active' => 1,
                    ]);
                }
            }

            //SEC Info
            $chapter = Chapters::with('secretary')->find($id);
            $secretary = $chapter->secretary;
            if ($secretary) {
                $user = $secretary->user;
                if ($request->input('SecVacant') == 'on') {
                    $secretary->delete();  // Delete board member and associated user if now Vacant
                    $user->delete();
                } else {
                    $user->update([   // Update user details if alrady exists
                        'first_name' => $request->input('ch_sec_fname'),
                        'last_name' => $request->input('ch_sec_lname'),
                        'email' => $request->input('ch_sec_email'),
                        'updated_at' => now(),
                    ]);
                    $secretary->update([   // Update board details if alrady exists
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
                        'last_updated_date' => now(),
                    ]);
                }
            } else {
                if ($request->input('SecVacant') != 'on') {
                    $user = User::create([  // Create user details if new
                        'first_name' => $request->input('ch_sec_fname'),
                        'last_name' => $request->input('ch_sec_lname'),
                        'email' => $request->input('ch_sec_email'),
                        'password' => Hash::make('TempPass4You'),
                        'user_type' => 'board',
                        'is_active' => 1,
                    ]);
                    $chapter->secretary()->create([  // Create board details if new
                        'user_id' => $user->id,
                        'first_name' => $request->input('ch_sec_fname'),
                        'last_name' => $request->input('ch_sec_lname'),
                        'email' => $request->input('ch_sec_email'),
                        'board_position_id' => 5,
                        'street_address' => $request->input('ch_sec_street'),
                        'city' => $request->input('ch_sec_city'),
                        'state' => $request->input('ch_sec_state'),
                        'zip' => $request->input('ch_sec_zip'),
                        'country' => 'USA',
                        'phone' => $request->input('ch_sec_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => now(),
                        'is_active' => 1,
                    ]);
                }
            }

            //Update Chapter MailData//
            $baseQueryUpd = $this->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $chConfId = $baseQueryUpd['chConfId'];
            $chPcId = $baseQueryUpd['chPcId'];
            $webStatusUpd = $ch_webstatus;
            $PresDetailsUpd = $baseQueryUpd['PresDetails'];
            $AVPDetailsUpd = $baseQueryUpd['AVPDetails'];
            $MVPDetailsUpd = $baseQueryUpd['MVPDetails'];
            $TRSDetailsUpd = $baseQueryUpd['TRSDetails'];
            $SECDetailsUpd = $baseQueryUpd['SECDetails'];
            $emailListChap = $baseQueryUpd['emailListChap'];  // Full Board
            $emailListCoord = $baseQueryUpd['emailListCoord'];  // Full Coordinaor List
            $emailCC = $baseQueryUpd['emailCC'];  // CC Email
            $pcDetails = $baseQueryUpd['chDetails']->primaryCoordinator;
            $pcEmail = $pcDetails->email;  // PC Email
            $EINCordEmail = 'jackie.mchenry@momsclub.org';  // EIN Coor Email

            $mailDataPres = [
                'chapter_name' => $chDetailsUpd->name,
                'chapter_state' => $stateShortName,
                'conference' => $chConfId,
                'updated_byUpd' => $lastUpdatedBy,
                'updated_byPre' => $lastupdatedDate,

                'inConPre' => $chDetailsPre->inquiries_contact,
                'chapemailPre' => $chDetailsPre->email,
                'poBoxPre' => $chDetailsPre->po_box,
                'webUrlPre' => $chDetailsPre->website_url,
                'webStatusPre' => $chDetailsPre->website_status,
                'egroupPre' => $chDetailsPre->egroup,
                'chapfnamePre' => $PresDetailsPre->first_name,
                'chaplnamePre' => $PresDetailsPre->last_name,
                'chapteremailPre' => $PresDetailsPre->email,
                'phonePre' => $PresDetailsPre->phone,
                'streetPre' => $PresDetailsPre->street,
                'cityPre' => $PresDetailsPre->city,
                'statePre' => $PresDetailsPre->state,
                'zipPre' => $PresDetailsPre->zip,

                'inConUpd' => $chDetailsUpd->inquiries_contact,
                'chapemailUpd' => $chDetailsUpd->email,
                'poBoxUpd' => $chDetailsUpd->po_box,
                'webUrlUpd' => $chDetailsUpd->website_url,
                'webStatusUpd' => $chDetailsUpd->website_status,
                'egroupUpd' => $chDetailsUpd->egroup,
                'chapfnameUpd' => $PresDetailsUpd->first_name,
                'chaplnameUpd' => $PresDetailsUpd->last_name,
                'chapteremailUpd' => $PresDetailsUpd->email,
                'phoneUpd' => $PresDetailsUpd->phone,
                'streetUpd' => $PresDetailsUpd->street,
                'cityUpd' => $PresDetailsUpd->city,
                'stateUpd' => $PresDetailsUpd->state,
                'zipUpd' => $PresDetailsUpd->zip,

                'ch_website_url' => $website,
            ];

            $mailData = array_merge($mailDataPres);
            if ($AVPDetailsUpd !== null) {
                $mailDataAvp = ['avpfnameUpd' => $AVPDetailsUpd->first_name,
                    'avplnameUpd' => $AVPDetailsUpd->last_name,
                    'avpemailUpd' => $AVPDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDataAvp);
            } else {
                $mailDataAvp = ['avpfnameUpd' => '',
                    'avplnameUpd' => '',
                    'avpemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataAvp);
            }
            if ($MVPDetailsUpd !== null) {
                $mailDataMvp = ['mvpfnameUpd' => $MVPDetailsUpd->first_name,
                    'mvplnameUpd' => $MVPDetailsUpd->last_name,
                    'mvpemailUpd' => $MVPDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDataMvp);
            } else {
                $mailDataMvp = ['mvpfnameUpd' => '',
                    'mvplnameUpd' => '',
                    'mvpemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataMvp);
            }
            if ($TRSDetailsUpd !== null) {
                $mailDatatres = ['tresfnameUpd' => $TRSDetailsUpd->first_name,
                    'treslnameUpd' => $TRSDetailsUpd->last_name,
                    'tresemailUpd' => $TRSDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDatatres);
            } else {
                $mailDatatres = ['tresfnameUpd' => '',
                    'treslnameUpd' => '',
                    'tresemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDatatres);
            }
            if ($SECDetailsUpd !== null) {
                $mailDataSec = ['secfnameUpd' => $SECDetailsUpd->first_name,
                    'seclnameUpd' => $SECDetailsUpd->last_name,
                    'secemailUpd' => $SECDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDataSec);
            } else {
                $mailDataSec = ['secfnameUpd' => '',
                    'seclnameUpd' => '',
                    'secemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataSec);
            }

            if ($AVPDetailsPre !== null) {
                $mailDataAvpp = ['avpfnamePre' => $AVPDetailsPre->first_name,
                    'avplnamePre' => $AVPDetailsPre->last_name,
                    'avpemailPre' => $AVPDetailsPre->email, ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            } else {
                $mailDataAvpp = ['avpfnamePre' => '',
                    'avplnamePre' => '',
                    'avpemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            }
            if ($MVPDetailsPre !== null) {
                $mailDataMvpp = ['mvpfnamePre' => $MVPDetailsPre->first_name,
                    'mvplnamePre' => $MVPDetailsPre->last_name,
                    'mvpemailPre' => $MVPDetailsPre->email, ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            } else {
                $mailDataMvpp = ['mvpfnamePre' => '',
                    'mvplnamePre' => '',
                    'mvpemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            }
            if ($TRSDetailsPre !== null) {
                $mailDatatresp = ['tresfnamePre' => $TRSDetailsPre->first_name,
                    'treslnamePre' => $TRSDetailsPre->last_name,
                    'tresemailPre' => $TRSDetailsPre->email, ];
                $mailData = array_merge($mailData, $mailDatatresp);
            } else {
                $mailDatatresp = ['tresfnamePre' => '',
                    'treslnamePre' => '',
                    'tresemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDatatresp);
            }
            if ($SECDetailsPre !== null) {
                $mailDataSecp = ['secfnamePre' => $SECDetailsPre->first_name,
                    'seclnamePre' => $SECDetailsPre->last_name,
                    'secemailPre' => $SECDetailsPre->email, ];
                $mailData = array_merge($mailData, $mailDataSecp);
            } else {
                $mailDataSecp = ['secfnamePre' => '',
                    'seclnamePre' => '',
                    'secemailPre' => '', ];
                $mailData = array_merge($mailData, $mailDataSecp);
            }

            if ($chDetailsUpd->name != $chDetailsPre->name || $PresDetailsUpd->bor_email != $PresDetailsPre->bor_email || $PresDetailsUpd->street_address != $PresDetailsPre->street_address || $PresDetailsUpd->city != $PresDetailsPre->city ||
                    $PresDetailsUpd->state != $PresDetailsPre->state || $PresDetailsUpd->first_name != $PresDetailsPre->first_name || $PresDetailsUpd->last_name != $PresDetailsPre->last_name ||
                    $PresDetailsUpd->zip != $PresDetailsPre->zip || $PresDetailsUpd->phone != $PresDetailsPre->phone || $chDetailsUpd->inquiries_contact != $chDetailsPre->inquiries_contact ||
                    $chDetailsUpd->email != $chDetailsPre->email || $chDetailsUpd->po_box != $chDetailsPre->po_box || $chDetailsUpd->website_url != $chDetailsPre->website_url ||
                    $chDetailsUpd->website_status != $chDetailsPre->website_status || $chDetailsUpd->egroup != $chDetailsPre->egroup ||
                    $mailDataAvpp['avpfnamePre'] != $mailDataAvp['avpfnameUpd'] || $mailDataAvpp['avplnamePre'] != $mailDataAvp['avplnameUpd'] || $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] ||
                    $mailDataMvpp['mvpfnamePre'] != $mailDataMvp['mvpfnameUpd'] || $mailDataMvpp['mvplnamePre'] != $mailDataMvp['mvplnameUpd'] || $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] ||
                    $mailDatatresp['tresfnamePre'] != $mailDatatres['tresfnameUpd'] || $mailDatatresp['treslnamePre'] != $mailDatatres['treslnameUpd'] || $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] ||
                    $mailDataSecp['secfnamePre'] != $mailDataSec['secfnameUpd'] || $mailDataSecp['seclnamePre'] != $mailDataSec['seclnameUpd'] || $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {

                Mail::to($pcEmail)
                    ->queue(new ChaptersUpdatePrimaryCoorPresident($mailData));
            }

            // //List Admin Notification//
            $to_email2 = 'listadmin@momsclub.org';

            if ($PresDetailsUpd->email != $PresDetailsPre->email || $PresDetailsUpd->email != $PresDetailsPre->email || $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] ||
                        $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] || $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] ||
                        $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {

                Mail::to($to_email2)
                    ->queue(new ChapersUpdateListAdmin($mailData));
            }

            //Website URL Change Notification//
            if ($webStatusUpd != $webStatusPre) {
                if ($webStatusUpd == 2) {
                    Mail::to($emailCC)
                        ->queue(new WebsiteReviewNotice($mailData));
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/home')->with('fail', 'Something went wrong, Please try again');
        }

        return redirect()->back()->with('success', 'Chapter has successfully updated');
    }


        // $chapterId = $id;
        // $user = $request->user();
        // $lastUpdatedBy = $user->first_name.' '.$user->last_name;

        // $chapterInfoPre = DB::table('chapters')
        //     ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'st.state_short_name as statename',
        //         'chapters.conference_id as conference', 'chapters.primary_coordinator_id as cor_id', 'bd.first_name as ch_pre_fname', 'bd.last_name as ch_pre_lname',
        //         'bd.email as ch_pre_email', 'cd.email as cor_email')
        //     ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
        //     ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
        //     ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
        //     // ->where('chapters.is_Active', '=', '1')
        //     ->where('chapters.id', $id)
        //     ->orderByDesc('chapters.id')
        //     ->get();

        // $chState = $chapterInfoPre[0]->statename;
        // $chConfId = $chapterInfoPre[0]->conference;
        // $chPCId = $chapterInfoPre[0]->cor_id;
        // $pc_email = $chapterInfoPre[0]->cor_email;

        // $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        // if (empty(trim($ch_webstatus))) {
        //     $ch_webstatus = 0; // Set it to 0 if it's blank
        // }

        // $website = $request->input('ch_website');
        // // Ensure it starts with "http://" or "https://"
        // if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
        //     $website = 'http://'.$website;
        // }

        // $presInfoPre = DB::table('chapters')
        //     ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'bd.street_address as street',
        //         'bd.city as city', 'bd.zip as zip', 'st.state_short_name as state')
        //     ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
        //     ->leftJoin('state as st', 'bd.state', '=', 'st.id')
        //     ->where('chapters.is_Active', '=', '1')
        //     ->where('bd.board_position_id', '=', '1')
        //     ->where('chapters.id', $id)
        //     ->get();

        // $AVPInfoPre = DB::table('chapters')
        //     ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
        //     ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
        //     ->where('chapters.is_Active', '=', '1')
        //     ->where('bd.board_position_id', '=', '2')
        //     ->where('chapters.id', $id)
        //     ->get();

        // $MVPInfoPre = DB::table('chapters')
        //     ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
        //     ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
        //     ->where('chapters.is_Active', '=', '1')
        //     ->where('bd.board_position_id', '=', '3')
        //     ->where('chapters.id', $id)
        //     ->get();

        // $tresInfoPre = DB::table('chapters')
        //     ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
        //     ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
        //     ->where('chapters.is_Active', '=', '1')
        //     ->where('bd.board_position_id', '=', '4')
        //     ->where('chapters.id', $id)
        //     ->get();

        // $secInfoPre = DB::table('chapters')
        //     ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
        //     ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
        //     ->where('chapters.is_Active', '=', '1')
        //     ->where('bd.board_position_id', '=', '5')
        //     ->where('chapters.id', $id)
        //     ->get();

        // $chapter = Chapters::find($chapterId);
        // DB::beginTransaction();
        // try {
        //     $chapter->website_url = $website;
        //     $chapter->website_status = $request->input('ch_webstatus');
        //     $chapter->email = $request->input('ch_email');
        //     $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
        //     $chapter->egroup = $request->input('ch_onlinediss');
        //     $chapter->social1 = $request->input('ch_social1');
        //     $chapter->social2 = $request->input('ch_social2');
        //     $chapter->social3 = $request->input('ch_social3');
        //     $chapter->po_box = $request->input('ch_pobox');
        //     $chapter->last_updated_by = $lastUpdatedBy;
        //     $chapter->last_updated_date = date('Y-m-d H:i:s');

        //     $chapter->save();

        //     //President Info
        //     if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
        //         $PREDetails = DB::table('boards')
        //             ->select('id as board_id', 'user_id')
        //             ->where('chapter_id', '=', $chapterId)
        //             ->where('board_position_id', '=', '1')
        //             ->get();
        //         if (count($PREDetails) != 0) {
        //             $userId = $PREDetails[0]->user_id;
        //             $boardId = $PREDetails[0]->board_id;

        //             $user = User::find($userId);
        //             $user->first_name = $request->input('ch_pre_fname');
        //             $user->last_name = $request->input('ch_pre_lname');
        //             $user->email = $request->input('ch_pre_email');
        //             $user->updated_at = now();
        //             $user->save();

        //             $board = Boards::find($boardId);
        //             $board->first_name = $request->input('ch_pre_fname');
        //             $board->last_name = $request->input('ch_pre_lname');
        //             $board->email = $request->input('ch_pre_email');
        //             $board->street_address = $request->input('ch_pre_street');
        //             $board->city = $request->input('ch_pre_city');
        //             $board->state = $request->input('ch_pre_state');
        //             $board->zip = $request->input('ch_pre_zip');
        //             $board->country = 'USA';
        //             $board->phone = $request->input('ch_pre_phone');
        //             $board->last_updated_by = $lastUpdatedBy;
        //             $board->last_updated_date = now();
        //             $board->save();
        //         }
        //     }
        //     //AVP Info
        //     $AVPDetails = DB::table('boards')
        //         ->select('id as board_id', 'user_id')
        //         ->where('chapter_id', '=', $chapterId)
        //         ->where('board_position_id', '=', '2')
        //         ->get();
        //     if (count($AVPDetails) != 0) {
        //         $userId = $AVPDetails[0]->user_id;
        //         $boardId = $AVPDetails[0]->board_id;
        //         if ($request->input('AVPVacant') == 'on') {
        //             //Delete Details of Board memebers
        //             DB::table('boards')
        //                 ->where('id', $boardId)
        //                 ->delete();
        //             //Delete Details of Board memebers from users table
        //             DB::table('users')
        //                 ->where('id', $userId)
        //                 ->delete();
        //         } else {
        //             $user = User::find($userId);
        //             $user->first_name = $request->input('ch_avp_fname');
        //             $user->last_name = $request->input('ch_avp_lname');
        //             $user->email = $request->input('ch_avp_email');
        //             $user->updated_at = now();
        //             $user->save();

        //             $board = Boards::find($boardId);
        //             $board->first_name = $request->input('ch_avp_fname');
        //             $board->last_name = $request->input('ch_avp_lname');
        //             $board->email = $request->input('ch_avp_email');
        //             $board->street_address = $request->input('ch_avp_street');
        //             $board->city = $request->input('ch_avp_city');
        //             $board->state = $request->input('ch_avp_state');
        //             $board->zip = $request->input('ch_avp_zip');
        //             $board->country = 'USA';
        //             $board->phone = $request->input('ch_avp_phone');
        //             $board->last_updated_by = $lastUpdatedBy;
        //             $board->last_updated_date = now();
        //             $board->save();
        //         }
        //     } else {
        //         if ($request->input('AVPVacant') != 'on') {
        //             $userId = DB::table('users')->insertGetId(
        //                 ['first_name' => $request->input('ch_avp_fname'),
        //                     'last_name' => $request->input('ch_avp_lname'),
        //                     'email' => $request->input('ch_avp_email'),
        //                     'password' => Hash::make('TempPass4You'),
        //                     'user_type' => 'board',
        //                     'is_active' => 1]
        //             );

        //             $boardId = DB::table('boards')->insertGetId(
        //                 ['user_id' => $userId,
        //                     'first_name' => $request->input('ch_avp_fname'),
        //                     'last_name' => $request->input('ch_avp_lname'),
        //                     'email' => $request->input('ch_avp_email'),
        //                     'board_position_id' => 2,
        //                     'chapter_id' => $chapterId,
        //                     'street_address' => $request->input('ch_avp_street'),
        //                     'city' => $request->input('ch_avp_city'),
        //                     'state' => $request->input('ch_avp_state'),
        //                     'zip' => $request->input('ch_avp_zip'),
        //                     'country' => 'USA',
        //                     'phone' => $request->input('ch_avp_phone'),
        //                     'last_updated_by' => $lastUpdatedBy,
        //                     'last_updated_date' => date('Y-m-d H:i:s'),
        //                     'is_active' => 1]
        //             );
        //         }
        //     }
        //     //MVP Info
        //     $MVPDetails = DB::table('boards')
        //         ->select('id as board_id', 'user_id')
        //         ->where('chapter_id', '=', $chapterId)
        //         ->where('board_position_id', '=', '3')
        //         ->get();
        //     if (count($MVPDetails) != 0) {
        //         $userId = $MVPDetails[0]->user_id;
        //         $boardId = $MVPDetails[0]->board_id;
        //         if ($request->input('MVPVacant') == 'on') {
        //             //Delete Details of Board memebers
        //             DB::table('boards')
        //                 ->where('id', $boardId)
        //                 ->delete();
        //             //Delete Details of Board memebers from users table
        //             DB::table('users')
        //                 ->where('id', $userId)
        //                 ->delete();
        //         } else {
        //             $user = User::find($userId);
        //             $user->first_name = $request->input('ch_mvp_fname');
        //             $user->last_name = $request->input('ch_mvp_lname');
        //             $user->email = $request->input('ch_mvp_email');
        //             $user->updated_at = date('Y-m-d H:i:s');
        //             $user->save();

        //             $board = Boards::find($boardId);
        //             $board->first_name = $request->input('ch_mvp_fname');
        //             $board->last_name = $request->input('ch_mvp_lname');
        //             $board->email = $request->input('ch_mvp_email');
        //             $board->street_address = $request->input('ch_mvp_street');
        //             $board->city = $request->input('ch_mvp_city');
        //             $board->state = $request->input('ch_mvp_state');
        //             $board->zip = $request->input('ch_mvp_zip');
        //             $board->country = 'USA';
        //             $board->phone = $request->input('ch_mvp_phone');
        //             $board->last_updated_by = $lastUpdatedBy;
        //             $board->last_updated_date = now();
        //             $board->save();
        //         }
        //     } else {
        //         if ($request->input('MVPVacant') != 'on') {
        //             $userId = DB::table('users')->insertGetId(
        //                 ['first_name' => $request->input('ch_mvp_fname'),
        //                     'last_name' => $request->input('ch_mvp_lname'),
        //                     'email' => $request->input('ch_mvp_email'),
        //                     'password' => Hash::make('TempPass4You'),
        //                     'user_type' => 'board',
        //                     'is_active' => 1]
        //             );

        //             $boardId = DB::table('boards')->insertGetId(
        //                 ['user_id' => $userId,
        //                     'first_name' => $request->input('ch_mvp_fname'),
        //                     'last_name' => $request->input('ch_mvp_lname'),
        //                     'email' => $request->input('ch_mvp_email'),
        //                     'board_position_id' => 3,
        //                     'chapter_id' => $chapterId,
        //                     'street_address' => $request->input('ch_mvp_street'),
        //                     'city' => $request->input('ch_mvp_city'),
        //                     'state' => $request->input('ch_mvp_state'),
        //                     'zip' => $request->input('ch_mvp_zip'),
        //                     'country' => 'USA',
        //                     'phone' => $request->input('ch_mvp_phone'),
        //                     'last_updated_by' => $lastUpdatedBy,
        //                     'last_updated_date' => date('Y-m-d H:i:s'),
        //                     'is_active' => 1]
        //             );
        //         }
        //     }
        //     //TRS Info
        //     $TRSDetails = DB::table('boards')
        //         ->select('id as board_id', 'user_id')
        //         ->where('chapter_id', '=', $chapterId)
        //         ->where('board_position_id', '=', '4')
        //         ->get();
        //     if (count($TRSDetails) != 0) {
        //         $userId = $TRSDetails[0]->user_id;
        //         $boardId = $TRSDetails[0]->board_id;
        //         if ($request->input('TreasVacant') == 'on') {
        //             //Delete Details of Board memebers
        //             DB::table('boards')
        //                 ->where('id', $boardId)
        //                 ->delete();
        //             //Delete Details of Board memebers from users table
        //             DB::table('users')
        //                 ->where('id', $userId)
        //                 ->delete();
        //         } else {
        //             $user = User::find($userId);
        //             $user->first_name = $request->input('ch_trs_fname');
        //             $user->last_name = $request->input('ch_trs_lname');
        //             $user->email = $request->input('ch_trs_email');
        //             $user->updated_at = date('Y-m-d H:i:s');
        //             $user->save();

        //             $board = Boards::find($boardId);
        //             $board->first_name = $request->input('ch_trs_fname');
        //             $board->last_name = $request->input('ch_trs_lname');
        //             $board->email = $request->input('ch_trs_email');
        //             $board->street_address = $request->input('ch_trs_street');
        //             $board->city = $request->input('ch_trs_city');
        //             $board->state = $request->input('ch_trs_state');
        //             $board->zip = $request->input('ch_trs_zip');
        //             $board->country = 'USA';
        //             $board->phone = $request->input('ch_trs_phone');
        //             $board->last_updated_by = $lastUpdatedBy;
        //             $board->last_updated_date = now();
        //             $board->save();
        //         }
        //     } else {
        //         if ($request->input('TreasVacant') != 'on') {
        //             $userId = DB::table('users')->insertGetId(
        //                 ['first_name' => $request->input('ch_trs_fname'),
        //                     'last_name' => $request->input('ch_trs_lname'),
        //                     'email' => $request->input('ch_trs_email'),
        //                     'password' => Hash::make('TempPass4You'),
        //                     'user_type' => 'board',
        //                     'is_active' => 1]
        //             );

        //             $boardId = DB::table('boards')->insertGetId(
        //                 ['user_id' => $userId,
        //                     'first_name' => $request->input('ch_trs_fname'),
        //                     'last_name' => $request->input('ch_trs_lname'),
        //                     'email' => $request->input('ch_trs_email'),
        //                     'board_position_id' => 4,
        //                     'chapter_id' => $chapterId,
        //                     'street_address' => $request->input('ch_trs_street'),
        //                     'city' => $request->input('ch_trs_city'),
        //                     'state' => $request->input('ch_trs_state'),
        //                     'zip' => $request->input('ch_trs_zip'),
        //                     'country' => 'USA',
        //                     'phone' => $request->input('ch_trs_phone'),
        //                     'last_updated_by' => $lastUpdatedBy,
        //                     'last_updated_date' => date('Y-m-d H:i:s'),
        //                     'is_active' => 1]
        //             );
        //         }
        //     }
        //     //SEC Info
        //     $SECDetails = DB::table('boards')
        //         ->select('id as board_id', 'user_id')
        //         ->where('chapter_id', '=', $chapterId)
        //         ->where('board_position_id', '=', '5')
        //         ->get();
        //     if (count($SECDetails) != 0) {
        //         $userId = $SECDetails[0]->user_id;
        //         $boardId = $SECDetails[0]->board_id;
        //         if ($request->input('SecVacant') == 'on') {
        //             //Delete Details of Board memebers
        //             DB::table('boards')
        //                 ->where('id', $boardId)
        //                 ->delete();
        //             //Delete Details of Board memebers from users table
        //             DB::table('users')
        //                 ->where('id', $userId)
        //                 ->delete();
        //         } else {
        //             $user = User::find($userId);
        //             $user->first_name = $request->input('ch_sec_fname');
        //             $user->last_name = $request->input('ch_sec_lname');
        //             $user->email = $request->input('ch_sec_email');
        //             $user->updated_at = date('Y-m-d H:i:s');
        //             $user->save();

        //             $board = Boards::find($boardId);
        //             $board->first_name = $request->input('ch_sec_fname');
        //             $board->last_name = $request->input('ch_sec_lname');
        //             $board->email = $request->input('ch_sec_email');
        //             $board->street_address = $request->input('ch_sec_street');
        //             $board->city = $request->input('ch_sec_city');
        //             $board->state = $request->input('ch_sec_state');
        //             $board->zip = $request->input('ch_sec_zip');
        //             $board->country = 'USA';
        //             $board->phone = $request->input('ch_sec_phone');
        //             $board->last_updated_by = $lastUpdatedBy;
        //             $board->last_updated_date = now();
        //             $board->save();
        //         }
        //     } else {
        //         if ($request->input('SecVacant') != 'on') {
        //             $userId = DB::table('users')->insertGetId(
        //                 ['first_name' => $request->input('ch_sec_fname'),
        //                     'last_name' => $request->input('ch_sec_lname'),
        //                     'email' => $request->input('ch_sec_email'),
        //                     'password' => Hash::make('TempPass4You'),
        //                     'user_type' => 'board',
        //                     'is_active' => 1]
        //             );

        //             $boardId = DB::table('boards')->insertGetId(
        //                 ['user_id' => $userId,
        //                     'first_name' => $request->input('ch_sec_fname'),
        //                     'last_name' => $request->input('ch_sec_lname'),
        //                     'email' => $request->input('ch_sec_email'),
        //                     'board_position_id' => 5,
        //                     'chapter_id' => $chapterId,
        //                     'street_address' => $request->input('ch_sec_street'),
        //                     'city' => $request->input('ch_sec_city'),
        //                     'state' => $request->input('ch_sec_state'),
        //                     'zip' => $request->input('ch_sec_zip'),
        //                     'country' => 'USA',
        //                     'phone' => $request->input('ch_sec_phone'),
        //                     'last_updated_by' => $lastUpdatedBy,
        //                     'last_updated_date' => date('Y-m-d H:i:s'),
        //                     'is_active' => 1]
        //             );
        //         }
        //     }

        //     //Website Notifications//
        //     $chId = $chapter['id'];
        //     $chPcid = $chPCId;
        //     $chConf = $chConfId;

        //     $emailData = $this->userController->loadConferenceCoord($chPcid);
        //     $to_CCemail = $emailData['cc_email'];

        //     if ($request->input('ch_webstatus') != $request->input('ch_hid_webstatus')) {

        //         $mailData = [
        //             'chapter_name' => $request->input('ch_name'),
        //             'chapter_state' => $request->input('ch_state'),
        //             'ch_website_url' => $website,
        //         ];

        //         if ($request->input('ch_webstatus') == 1) {
        //             Mail::to($to_CCemail)
        //                 ->queue(new WebsiteAddNoticeAdmin($mailData));
        //         }

        //         if ($request->input('ch_webstatus') == 2) {
        //             Mail::to($to_CCemail)
        //                 ->queue(new WebsiteReviewNotice($mailData));
        //         }
        //     }

        //     //Update Chapter MailData//
        //     $chapterInfoUpd = DB::table('chapters')
        //         ->select('chapters.*', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cd.email as cor_email', 'st.state_short_name as state',
        //             'chapters.conference_id as conference', 'chapters.primary_coordinator_id as cor_id')
        //         ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
        //         ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
        //         ->where('chapters.is_Active', '=', '1')
        //         ->where('chapters.id', $chapterId)
        //         ->orderByDesc('chapters.id')
        //         ->get();

        //     $presInfoUpd = DB::table('chapters')
        //         ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email', 'bd.phone as phone', 'bd.street_address as street',
        //             'bd.city as city', 'bd.zip as zip', 'st.state_short_name as state')
        //         ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
        //         ->leftJoin('state as st', 'bd.state', '=', 'st.id')
        //         ->where('chapters.is_Active', '=', '1')
        //         ->where('bd.board_position_id', '=', '1')
        //         ->where('chapters.id', $chapterId)
        //         ->orderByDesc('chapters.id')
        //         ->get();

        //     $AVPInfoUpd = DB::table('chapters')
        //         ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
        //         ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
        //         ->where('chapters.is_Active', '=', '1')
        //         ->where('bd.board_position_id', '=', '2')
        //         ->where('chapters.id', $chapterId)
        //         ->get();

        //     $MVPInfoUpd = DB::table('chapters')
        //         ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
        //         ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
        //         ->where('chapters.is_Active', '=', '1')
        //         ->where('bd.board_position_id', '=', '3')
        //         ->where('chapters.id', $chapterId)
        //         ->get();

        //     $tresInfoUpd = DB::table('chapters')
        //         ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
        //         ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
        //         ->where('chapters.is_Active', '=', '1')
        //         ->where('bd.board_position_id', '=', '4')
        //         ->where('chapters.id', $chapterId)
        //         ->get();

        //     $secInfoUpd = DB::table('chapters')
        //         ->select('bd.first_name as bor_f_name', 'bd.last_name as bor_l_name', 'bd.email as bor_email')
        //         ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
        //         ->where('chapters.is_Active', '=', '1')
        //         ->where('bd.board_position_id', '=', '5')
        //         ->where('chapters.id', $chapterId)
        //         ->get();

        //     $mailDataPres = [
        //         'chapter_name' => $request->input('ch_name'),
        //         'chapter_state' => $request->input('ch_state'),
        //         'chapterNameUpd' => $chapterInfoUpd[0]->name,
        //         'chapterStateUpd' => $chapterInfoUpd[0]->state,
        //         'cor_fnameUpd' => $chapterInfoUpd[0]->cor_f_name,
        //         'cor_lnameUpd' => $chapterInfoUpd[0]->cor_l_name,
        //         'updated_byUpd' => $chapterInfoUpd[0]->last_updated_date,
        //         'chapfnameUpd' => $presInfoUpd[0]->bor_f_name,
        //         'chaplnameUpd' => $presInfoUpd[0]->bor_l_name,
        //         'chapteremailUpd' => $presInfoUpd[0]->bor_email,
        //         'streetUpd' => $presInfoUpd[0]->street,
        //         'cityUpd' => $presInfoUpd[0]->city,
        //         'stateUpd' => $presInfoUpd[0]->state,
        //         'zipUpd' => $presInfoUpd[0]->zip,
        //         'phoneUpd' => $presInfoUpd[0]->phone,
        //         'inConUpd' => $chapterInfoUpd[0]->inquiries_contact,
        //         'chapemailUpd' => $chapterInfoUpd[0]->email,
        //         'poBoxUpd' => $chapterInfoUpd[0]->po_box,
        //         'webUrlUpd' => $chapterInfoUpd[0]->website_url,
        //         'webStatusUpd' => $chapterInfoUpd[0]->website_status,
        //         'egroupUpd' => $chapterInfoUpd[0]->egroup,
        //         'chapfnamePre' => $presInfoPre[0]->bor_f_name,
        //         'chaplnamePre' => $presInfoPre[0]->bor_l_name,
        //         'chapteremailPre' => $presInfoPre[0]->bor_email,
        //         'streetPre' => $presInfoPre[0]->street,
        //         'cityPre' => $presInfoPre[0]->city,
        //         'statePre' => $presInfoPre[0]->state,
        //         'zipPre' => $presInfoPre[0]->zip,
        //         'phonePre' => $presInfoPre[0]->phone,
        //         'inConPre' => $chapterInfoPre[0]->inquiries_contact,
        //         'chapemailPre' => $chapterInfoPre[0]->email,
        //         'poBoxPre' => $chapterInfoPre[0]->po_box,
        //         'webUrlPre' => $chapterInfoPre[0]->website_url,
        //         'webStatusPre' => $chapterInfoPre[0]->website_status,
        //         'egroupPre' => $chapterInfoPre[0]->egroup,
        //     ];
        //     $mailData = array_merge($mailDataPres);
        //     if ($AVPInfoUpd !== null && count($AVPInfoUpd) > 0) {
        //         $mailDataAvp = ['avpfnameUpd' => $AVPInfoUpd[0]->bor_f_name,
        //             'avplnameUpd' => $AVPInfoUpd[0]->bor_l_name,
        //             'avpemailUpd' => $AVPInfoUpd[0]->bor_email, ];
        //         $mailData = array_merge($mailData, $mailDataAvp);
        //     } else {
        //         $mailDataAvp = ['avpfnameUpd' => '',
        //             'avplnameUpd' => '',
        //             'avpemailUpd' => '', ];
        //         $mailData = array_merge($mailData, $mailDataAvp);
        //     }
        //     if ($MVPInfoUpd !== null && count($MVPInfoUpd) > 0) {
        //         $mailDataMvp = ['mvpfnameUpd' => $MVPInfoUpd[0]->bor_f_name,
        //             'mvplnameUpd' => $MVPInfoUpd[0]->bor_l_name,
        //             'mvpemailUpd' => $MVPInfoUpd[0]->bor_email, ];
        //         $mailData = array_merge($mailData, $mailDataMvp);
        //     } else {
        //         $mailDataMvp = ['mvpfnameUpd' => '',
        //             'mvplnameUpd' => '',
        //             'mvpemailUpd' => '', ];
        //         $mailData = array_merge($mailData, $mailDataMvp);
        //     }
        //     if ($tresInfoUpd !== null && count($tresInfoUpd) > 0) {
        //         $mailDatatres = ['tresfnameUpd' => $tresInfoUpd[0]->bor_f_name,
        //             'treslnameUpd' => $tresInfoUpd[0]->bor_l_name,
        //             'tresemailUpd' => $tresInfoUpd[0]->bor_email, ];
        //         $mailData = array_merge($mailData, $mailDatatres);
        //     } else {
        //         $mailDatatres = ['tresfnameUpd' => '',
        //             'treslnameUpd' => '',
        //             'tresemailUpd' => '', ];
        //         $mailData = array_merge($mailData, $mailDatatres);
        //     }
        //     if ($secInfoUpd !== null && count($secInfoUpd) > 0) {
        //         $mailDataSec = ['secfnameUpd' => $secInfoUpd[0]->bor_f_name,
        //             'seclnameUpd' => $secInfoUpd[0]->bor_l_name,
        //             'secemailUpd' => $secInfoUpd[0]->bor_email, ];
        //         $mailData = array_merge($mailData, $mailDataSec);
        //     } else {
        //         $mailDataSec = ['secfnameUpd' => '',
        //             'seclnameUpd' => '',
        //             'secemailUpd' => '', ];
        //         $mailData = array_merge($mailData, $mailDataSec);
        //     }
        //     if ($AVPInfoPre !== null && count($AVPInfoPre) > 0) {
        //         $mailDataAvpp = ['avpfnamePre' => $AVPInfoPre[0]->bor_f_name,
        //             'avplnamePre' => $AVPInfoPre[0]->bor_l_name,
        //             'avpemailPre' => $AVPInfoPre[0]->bor_email, ];
        //         $mailData = array_merge($mailData, $mailDataAvpp);
        //     } else {
        //         $mailDataAvpp = ['avpfnamePre' => '',
        //             'avplnamePre' => '',
        //             'avpemailPre' => '', ];
        //         $mailData = array_merge($mailData, $mailDataAvpp);
        //     }
        //     if ($MVPInfoPre !== null && count($MVPInfoPre) > 0) {
        //         $mailDataMvpp = ['mvpfnamePre' => $MVPInfoPre[0]->bor_f_name,
        //             'mvplnamePre' => $MVPInfoPre[0]->bor_l_name,
        //             'mvpemailPre' => $MVPInfoPre[0]->bor_email, ];
        //         $mailData = array_merge($mailData, $mailDataMvpp);
        //     } else {
        //         $mailDataMvpp = ['mvpfnamePre' => '',
        //             'mvplnamePre' => '',
        //             'mvpemailPre' => '', ];
        //         $mailData = array_merge($mailData, $mailDataMvpp);
        //     }
        //     if ($tresInfoPre !== null && count($tresInfoPre) > 0) {
        //         $mailDatatresp = ['tresfnamePre' => $tresInfoPre[0]->bor_f_name,
        //             'treslnamePre' => $tresInfoPre[0]->bor_l_name,
        //             'tresemailPre' => $tresInfoPre[0]->bor_email, ];
        //         $mailData = array_merge($mailData, $mailDatatresp);
        //     } else {
        //         $mailDatatresp = ['tresfnamePre' => '',
        //             'treslnamePre' => '',
        //             'tresemailPre' => '', ];
        //         $mailData = array_merge($mailData, $mailDatatresp);
        //     }
        //     if ($secInfoPre !== null && count($secInfoPre) > 0) {
        //         $mailDataSecp = ['secfnamePre' => $secInfoPre[0]->bor_f_name,
        //             'seclnamePre' => $secInfoPre[0]->bor_l_name,
        //             'secemailPre' => $secInfoPre[0]->bor_email, ];
        //         $mailData = array_merge($mailData, $mailDataSecp);
        //     } else {
        //         $mailDataSecp = ['secfnamePre' => '',
        //             'seclnamePre' => '',
        //             'secemailPre' => '', ];
        //         $mailData = array_merge($mailData, $mailDataSecp);
        //     }

        //     //Primary Coordinator Notification//
        //     $to_email = $chapterInfoUpd[0]->cor_email;

        //     if ($presInfoUpd[0]->bor_email != $presInfoPre[0]->bor_email || $presInfoUpd[0]->street != $presInfoPre[0]->street || $presInfoUpd[0]->city != $presInfoPre[0]->city ||
        //         $presInfoUpd[0]->state != $presInfoPre[0]->state || $presInfoUpd[0]->bor_f_name != $presInfoPre[0]->bor_f_name || $presInfoUpd[0]->bor_l_name != $presInfoPre[0]->bor_l_name ||
        //             $presInfoUpd[0]->zip != $presInfoPre[0]->zip || $presInfoUpd[0]->phone != $presInfoPre[0]->phone || $chapterInfoUpd[0]->inquiries_contact != $chapterInfoPre[0]->inquiries_contact ||
        //             $chapterInfoUpd[0]->email != $chapterInfoPre[0]->email || $chapterInfoUpd[0]->po_box != $chapterInfoPre[0]->po_box || $chapterInfoUpd[0]->website_url != $chapterInfoPre[0]->website_url ||
        //             $chapterInfoUpd[0]->website_status != $chapterInfoPre[0]->website_status || $chapterInfoUpd[0]->egroup != $chapterInfoPre[0]->egroup ||
        //             $mailDataAvpp['avpfnamePre'] != $mailDataAvp['avpfnameUpd'] || $mailDataAvpp['avplnamePre'] != $mailDataAvp['avplnameUpd'] || $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] ||
        //             $mailDataMvpp['mvpfnamePre'] != $mailDataMvp['mvpfnameUpd'] || $mailDataMvpp['mvplnamePre'] != $mailDataMvp['mvplnameUpd'] || $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] ||
        //             $mailDatatresp['tresfnamePre'] != $mailDatatres['tresfnameUpd'] || $mailDatatresp['treslnamePre'] != $mailDatatres['treslnameUpd'] || $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] ||
        //             $mailDataSecp['secfnamePre'] != $mailDataSec['secfnameUpd'] || $mailDataSecp['seclnamePre'] != $mailDataSec['seclnameUpd'] || $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {

        //         Mail::to($to_email)
        //             ->queue(new ChaptersUpdatePrimaryCoorPresident($mailData));
        //     }

        //     //List Admin Notification//
        //     $to_email2 = 'listadmin@momsclub.org';

        //     if ($chapterInfoUpd[0]->email != $chapterInfoPre[0]->email || $presInfoUpd[0]->bor_email != $presInfoPre[0]->bor_email ||
        //     $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] || $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] ||
        //     $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] || $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {
        //         Mail::to($to_email2)
        //             ->queue(new ChapersUpdateListAdmin($mailData));
        //     }

        //     DB::commit();
        // } catch (\Exception $e) {
        //     // Rollback Transaction
        //     echo $e->getMessage();
        //     exit();
        //     DB::rollback();
        //     // Log the error
        //     Log::error($e);

    //         return redirect()->to('/home')->with('fail', 'Something went wrong, Please try again');
    //     }

    //     return redirect()->back()->with('success', 'Chapter has successfully updated');
    // }

    /**
     * Update Board Details Board Member Login
     */
    public function updateMember(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName = $user->first_name.' '.$user->last_name;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $bdPositionid = $bdDetails->board_position_id;
        $lastUpdatedBy = $bdDetails->first_name.' '.$bdDetails->last_name;
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQueryPre = $this->getChapterDetails($id);
        $chDetailsPre = $baseQueryPre['chDetails'];
        // $PresDetailsPre = $baseQueryPre['PresDetails'];
        $AVPDetailsPre = $baseQueryPre['AVPDetails'];
        $MVPDetailsPre = $baseQueryPre['MVPDetails'];
        $TRSDetailsPre = $baseQueryPre['TRSDetails'];
        $SECDetailsPre = $baseQueryPre['SECDetails'];

        if ($bdPositionid == 2) {
            $borDetailsPre = $AVPDetailsPre;
        } elseif ($bdPositionid == 3) {
            $borDetailsPre = $MVPDetailsPre;
        } elseif ($bdPositionid == 4) {
            $borDetailsPre = $TRSDetailsPre;
        } elseif ($bdPositionid == 5) {
            $borDetailsPre = $SECDetailsPre;
        }

        $input = $request->all();
        $webStatusPre = $input['ch_hid_webstatus'];

        // $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        // if (empty(trim($ch_webstatus))) {
        //     $ch_webstatus = 0; // Set it to 0 if it's blank
        // }

        // $website = $request->input('ch_website');
        // // Ensure it starts with "http://" or "https://"
        // if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
        //     $website = 'http://'.$website;
        // }

                // Handle web status - allow null values
$ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
// Only convert to 0 if the website is not null but status is empty
if (!is_null($request->input('ch_website')) && empty(trim($ch_webstatus))) {
    $ch_webstatus = 0;
}

// Handle website URL
$website = $request->input('ch_website');
// Only add http:// if the website field is not null or empty
if (!is_null($website) && !empty(trim($website))) {
    if (!str_starts_with($website, 'http://') && !str_starts_with($website, 'https://')) {
        $website = 'http://' . $website;
    }
}

        $chapter = Chapters::find($id);
        $user = User::find($userId);
        $board = Boards::find($bdId);

        DB::beginTransaction();
        try {
            $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
            $chapter->email = $request->input('ch_email');
            $chapter->po_box = $request->input('ch_pobox');
            $chapter->website_url = $website;
            $chapter->website_status = $request->input('ch_webstatus');
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            // Update User Details
            $user->first_name = $request->input('bor_fname');
            $user->last_name = $request->input('bor_lname');
            $user->email = $request->input('bor_email');
            $user->updated_at = now();
            $user->save();

            // Update Board Details
            $board->first_name = $request->input('bor_fname');
            $board->last_name = $request->input('bor_lname');
            $board->email = $request->input('bor_email');
            $board->phone = $request->input('bor_phone');
            $board->street_address = $request->input('bor_addr');
            $board->city = $request->input('bor_city');
            $board->state = $request->input('bor_state');
            $board->zip = $request->input('bor_zip');
            $board->country = 'USA';
            $board->last_updated_by = $lastUpdatedBy;
            $board->last_updated_date = now();
            $board->save();

            //Update Chapter MailData//
            $baseQueryUpd = $this->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $chConfId = $baseQueryUpd['chConfId'];
            $webStatusUpd = $ch_webstatus;
            $AVPDetailsUpd = $baseQueryUpd['AVPDetails'];
            $MVPDetailsUpd = $baseQueryUpd['MVPDetails'];
            $TRSDetailsUpd = $baseQueryUpd['TRSDetails'];
            $SECDetailsUpd = $baseQueryUpd['SECDetails'];
            $emailCC = $baseQueryUpd['emailCC'];  // CC Email
            $pcDetails = $baseQueryUpd['chDetails']->primaryCoordinator;
            $pcEmail = $pcDetails->email;  // PC Email

            if ($bdPositionid == 2) {
                $borDetailsUpd = $AVPDetailsUpd;
            } elseif ($bdPositionid == 3) {
                $borDetailsUpd = $MVPDetailsUpd;
            } elseif ($bdPositionid == 4) {
                $borDetailsUpd = $TRSDetailsUpd;
            } elseif ($bdPositionid == 5) {
                $borDetailsUpd = $SECDetailsUpd;
            }

            $mailData = [
                'chapter_name' => $chDetailsUpd->name,
                'chapter_state' => $stateShortName,
                'conference' => $chConfId,
                'borposition' => $borDetailsPre->position->position,
                'updated_byUpd' => $lastUpdatedBy,
                'updated_byPre' => $lastupdatedDate,

                'inConPre' => $chDetailsPre->inquiries_contact,
                'chapemailPre' => $chDetailsPre->email,
                'poBoxPre' => $chDetailsPre->po_box,
                'webUrlPre' => $chDetailsPre->website_url,
                'webStatusPre' => $chDetailsPre->website_status,
                'egroupPre' => $chDetailsPre->egroup,
                'borfname' => $borDetailsPre->first_name,
                'borlname' => $borDetailsPre->last_name,
                'boremail' => $borDetailsPre->email,

                'inConUpd' => $chDetailsUpd->inquiries_contact,
                'chapemailUpd' => $chDetailsUpd->email,
                'poBoxUpd' => $chDetailsUpd->po_box,
                'webUrlUpd' => $chDetailsUpd->website_url,
                'webStatusUpd' => $chDetailsUpd->website_status,
                'egroupUpd' => $chDetailsUpd->egroup,
                'borfnameUpd' => $borDetailsUpd->first_name,
                'borlnameUpd' => $borDetailsUpd->last_name,
                'boremailUpd' => $borDetailsUpd->email,

                'ch_website_url' => $website,
            ];

            if ($chDetailsUpd->name != $chDetailsPre->name || $borDetailsUpd->bor_email != $borDetailsPre->bor_email || $borDetailsUpd->street_address != $borDetailsPre->street_address || $borDetailsUpd->city != $borDetailsPre->city ||
                    $borDetailsUpd->state != $borDetailsPre->state || $borDetailsUpd->first_name != $borDetailsPre->first_name || $borDetailsUpd->last_name != $borDetailsPre->last_name ||
                    $borDetailsUpd->zip != $borDetailsPre->zip || $borDetailsUpd->phone != $borDetailsPre->phone || $chDetailsUpd->inquiries_contact != $chDetailsPre->inquiries_contact ||
                    $chDetailsUpd->email != $chDetailsPre->email || $chDetailsUpd->po_box != $chDetailsPre->po_box || $chDetailsUpd->website_url != $chDetailsPre->website_url ||
                    $chDetailsUpd->website_status != $chDetailsPre->website_status || $chDetailsUpd->egroup != $chDetailsPre->egroup) {

                Mail::to($pcEmail)
                    ->queue(new ChapersUpdatePrimaryCoorMember($mailData));
            }

            // //List Admin Notification//
            $to_email2 = 'listadmin@momsclub.org';

            if ($borDetailsUpd->email != $borDetailsPre->email ) {

                Mail::to($to_email2)
                    ->queue(new ChapersUpdateListAdmin($mailData));
            }

            //Website URL Change Notification//
            if ($webStatusUpd != $webStatusPre) {
                if ($webStatusUpd == 2) {
                    Mail::to($emailCC)
                        ->queue(new WebsiteReviewNotice($mailData));
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/home')->with('fail', 'Something went wrong, Please try again');
        }

        return redirect()->back()->with('success', 'Chapter has successfully updated');
    }



        // DB::beginTransaction();
        // try {
        //     $chapterId = $id;
        //     $posId = $request->input('bor_positionid');

        //     // Fetch User Details
        //     $user = $request->user();
        //     $lastUpdatedBy = $user->first_name.' '.$user->last_name;

        //     // Fetch Board Details
        //     $boardDetails = DB::table('boards')
        //         ->select('boards.id as board_id', 'boards.user_id', 'boards.first_name as bor_fname', 'boards.last_name as bor_lname', 'boards.email as bor_email', 'bp.position as bor_position')
        //         ->leftJoin('board_position as bp', 'boards.board_position_id', '=', 'bp.id')
        //         ->where('boards.chapter_id', '=', $chapterId)
        //         ->where('boards.board_position_id', '=', $posId)
        //         ->get();

        //     // Fetch Chapter Info
        //     $chapterInfo = DB::table('chapters')
        //         ->select('chapters.id as chapter_id', 'chapters.name', 'chapters.state_id', 'cd.first_name as cor_fname', 'cd.last_name as cor_lname', 'cd.email as cor_email', 'st.state_short_name as state')
        //         ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
        //         ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
        //         ->where('chapters.id', '=', $chapterId)
        //         ->get();

        //     $ch_webstatus = $request->input('ch_webstatus') ?: $request->input('ch_hid_webstatus');
        //     if (empty(trim($ch_webstatus))) {
        //         $ch_webstatus = 0; // Set it to 0 if it's blank
        //     }

        //     $website = $request->input('ch_website');
        //     // Ensure it starts with "http://" or "https://"
        //     if (! str_starts_with($website, 'http://') && ! str_starts_with($website, 'https://')) {
        //         $website = 'http://'.$website;
        //     }

        //     if (count($boardDetails) != 0) {
        //         $userId = $boardDetails[0]->user_id;
        //         $boardId = $boardDetails[0]->board_id;

        //         // Update User Details
        //         $user = User::find($userId);
        //         $user->first_name = $request->input('bor_fname');
        //         $user->last_name = $request->input('bor_lname');
        //         $user->email = $request->input('bor_email');
        //         $user->updated_at = now();
        //         $user->save();

        //         // Update Board Details
        //         $board = Boards::find($boardId);
        //         $board->first_name = $request->input('bor_fname');
        //         $board->last_name = $request->input('bor_lname');
        //         $board->email = $request->input('bor_email');
        //         $board->phone = $request->input('bor_phone');
        //         $board->street_address = $request->input('bor_addr');
        //         $board->city = $request->input('bor_city');
        //         $board->state = $request->input('bor_state');
        //         $board->zip = $request->input('bor_zip');
        //         $board->country = 'USA';
        //         $board->last_updated_by = $lastUpdatedBy;
        //         $board->last_updated_date = now();
        //         $board->save();

        //         // Update Chapter Details
        //         $chapter = Chapters::find($chapterId);
        //         $chapter->website_url = $website;
        //         $chapter->website_status = $request->input('ch_webstatus');
        //         $chapter->email = $request->input('ch_email');
        //         $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
        //         $chapter->egroup = $request->input('ch_onlinediss');
        //         $chapter->social1 = $request->input('ch_social1');
        //         $chapter->social2 = $request->input('ch_social2');
        //         $chapter->social3 = $request->input('ch_social3');
        //         $chapter->po_box = $request->input('ch_pobox');
        //         $chapter->last_updated_by = $lastUpdatedBy;
        //         $chapter->last_updated_date = date('Y-m-d H:i:s');

        //         $chapter->save();
        //     }

        //     // Fetch Updated Board Details
        //     $boardDetailsUpd = DB::table('boards')
        //         ->select('boards.id as board_id', 'boards.user_id', 'boards.first_name as bor_fname', 'boards.last_name as bor_lname', 'boards.email as bor_email')
        //         ->where('boards.chapter_id', '=', $chapterId)
        //         ->where('boards.board_position_id', '=', $posId)
        //         ->get();

        //     $mailData = [
        //         'cor_fname' => $chapterInfo[0]->cor_fname,
        //         'chapter_name' => $chapterInfo[0]->name,
        //         'chapter_state' => $chapterInfo[0]->state,
        //         'borposition' => $boardDetails[0]->bor_position,
        //         'borfnameUpd' => $boardDetailsUpd[0]->bor_fname,
        //         'borlnameUpd' => $boardDetailsUpd[0]->bor_lname,
        //         'boremailUpd' => $boardDetailsUpd[0]->bor_email,
        //         'borfname' => $boardDetails[0]->bor_fname,
        //         'borlname' => $boardDetails[0]->bor_lname,
        //         'boremail' => $boardDetails[0]->bor_email,
        //     ];

        //     // PC Admin Notification
        //     $to_email = $chapterInfo[0]->cor_email;
        //     if ($boardDetailsUpd[0]->bor_email != $boardDetails[0]->bor_email || $boardDetailsUpd[0]->bor_fname != $boardDetails[0]->bor_fname ||
        //         $boardDetailsUpd[0]->bor_lname != $boardDetails[0]->bor_lname) {

        //         Mail::to($to_email)
        //             ->queue(new ChapersUpdatePrimaryCoorMember($mailData));
        //     }

        //     // List Admin Notification
        //     $to_email2 = 'listadmin@momsclub.org';
        //     if ($boardDetailsUpd[0]->bor_email != $boardDetails[0]->bor_email) {
        //         Mail::to($to_email2)
        //             ->queue(new ChapersUpdateListAdminMember($mailData));
        //     }

        //     DB::commit();
        // } catch (\Exception $e) {
        //     // Rollback Transaction
        //     DB::rollback();
        //     // Log the error
        //     Log::error($e);

    //         return redirect()->to('/home')->with('fail', 'Something went wrong, Please try again');
    //     }

    //     return redirect()->back()->with('success', 'Chapter has successfully updated');
    // }

    /**
     * Show Re-Registrstion Payment Form All Board Members
     */
    public function showReregistrationPaymentForm(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $id = $bdDetails->chapter_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $start_month = $chDetails->start_month_id;
        $next_renewal_year = $chDetails->next_renewal_year;
        $due_date = Carbon::create($next_renewal_year, $start_month, 1);
        $rangeEndDate = $due_date->copy()->subMonth()->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName,
            'startMonthName' => $startMonthName, 'endRange' => $rangeEndDateFormatted, 'startRange' => $rangeStartDateFormatted,
            'thisMonth' => $month, 'due_date' => $due_date, 'user_type' => $user_type,
        ];

        return view('boards.payment')->with($data);
    }

    /**
     * Show M2M Donation Form All Board Members
     */
    public function showM2MDonationForm(Request $request)
    {
        $user = $request->user();

        $borDetails = $user->board;
        // Check if BoardDetails is not found for the user
        if (! $borDetails) {
            return to_route('home');
        }

        $borPositionId = $borDetails['board_position_id'];
        $chapterId = $borDetails['chapter_id'];
        $isActive = $borDetails['is_active'];

        $chapterDetails = Chapters::find($chapterId);
        $stateArr = DB::table('state')
            ->select('state.*')
            ->orderBy('id')
            ->get();

        $chapterState = DB::table('state')
            ->select('state_short_name')
            ->where('id', '=', $chapterDetails->state)
            ->get();
        $chapterState = $chapterState[0]->state_short_name;

        $boardPosition = ['1' => 'President', '2' => 'AVP', '3' => 'MVP', '4' => 'Treasurer', '5' => 'Secretary'];
        $boardPositionCode = $borPositionId;
        $boardPositionAbbreviation = isset($boardPosition[$boardPositionCode]) ? $boardPosition[$boardPositionCode] : '';

        $chapterList = DB::table('chapters as ch')
            ->select('ch.*', 'bd.first_name', 'bd.last_name', 'bd.email as bd_email', 'bd.board_position_id', 'bd.street_address',
                'bd.city', 'bd.zip', 'bd.phone', 'bd.state as bd_state', 'bd.user_id as user_id')
            ->leftJoin('boards as bd', 'ch.id', '=', 'bd.chapter_id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $chapterId)
            ->where('bd.board_position_id', '=', '1')
            ->get();

        $data = ['chapterState' => $chapterState, 'stateArr' => $stateArr, 'chapterList' => $chapterList, 'boardPositionAbbreviation' => $boardPositionAbbreviation];

        return view('boards.donation')->with($data);
    }

    /**
     * Show Chater Resources
     */
    public function showResources(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $id = $bdDetails->chapter_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];

        $resources = Resources::with('categoryName')->get();

        $data = ['stateShortName' => $stateShortName, 'chDetails' => $chDetails, 'resources' => $resources];

        return view('boards.resources')->with($data);
    }

    /**
     * Show EOY BoardInfo All Board Members
     */
    public function showBoardInfo(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;
        $id = $bdDetails->chapter_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];

        $allWebLinks = $baseQuery['allWebLinks'];
        $allStates = $baseQuery['allStates'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $data = ['stateShortName' => $stateShortName, 'startMonthName' => $startMonthName, 'allStates' => $allStates, 'SECDetails' => $SECDetails,
            'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PresDetails' => $PresDetails, 'chDetails' => $chDetails, 'user_type' => $user_type,
            'allWebLinks' => $allWebLinks,
        ];

        return view('boards.boardinfo')->with($data);
    }

    /**
     * Update EOY BoardInfo All Board Members
     */
    public function createBoardInfo(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];

        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];
        $emailCC = $baseQuery['emailCC'];

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

        $chapter = Chapters::find($id);
        $documents = Documents::find($id);

        DB::beginTransaction();
        try {
            $chDetails->inquiries_contact = $request->input('InquiriesContact');
            $chDetails->boundary_issues = $request->input('BoundaryStatus');
            $chDetails->boundary_issue_notes = $issue_note;
            $chDetails->website_url = $website;
            $chDetails->website_status = $ch_webstatus;
            $chDetails->egroup = $request->input('ch_onlinediss');
            $chDetails->social1 = $request->input('ch_social1');
            $chDetails->social2 = $request->input('ch_social2');
            $chDetails->social3 = $request->input('ch_social3');
            $chDetails->last_updated_by = $lastUpdatedBy;
            $chDetails->last_updated_date = date('Y-m-d H:i:s');
            $chDetails->save();

            $documents->new_board_submitted = 1;
            $documents->save();

            //President Info
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $PREDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $id)
                    ->where('board_position_id', '=', '1')
                    ->get();
                $id = $request->input('presID');
                if (count($PREDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $bdId)
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
                            'chapter_id' => $id,
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
                $bdId = $request->input('avpID');
                DB::table('incoming_board_member')
                    ->where('id', $bdId)
                    ->delete();
            }
            if ($request->input('ch_avp_fname') != '' && $request->input('ch_avp_lname') != '' && $request->input('ch_avp_email') != '') {
                $AVPDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $id)
                    ->where('board_position_id', '=', '2')
                    ->get();
                $id = $request->input('avpID');
                if (count($AVPDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $bdId)
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
                            'chapter_id' => $id,
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
                $bdId = $request->input('mvpID');
                DB::table('incoming_board_member')
                    ->where('id', $bdId)
                    ->delete();
            }
            if ($request->input('ch_mvp_fname') != '' && $request->input('ch_mvp_lname') != '' && $request->input('ch_mvp_email') != '') {
                $MVPDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $id)
                    ->where('board_position_id', '=', '3')
                    ->get();
                $id = $request->input('mvpID');
                if (count($MVPDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $bdId)
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
                            'chapter_id' => $id,
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
                $bdId = $request->input('trsID');
                DB::table('incoming_board_member')
                    ->where('id', $bdId)
                    ->delete();
            }
            if ($request->input('ch_trs_fname') != '' && $request->input('ch_trs_lname') != '' && $request->input('ch_trs_email') != '') {
                $TRSDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $id)
                    ->where('board_position_id', '=', '4')
                    ->get();
                $id = $request->input('trsID');
                if (count($TRSDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $bdId)
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
                            'chapter_id' => $id,
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
                $bdId = $request->input('secID');
                DB::table('incoming_board_member')
                    ->where('id', $bdId)
                    ->delete();
            }
            if ($request->input('ch_sec_fname') != '' && $request->input('ch_sec_lname') != '' && $request->input('ch_sec_email') != '') {
                $SECDetails = DB::table('incoming_board_member')
                    ->select('id')
                    ->where('chapter_id', '=', $id)
                    ->where('board_position_id', '=', '5')
                    ->get();
                $id = $request->input('secID');
                if (count($SECDetails) != 0) {
                    DB::table('incoming_board_member')
                        ->where('id', $bdId)
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
                            'chapter_id' => $id,
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

            // Send email to Conference Coordinator
            $to_email = $emailCC;

            // Send email to full Board
            $to_email2 = $emailListChap;

            $mailData = [
                'chapterid' => $id,
                'chapter_name' => $chDetails->name,
                'chapter_state' => $stateShortName,
            ];

            Mail::to($to_email)
                ->queue(new EOYElectionReportSubmitted($mailData));

            Mail::to($to_email2)
                ->queue(new EOYElectionReportThankYou($mailData));

            DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // Log the error
            Log::error($e);

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->back()->with('success', 'Board Info has been Submitted');
    }

    /**
     * Show EOY Financial Report All Board Members
     */
    public function showFinancialReport(Request $request, $chapterId): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;
        $userName = $user->first_name.' '.$user->last_name;
        $userEmail = $user->email;
        $loggedInName = $user->first_name.' '.$user->last_name;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;

        $id = $bdDetails->chapter_id;

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $submitted = $baseQuery['submitted'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $awards = $baseQuery['awards'];
        $allAwards = $baseQuery['allAwards'];

        $resources = Resources::with('categoryName')->get();

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'submitted' => $submitted, 'chDetails' => $chDetails, 'user_type' => $user_type,
            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
            'awards' => $awards, 'allAwards' => $allAwards,
        ];

        return view('boards.financial')->with($data);

    }

    /**
     * Save EOY Financial Report All Board Members
     */
    public function storeFinancialReport(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $user_type = $user->user_type;
        $userName = $user->first_name.' '.$user->last_name;
        $userEmail = $user->email;
        $lastUpdatedBy = $user->first_name.' '.$user->last_name;

        $bdDetails = $request->user()->board;
        $bdId = $bdDetails->id;

        $id = $bdDetails->chapter_id;

        $input = $request->all();
        $farthest_step_visited = $input['FurthestStep'];
        $reportReceived = $input['submitted'];

        $baseQuery = $this->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];
        $emailCC = $baseQuery['emailCC'];
        $cc_id = $baseQuery['cc_id'];
        $reviewerEmail = $baseQuery['reviewerEmail'];

        $roster_path = $chDocuments->roster_path;
        $irs_path = $chDocuments->irs_path;
        $statement_1_path = $chDocuments->statement_1_path;
        $statement_2_path = $chDocuments->statement_2_path;
        $financial_pdf_path = $chDocuments->financial_pdf_path;

        // CHAPTER DUES
        $changed_dues = isset($input['optChangeDues']) ? $input['optChangeDues'] : null;
        $different_dues = isset($input['optNewOldDifferent']) ? $input['optNewOldDifferent'] : null;
        $not_all_full_dues = isset($input['optNoFullDues']) ? $input['optNoFullDues'] : null;
        $total_new_members = $input['TotalNewMembers'];
        $total_renewed_members = $input['TotalRenewedMembers'];
        $dues_per_member = $input['MemberDues'];
        $total_new_members_changed_dues = $input['TotalNewMembersNewFee'];
        $total_renewed_members_changed_dues = $input['TotalRenewedMembersNewFee'];
        $dues_per_member_renewal = $input['MemberDuesRenewal'];
        $dues_per_member_new_changed = $input['NewMemberDues'];
        $dues_per_member_renewal_changed = $input['NewMemberDuesRenewal'];
        $members_who_paid_no_dues = $input['MembersNoDues'];
        $members_who_paid_partial_dues = $input['TotalPartialDuesMembers'];
        $total_partial_fees_collected = $input['PartialDuesMemberDues'];
        $total_associate_members = $input['TotalAssociateMembers'];
        $associate_member_fee = $input['AssociateMemberDues'];

        // MONTHLY MEETING EXPENSES
        $manditory_meeting_fees_paid = $input['ManditoryMeetingFeesPaid'];
        $voluntary_donations_paid = $input['VoluntaryDonationsPaid'];
        $meeting_speakers = isset($input['MeetingSpeakers']) ? $input['MeetingSpeakers'] : null;
        $meeting_speakers_array = isset($input['Speakers']) ? $input['Speakers'] : null;
        $discussion_topic_frequency = isset($input['SpeakerFrequency']) ? $input['SpeakerFrequency'] : null;
        $childrens_room_sitters = isset($input['ChildrensRoom']) ? $input['ChildrensRoom'] : null;
        $paid_baby_sitters = $input['PaidBabySitters'];

        $ChildrenRoomArray = null;
        $FieldCount = $input['ChildrensExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $ChildrenRoomArray[$i]['childrens_room_desc'] = $input['ChildrensRoomDesc'.$i] ?? null;
            $ChildrenRoomArray[$i]['childrens_room_supplies'] = $input['ChildrensRoomSupplies'.$i] ?? null;
            $ChildrenRoomArray[$i]['childrens_room_other'] = $input['ChildrensRoomOther'.$i] ?? null;
        }
        $childrens_room_expenses = base64_encode(serialize($ChildrenRoomArray));

        // SERVICE PROJECTS
        $at_least_one_service_project = isset($input['PerformServiceProject']) ? $input['PerformServiceProject'] : null;
        $at_least_one_service_project_explanation = $input['PerformServiceProjectExplanation'];
        $contributions_not_registered_charity = isset($input['ContributionsNotRegNP']) ? $input['ContributionsNotRegNP'] : null;
        $contributions_not_registered_charity_explanation = $input['ContributionsNotRegNPExplanation'];

        $ServiceProjectFields = null;
        $FieldCount = $input['ServiceProjectRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $ServiceProjectFields[$i]['service_project_desc'] = $input['ServiceProjectDesc'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_income'] = $input['ServiceProjectIncome'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_supplies'] = $input['ServiceProjectSupplies'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_charity'] = $input['ServiceProjectDonatedCharity'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_m2m'] = $input['ServiceProjectDonatedM2M'.$i] ?? null;
        }
        $service_project_array = base64_encode(serialize($ServiceProjectFields));

        // PARTIES & MEMBER BENEFITS
        $PartyExpenseFields = null;
        $FieldCount = $input['PartyExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $PartyExpenseFields[$i]['party_expense_desc'] = $input['PartyDesc'.$i] ?? null;
            $PartyExpenseFields[$i]['party_expense_income'] = $input['PartyIncome'.$i] ?? null;
            $PartyExpenseFields[$i]['party_expense_expenses'] = $input['PartyExpenses'.$i] ?? null;
        }
        $party_expense_array = base64_encode(serialize($PartyExpenseFields));

        // OFFICE & OPERATING EXPENSES
        $office_printing_costs = $input['PrintingCosts'];
        $office_postage_costs = $input['PostageCosts'];
        $office_membership_pins_cost = $input['MembershipPins'];

        $OfficeOtherArray = null;
        $FieldCount = $input['OfficeExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $OfficeOtherArray[$i]['office_other_desc'] = $input['OfficeDesc'.$i] ?? null;
            $OfficeOtherArray[$i]['office_other_expense'] = $input['OfficeExpenses'.$i] ?? null;
        }
        $office_other_expenses = base64_encode(serialize($OfficeOtherArray));

        // INTERNATIONAL EVENTS & RE-REGISTRATION
        $annual_registration_fee = $input['AnnualRegistrationFee'];
        $international_event = isset($input['InternationalEvent']) ? $input['InternationalEvent'] : null;

        $InternationalEventArray = null;
        $FieldCount = $input['InternationalEventRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $InternationalEventArray[$i]['intl_event_desc'] = $input['InternationalEventDesc'.$i] ?? null;
            $InternationalEventArray[$i]['intl_event_income'] = $input['InternationalEventIncome'.$i] ?? null;
            $InternationalEventArray[$i]['intl_event_expenses'] = $input['InternationalEventExpense'.$i] ?? null;
        }
        $international_event_array = base64_encode(serialize($InternationalEventArray));

        // DONATIONS TO CHAPTER
        $MonetaryDonation = null;
        $FieldCount = $input['MonDonationRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $MonetaryDonation[$i]['mon_donation_desc'] = $input['DonationDesc'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_info'] = $input['DonorInfo'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_date'] = $input['MonDonationDate'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_amount'] = $input['DonationAmount'.$i] ?? null;
        }
        $monetary_donations_to_chapter = base64_encode(serialize($MonetaryDonation));

        $NonMonetaryDonation = null;
        $FieldCount = $input['NonMonDonationRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $NonMonetaryDonation[$i]['nonmon_donation_desc'] = $input['NonMonDonationDesc'.$i] ?? null;
            $NonMonetaryDonation[$i]['nonmon_donation_info'] = $input['NonMonDonorInfo'.$i] ?? null;
            $NonMonetaryDonation[$i]['nonmon_donation_date'] = $input['NonMonDonationDate'.$i] ?? null;
        }
        $non_monetary_donations_to_chapter = base64_encode(serialize($NonMonetaryDonation));

        // OTHER INCOME & EXPENSES
        $OtherOffice = null;
        $FieldCount = $input['OtherOfficeExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $OtherOffice[$i]['other_desc'] = $input['OtherOfficeDesc'.$i] ?? null;
            $OtherOffice[$i]['other_expenses'] = $input['OtherOfficeExpenses'.$i] ?? null;
            $OtherOffice[$i]['other_income'] = $input['OtherOfficeIncome'.$i] ?? null;
        }
        $other_income_and_expenses_array = base64_encode(serialize($OtherOffice));

        // BANK RECONCILLIATION
        $bank_statement_included = isset($input['BankStatementIncluded']) ? $input['BankStatementIncluded'] : null;
        $bank_statement_included_explanation = $input['BankStatementIncludedExplanation'];
        $wheres_the_money = $input['WheresTheMoney'];
        $amount_reserved_from_previous_year = $input['AmountReservedFromLastYear'];
        $amount_reserved_from_previous_year = str_replace(',', '', $amount_reserved_from_previous_year);
        $amount_reserved_from_previous_year = $amount_reserved_from_previous_year === '' ? null : $amount_reserved_from_previous_year;
        $bank_balance_now = $input['BankBalanceNow'];
        $bank_balance_now = str_replace(',', '', $bank_balance_now);
        $bank_balance_now = $bank_balance_now === '' ? null : $bank_balance_now;
        $BankRecArray = null;
        $FieldCount = $input['BankRecRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $BankRecArray[$i]['bank_rec_date'] = $input['BankRecDate'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_check_no'] = $input['BankRecCheckNo'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_desc'] = $input['BankRecDesc'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_payment_amount'] = $input['BankRecPaymentAmount'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_desposit_amount'] = $input['BankRecDepositAmount'.$i] ?? null;
        }
        $bank_reconciliation_array = base64_encode(serialize($BankRecArray));

        // 990 IRS FILING
        $file_irs = isset($input['FileIRS']) ? $input['FileIRS'] : null;
        $file_irs_explanation = $input['FileIRSExplanation'];

        // CHPATER QUESTIONS
        //Question 1
        $bylaws_available = isset($input['ByLawsAvailable']) ? $input['ByLawsAvailable'] : null;
        $bylaws_available_explanation = $input['ByLawsAvailableExplanation'];
        //Question 2
        $vote_all_activities = isset($input['VoteAllActivities']) ? $input['VoteAllActivities'] : null;
        $vote_all_activities_explanation = $input['VoteAllActivitiesExplanation'];
        //Question 3
        $child_outings = isset($input['ChildOutings']) ? $input['ChildOutings'] : null;
        $child_outings_explanation = $input['ChildOutingsExplanation'];
        //Question 4
        $playgroups = isset($input['Playgroups']) ? $input['Playgroups'] : null;
        $had_playgroups_explanation = $input['PlaygroupsExplanation'];
        //Question 5
        $park_day_frequency = isset($input['ParkDays']) ? $input['ParkDays'] : null;
        $park_day_frequency_explanation = $input['ParkDaysExplanation'];
        //Question 6
        $mother_outings = isset($input['MotherOutings']) ? $input['MotherOutings'] : null;
        $mother_outings_explanation = $input['MotherOutingsExplanation'];
        //Question 7
        $activity_array = isset($input['Activity']) ? $input['Activity'] : null;
        $activity_other_explanation = $input['ActivityOtherExplanation'];
        //Question 8
        $offered_merch = isset($input['OfferedMerch']) ? $input['OfferedMerch'] : null;
        $offered_merch_explanation = $input['OfferedMerchExplanation'];
        //Question 9
        $bought_merch = isset($input['BoughtMerch']) ? $input['BoughtMerch'] : null;
        $bought_merch_explanation = $input['BoughtMerchExplanation'];
        //Question 10
        $purchase_pins = isset($input['BoughtPins']) ? $input['BoughtPins'] : null;
        $purchase_pins_explanation = $input['BoughtPinsExplanation'];
        //Question 11
        $receive_compensation = isset($input['ReceiveCompensation']) ? $input['ReceiveCompensation'] : null;
        $receive_compensation_explanation = $input['ReceiveCompensationExplanation'];
        //Question 12
        $financial_benefit = isset($input['FinancialBenefit']) ? $input['FinancialBenefit'] : null;
        $financial_benefit_explanation = $input['FinancialBenefitExplanation'];
        //Question 13
        $influence_political = isset($input['InfluencePolitical']) ? $input['InfluencePolitical'] : null;
        $influence_political_explanation = $input['InfluencePoliticalExplanation'];
        //Question 14
        $sister_chapter = isset($input['SisterChapter']) ? $input['SisterChapter'] : null;
        $sister_chapter_explanation = $input['SisterChapterExplanation'];

        // AWARDS
        $ChapterAwards = null;
        $FieldCount = $input['ChapterAwardsRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $ChapterAwards[$i]['awards_type'] = $input['ChapterAwardsType'.$i] ?? null;
            $ChapterAwards[$i]['awards_desc'] = $input['ChapterAwardsDesc'.$i] ?? null;
        }
        $chapter_awards = base64_encode(serialize($ChapterAwards));

        if (isset($input['AwardsAgree']) && $input['AwardsAgree'] == false) {
            $award_agree = 0;
        } elseif (isset($input['AwardsAgree'])) {
            $award_agree = 1;
        } else {
            $award_agree = null;
        }

        $report = FinancialReport::find($id);
        $documents = Documents::find($id);
        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
            $report->changed_dues = $changed_dues;
            $report->different_dues = $different_dues;
            $report->not_all_full_dues = $not_all_full_dues;
            $report->total_new_members = $total_new_members;
            $report->total_renewed_members = $total_renewed_members;
            $report->dues_per_member = $dues_per_member;
            $report->total_new_members_changed_dues = $total_new_members_changed_dues;
            $report->total_renewed_members_changed_dues = $total_renewed_members_changed_dues;
            $report->dues_per_member_renewal = $dues_per_member_renewal;
            $report->dues_per_member_new_changed = $dues_per_member_new_changed;
            $report->dues_per_member_renewal_changed = $dues_per_member_renewal_changed;
            $report->members_who_paid_no_dues = $members_who_paid_no_dues;
            $report->members_who_paid_partial_dues = $members_who_paid_partial_dues;
            $report->total_partial_fees_collected = $total_partial_fees_collected;
            $report->total_associate_members = $total_associate_members;
            $report->associate_member_fee = $associate_member_fee;
            $report->manditory_meeting_fees_paid = $manditory_meeting_fees_paid;
            $report->voluntary_donations_paid = $voluntary_donations_paid;
            $report->paid_baby_sitters = $paid_baby_sitters;
            $report->childrens_room_expenses = $childrens_room_expenses;
            $report->service_project_array = $service_project_array;
            $report->party_expense_array = $party_expense_array;
            $report->office_printing_costs = $office_printing_costs;
            $report->office_postage_costs = $office_postage_costs;
            $report->office_membership_pins_cost = $office_membership_pins_cost;
            $report->office_other_expenses = $office_other_expenses;
            $report->international_event_array = $international_event_array;
            $report->annual_registration_fee = $annual_registration_fee;
            $report->monetary_donations_to_chapter = $monetary_donations_to_chapter;
            $report->non_monetary_donations_to_chapter = $non_monetary_donations_to_chapter;
            $report->other_income_and_expenses_array = $other_income_and_expenses_array;
            $report->amount_reserved_from_previous_year = $amount_reserved_from_previous_year;
            $report->bank_balance_now = $bank_balance_now;
            $report->bank_reconciliation_array = $bank_reconciliation_array;
            $report->receive_compensation = $receive_compensation;
            $report->receive_compensation_explanation = $receive_compensation_explanation;
            $report->financial_benefit = $financial_benefit;
            $report->financial_benefit_explanation = $financial_benefit_explanation;
            $report->influence_political = $influence_political;
            $report->influence_political_explanation = $influence_political_explanation;
            $report->vote_all_activities = $vote_all_activities;
            $report->vote_all_activities_explanation = $vote_all_activities_explanation;
            $report->purchase_pins = $purchase_pins;
            $report->purchase_pins_explanation = $purchase_pins_explanation;
            $report->bought_merch = $bought_merch;
            $report->bought_merch_explanation = $bought_merch_explanation;
            $report->offered_merch = $offered_merch;
            $report->offered_merch_explanation = $offered_merch_explanation;
            $report->bylaws_available = $bylaws_available;
            $report->bylaws_available_explanation = $bylaws_available_explanation;
            $report->childrens_room_sitters = $childrens_room_sitters;
            $report->playgroups = $playgroups;
            $report->had_playgroups_explanation = $had_playgroups_explanation;
            $report->child_outings = $child_outings;
            $report->child_outings_explanation = $child_outings_explanation;
            $report->mother_outings = $mother_outings;
            $report->mother_outings_explanation = $mother_outings_explanation;
            $report->meeting_speakers = $meeting_speakers;
            $report->meeting_speakers_array = $meeting_speakers_array;
            $report->discussion_topic_frequency = $discussion_topic_frequency;
            $report->park_day_frequency = $park_day_frequency;
            $report->park_day_frequency_explanation = $park_day_frequency_explanation;
            $report->activity_array = $activity_array;
            $report->activity_other_explanation = $activity_other_explanation;
            $report->contributions_not_registered_charity = $contributions_not_registered_charity;
            $report->contributions_not_registered_charity_explanation = $contributions_not_registered_charity_explanation;
            $report->at_least_one_service_project = $at_least_one_service_project;
            $report->at_least_one_service_project_explanation = $at_least_one_service_project_explanation;
            $report->sister_chapter = $sister_chapter;
            $report->sister_chapter_explanation = $sister_chapter_explanation;
            $report->international_event = $international_event;
            $report->file_irs = $file_irs;
            $report->file_irs_explanation = $file_irs_explanation;
            $report->bank_statement_included = $bank_statement_included;
            $report->bank_statement_included_explanation = $bank_statement_included_explanation;
            $report->wheres_the_money = $wheres_the_money;
            $report->chapter_awards = $chapter_awards;
            $report->award_agree = $award_agree;
            $report->farthest_step_visited = $farthest_step_visited;
            $report->completed_name = $userName;
            $report->completed_email = $userEmail;
            $report->submitted = date('Y-m-d H:i:s');

            $mailData = [
                'chapterid' => $id,
                'chapter_name' => $chDetails->name,
                'chapter_state' => $stateShortName,
                'completed_name' => $userName,
                'completed_email' => $userEmail,
                'roster_path' => $roster_path,
                'file_irs_path' => $irs_path,
                'bank_statement_included_path' => $statement_1_path,
                'bank_statement_2_included_path' => $statement_2_path,
                'financial_pdf_path' => $financial_pdf_path,
            ];

            // Send emails
            $to_email = $emailCC;
            $to_email3 = $reviewerEmail;
            $to_email2 = $userEmail;
            $to_email4 = $emailListChap;

            if ($reportReceived == 1) {
                $pdfPath = $this->pdfController->generateAndSavePdf($id, $userId);   // Generate and save the PDF
                Mail::to($to_email2)
                    ->cc($to_email4)
                    ->queue(new EOYFinancialReportThankYou($mailData, $pdfPath));

                if ($chFinancialReport->reviewer_id == null) {
                    DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [$cc_id, $id]);
                    Mail::to($to_email)
                        ->queue(new EOYFinancialSubmitted($mailData, $pdfPath));
                }

                if ($chFinancialReport->reviewer_id != null) {
                    Mail::to($to_email3)
                        ->queue(new EOYFinancialSubmitted($mailData, $pdfPath));
                }
            }

            $report->save();

            if ($reportReceived == 1) {
                $documents->financial_report_received = 1;
                $documents->report_received = date('Y-m-d H:i:s');
            }

            $documents->save();

            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');
            $chapter->save();

            DB::commit();
            if ($reportReceived == 1) {
                return redirect()->back()->with('success', 'Report has been successfully Submitted');
            } else {
                return redirect()->back()->with('success', 'Report has been successfully updated');
            }

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong Please try again.');
        }
    }

    public function getRosterfile(): BinaryFileResponse
    {
        $filename = 'roster_template.xlsx';

        $file_path = '/home/momsclub/public_html/mimi/storage/app/public';

        return Response::download($file_path, $filename, [
            'Content-Length: '.filesize($file_path),
        ]);
    }
}
