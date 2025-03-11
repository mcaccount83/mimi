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
use App\Models\State;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BoardController extends Controller
{
    protected $userController;
    protected $baseBoardController;
    protected $pdfController;
    protected $baseMailDataController;

    public function __construct(UserController $userController, BaseBoardController $baseBoardController, PDFController $pdfController,
        BaseMailDataController $baseMailDataController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndBoard::class);
        $this->userController = $userController;
        $this->pdfController = $pdfController;
        $this->baseBoardController = $baseBoardController;
        $this->baseMailDataController = $baseMailDataController;
    }

    /*/ Base Board Controller /*/
    //  $this->baseBoardController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    //  $this->baseBoardController->getZappedBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    //  $this->baseBoardController->getChapterDetails($chId)

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
    public function showPresident(Request $request): View
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

        $admin = Admin::orderBy('id', 'desc')
            ->limit(1)
            ->first();
        $display_testing = ($admin->display_testing == 1);
        $display_live = ($admin->display_live == 1);

        $data = ['chDetails' => $chDetails, 'chFinancialReport' => $chFinancialReport, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'allWebLinks' => $allWebLinks,
            'PresDetails' => $PresDetails, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails,
            'startMonthName' => $startMonthName, 'thisMonth' => $month, 'due_date' => $due_date, 'userType' => $userType,
            'displayTESTING' => $displayTESTING, 'displayLIVE' => $displayLIVE, 'chDocuments' => $chDocuments
        ];

        return view('boards.president')->with($data);
    }

    /**
     * View Board Details President Login
     */
    public function showMember(Request $request): View
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

        $admin = Admin::orderBy('id', 'desc')
            ->limit(1)
            ->first();
        $display_testing = ($admin->display_testing == 1);
        $display_live = ($admin->display_live == 1);

        $data = ['chDetails' => $chDetails, 'chFinancialReport' => $chFinancialReport, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'allWebLinks' => $allWebLinks,
            'borDetails' => $borDetails, 'startMonthName' => $startMonthName, 'thisMonth' => $month, 'due_date' => $due_date, 'userType' => $userType,
            'display_testing' => $display_testing, 'display_live' => $display_live, 'chDocuments' => $chDocuments
        ];

        return view('boards.member')->with($data);
    }

    /**
     * Update Board Details President Login
     */
    public function updatePresident(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];;
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQueryPre = $this->baseBoardController->getChapterDetails($id);
        $chDetailsPre = $baseQueryPre['chDetails'];
        $pcDetailsPre = $baseQueryPre['pcDetails'];
        $PresDetailsPre = $baseQueryPre['PresDetails'];
        $AVPDetailsPre = $baseQueryPre['AVPDetails'];
        $MVPDetailsPre = $baseQueryPre['MVPDetails'];
        $TRSDetailsPre = $baseQueryPre['TRSDetails'];
        $SECDetailsPre = $baseQueryPre['SECDetails'];

        $input = $request->all();
        $webStatusPre = $input['ch_hid_webstatus'];

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
            $chapter->website_status = $ch_webstatus;
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
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
                        'is_active' => 1,
                    ]);
                }
            }

            //Update Chapter MailData//
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
                $this->baseMailDataController->getChapterBasicData($chDetailsUpd, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getPresPreviousData($PresDetailsPre),
                $this->baseMailDataController->getPresUpdatedData($PresDetailsUpd),
                $this->baseMailDataController->getChapterPreviousData($chDetailsPre, $pcDetailsPre),
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

    /**
     * Update Board Details Board Member Login
     */
    public function updateMember(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $bdId = $user['user_bdId'];
        $bdPositionid = $user['user_bdPositionId'];
        $lastUpdatedBy = $user['user_name'];;
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQueryPre = $this->baseBoardController->getChapterDetails($id);
        $chDetailsPre = $baseQueryPre['chDetails'];
        $pcDetailsPre = $baseQueryPre['pcDetails'];
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

            //Update Chapter MailData//
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
                $this->baseMailDataController->getChapterBasicData($chDetailsUpd, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getBoardPreviousData($borDetailsPre),
                $this->baseMailDataController->getBoardUpdatedData($borDetailsUpd),
                $this->baseMailDataController->getChapterPreviousData($chDetailsPre, $pcDetailsPre),
                $this->baseMailDataController->getChapterUpdatedData($chDetailsUpd, $pcDetailsUpd),
                [
                    'ch_website_url' => $website,
                ]
            );

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

    /**
     * Show Re-Registrstion Payment Form All Board Members
     */
    public function showReregistrationPaymentForm(Request $request): View
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
    public function showM2MDonationForm(Request $request)
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
    public function showResources(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $chId = $user['user_chapterId'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
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
    public function createBoardInfo(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];;
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

        $boundaryStatus = $request->input('BoundaryStatus');
        $issue_note = $request->input('BoundaryIssue');
        //Boundary Issues Correct 0 | Not Correct 1
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

            //AVP Info
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

            //MVP Info
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

            //TRS Info
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

            //SEC Info
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
                $this->baseMailDataController->getChapterBasicData($chDetails, $stateShortName),
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
    public function showFinancialReport(Request $request, $chapterId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userName = $loggedInName = $user['user_name'];
        $userEmail = $user['user_email'];
        $chId = $user['user_chapterId'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $submitted = $baseQuery['submitted'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $awards = $baseQuery['awards'];
        $allAwards = $baseQuery['allAwards'];

        $resources = Resources::with('categoryName')->get();

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'submitted' => $submitted, 'chDetails' => $chDetails, 'userType' => $userType,
            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
            'awards' => $awards, 'allAwards' => $allAwards,
        ];

        return view('boards.financial')->with($data);

    }

    /**
     * Save EOY Financial Report All Board Members
     */
    public function storeFinancialReport(Request $request, $chapterId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userName = $user['user_name'];
        $userEmail = $user['user_email'];
        $lastUpdatedBy = $user['user_name'];;
        $lastupdatedDate = date('Y-m-d H:i:s');

        // $user = User::find($request->user()->id);
        // $userId = $user->id;
        // $userType = $user->user_type;
        // $userName = $user->first_name.' '.$user->last_name;
        // $userEmail = $user->email;
        // $lastUpdatedBy = $user->first_name.' '.$user->last_name;

        // $bdDetails = $request->user()->board;
        // $bdId = $bdDetails->id;

        // $id = $bdDetails->chapter_id;

        $input = $request->all();
        $farthest_step_visited = $input['FurthestStep'];
        $reportReceived = $input['submitted'];

        // $roster_path = $chDocuments->roster_path;
        // $irs_path = $chDocuments->irs_path;
        // $statement_1_path = $chDocuments->statement_1_path;
        // $statement_2_path = $chDocuments->statement_2_path;
        // $financial_pdf_path = $chDocuments->financial_pdf_path;

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
        // 1
        $bylaws_available = isset($input['ByLawsAvailable']) ? $input['ByLawsAvailable'] : null;
        $bylaws_available_explanation = $input['ByLawsAvailableExplanation'];
        // 2
        $vote_all_activities = isset($input['VoteAllActivities']) ? $input['VoteAllActivities'] : null;
        $vote_all_activities_explanation = $input['VoteAllActivitiesExplanation'];
        // 3
        $child_outings = isset($input['ChildOutings']) ? $input['ChildOutings'] : null;
        $child_outings_explanation = $input['ChildOutingsExplanation'];
        // 4
        $playgroups = isset($input['Playgroups']) ? $input['Playgroups'] : null;
        $had_playgroups_explanation = $input['PlaygroupsExplanation'];
        // 5
        $park_day_frequency = isset($input['ParkDays']) ? $input['ParkDays'] : null;
        $park_day_frequency_explanation = $input['ParkDaysExplanation'];
        // 6
        $mother_outings = isset($input['MotherOutings']) ? $input['MotherOutings'] : null;
        $mother_outings_explanation = $input['MotherOutingsExplanation'];
        // 7
        $activity_array = isset($input['Activity']) ? $input['Activity'] : null;
        $activity_other_explanation = $input['ActivityOtherExplanation'];
        // 8
        $offered_merch = isset($input['OfferedMerch']) ? $input['OfferedMerch'] : null;
        $offered_merch_explanation = $input['OfferedMerchExplanation'];
        // 9
        $bought_merch = isset($input['BoughtMerch']) ? $input['BoughtMerch'] : null;
        $bought_merch_explanation = $input['BoughtMerchExplanation'];
        // 10
        $purchase_pins = isset($input['BoughtPins']) ? $input['BoughtPins'] : null;
        $purchase_pins_explanation = $input['BoughtPinsExplanation'];
        // 11
        $receive_compensation = isset($input['ReceiveCompensation']) ? $input['ReceiveCompensation'] : null;
        $receive_compensation_explanation = $input['ReceiveCompensationExplanation'];
        // 12
        $financial_benefit = isset($input['FinancialBenefit']) ? $input['FinancialBenefit'] : null;
        $financial_benefit_explanation = $input['FinancialBenefitExplanation'];
        // 13
        $influence_political = isset($input['InfluencePolitical']) ? $input['InfluencePolitical'] : null;
        $influence_political_explanation = $input['InfluencePoliticalExplanation'];
        // 14
        $sister_chapter = isset($input['SisterChapter']) ? $input['SisterChapter'] : null;
        $sister_chapter_explanation = $input['SisterChapterExplanation'];

        // AWARDS
        $outstanding_follow_bylaws = isset($input['OutstandingFollowByLaws']) ? $input['OutstandingFollowByLaws'] : null;
        $outstanding_well_rounded = isset($input['OutstandingWellRounded']) ? $input['OutstandingWellRounded'] : null;
        $outstanding_communicated = isset($input['OutstandingCommunicated']) ? $input['OutstandingCommunicated'] : null;
        $outstanding_support_international = isset($input['OutstandingSupportMomsClub']) ? $input['OutstandingSupportMomsClub'] : null;
        $ChapterAwards = null;
        $FieldCount = $input['ChapterAwardsRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $ChapterAwards[$i]['awards_type'] = $input['ChapterAwardsType'.$i] ?? null;
            $ChapterAwards[$i]['awards_desc'] = $input['ChapterAwardsDesc'.$i] ?? null;
            $ChapterAwards[$i]['awards_approved'] = false;
        }
        $chapter_awards = base64_encode(serialize($ChapterAwards));

        if (isset($input['AwardsAgree']) && $input['AwardsAgree'] == false) {
            $award_agree = 0;
        } elseif (isset($input['AwardsAgree'])) {
            $award_agree = 1;
        } else {
            $award_agree = null;
        }

        $financialReport = FinancialReport::find($chapterId);
        $documents = Documents::find($chapterId);
        $chapter = Chapters::find($chapterId);

        DB::beginTransaction();
        try {
            $financialReport->changed_dues = $changed_dues;
            $financialReport->different_dues = $different_dues;
            $financialReport->not_all_full_dues = $not_all_full_dues;
            $financialReport->total_new_members = $total_new_members;
            $financialReport->total_renewed_members = $total_renewed_members;
            $financialReport->dues_per_member = $dues_per_member;
            $financialReport->total_new_members_changed_dues = $total_new_members_changed_dues;
            $financialReport->total_renewed_members_changed_dues = $total_renewed_members_changed_dues;
            $financialReport->dues_per_member_renewal = $dues_per_member_renewal;
            $financialReport->dues_per_member_new_changed = $dues_per_member_new_changed;
            $financialReport->dues_per_member_renewal_changed = $dues_per_member_renewal_changed;
            $financialReport->members_who_paid_no_dues = $members_who_paid_no_dues;
            $financialReport->members_who_paid_partial_dues = $members_who_paid_partial_dues;
            $financialReport->total_partial_fees_collected = $total_partial_fees_collected;
            $financialReport->total_associate_members = $total_associate_members;
            $financialReport->associate_member_fee = $associate_member_fee;
            $financialReport->manditory_meeting_fees_paid = $manditory_meeting_fees_paid;
            $financialReport->voluntary_donations_paid = $voluntary_donations_paid;
            $financialReport->paid_baby_sitters = $paid_baby_sitters;
            $financialReport->childrens_room_expenses = $childrens_room_expenses;
            $financialReport->service_project_array = $service_project_array;
            $financialReport->party_expense_array = $party_expense_array;
            $financialReport->office_printing_costs = $office_printing_costs;
            $financialReport->office_postage_costs = $office_postage_costs;
            $financialReport->office_membership_pins_cost = $office_membership_pins_cost;
            $financialReport->office_other_expenses = $office_other_expenses;
            $financialReport->international_event_array = $international_event_array;
            $financialReport->annual_registration_fee = $annual_registration_fee;
            $financialReport->monetary_donations_to_chapter = $monetary_donations_to_chapter;
            $financialReport->non_monetary_donations_to_chapter = $non_monetary_donations_to_chapter;
            $financialReport->other_income_and_expenses_array = $other_income_and_expenses_array;
            $financialReport->amount_reserved_from_previous_year = $amount_reserved_from_previous_year;
            $financialReport->bank_balance_now = $bank_balance_now;
            $financialReport->bank_reconciliation_array = $bank_reconciliation_array;
            $financialReport->receive_compensation = $receive_compensation;
            $financialReport->receive_compensation_explanation = $receive_compensation_explanation;
            $financialReport->financial_benefit = $financial_benefit;
            $financialReport->financial_benefit_explanation = $financial_benefit_explanation;
            $financialReport->influence_political = $influence_political;
            $financialReport->influence_political_explanation = $influence_political_explanation;
            $financialReport->vote_all_activities = $vote_all_activities;
            $financialReport->vote_all_activities_explanation = $vote_all_activities_explanation;
            $financialReport->purchase_pins = $purchase_pins;
            $financialReport->purchase_pins_explanation = $purchase_pins_explanation;
            $financialReport->bought_merch = $bought_merch;
            $financialReport->bought_merch_explanation = $bought_merch_explanation;
            $financialReport->offered_merch = $offered_merch;
            $financialReport->offered_merch_explanation = $offered_merch_explanation;
            $financialReport->bylaws_available = $bylaws_available;
            $financialReport->bylaws_available_explanation = $bylaws_available_explanation;
            $financialReport->childrens_room_sitters = $childrens_room_sitters;
            $financialReport->playgroups = $playgroups;
            $financialReport->had_playgroups_explanation = $had_playgroups_explanation;
            $financialReport->child_outings = $child_outings;
            $financialReport->child_outings_explanation = $child_outings_explanation;
            $financialReport->mother_outings = $mother_outings;
            $financialReport->mother_outings_explanation = $mother_outings_explanation;
            $financialReport->meeting_speakers = $meeting_speakers;
            $financialReport->meeting_speakers_array = $meeting_speakers_array;
            $financialReport->discussion_topic_frequency = $discussion_topic_frequency;
            $financialReport->park_day_frequency = $park_day_frequency;
            $financialReport->park_day_frequency_explanation = $park_day_frequency_explanation;
            $financialReport->activity_array = $activity_array;
            $financialReport->activity_other_explanation = $activity_other_explanation;
            $financialReport->contributions_not_registered_charity = $contributions_not_registered_charity;
            $financialReport->contributions_not_registered_charity_explanation = $contributions_not_registered_charity_explanation;
            $financialReport->at_least_one_service_project = $at_least_one_service_project;
            $financialReport->at_least_one_service_project_explanation = $at_least_one_service_project_explanation;
            $financialReport->sister_chapter = $sister_chapter;
            $financialReport->sister_chapter_explanation = $sister_chapter_explanation;
            $financialReport->international_event = $international_event;
            $financialReport->file_irs = $file_irs;
            $financialReport->file_irs_explanation = $file_irs_explanation;
            $financialReport->bank_statement_included = $bank_statement_included;
            $financialReport->bank_statement_included_explanation = $bank_statement_included_explanation;
            $financialReport->wheres_the_money = $wheres_the_money;
            $financialReport->chapter_awards = $chapter_awards;
            $financialReport->outstanding_follow_bylaws = $outstanding_follow_bylaws;
            $financialReport->outstanding_well_rounded = $outstanding_well_rounded;
            $financialReport->outstanding_communicated = $outstanding_communicated;
            $financialReport->outstanding_support_international = $outstanding_support_international;
            $financialReport->award_agree = $award_agree;
            $financialReport->farthest_step_visited = $farthest_step_visited;
            $financialReport->completed_name = $userName;
            $financialReport->completed_email = $userEmail;
            $financialReport->submitted = $lastupdatedDate;

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
                $this->baseMailDataController->getChapterBasicData($chDetails, $stateShortName),
                $this->baseMailDataController->getPCData($pcDetails),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport),
            );

            if ($reportReceived == 1) {
                $pdfPath =  $this->pdfController->saveFinancialReport($request, $chapterId);   // Generate and Send the PDF
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
}
