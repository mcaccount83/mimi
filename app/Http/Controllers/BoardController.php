<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckCurrentPasswordBoardRequest;
use App\Http\Requests\UpdatePasswordBoardRequest;
use App\Mail\BorUpdateListNoitce;
use App\Mail\ChapProfileUpdatePCNotice;
use App\Mail\EOYElectionReportSubmitted;
use App\Mail\EOYElectionReportThankYou;
use App\Mail\EOYFinancialReportThankYou;
use App\Mail\EOYFinancialSubmitted;
use App\Mail\ProbationReportSubmitted;
use App\Mail\ProbationReportThankYou;
use App\Mail\NewWebsiteReviewNotice;
use App\Models\Admin;
use App\Models\Chapters;
use App\Models\Documents;
use App\Models\FinancialReport;
use App\Models\BoardsIncoming;
use App\Models\ProbationSubmission;
use App\Models\ResourceCategory;
use App\Models\Resources;
use App\Models\User;
use App\Services\PositionConditionsService;
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

        protected $positionConditionsService;

    protected $baseBoardController;

    protected $pdfController;

    protected $baseMailDataController;

    protected $emailTableController;

    protected $financialReportController;

    public function __construct(UserController $userController, BaseBoardController $baseBoardController, PDFController $pdfController, PositionConditionsService $positionConditionsService,
        BaseMailDataController $baseMailDataController, FinancialReportController $financialReportController, EmailTableController $emailTableController)
    {
        $this->userController = $userController;
        $this->pdfController = $pdfController;
        $this->baseBoardController = $baseBoardController;
                $this->positionConditionsService = $positionConditionsService;
        $this->baseMailDataController = $baseMailDataController;
        $this->emailTableController = $emailTableController;
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
    public function editProfile(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];
        $chPayments = $baseQuery['chPayments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $chDocuments = $baseQuery['chDocuments'];
        $probationReason = $baseQuery['probationReason'];

        $allProbation = $baseQuery['allProbation'];
        $allWebLinks = $baseQuery['allWebLinks'];
        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        if ($userType = 'coordinator'){
            $bdPositionId = '1';
            $borDetails = $PresDetails;
        }
        else {
            $bdPositionId = $user['user_bdPositionId'];
            $borDetails = $user['user_bdDetails'];
        }

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
            'PresDetails' => $PresDetails, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'allCountries' => $allCountries,
            'startMonthName' => $startMonthName, 'thisMonth' => $month, 'due_date' => $due_date, 'userType' => $userType, 'allProbation' => $allProbation, 'userAdmin' => $userAdmin,
            'displayTESTING' => $displayTESTING, 'displayLIVE' => $displayLIVE, 'chDocuments' => $chDocuments, 'probationReason' => $probationReason, 'chPayments' => $chPayments,
            'bdPositionId' => $bdPositionId, 'borDetails' => $borDetails
        ];

        return view('boards.profile')->with($data);
    }

    public function updateProfile(Request $request, $id): RedirectResponse
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
                    'state_id' => $request->input('ch_pre_state'),
                    'zip' => $request->input('ch_pre_zip'),
                    'country_id' => $request->input('ch_pre_country') ?? '198',
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
                        'state_id' => $request->input('ch_avp_state'),
                        'zip' => $request->input('ch_avp_zip'),
                        'country_id' => $request->input('ch_avp_country') ?? '198',
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
                        'state_id' => $request->input('ch_avp_state'),
                        'zip' => $request->input('ch_avp_zip'),
                        'country_id' => $request->input('ch_avp_country') ?? '198',
                        'phone' => $request->input('ch_avp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
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
                        'state_id' => $request->input('ch_mvp_state'),
                        'zip' => $request->input('ch_mvp_zip'),
                        'country_id' => $request->input('ch_mvp_country') ?? '198',
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
                        'state_id' => $request->input('ch_mvp_state'),
                        'zip' => $request->input('ch_mvp_zip'),
                        'country_id' => $request->input('ch_mvp_country') ?? '198',
                        'phone' => $request->input('ch_mvp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
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
                        'state_id' => $request->input('ch_trs_state'),
                        'zip' => $request->input('ch_trs_zip'),
                        'country_id' => $request->input('ch_trs_country') ?? '198',
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
                        'state_id' => $request->input('ch_trs_state'),
                        'zip' => $request->input('ch_trs_zip'),
                        'country_id' => $request->input('ch_trs_country') ?? '198',
                        'phone' => $request->input('ch_trs_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
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
                        'state_id' => $request->input('ch_sec_state'),
                        'zip' => $request->input('ch_sec_zip'),
                        'country_id' => $request->input('ch_sec_country') ?? '198',
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
                        'state_id' => $request->input('ch_sec_state'),
                        'zip' => $request->input('ch_sec_zip'),
                        'country_id' => $request->input('ch_sec_country') ?? '198',
                        'phone' => $request->input('ch_sec_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
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
            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $einAdmin = $adminEmail['ein_admin'];  // EIN Coor Email

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
                $mailDataAvp = ['avpNameUpd' => $AVPDetailsUpd->first_name.' '.$AVPDetailsUpd->last_name,
                    'avpemailUpd' => $AVPDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDataAvp);
            } else {
                $mailDataAvp = ['avpNameUpd' => '',
                    'avpemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataAvp);
            }
            if ($MVPDetailsUpd !== null) {
                $mailDataMvp = ['mvpNameUpd' => $MVPDetailsUpd->first_name.' '.$MVPDetailsUpd->last_name,
                    'mvpemailUpd' => $MVPDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDataMvp);
            } else {
                $mailDataMvp = ['mvpNameUpd' => '',
                    'mvpemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataMvp);
            }
            if ($TRSDetailsUpd !== null) {
                $mailDatatres = ['tresNameUpd' => $TRSDetailsUpd->first_name.' '.$TRSDetailsUpd->last_name,
                    'tresemailUpd' => $TRSDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDatatres);
            } else {
                $mailDatatres = ['tresNameUpd' => '',
                    'tresemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDatatres);
            }
            if ($SECDetailsUpd !== null) {
                $mailDataSec = ['secNameUpd' => $SECDetailsUpd->first_name.' '.$SECDetailsUpd->last_name,
                    'secemailUpd' => $SECDetailsUpd->email, ];
                $mailData = array_merge($mailData, $mailDataSec);
            } else {
                $mailDataSec = ['secNameUpd' => '',
                    'secemailUpd' => '', ];
                $mailData = array_merge($mailData, $mailDataSec);
            }

            if ($AVPDetails !== null) {
                $mailDataAvpp = ['avpName' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
                    'avpemail' => $AVPDetails->email, ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            } else {
                $mailDataAvpp = ['avpName' => '',
                    'avpemail' => '', ];
                $mailData = array_merge($mailData, $mailDataAvpp);
            }
            if ($MVPDetails !== null) {
                $mailDataMvpp = ['mvpName' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
                    'mvpemail' => $MVPDetails->email, ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            } else {
                $mailDataMvpp = ['mvpName' => '',
                    'mvpemail' => '', ];
                $mailData = array_merge($mailData, $mailDataMvpp);
            }
            if ($TRSDetails !== null) {
                $mailDatatresp = ['tresName' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
                    'tresemail' => $TRSDetails->email, ];
                $mailData = array_merge($mailData, $mailDatatresp);
            } else {
                $mailDatatresp = ['tresName' => '',
                    'tresemail' => '', ];
                $mailData = array_merge($mailData, $mailDatatresp);
            }
            if ($SECDetails !== null) {
                $mailDataSecp = ['secName' => $SECDetails->first_name.' '.$SECDetails->last_name,
                    'secemail' => $SECDetails->email, ];
                $mailData = array_merge($mailData, $mailDataSecp);
            } else {
                $mailDataSecp = ['secName' => '',
                    'secemail' => '', ];
                $mailData = array_merge($mailData, $mailDataSecp);
            }

            $mailTableListAdmin = $this->emailTableController->createListAdminUpdateBoardTable($mailData);
            $mailTablePrimary = $this->emailTableController->createPrimaryUpdateBoardTable($mailData);

            $mailData = array_merge($mailData, [
                'mailTableListAdmin' => $mailTableListAdmin,
                'mailTablePrimary' => $mailTablePrimary,
            ]);

            if ($chDetailsUpd->name != $chDetails->name || $PresDetailsUpd->bor_email != $PresDetails->bor_email || $PresDetailsUpd->street_address != $PresDetails->street_address || $PresDetailsUpd->city != $PresDetails->city ||
                    $PresDetailsUpd->state_id != $PresDetails->state_id || $PresDetailsUpd->first_name != $PresDetails->first_name || $PresDetailsUpd->last_name != $PresDetails->last_name ||
                    $PresDetailsUpd->zip != $PresDetails->zip || $PresDetailsUpd->phone != $PresDetails->phone || $chDetailsUpd->inquiries_contact != $chDetails->inquiries_contact ||
                    $chDetailsUpd->email != $chDetails->email || $chDetailsUpd->po_box != $chDetails->po_box || $chDetailsUpd->website_url != $chDetails->website_url ||
                    $chDetailsUpd->website_status != $chDetails->website_status || $chDetailsUpd->egroup != $chDetails->egroup ||
                    $mailDataAvpp['avpName'] != $mailDataAvp['avpNameUpd'] || $mailDataAvpp['avpemail'] != $mailDataAvp['avpemailUpd'] ||
                    $mailDataMvpp['mvpName'] != $mailDataMvp['mvpNameUpd'] || $mailDataMvpp['mvpemail'] != $mailDataMvp['mvpemailUpd'] ||
                    $mailDatatresp['tresName'] != $mailDatatres['tresNameUpd'] || $mailDatatresp['tresemail'] != $mailDatatres['tresemailUpd'] ||
                    $mailDataSecp['secName'] != $mailDataSec['secNameUpd'] || $mailDataSecp['secemail'] != $mailDataSec['secemailUpd']) {

                Mail::to($pcEmail)
                    ->queue(new ChapProfileUpdatePCNotice($mailData));
            }

            // //List Admin Notification//
            // $to_email2 = 'listadmin@momsclub.org';
            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $listAdmin = $adminEmail['list_admin'];

            if ($PresDetailsUpd->email != $PresDetails->email || $PresDetailsUpd->email != $PresDetails->email || $mailDataAvpp['avpemail'] != $mailDataAvp['avpemailUpd'] ||
                        $mailDataMvpp['mvpemail'] != $mailDataMvp['mvpemailUpd'] || $mailDatatresp['tresemail'] != $mailDatatres['tresemailUpd'] ||
                        $mailDataSecp['secemail'] != $mailDataSec['secemailUpd']) {

                Mail::to($listAdmin)
                    ->queue(new BorUpdateListNoitce($mailData));
            }

            // Website URL Change Notification//
            if ($webStatusUpd != $webStatusPre) {
                if ($webStatusUpd == 2) {
                    Mail::to($emailCC)
                        ->queue(new NewWebsiteReviewNotice($mailData));
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Chapter has successfully updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/home')->with('fail', 'Something went wrong, Please try again');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    // /**
    //  * Show Re-Registrstion Payment Form All Board Members
    //  */
    // public function editReregistrationPaymentForm(Request $request, $chId): View
    // {
    //     $user = $this->userController->loadUserInformation($request);
    //     $userType = $user['userType'];
    //     $userAdmin = $user['userAdmin'];

    //     $baseQuery = $this->baseBoardController->getChapterDetails($chId);
    //     $chDetails = $baseQuery['chDetails'];
    //     $chActiveId = $baseQuery['chActiveId'];
    //     $stateShortName = $baseQuery['stateShortName'];
    //     $startMonthName = $baseQuery['startMonthName'];

    //     $now = Carbon::now();
    //     $month = $now->month;
    //     $year = $now->year;
    //     $start_month = $chDetails->start_month_id;
    //     $next_renewal_year = $chDetails->next_renewal_year;
    //     $due_date = Carbon::create($next_renewal_year, $start_month, 1);
    //     $rangeEndDate = $due_date->copy()->subMonth()->endOfMonth();
    //     $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

    //     $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
    //     $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

    //     $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'userAdmin' => $userAdmin,
    //         'startMonthName' => $startMonthName, 'endRange' => $rangeEndDateFormatted, 'startRange' => $rangeStartDateFormatted,
    //         'thisMonth' => $month, 'due_date' => $due_date, 'userType' => $userType, 'chActiveId' => $chActiveId,
    //     ];

    //     return view('boards.payment')->with($data);
    // }

    // /**
    //  * Show M2M Donation Form All Board Members
    //  */
    // public function editDonationForm(Request $request, $chId): View
    // {
    //     $user = $this->userController->loadUserInformation($request);
    //     $userType = $user['userType'];
    //     $userAdmin = $user['userAdmin'];

    //     $baseQuery = $this->baseBoardController->getChapterDetails($chId);
    //     $chDetails = $baseQuery['chDetails'];
    //     $chActiveId = $baseQuery['chActiveId'];
    //     $stateShortName = $baseQuery['stateShortName'];
    //     $allStates = $baseQuery['allStates'];
    //     $allCountries = $baseQuery['allCountries'];
    //     $PresDetails = $baseQuery['PresDetails'];

    //     $now = Carbon::now();
    //     $month = $now->month;
    //     $year = $now->year;

    //     $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'userType' => $userType, 'userAdmin' => $userAdmin, 'chActiveId' => $chActiveId,
    //         'PresDetails' => $PresDetails, 'allStates' => $allStates, 'allCountries' => $allCountries,
    //     ];

    //     return view('boards.donation')->with($data);
    // }

    /**
     * Show Manual Order Form All Board Members
     */
    public function editManualOrderForm(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'userType' => $userType, 'userAdmin' => $userAdmin, 'chActiveId' => $chActiveId,
            'allStates' => $allStates, 'allCountries' => $allCountries,
        ];

            return view('boards.manualorder')->with($data);
    }

    /**
     * Show Probation Submission Form All Board Members
     */
    public function editProbationSubmission(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

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

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'userAdmin' => $userAdmin,
            'startMonthName' => $startMonthName, 'endRange' => $rangeEndDateFormatted, 'startRange' => $rangeStartDateFormatted,
            'thisMonth' => $month, 'due_date' => $due_date, 'userType' => $userType,
        ];

        return view('boards.probation')->with($data);
    }

    /**
     * Update Probation Submission Form All Board Members
     */
    public function updateProbationSubmission(Request $request, $chId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];
        $emailCC = $baseQuery['emailCC'];

        $input = $request->all();

        $chapter = Chapters::find($chId);
        $probation = ProbationSubmission::find($chId);

        DB::beginTransaction();
        try {
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            if ($probation) {
                $probation->update([
                    $probation->q1_dues = $input['q1_dues'] ?? null,
                    $probation->q1_benefit = $input['q1_benefit'] ?? null,
                    $probation->q2_dues = $input['q2_dues'] ?? null,
                    $probation->q2_benefit = $input['q2_benefit'] ?? null,
                    $probation->q3_dues = $input['q3_dues'] ?? null,
                    $probation->q3_benefit = $input['q3_benefit'] ?? null,
                    $probation->q4_dues = $input['q4_dues'] ?? null,
                    $probation->q4_benefit = $input['q4_benefit'] ?? null,
                ]);
            }

            $mailTable = $this->emailTableController->createProbationSubmissionTable($input);

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getProbationData($input),
                [
                    'mailTable' => $mailTable,
                ]
            );

            Mail::to($emailCC)
                ->queue(new ProbationReportSubmitted($mailData));

            Mail::to($emailListChap)
                ->queue(new ProbationReportThankYou($mailData));

            DB::commit();

            return redirect()->back()->with('success', 'Quarterly Report has been Submitted');
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
     * Show Chater Resources
     */
    public function viewResources(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        $data = ['stateShortName' => $stateShortName, 'chDetails' => $chDetails, 'resources' => $resources, 'resourceCategories' => $resourceCategories,
            'userType' => $userType, 'userAdmin' => $userAdmin,
        ];

        return view('boards.resources')->with($data);
    }

    /**
     * Show EOY BoardInfo All Board Members
     */
    public function editBoardReport(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];

        $allWebLinks = $baseQuery['allWebLinks'];
        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $data = ['stateShortName' => $stateShortName, 'startMonthName' => $startMonthName, 'allStates' => $allStates, 'SECDetails' => $SECDetails, 'userAdmin' => $userAdmin,
            'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PresDetails' => $PresDetails, 'chDetails' => $chDetails, 'userType' => $userType,
            'allWebLinks' => $allWebLinks, 'allCountries' => $allCountries,
        ];

        return view('boards.boardinfo')->with($data);
    }

    /**
     * Update EOY BoardInfo All Board Members
     */
    public function updateBoardReport(Request $request, $chId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        // $chId = $user['user_chapterId'];

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
        $documents = Documents::find($chId);

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
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            $documents->new_board_submitted = 1;
            $documents->save();

            // President Info
            if ($request->input('ch_pre_fname') != '' && $request->input('ch_pre_lname') != '' && $request->input('ch_pre_email') != '') {
                $PREDetails = BoardsIncoming::where('chapter_id', $chId)
                    ->where('board_position_id', '1')
                    ->get();
                $presId = $request->input('presID');
                if (count($PREDetails) != 0) {
                    BoardsIncoming::where('id', $presId)
                        ->update([   // Update board details
                            'first_name' => $request->input('ch_pre_fname'),
                            'last_name' => $request->input('ch_pre_lname'),
                            'email' => $request->input('ch_pre_email'),
                            'street_address' => $request->input('ch_pre_street'),
                            'city' => $request->input('ch_pre_city'),
                            'state_id' => $request->input('ch_pre_state'),
                            'zip' => $request->input('ch_pre_zip'),
                            'country_id' => $request->input('ch_pre_country') ?? '198',
                            'phone' => $request->input('ch_pre_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                } else {
                    BoardsIncoming::create([  // Create board details if new
                        'chapter_id' => $chId,
                        'board_position_id' => '1',
                        'first_name' => $request->input('ch_pre_fname'),
                        'last_name' => $request->input('ch_pre_lname'),
                        'email' => $request->input('ch_pre_email'),
                        'street_address' => $request->input('ch_pre_street'),
                        'city' => $request->input('ch_pre_city'),
                        'state_id' => $request->input('ch_pre_state'),
                        'zip' => $request->input('ch_pre_zip'),
                        'country_id' => $request->input('ch_pre_country') ?? '198',
                        'phone' => $request->input('ch_pre_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
                    ]);
                }
            }

            // AVP Info
            $AVPDetails = BoardsIncoming::where('chapter_id', $chId)
                ->where('board_position_id', '2')
                ->get();

            if (count($AVPDetails) > 0) {
                if ($request->input('AVPVacant') == 'on') {
                    BoardsIncoming::where('chapter_id', $chId)
                        ->where('board_position_id', '2')
                        ->delete();  // Delete board member if now Vacant
                } else {
                    $AVPId = $request->input('avpID');
                    BoardsIncoming::where('id', $AVPId)
                        ->update([   // Update board details if already exists
                            'first_name' => $request->input('ch_avp_fname'),
                            'last_name' => $request->input('ch_avp_lname'),
                            'email' => $request->input('ch_avp_email'),
                            'street_address' => $request->input('ch_avp_street'),
                            'city' => $request->input('ch_avp_city'),
                            'state_id' => $request->input('ch_avp_state'),
                            'zip' => $request->input('ch_avp_zip'),
                            'country_id' => $request->input('ch_avp_country') ?? '198',
                            'phone' => $request->input('ch_avp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                }
            } else {
                if ($request->input('AVPVacant') != 'on') {
                    BoardsIncoming::create([  // Create board details if new
                        'chapter_id' => $chId,
                        'board_position_id' => '2',
                        'first_name' => $request->input('ch_avp_fname'),
                        'last_name' => $request->input('ch_avp_lname'),
                        'email' => $request->input('ch_avp_email'),
                        'street_address' => $request->input('ch_avp_street'),
                        'city' => $request->input('ch_avp_city'),
                        'state_id' => $request->input('ch_avp_state'),
                        'zip' => $request->input('ch_avp_zip'),
                        'country_id' => $request->input('ch_avp_country') ?? '198',
                        'phone' => $request->input('ch_avp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
                    ]);
                }
            }

            // MVP Info
            $MVPDetails = BoardsIncoming::where('chapter_id', $chId)
                ->where('board_position_id', '3')
                ->get();

            if (count($MVPDetails) > 0) {
                if ($request->input('MVPVacant') == 'on') {
                    BoardsIncoming::where('chapter_id', $chId)
                        ->where('board_position_id', '3')
                        ->delete();  // Delete board member if now Vacant
                } else {
                    $MVPId = $request->input('mvpID');
                    BoardsIncoming::where('id', $MVPId)
                        ->update([   // Update board details if already exists
                            'first_name' => $request->input('ch_mvp_fname'),
                            'last_name' => $request->input('ch_mvp_lname'),
                            'email' => $request->input('ch_mvp_email'),
                            'street_address' => $request->input('ch_mvp_street'),
                            'city' => $request->input('ch_mvp_city'),
                            'state_id' => $request->input('ch_mvp_state'),
                            'zip' => $request->input('ch_mvp_zip'),
                            'country_id' => $request->input('ch_mvp_country') ?? '198',
                            'phone' => $request->input('ch_mvp_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                }
            } else {
                if ($request->input('MVPVacant') != 'on') {
                    BoardsIncoming::create([  // Create board details if new
                        'chapter_id' => $chId,
                        'board_position_id' => '3',
                        'first_name' => $request->input('ch_mvp_fname'),
                        'last_name' => $request->input('ch_mvp_lname'),
                        'email' => $request->input('ch_mvp_email'),
                        'street_address' => $request->input('ch_mvp_street'),
                        'city' => $request->input('ch_mvp_city'),
                        'state_id' => $request->input('ch_mvp_state'),
                        'zip' => $request->input('ch_mvp_zip'),
                        'country_id' => $request->input('ch_mvp_country') ?? '198',
                        'phone' => $request->input('ch_mvp_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
                    ]);
                }
            }

            // TRS Info
            $TRSDetails = BoardsIncoming::where('chapter_id', $chId)
                ->where('board_position_id', '4')
                ->get();

            if (count($TRSDetails) > 0) {
                if ($request->input('TreasVacant') == 'on') {
                    BoardsIncoming::where('chapter_id', $chId)
                        ->where('board_position_id', '4')
                        ->delete();  // Delete board member if now Vacant
                } else {
                    $TRSId = $request->input('trsID');
                    BoardsIncoming::where('id', $TRSId)
                        ->update([   // Update board details if already exists
                            'first_name' => $request->input('ch_trs_fname'),
                            'last_name' => $request->input('ch_trs_lname'),
                            'email' => $request->input('ch_trs_email'),
                            'street_address' => $request->input('ch_trs_street'),
                            'city' => $request->input('ch_trs_city'),
                            'state_id' => $request->input('ch_trs_state'),
                            'zip' => $request->input('ch_trs_zip'),
                            'country_id' => $request->input('ch_trs_country') ?? '198',
                            'phone' => $request->input('ch_trs_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                }
            } else {
                if ($request->input('TreasVacant') != 'on') {
                    BoardsIncoming::create([  // Create board details if new
                        'chapter_id' => $chId,
                        'board_position_id' => '4',
                        'first_name' => $request->input('ch_trs_fname'),
                        'last_name' => $request->input('ch_trs_lname'),
                        'email' => $request->input('ch_trs_email'),
                        'street_address' => $request->input('ch_trs_street'),
                        'city' => $request->input('ch_trs_city'),
                        'state_id' => $request->input('ch_trs_state'),
                        'zip' => $request->input('ch_trs_zip'),
                        'country_id' => $request->input('ch_trs_country') ?? '198',
                        'phone' => $request->input('ch_trs_phone'),
                        'last_updated_by' => $lastUpdatedBy,
                        'last_updated_date' => $lastupdatedDate,
                    ]);
                }
            }

            // SEC Info
            $SECDetails = BoardsIncoming::where('chapter_id', $chId)
                ->where('board_position_id', '5')
                ->get();

            if (count($SECDetails) > 0) {
                if ($request->input('SecVacant') == 'on') {
                    BoardsIncoming::where('chapter_id', $chId)
                        ->where('board_position_id', '5')
                        ->delete();  // Delete board member if now Vacant
                } else {
                    $SECId = $request->input('secID');
                    BoardsIncoming::where('id', $SECId)
                        ->update([   // Update board details if already exists
                            'first_name' => $request->input('ch_sec_fname'),
                            'last_name' => $request->input('ch_sec_lname'),
                            'email' => $request->input('ch_sec_email'),
                            'street_address' => $request->input('ch_sec_street'),
                            'city' => $request->input('ch_sec_city'),
                            'state_id' => $request->input('ch_sec_state'),
                            'zip' => $request->input('ch_sec_zip'),
                            'country_id' => $request->input('ch_sec_country') ?? '198',
                            'phone' => $request->input('ch_sec_phone'),
                            'last_updated_by' => $lastUpdatedBy,
                            'last_updated_date' => $lastupdatedDate,
                        ]);
                }
            } else {
                if ($request->input('SecVacant') != 'on') {
                    BoardsIncoming::create([  // Create board details if new
                        'chapter_id' => $chId,
                        'board_position_id' => '5',
                        'first_name' => $request->input('ch_sec_fname'),
                        'last_name' => $request->input('ch_sec_lname'),
                        'email' => $request->input('ch_sec_email'),
                        'street_address' => $request->input('ch_sec_street'),
                        'city' => $request->input('ch_sec_city'),
                        'state_id' => $request->input('ch_sec_state'),
                        'zip' => $request->input('ch_sec_zip'),
                        'country_id' => $request->input('ch_sec_country') ?? '198',
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

            return redirect()->back()->with('success', 'Board Info has been Submitted');
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
     * Show EOY Financial Report All Board Members
     */
    public function editFinancialReport(Request $request, $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userName = $loggedInName = $user['user_name'];
        $userEmail = $user['user_email'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $awards = $baseQuery['awards'];
        $allAwards = $baseQuery['allAwards'];

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userType' => $userType, 'userAdmin' => $userAdmin,
            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
            'awards' => $awards, 'allAwards' => $allAwards, 'chActiveId' => $chActiveId, 'resourceCategories' => $resourceCategories,
        ];

        return view('boards.financial')->with($data);

    }

    /**
     * Save EOY Financial Report All Board Members
     */
    public function updateFinancialReport(Request $request, $chId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userName = $user['user_name'];
        $userEmail = $user['user_email'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $input = $request->all();
        $reportReceived = $input['submitted'] ?? null;

        $financialReport = FinancialReport::find($chId);
        $documents = Documents::find($chId);
        $chapter = Chapters::find($chId);

        DB::beginTransaction();
        try {
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

            $baseQuery = $this->baseBoardController->getChapterDetails($chId);
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
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport, $reviewer_email_message=null),
            );

            if ($reportReceived == 1) {
                $pdfPath = $this->pdfController->saveFinancialReport($request, $chId);   // Generate and Send the PDF
                Mail::to($userEmail)
                    ->cc($emailListChap)
                    ->queue(new EOYFinancialReportThankYou($mailData, $pdfPath));

                if ($chFinancialReport->reviewer_id == null) {
                    DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [$cc_id, $chId]);
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
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
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
