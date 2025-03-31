<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckCurrentPasswordBoardRequest;
use App\Http\Requests\UpdatePasswordBoardRequest;
use App\Mail\ChapersUpdateListAdmin;
use App\Mail\ChapersUpdatePrimaryCoorMember;
use App\Mail\ChaptersUpdatePrimaryCoorPresident;
use App\Mail\EOYElectionReportSubmitted;
use App\Mail\EOYElectionReportThankYou;
use App\Mail\EOYFinancialReportThankYou;
use App\Mail\EOYFinancialSubmitted;
use App\Mail\WebsiteReviewNotice;
use App\Models\Admin;
use App\Models\Boards;
use App\Models\Chapters;
use App\Models\Documents;
use App\Models\FinancialReport;
use App\Models\incomingboard;
use App\Models\Resources;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BoardController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseBoardController;

    protected $pdfController;

    protected $baseMailDataController;

    protected $financialReportController;

        public function __construct(UserController $userController, BaseBoardController $baseBoardController, PDFController $pdfController,
        BaseMailDataController $baseMailDataController, FinancialReportController $financialReportController)
    {
        $this->userController = $userController;
        $this->pdfController = $pdfController;
        $this->baseBoardController = $baseBoardController;
        $this->baseMailDataController = $baseMailDataController;
        $this->financialReportController = $financialReportController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndBoard::class,
        ];
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
     * View Board Details President Login
     */
    public function editPresident(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $chId = $user['user_chapterId'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
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

        $displayEOY = $baseQuery['displayEOY'];
        $displayTESTING = $displayEOY['displayTESTING'];
        $displayLIVE = $displayEOY['displayLIVE'];

        $admin = Admin::orderByDesc('id')
            ->limit(1)
            ->first();
        $display_testing = ($admin->display_testing == 1);
        $display_live = ($admin->display_live == 1);

        $data = ['chDetails' => $chDetails, 'chFinancialReport' => $chFinancialReport, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'allWebLinks' => $allWebLinks,
            'PresDetails' => $PresDetails, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails,
            'startMonthName' => $startMonthName, 'thisMonth' => $month, 'due_date' => $due_date, 'userType' => $userType,
            'displayTESTING' => $displayTESTING, 'displayLIVE' => $displayLIVE, 'chDocuments' => $chDocuments,
        ];

        return view('boards.president')->with($data);
    }

    /**
     * View Board Details President Login
     */
    public function editMember(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $bdPositionId = $user['user_bdPositionId'];
        $chId = $user['user_chapterId'];
        $borDetails = $user['user_bdDetails'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
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

        if ($bdPositionId == 2) {
            $borDetails = $AVPDetails;
        } elseif ($bdPositionId == 3) {
            $borDetails = $MVPDetails;
        } elseif ($bdPositionId == 4) {
            $borDetails = $TRSDetails;
        } elseif ($bdPositionId == 5) {
            $borDetails = $SECDetails;
        }

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $start_month = $chDetails->start_month_id;
        $next_renewal_year = $chDetails->next_renewal_year;
        $due_date = Carbon::create($next_renewal_year, $start_month, 1);
        // $due_date = Carbon::create($next_renewal_year, $start_month, 1)->endOfMonth();

        $admin = Admin::orderByDesc('id')
            ->limit(1)
            ->first();
        $display_testing = ($admin->display_testing == 1);
        $display_live = ($admin->display_live == 1);

        $data = ['chDetails' => $chDetails, 'chFinancialReport' => $chFinancialReport, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'allWebLinks' => $allWebLinks,
            'borDetails' => $borDetails, 'startMonthName' => $startMonthName, 'thisMonth' => $month, 'due_date' => $due_date, 'userType' => $userType,
            'display_testing' => $display_testing, 'display_live' => $display_live, 'chDocuments' => $chDocuments,
        ];

        return view('boards.member')->with($data);
    }

    /**
     * Update Board Details President Login
     */
    public function updatePresident(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQuery = $this->baseBoardController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $pcDetails = $baseQuery['pcDetails'];
        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $input = $request->all();
        $webStatusPre = $input['ch_hid_webstatus'];

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

        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
            $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
            $chapter->email = $request->input('ch_email');
            $chapter->po_box = $request->input('ch_pobox');
            $chapter->website_url = $website;
            $chapter->website_status = $ch_webstatus;
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            // President Info
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $chapter = Chapters::with('president')->find($id);
                $president = $chapter->president;
                $user = $president->user;

                $user->update([   // Update user details
                    'first_name' => $request->input('ch_pre_fname'),
                    'last_name' => $request->input('ch_pre_lname'),
                    'email' => $request->input('ch_pre_email'),
                    'updated_at' => $lastupdatedDate,
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
                    'last_updated_date' => $lastupdatedDate,
                ]);
            }

            // AVP Info
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
                        'updated_at' => $lastupdatedDate,
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
                        'last_updated_date' => $lastupdatedDate,
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
                        'last_updated_date' => $lastupdatedDate,
                        // 'is_active' => 1,
                    ]);
                }
            }

            // MVP Info
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
                        'updated_at' => $lastupdatedDate,
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
                        'last_updated_date' => $lastupdatedDate,
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
                        'last_updated_date' => $lastupdatedDate,
                        // 'is_active' => 1,
                    ]);
                }
            }

            // TRS Info
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
                        'updated_at' => $lastupdatedDate,
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
                        'last_updated_date' => $lastupdatedDate,
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
                        'last_updated_date' => $lastupdatedDate,
                        // 'is_active' => 1,
                    ]);
                }
            }

            // SEC Info
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
                        'updated_at' => $lastupdatedDate,
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
                        'last_updated_date' => $lastupdatedDate,
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
                        'last_updated_date' => $lastupdatedDate,
                        // 'is_active' => 1,
                    ]);
                }
            }

            // Update Chapter MailData//
            $baseQueryUpd = $this->baseBoardController->getChapterDetails($id);
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
            $pcDetailsUpd = $baseQueryUpd['chDetails']->primaryCoordinator;
            $pcEmail = $pcDetailsUpd->email;  // PC Email
            // $pcDetails = $baseQueryUpd['chDetails']->primaryCoordinator;
            // $pcEmail = $pcDetails->email;  // PC Email
            $EINCordEmail = 'jackie.mchenry@momsclub.org';  // EIN Coor Email

            $mailDataPres = array_merge(
                $this->baseMailDataController->getChapterData($chDetailsUpd, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getPresData($PresDetails),
                $this->baseMailDataController->getPresUpdatedData($PresDetailsUpd),
                $this->baseMailDataController->getChapterUpdatedData($chDetailsUpd, $pcDetailsUpd),
                [
                    'ch_website_url' => $website,
                ]
            );

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

            if ($AVPDetails !== null) {
                $mailDataAvpp = ['avpfname' => $AVPDetails->first_name,
                    'avplname' => $AVPDetails->last_name,
                    'avpemail' => $AVPDetails->email, ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            } else {
                $mailDataAvpp = ['avpfname' => '',
                    'avplname' => '',
                    'avpemail' => '', ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            }
            if ($MVPDetails !== null) {
                $mailDataMvpp = ['mvpfname' => $MVPDetails->first_name,
                    'mvplname' => $MVPDetails->last_name,
                    'mvpemail' => $MVPDetails->email, ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            } else {
                $mailDataMvpp = ['mvpfname' => '',
                    'mvplname' => '',
                    'mvpemail' => '', ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            }
            if ($TRSDetails !== null) {
                $mailDatatresp = ['tresfname' => $TRSDetails->first_name,
                    'treslname' => $TRSDetails->last_name,
                    'tresemail' => $TRSDetails->email, ];
                $mailData = array_merge($mailData, $mailDatatresp);
            } else {
                $mailDatatresp = ['tresfname' => '',
                    'treslname' => '',
                    'tresemail' => '', ];
                $mailData = array_merge($mailData, $mailDatatresp);
            }
            if ($SECDetails !== null) {
                $mailDataSecp = ['secfname' => $SECDetails->first_name,
                    'seclname' => $SECDetails->last_name,
                    'secemail' => $SECDetails->email, ];
                $mailData = array_merge($mailData, $mailDataSecp);
            } else {
                $mailDataSecp = ['secfname' => '',
                    'seclname' => '',
                    'secemail' => '', ];
                $mailData = array_merge($mailData, $mailDataSecp);
            }

            if ($chDetailsUpd->name != $chDetails->name || $PresDetailsUpd->bor_email != $PresDetails->bor_email || $PresDetailsUpd->street_address != $PresDetails->street_address || $PresDetailsUpd->city != $PresDetails->city ||
                    $PresDetailsUpd->state != $PresDetails->state || $PresDetailsUpd->first_name != $PresDetails->first_name || $PresDetailsUpd->last_name != $PresDetails->last_name ||
                    $PresDetailsUpd->zip != $PresDetails->zip || $PresDetailsUpd->phone != $PresDetails->phone || $chDetailsUpd->inquiries_contact != $chDetails->inquiries_contact ||
                    $chDetailsUpd->email != $chDetails->email || $chDetailsUpd->po_box != $chDetails->po_box || $chDetailsUpd->website_url != $chDetails->website_url ||
                    $chDetailsUpd->website_status != $chDetails->website_status || $chDetailsUpd->egroup != $chDetails->egroup ||
                    $mailDataAvpp['avpfname'] != $mailDataAvp['avpfnameUpd'] || $mailDataAvpp['avplname'] != $mailDataAvp['avplnameUpd'] || $mailDataAvpp['avpemail'] != $mailDataAvp['avpemailUpd'] ||
                    $mailDataMvpp['mvpfname'] != $mailDataMvp['mvpfnameUpd'] || $mailDataMvpp['mvplname'] != $mailDataMvp['mvplnameUpd'] || $mailDataMvpp['mvpemail'] != $mailDataMvp['mvpemailUpd'] ||
                    $mailDatatresp['tresfname'] != $mailDatatres['tresfnameUpd'] || $mailDatatresp['treslname'] != $mailDatatres['treslnameUpd'] || $mailDatatresp['tresemail'] != $mailDatatres['tresemailUpd'] ||
                    $mailDataSecp['secfname'] != $mailDataSec['secfnameUpd'] || $mailDataSecp['seclname'] != $mailDataSec['seclnameUpd'] || $mailDataSecp['secemail'] != $mailDataSec['secemailUpd']) {

                Mail::to($pcEmail)
                    ->queue(new ChaptersUpdatePrimaryCoorPresident($mailData));
            }

            // //List Admin Notification//
            $to_email2 = 'listadmin@momsclub.org';

            if ($PresDetailsUpd->email != $PresDetails->email || $PresDetailsUpd->email != $PresDetails->email || $mailDataAvpp['avpemail'] != $mailDataAvp['avpemailUpd'] ||
                        $mailDataMvpp['mvpemail'] != $mailDataMvp['mvpemailUpd'] || $mailDatatresp['tresemail'] != $mailDatatres['tresemailUpd'] ||
                        $mailDataSecp['secemail'] != $mailDataSec['secemailUpd']) {

                Mail::to($to_email2)
                    ->queue(new ChapersUpdateListAdmin($mailData));
            }

            // Website URL Change Notification//
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

    /**
     * Update Board Details Board Member Login
     */
    public function updateMember(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $bdId = $user['user_bdId'];
        $bdPositionid = $user['user_bdPositionId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQuery = $this->baseBoardController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $pcDetails = $baseQuery['pcDetails'];
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

        $input = $request->all();
        $webStatusPre = $input['ch_hid_webstatus'];

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
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            // Update User Details
            $user->first_name = $request->input('bor_fname');
            $user->last_name = $request->input('bor_lname');
            $user->email = $request->input('bor_email');
            $user->updated_at = $lastupdatedDate;
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
            $board->last_updated_date = $lastupdatedDate;
            $board->save();

            // Update Chapter MailData//
            $baseQueryUpd = $this->baseBoardController->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $webStatusUpd = $ch_webstatus;
            $AVPDetailsUpd = $baseQueryUpd['AVPDetails'];
            $MVPDetailsUpd = $baseQueryUpd['MVPDetails'];
            $TRSDetailsUpd = $baseQueryUpd['TRSDetails'];
            $SECDetailsUpd = $baseQueryUpd['SECDetails'];
            $emailCC = $baseQueryUpd['emailCC'];  // CC Email
            $pcDetailsUpd = $baseQueryUpd['chDetails']->primaryCoordinator;
            $pcEmail = $pcDetailsUpd->email;  // PC Email

            if ($bdPositionid == 2) {
                $borDetailsUpd = $AVPDetailsUpd;
            } elseif ($bdPositionid == 3) {
                $borDetailsUpd = $MVPDetailsUpd;
            } elseif ($bdPositionid == 4) {
                $borDetailsUpd = $TRSDetailsUpd;
            } elseif ($bdPositionid == 5) {
                $borDetailsUpd = $SECDetailsUpd;
            }

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getBoardData($borDetails),
                $this->baseMailDataController->getBoardUpdatedData($borDetailsUpd),
                $this->baseMailDataController->getChapterUpdatedData($chDetailsUpd, $pcDetailsUpd),
                [
                    'ch_website_url' => $website,
                ]
            );

            if ($chDetailsUpd->name != $chDetails->name || $borDetailsUpd->bor_email != $borDetails->bor_email || $borDetailsUpd->street_address != $borDetails->street_address || $borDetailsUpd->city != $borDetails->city ||
                    $borDetailsUpd->state != $borDetails->state || $borDetailsUpd->first_name != $borDetails->first_name || $borDetailsUpd->last_name != $borDetails->last_name ||
                    $borDetailsUpd->zip != $borDetails->zip || $borDetailsUpd->phone != $borDetails->phone || $chDetailsUpd->inquiries_contact != $chDetails->inquiries_contact ||
                    $chDetailsUpd->email != $chDetails->email || $chDetailsUpd->po_box != $chDetails->po_box || $chDetailsUpd->website_url != $chDetails->website_url ||
                    $chDetailsUpd->website_status != $chDetails->website_status || $chDetailsUpd->egroup != $chDetails->egroup) {

                Mail::to($pcEmail)
                    ->queue(new ChapersUpdatePrimaryCoorMember($mailData));
            }

            // //List Admin Notification//
            $to_email2 = 'listadmin@momsclub.org';

            if ($borDetailsUpd->email != $borDetails->email) {

                Mail::to($to_email2)
                    ->queue(new ChapersUpdateListAdmin($mailData));
            }

            // Website URL Change Notification//
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

    /**
     * Show Re-Registrstion Payment Form All Board Members
     */
    public function editReregistrationPaymentForm(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $chId = $user['user_chapterId'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
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
            'thisMonth' => $month, 'due_date' => $due_date, 'userType' => $userType,
        ];

        return view('boards.payment')->with($data);
    }

    /**
     * Show M2M Donation Form All Board Members
     */
    public function editM2MDonationForm(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $chId = $user['user_chapterId'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'userType' => $userType,
        ];

        return view('boards.donation')->with($data);
    }

    /**
     * Show Chater Resources
     */
    public function viewResources(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $chId = $user['user_chapterId'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];

        $resources = Resources::with('resourceCategory')->get();

        $data = ['stateShortName' => $stateShortName, 'chDetails' => $chDetails, 'resources' => $resources];

        return view('boards.resources')->with($data);
    }

    /**
     * Show EOY BoardInfo All Board Members
     */
    public function editBoardReport(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $chId = $user['user_chapterId'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
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
            'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PresDetails' => $PresDetails, 'chDetails' => $chDetails, 'userType' => $userType,
            'allWebLinks' => $allWebLinks,
        ];

        return view('boards.boardinfo')->with($data);
    }

    /**
     * Update EOY BoardInfo All Board Members
     */
    public function updateBoardReport(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $chId = $user['user_chapterId'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];
        $emailCC = $baseQuery['emailCC'];

        $input = $request->all();

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

        $boundaryStatus = $request->input('BoundaryStatus');
        $issue_note = $request->input('BoundaryIssue');
        // Boundary Issues Correct 0 | Not Correct 1
        if ($boundaryStatus == 0) {
            $issue_note = '';
        }

        $chapter = Chapters::find($chId);
        $documents = Documents::find($id);

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

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            );

            Mail::to($emailCC)
                ->queue(new EOYElectionReportSubmitted($mailData));

            Mail::to($emailListChap)
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
    public function editFinancialReport(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userName = $loggedInName = $user['user_name'];
        $userEmail = $user['user_email'];
        $chId = $user['user_chapterId'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $chIsActive =  $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        // $submitted = $baseQuery['submitted'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $awards = $baseQuery['awards'];
        $allAwards = $baseQuery['allAwards'];

        $resources = Resources::with('resourceCategory')->get();

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userType' => $userType,
            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
            'awards' => $awards, 'allAwards' => $allAwards, 'chIsActive' => $chIsActive
        ];

        return view('boards.financial')->with($data);

    }

    /**
     * Save EOY Financial Report All Board Members
     */
    public function updateFinancialReport(Request $request, $chapterId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userName = $user['user_name'];
        $userEmail = $user['user_email'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $input = $request->all();
        $reportReceived = $input['submitted']?? null;

        $financialReport = FinancialReport::find($chapterId);
        $documents = Documents::find($chapterId);
        $chapter = Chapters::find($chapterId);

        DB::beginTransaction();
        try{
            $this->financialReportController->saveAccordionFields($financialReport, $input);

            if ($reportReceived == 1) {
                $financialReport->completed_name = $userName;
                $financialReport->completed_email = $userEmail;
                $financialReport->submitted = $lastupdatedDate;
            }

            $financialReport->save();

            if ($reportReceived == 1) {
                $documents->financial_report_received = 1;
                $documents->report_received = $lastupdatedDate;

                $documents->save();
            }

            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;

            $chapter->save();

            $baseQuery = $this->baseBoardController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chDocuments = $baseQuery['chDocuments'];
            $chFinancialReport = $baseQuery['chFinancialReport'];
            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];
            $pcDetails = $baseQuery['pcDetails'];
            $emailCC = $baseQuery['emailCC'];
            $cc_id = $baseQuery['cc_id'];
            $reviewerEmail = $baseQuery['reviewerEmail'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPCData($pcDetails),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport),
            );

            if ($reportReceived == 1) {
                $pdfPath = $this->pdfController->saveFinancialReport($request, $chapterId);   // Generate and Send the PDF
                Mail::to($userEmail)
                    ->cc($emailListChap)
                    ->queue(new EOYFinancialReportThankYou($mailData, $pdfPath));

                if ($chFinancialReport->reviewer_id == null) {
                    DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [$cc_id, $chapterId]);
                    Mail::to($emailCC)
                        ->queue(new EOYFinancialSubmitted($mailData, $pdfPath));
                }

                if ($chFinancialReport->reviewer_id != null) {
                    Mail::to($reviewerEmail)
                        ->queue(new EOYFinancialSubmitted($mailData, $pdfPath));
                }
            }

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


    /**
     * Save EOY Financial Report Accordion
     */
    // protected function saveAccordionFields($financialReport, $input)
    // {
    //     $financialReport->farthest_step_visited = $input['FurthestStep'];

    //     // CHAPTER DUES
    //     $financialReport->changed_dues = $input['optChangeDues'] ?? null;
    //     $financialReport->different_dues = $input['optNewOldDifferent'] ?? null;
    //     $financialReport->not_all_full_dues = $input['optNoFullDues'] ?? null;
    //     $financialReport->total_new_members = $input['TotalNewMembers'] ?? null;
    //     $financialReport->total_renewed_members = $input['TotalRenewedMembers'] ?? null;
    //     $financialReport->dues_per_member = $input['MemberDues'] ?? null;
    //     $financialReport->total_new_members_changed_dues = $input['TotalNewMembersNewFee'] ?? null;
    //     $financialReport->total_renewed_members_changed_dues = $input['TotalRenewedMembersNewFee'] ?? null;
    //     $financialReport->dues_per_member_renewal = $input['MemberDuesRenewal'] ?? null;
    //     $financialReport->dues_per_member_new_changed = $input['NewMemberDues'] ?? null;
    //     $financialReport->dues_per_member_renewal_changed = $input['NewMemberDuesRenewal'] ?? null;
    //     $financialReport->members_who_paid_no_dues = $input['MembersNoDues'] ?? null;
    //     $financialReport->members_who_paid_partial_dues = $input['TotalPartialDuesMembers'] ?? null;
    //     $financialReport->total_partial_fees_collected = $input['PartialDuesMemberDues'] ?? null;
    //     $financialReport->total_associate_members = $input['TotalAssociateMembers'] ?? null;
    //     $financialReport->associate_member_fee = $input['AssociateMemberDues'] ?? null;

    //     // MONTHLY MEETING EXPENSES
    //     $financialReport->manditory_meeting_fees_paid = $input['ManditoryMeetingFeesPaid'] ?? null;
    //     $financialReport->voluntary_donations_paid = $input['VoluntaryDonationsPaid'] ?? null;
    //     $financialReport->meeting_speakers = $input['MeetingSpeakers'] ?? null;
    //     $financialReport->meeting_speakers_array = $input['Speakers'] ?? null;
    //     $financialReport->discussion_topic_frequency = $input['SpeakerFrequency'] ?? null;
    //     $financialReport->childrens_room_sitters = $input['ChildrensRoom'] ?? null;
    //     $financialReport->paid_baby_sitters = $input['PaidBabySitters'] ?? null;

    //     // Children Room Expenses (serialized)
    //     $ChildrenRoomArray = null;
    //     $FieldCount = $input['ChildrensExpenseRowCount'];
    //     for ($i = 0; $i < $FieldCount; $i++) {
    //         $ChildrenRoomArray[$i]['childrens_room_desc'] = $input['ChildrensRoomDesc'.$i] ?? null;
    //         $ChildrenRoomArray[$i]['childrens_room_supplies'] = $input['ChildrensRoomSupplies'.$i] ?? null;
    //         $ChildrenRoomArray[$i]['childrens_room_other'] = $input['ChildrensRoomOther'.$i] ?? null;
    //     }
    //     $financialReport->childrens_room_expenses = base64_encode(serialize($ChildrenRoomArray));

    //     // SERVICE PROJECTS
    //     $financialReport->at_least_one_service_project = $input['PerformServiceProject'] ?? null;
    //     $financialReport->at_least_one_service_project_explanation = $input['PerformServiceProjectExplanation'] ?? null;
    //     $financialReport->contributions_not_registered_charity = $input['ContributionsNotRegNP'] ?? null;
    //     $financialReport->contributions_not_registered_charity_explanation = $input['ContributionsNotRegNPExplanation'] ?? null;

    //     // Service Projects (serialized)
    //     $ServiceProjectFields = null;
    //     $FieldCount = $input['ServiceProjectRowCount'];
    //     for ($i = 0; $i < $FieldCount; $i++) {
    //         $ServiceProjectFields[$i]['service_project_desc'] = $input['ServiceProjectDesc'.$i] ?? null;
    //         $ServiceProjectFields[$i]['service_project_income'] = $input['ServiceProjectIncome'.$i] ?? null;
    //         $ServiceProjectFields[$i]['service_project_supplies'] = $input['ServiceProjectSupplies'.$i] ?? null;
    //         $ServiceProjectFields[$i]['service_project_charity'] = $input['ServiceProjectDonatedCharity'.$i] ?? null;
    //         $ServiceProjectFields[$i]['service_project_m2m'] = $input['ServiceProjectDonatedM2M'.$i] ?? null;
    //     }
    //     $financialReport->service_project_array = base64_encode(serialize($ServiceProjectFields));

    //     // Party Expenses (serialized)
    //     $PartyExpenseFields = null;
    //     $FieldCount = $input['PartyExpenseRowCount'];
    //     for ($i = 0; $i < $FieldCount; $i++) {
    //         $PartyExpenseFields[$i]['party_expense_desc'] = $input['PartyDesc'.$i] ?? null;
    //         $PartyExpenseFields[$i]['party_expense_income'] = $input['PartyIncome'.$i] ?? null;
    //         $PartyExpenseFields[$i]['party_expense_expenses'] = $input['PartyExpenses'.$i] ?? null;
    //     }
    //     $financialReport->party_expense_array = base64_encode(serialize($PartyExpenseFields));


    //     // OFFICE & OPERATING EXPENSES
    //     $financialReport->office_printing_costs = $input['PrintingCosts'] ?? null;
    //     $financialReport->office_postage_costs = $input['PostageCosts'] ?? null;
    //     $financialReport->office_membership_pins_cost = $input['MembershipPins'] ?? null;

    //     // Office Other Expenses (serialized)
    //     $OfficeOtherArray = null;
    //     $FieldCount = $input['OfficeExpenseRowCount'];
    //     for ($i = 0; $i < $FieldCount; $i++) {
    //         $OfficeOtherArray[$i]['office_other_desc'] = $input['OfficeDesc'.$i] ?? null;
    //         $OfficeOtherArray[$i]['office_other_expense'] = $input['OfficeExpenses'.$i] ?? null;
    //     }
    //     $financialReport->office_other_expenses = base64_encode(serialize($OfficeOtherArray));

    //     // INTERNATIONAL EVENTS & RE-REGISTRATION
    //     $financialReport->annual_registration_fee = $input['AnnualRegistrationFee'] ?? null;
    //     $financialReport->international_event = $input['InternationalEvent'] ?? null;

    //     // International Events (serialized)
    //     $InternationalEventArray = null;
    //     $FieldCount = $input['InternationalEventRowCount'];
    //     for ($i = 0; $i < $FieldCount; $i++) {
    //         $InternationalEventArray[$i]['intl_event_desc'] = $input['InternationalEventDesc'.$i] ?? null;
    //         $InternationalEventArray[$i]['intl_event_income'] = $input['InternationalEventIncome'.$i] ?? null;
    //         $InternationalEventArray[$i]['intl_event_expenses'] = $input['InternationalEventExpense'.$i] ?? null;
    //     }
    //     $financialReport->international_event_array = base64_encode(serialize($InternationalEventArray));

    //     // Donations to Chapte (serialized)
    //     $MonetaryDonation = null;
    //     $FieldCount = $input['MonDonationRowCount'];
    //     for ($i = 0; $i < $FieldCount; $i++) {
    //         $MonetaryDonation[$i]['mon_donation_desc'] = $input['DonationDesc'.$i] ?? null;
    //         $MonetaryDonation[$i]['mon_donation_info'] = $input['DonorInfo'.$i] ?? null;
    //         $MonetaryDonation[$i]['mon_donation_date'] = $input['MonDonationDate'.$i] ?? null;
    //         $MonetaryDonation[$i]['mon_donation_amount'] = $input['DonationAmount'.$i] ?? null;
    //     }
    //     $financialReport->monetary_donations_to_chapter = base64_encode(serialize($MonetaryDonation));

    //     $NonMonetaryDonation = null;
    //     $FieldCount = $input['NonMonDonationRowCount'];
    //     for ($i = 0; $i < $FieldCount; $i++) {
    //         $NonMonetaryDonation[$i]['nonmon_donation_desc'] = $input['NonMonDonationDesc'.$i] ?? null;
    //         $NonMonetaryDonation[$i]['nonmon_donation_info'] = $input['NonMonDonorInfo'.$i] ?? null;
    //         $NonMonetaryDonation[$i]['nonmon_donation_date'] = $input['NonMonDonationDate'.$i] ?? null;
    //     }
    //     $financialReport->non_monetary_donations_to_chapter = base64_encode(serialize($NonMonetaryDonation));

    //     // OTHER INCOME & EXPENSES (seralized)
    //     $OtherOffice = null;
    //     $FieldCount = $input['OtherOfficeExpenseRowCount'];
    //     for ($i = 0; $i < $FieldCount; $i++) {
    //         $OtherOffice[$i]['other_desc'] = $input['OtherOfficeDesc'.$i] ?? null;
    //         $OtherOffice[$i]['other_expenses'] = $input['OtherOfficeExpenses'.$i] ?? null;
    //         $OtherOffice[$i]['other_income'] = $input['OtherOfficeIncome'.$i] ?? null;
    //     }
    //     $financialReport->other_income_and_expenses_array = base64_encode(serialize($OtherOffice));

    //     // BANK RECONCILLIATION
    //     $financialReport->bank_statement_included = $input['BankStatementIncluded'] ?? null;
    //     $financialReport->bank_statement_included_explanation = $input['BankStatementIncludedExplanation'] ?? null;
    //     $financialReport->wheres_the_money = $input['WheresTheMoney'] ?? null;
    //     $amount_reserved_from_previous_year = $input['AmountReservedFromLastYear'];
    //     $amount_reserved_from_previous_year = str_replace(',', '', $amount_reserved_from_previous_year);
    //     $financialReport->amount_reserved_from_previous_year = $amount_reserved_from_previous_year === '' ? null : $amount_reserved_from_previous_year;
    //     $bank_balance_now = $input['BankBalanceNow'];
    //     $bank_balance_now = str_replace(',', '', $bank_balance_now);
    //     $financialReport->bank_balance_now = $bank_balance_now === '' ? null : $bank_balance_now;

    //     // Bank Reconciliation (serialized)
    //     $BankRecArray = null;
    //     $FieldCount = $input['BankRecRowCount'];
    //     for ($i = 0; $i < $FieldCount; $i++) {
    //         $BankRecArray[$i]['bank_rec_date'] = $input['BankRecDate'.$i] ?? null;
    //         $BankRecArray[$i]['bank_rec_check_no'] = $input['BankRecCheckNo'.$i] ?? null;
    //         $BankRecArray[$i]['bank_rec_desc'] = $input['BankRecDesc'.$i] ?? null;
    //         $BankRecArray[$i]['bank_rec_payment_amount'] = $input['BankRecPaymentAmount'.$i] ?? null;
    //         $BankRecArray[$i]['bank_rec_desposit_amount'] = $input['BankRecDepositAmount'.$i] ?? null;
    //     }
    //     $financialReport->bank_reconciliation_array = base64_encode(serialize($BankRecArray));

    //     // 990 IRS FILING
    //     $financialReport->file_irs = $input['FileIRS'] ?? null;
    //     $financialReport->file_irs_explanation = $input['FileIRSExplanation'] ?? null;

    //     // CHPATER QUESTIONS
    //     // Question 1
    //     $financialReport->bylaws_available = $input['ByLawsAvailable'] ?? null;
    //     $financialReport->bylaws_available_explanation = $input['ByLawsAvailableExplanation'] ?? null;
    //     // Question 2
    //     $financialReport->vote_all_activities = $input['VoteAllActivities'] ?? null;
    //     $financialReport->vote_all_activities_explanation = $input['VoteAllActivitiesExplanation'] ?? null;
    //     // Question 3
    //     $financialReport->child_outings = $input['ChildOutings'] ?? null;
    //     $financialReport->child_outings_explanation = $input['ChildOutingsExplanation'] ?? null;
    //     // Question 4
    //     $financialReport->playgroups = $input['Playgroups'] ?? null;
    //     $financialReport->had_playgroups_explanation = $input['PlaygroupsExplanation'] ?? null;
    //     // Question 5
    //     $financialReport->park_day_frequency = $input['ParkDays'] ?? null;
    //     $financialReport->park_day_frequency_explanation = $input['ParkDaysExplanation'] ?? null;
    //     // Question 6
    //     $financialReport->mother_outings = $input['MotherOutings'] ?? null;
    //     $financialReport->mother_outings_explanation = $input['MotherOutingsExplanation'] ?? null;
    //     // Question 7
    //     $financialReport->activity_array = $input['Activity'] ?? null;
    //     $financialReport->activity_other_explanation = $input['ActivityOtherExplanation'] ?? null;
    //     // Question 8
    //     $financialReport->offered_merch = $input['OfferedMerch'] ?? null;
    //     $financialReport->offered_merch_explanation = $input['OfferedMerchExplanation'] ?? null;
    //     // Question 9
    //     $financialReport->bought_merch = $input['BoughtMerch'] ?? null;
    //     $financialReport->bought_merch_explanation = $input['BoughtMerchExplanation'] ?? null;
    //     // Question 10
    //     $financialReport->purchase_pins = $input['BoughtPins'] ?? null;
    //     $financialReport->purchase_pins_explanation = $input['BoughtPinsExplanation'] ?? null;
    //     // Question 11
    //     $financialReport->receive_compensation = $input['ReceiveCompensation'] ?? null;
    //     $financialReport->receive_compensation_explanation = $input['ReceiveCompensationExplanation'];
    //     // Question 12
    //     $financialReport->financial_benefit = $input['FinancialBenefit'] ?? null;
    //     $financialReport->inancial_benefit_explanation = $input['FinancialBenefitExplanation'] ?? null;
    //     // Question 13
    //     $financialReport->influence_political = $input['InfluencePolitical'] ?? null;
    //     $financialReport->influence_political_explanation = $input['InfluencePoliticalExplanation'] ?? null;
    //     // Question 14
    //     $financialReport->sister_chapter = $input['SisterChapter'] ?? null;
    //     $financialReport->sister_chapter_explanation = $input['SisterChapterExplanation'] ?? null;

    //     // AWARDS
    //     $financialReport->outstanding_follow_bylaws = $input['OutstandingFollowByLaws'] ?? null;
    //     $financialReport->outstanding_well_rounded = $input['OutstandingWellRounded'] ?? null;
    //     $financialReport->outstanding_communicated = $input['OutstandingCommunicated'] ?? null;
    //     $financialReport->outstanding_support_international = $input['OutstandingSupportMomsClub'] ?? null;

    //     // Awards (seralized)
    //     $ChapterAwards = null;
    //     $FieldCount = $input['ChapterAwardsRowCount'];
    //     for ($i = 0; $i < $FieldCount; $i++) {
    //         $ChapterAwards[$i]['awards_type'] = $input['ChapterAwardsType'.$i] ?? null;
    //         $ChapterAwards[$i]['awards_desc'] = $input['ChapterAwardsDesc'.$i] ?? null;
    //         $ChapterAwards[$i]['awards_approved'] = false;
    //     }
    //     $financialReport->chapter_awards = base64_encode(serialize($ChapterAwards));

    //     if (isset($input['AwardsAgree']) && $input['AwardsAgree'] == false) {
    //         $financialReport->award_agree = 0;
    //     } elseif (isset($input['AwardsAgree'])) {
    //         $financialReport->award_agree = 1;
    //     } else {
    //         $financialReport->award_agree = null;
    //     }

    // }

    /**
     * Save EOY Financial Report All Board Members
     */
    // public function updateDisbandChecklist(Request $request, $chapterId): RedirectResponse
    // {
    //     $user = $this->userController->loadUserInformation($request);
    //     $userName = $user['user_name'];
    //     $userEmail = $user['user_email'];
    //     $lastUpdatedBy = $user['user_name'];
    //     $lastupdatedDate = date('Y-m-d H:i:s');

    //     $input = $request->all();
    //     $reportReceived = $input['submitted']?? null;

    //     $financialReport = FinancialReport::find($chapterId);
    //     $documents = Documents::find($chapterId);
    //     $chapter = Chapters::find($chapterId);
    //     $disbandChecklist = DisbandedChecklist::find($chapterId);

    //     DB::beginTransaction();
    //     try{
    //         $this->saveAccordionFields($financialReport, $input);

    //         $financialReport->save();

    //         if ($reportReceived == 1) {
    //             $documents->financial_report_received = 1;
    //             $documents->report_received = $lastupdatedDate;

    //             $documents->save();
    //         }

    //         $disbandChecklist->final_payment = $input['FinalPayment'] ?? null;
    //         $disbandChecklist->donate_funds = $input['DonateFunds'] ?? null;
    //         $disbandChecklist->destroy_manual = $input['DestroyManual'] ?? null;
    //         $disbandChecklist->remove_online = $input['RenoveOnline'] ?? null;
    //         $disbandChecklist->file_irs = $input['FileIrs'] ?? null;
    //         $disbandChecklist->file_financial = $input['FileFinancial'] ?? null;

    //         $disbandChecklist->save();

    //         $checklistComplete = ($disbandChecklist->final_payment == '1' && $disbandChecklist->donate_funds == '1' &&
    //             $disbandChecklist->destroy_manual == '1' && $disbandChecklist->remove_online == '1' &&
    //             $disbandChecklist->file_irs == '1' && $disbandChecklist->file_financial == '1');

    //         $chapter->last_updated_by = $lastUpdatedBy;
    //         $chapter->last_updated_date = $lastupdatedDate;

    //         $chapter->save();

    //         $baseQuery = $this->baseBoardController->getChapterDetails($chapterId);
    //         $chDetails = $baseQuery['chDetails'];
    //         $stateShortName = $baseQuery['stateShortName'];
    //         $chDocuments = $baseQuery['chDocuments'];
    //         $chFinancialReport = $baseQuery['chFinancialReport'];
    //         $emailListChap = $baseQuery['emailListChap'];
    //         $emailListCoord = $baseQuery['emailListCoord'];
    //         $pcDetails = $baseQuery['pcDetails'];
    //         $emailCC = $baseQuery['emailCC'];
    //         $cc_id = $baseQuery['cc_id'];
    //         $reviewerEmail = $baseQuery['reviewerEmail'];

    //         $mailData = array_merge(
    //             $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
    //             $this->baseMailDataController->getPCData($pcDetails),
    //             $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport),
    //         );

    //         if ($reportReceived == 1) {
    //             $pdfPath = $this->pdfController->saveFinalFinancialReport($request, $chapterId);   // Generate and Send the PDF
    //             Mail::to($userEmail)
    //                 ->cc($emailListChap)
    //                 ->queue(new EOYFinancialReportThankYou($mailData, $pdfPath));

    //             if ($chFinancialReport->reviewer_id == null) {
    //                 DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [$cc_id, $chapterId]);
    //                 Mail::to($emailCC)
    //                     ->queue(new EOYFinancialSubmitted($mailData, $pdfPath));
    //             }
    //         }

    //         // if ($documents->financial_report_received == '1' && $checklistComplete == '1'){
    //         //     Mail::to($userEmail)
    //         //         ->cc($emailListChap)
    //         //         ->queue(new DisbandChecklistThankYou($mailData));

    //         //     Mail::to($emailCC)
    //         //         ->queue(new DisbandChecklistComplete($mailData));
    //         // }

    //         DB::commit();
    //         if ($reportReceived == 1) {
    //             return redirect()->back()->with('success', 'Checklist/Report has been successfully Submitted');
    //         } else {
    //             return redirect()->back()->with('success', 'Checklist/Report has been successfully updated');
    //         }

    //     } catch (\Exception $e) {
    //         DB::rollback();  // Rollback Transaction
    //         Log::error($e);  // Log the error

    //         return redirect()->back()->with('fail', 'Something went wrong Please try again.');
    //     }
    // }



}
