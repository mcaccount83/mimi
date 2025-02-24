<?php

namespace App\Http\Controllers;

use App\Mail\ChapersUpdateEINCoor;
use App\Mail\ChapersUpdateListAdmin;
use App\Mail\ChapterAddListAdmin;
use App\Mail\ChapterAddPrimaryCoor;
use App\Mail\ChapterReAddListAdmin;
use App\Mail\ChapterRemoveListAdmin;
use App\Mail\ChaptersPrimaryCoordinatorChange;
use App\Mail\ChaptersPrimaryCoordinatorChangePCNotice;
use App\Mail\ChaptersUpdatePrimaryCoorBoard;
use App\Mail\ChaptersUpdatePrimaryCoorChapter;
use App\Mail\NewChapterWelcome;
use App\Mail\PaymentsM2MChapterThankYou;
use App\Mail\PaymentsReRegChapterThankYou;
use App\Mail\PaymentsReRegLate;
use App\Mail\PaymentsReRegReminder;
use App\Mail\PaymentsSustainingChapterThankYou;
use App\Mail\WebsiteAddNoticeAdmin;
use App\Mail\WebsiteAddNoticeChapter;
use App\Mail\WebsiteReviewNotice;
use App\Mail\WebsiteUpdatePrimaryCoor;
use App\Models\Boards;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\Documents;
use App\Models\FinancialReport;
use App\Models\Conference;
use App\Models\Region;
use App\Models\State;
use App\Models\Status;
use App\Models\User;
use App\Models\Website;
use App\Models\ForumCategorySubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ChapterController extends Controller
{
    protected $userController;
    protected $pdfController;
    protected $baseChapterController;
    protected $forumSubscriptionController;

    public function __construct(UserController $userController, PDFController $pdfController, BaseChapterController $baseChapterController,
        ForumSubscriptionController $forumSubscriptionController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
        $this->pdfController = $pdfController;
        $this->baseChapterController = $baseChapterController;
        $this->forumSubscriptionController = $forumSubscriptionController;
    }

    /*/Custom Helpers/*/
    // $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);

    /*/ Base Chapter Controller /*/
    //  $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    //  $this->baseChapterController->getZappedBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    //  $this->baseChapterController->getActiveInquiriesBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    //  $this->baseChapterController->getZappedInquiriesBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid)
    //  $this->baseChapterController->getActiveInternationalBaseQuery($cdId)
    //  $this->baseChapterController->getZappedBaseInternationalQuery($cdId)
    //  $this->baseChapterController->getChapterDetails($chId)

    /*/ Forum Subscription Controller /*/
    //  $this->forumSubscriptionController->defaultCategories()

    /**
     * Display the Active chapter list mapped with login coordinator
     */
    public function showChapters(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $countList = $chapterList->count();
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('chapters.chaplist')->with($data);
    }

    /**
     * Display the Zapped chapter list mapped with Conference Region
     */
    public function showZappedChapter(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getZappedBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('chapters.chapzapped')->with($data);
    }

    /**
     * Display the Inquiries Chapter list
     */
    public function showChapterInquiries(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getActiveInquiriesBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $countList = $chapterList->count();
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('chapters.chapinquiries')->with($data);
    }

    /**
     * Display the Zapped Inquiries list
     */
    public function showZappedChapterInquiries(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getZappedInquiriesBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('chapters.chapinquirieszapped')->with($data);
    }

    /**
     * Display the International chapter list
     */
    public function showIntChapter(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($cdId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('international.intchapter')->with($data);
    }

    /**
     * Display the International Zapped chapter list
     */
    public function showIntZappedChapter(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->baseChapterController->getZappedInternationalBaseQuery($cdId);
        $chapterList = $baseQuery['query']->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('international.intchapterzapped')->with($data);
    }

    /**
     * Display the Chapter Details for ALL lists - Active, Zapped, Inquiries, International
     */
    public function viewChapterDetails(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $reviewComplete = $baseQuery['reviewComplete'];
        $displayEOY = $baseQuery['displayEOY'];
        $displayTESTING = $displayEOY['displayTESTING'];
        $displayLIVE = $displayEOY['displayLIVE'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $websiteLink = $baseQuery['websiteLink'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $now = Carbon::now();
        $threeMonthsAgo = $now->copy()->subMonths(3);
        $startMonthId = $chDetails->start_month_id;
        $startYear = $chDetails->start_year;
        $startDate = Carbon::createFromDate($startYear, $startMonthId, 1);

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'cdPositionid' => $cdPositionid, 'cdId' => $cdId, 'reviewComplete' => $reviewComplete, 'threeMonthsAgo' => $threeMonthsAgo,
            'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails, 'PresDetails' => $PresDetails, 'chDetails' => $chDetails, 'websiteLink' => $websiteLink,
            'startMonthName' => $startMonthName, 'cdConfId' => $cdConfId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chapterStatus' => $chapterStatus, 'startDate' => $startDate,
            'chFinancialReport' => $chFinancialReport, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'displayTESTING' => $displayTESTING, 'displayLIVE' => $displayLIVE
        ];

        return view('chapters.view')->with($data);
    }

    public function checkEIN(Request $request): JsonResponse
    {
        $chapterId = $request->input('chapter_id');
        $chapter = DB::table('chapters')->where('id', $chapterId)->first();

        return response()->json([
            'ein' => $chapter->ein ?? null,
        ]);
    }

    public function updateEIN(Request $request): JsonResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName = $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;
        $lastupdatedDate = date('Y-m-d H:i:s');

        $ein = $request->input('ein');
        $chapterId = $request->input('chapter_id');

        try {
            DB::beginTransaction();

            DB::table('chapters')
                ->where('id', $chapterId)
                ->update(['ein' => $ein,
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => $lastupdatedDate,
                ]);

            // Commit the transaction
            DB::commit();

            $message = 'Chapter EIN successfully updated';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterId]),
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on exception
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterId]),
            ]);
        }
    }

    /**
     * Function for sending New Chapter Email with Attachments
     */
    public function sendNewChapterEmail(Request $request): JsonResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdName = $cdDetails->first_name.' '.$cdDetails->last_name;
        $cdEmail = $cdDetails->email;
        $cdPosition = $cdDetails->displayPosition->long_title;
        $cdConfId = $cdDetails->conference_id;
        $cdCoferenceDescription = $cdDetails->conference->conference_description;

        $input = $request->all();
        $chapterid = $input['chapterid'];

        try {
            DB::beginTransaction();

         //Load Chapter MailData//
         $baseQuery = $this->baseChapterController->getChapterDetails($chapterid);
         $chDetails = $baseQuery['chDetails'];
         $stateShortName = $baseQuery['stateShortName'];
         $chConfId = $baseQuery['chConfId'];
         $chPcId = $baseQuery['chPcId'];
         $emailPC = $baseQuery['emailPC'];
         $pcName = $baseQuery['pcName'];
         $PresDetails = $baseQuery['PresDetails'];
         $emailListChap = $baseQuery['emailListChap'];
         $emailListCoord = $baseQuery['emailListCoord'];

        $mailData = [
            'chapter' => $chDetails->name,
            'state' => $stateShortName,
            'ein' => $chDetails->ein,
            'firstName' => $PresDetails->first_name,
            'email' => $PresDetails->email,
            'cor_name' => $pcName,
            'cor_email' => $emailPC,
            'conf' => $chConfId,
            'userName' => $cdName,
            'userEmail' => $cdEmail,
            'positionTitle' => $cdPosition,
            'conf' => $cdConfId,
            'conf_name' => $cdCoferenceDescription,
        ];

        $pdfPath2 = 'https://drive.google.com/uc?export=download&id=1A3Z-LZAgLm_2dH5MEQnBSzNZEhKs5FZ3';
        $pdfPath =  $this->pdfController->saveGoodStandingLetter($chapterid);   // Generate and save the PDF
        Mail::to($emailListChap)
            ->cc($emailListCoord)
            ->queue(new NewChapterWelcome($mailData, $pdfPath, $pdfPath2));

             // Commit the transaction
             DB::commit();

             $message = 'New Chapter email successfully sent';

             // Return JSON response
             return response()->json([
                 'status' => 'success',
                 'message' => $message,
                 'redirect' => route('chapters.view', ['id' => $chapterid]),
             ]);

         } catch (\Exception $e) {
             // Rollback transaction on exception
             DB::rollback();
             Log::error($e);

             $message = 'Something went wrong, Please try again.';

             // Return JSON error response
             return response()->json([
                 'status' => 'error',
                 'message' => $message,
                 'redirect' => route('chapters.view', ['id' => $chapterid]),
             ]);
         }

    }

    /**
     * Function for Zapping a Chapter (store)
     */
    public function updateChapterDisband(Request $request): JsonResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName = $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;
        $lastupdatedDate = date('Y-m-d H:i:s');

        $input = $request->all();
        $chapterid = $input['chapterid'];
        $disbandReason = $input['reason'];
        $disbandLetter = $input['letter'];
        $letterType = $input['letterType'];

        $chapter = Chapters::find($chapterid);
        $documents = Documents::find($chapterid);

        try {
            DB::beginTransaction();

            $chapter->is_active = '0';
            $chapter->disband_reason = $disbandReason;
            $chapter->zap_date = date('Y-m-d');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            $documents->disband_letter = $disbandLetter;
            $documents->save();

            $userRelatedChapterList = Boards::where('chapter_id', $chapterid)->get();

            if ($userRelatedChapterList->isNotEmpty()) {
                $userIds = $userRelatedChapterList->pluck('user_id');
                User::whereIn('id', $userIds)->update([
                    'is_active' => 0,
                    'updated_at' => $lastupdatedDate,
                ]);
            }

            Boards::where('chapter_id', $chapterid)->update([
                'is_active' => 0,
                'last_updated_by' => $lastUpdatedBy,
                'last_updated_date' => $lastupdatedDate,
            ]);

            if ($userRelatedChapterList->isNotEmpty()) {
                ForumCategorySubscription::whereIn('user_id', $userIds)->delete();
            }

             //Update Chapter MailData//
             $baseQuery = $this->baseChapterController->getChapterDetails($chapterid);
             $chDetails = $baseQuery['chDetails'];
             $stateShortName = $baseQuery['stateShortName'];
             $chConfId = $baseQuery['chConfId'];
             $chPcId = $baseQuery['chPcId'];
             $emailPC = $baseQuery['emailPC'];
             $PresDetails = $baseQuery['PresDetails'];
             $AVPDetails = $baseQuery['AVPDetails'];
             $MVPDetails = $baseQuery['MVPDetails'];
             $TRSDetails = $baseQuery['TRSDetails'];
             $SECDetails = $baseQuery['SECDetails'];

             $emailListChap = $baseQuery['emailListChap'];
             $emailListCoord = $baseQuery['emailListCoord'];
            $cc_email = $baseQuery['emailCC'];
            $cc_fname = $baseQuery['cc_fname'];
            $cc_lname = $baseQuery['cc_lname'];
            $cc_pos = $baseQuery['cc_pos'];
            $cc_conf_name = $baseQuery['cc_conf_name'];
            $cc_conf_desc = $baseQuery['cc_conf_desc'];

            $mailData = [
                'chapterName' => $chDetails->name,
                'chapterEmail' => $chDetails->email,
                'chapterState' => $stateShortName,
                'pfirst' => $PresDetails->first_name,
                'plast' => $PresDetails->last_name,
                'pemail' => $PresDetails->email,
                'afirst' => $AVPDetails->first_name,
                'alast' => $AVPDetails->last_name,
                'aemail' => $AVPDetails->email,
                'mfirst' => $MVPDetails->first_name,
                'mlast' => $MVPDetails->last_name,
                'memail' => $MVPDetails->email,
                'tfirst' => $TRSDetails->first_name,
                'tlast' => $TRSDetails->last_name,
                'temail' => $TRSDetails->email,
                'sfirst' => $SECDetails->first_name,
                'slast' => $SECDetails->last_name,
                'semail' => $SECDetails->email,
                'conf' => $chConfId,
                'cc_fname' => $cc_fname,
                'cc_lname' => $cc_lname,
                'cc_pos' => $cc_pos,
                'cc_conf_name' => $cc_conf_name,
                'cc_conf_desc' => $cc_conf_desc,
                'cc_email' => $cc_email,
            ];

            //Primary Coordinator Notification//
            $to_email = 'listadmin@momsclub.org';
            Mail::to($to_email)
                ->queue(new ChapterRemoveListAdmin($mailData));

            //Standard Disbanding Letter Send to Board & Coordinators//
            if ($disbandLetter == 1) {
                $pdfPath =  $this->pdfController->saveDisbandLetter($chapterid, $letterType);   // Generate and Send the PDF
            }

            // Commit the transaction
            DB::commit();

            $message = 'Chapter successfully unzapped';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on exception
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);
        }
    }

    /**
     * Function for unZapping a Chapter (store)
     */
    public function updateChapterUnZap(Request $request): JsonResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName = $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;
        $lastupdatedDate = date('Y-m-d H:i:s');

        $input = $request->all();
        $chapterid = $input['chapterid'];

        $chapter = Chapters::find($chapterid);
        $documents = Documents::find($chapterid);

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultBoardCategories = $defaultCategories['boardCategories'];

        $coordinatorData = $this->userController->loadReportingTree($cdId);

        try {
            DB::beginTransaction();

            $chapter->is_active = '1';
            $chapter->disband_reason = null;
            $chapter->zap_date = null;
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;
            $chapter->save();

            $documents->disband_letter = null;
            $documents->save();

            $userRelatedChapterList = Boards::where('chapter_id', $chapterid)->get();

            if ($userRelatedChapterList->isNotEmpty()) {
                $userIds = $userRelatedChapterList->pluck('user_id');
                User::whereIn('id', $userIds)->update([
                    'is_active' => 1,
                    'updated_at' => $lastupdatedDate,
                ]);
            }

            Boards::where('chapter_id', $chapterid)->update([
                'is_active' => 1,
                'last_updated_by' => $lastUpdatedBy,
                'last_updated_date' => $lastupdatedDate,
            ]);

            foreach ($userIds as $userId) {
                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,  // Now passing a single ID instead of collection
                        'category_id' => $categoryId,
                    ]);
                }
            }

            //Update Chapter MailData//
            $baseQuery = $this->baseChapterController->getChapterDetails($chapterid);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chConfId = $baseQuery['chConfId'];
            $chPcId = $baseQuery['chPcId'];
            $emailPC = $baseQuery['emailPC'];
            $PresDetails = $baseQuery['PresDetails'];
            $AVPDetails = $baseQuery['AVPDetails'];
            $MVPDetails = $baseQuery['MVPDetails'];
            $TRSDetails = $baseQuery['TRSDetails'];
            $SECDetails = $baseQuery['SECDetails'];

            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];
            $cc_email = $baseQuery['emailCC'];
            $cc_fname = $baseQuery['cc_fname'];
            $cc_lname = $baseQuery['cc_lname'];
            $cc_pos = $baseQuery['cc_pos'];
            $cc_conf_name = $baseQuery['cc_conf_name'];
            $cc_conf_desc = $baseQuery['cc_conf_desc'];

            $mailData = [
                'chapterName' => $chDetails->name,
                'chapterEmail' => $chDetails->email,
                'chapterState' => $stateShortName,
                'pfirst' => $PresDetails->first_name,
                'plast' => $PresDetails->last_name,
                'pemail' => $PresDetails->email,
                'afirst' => $AVPDetails->first_name,
                'alast' => $AVPDetails->last_name,
                'aemail' => $AVPDetails->email,
                'mfirst' => $MVPDetails->first_name,
                'mlast' => $MVPDetails->last_name,
                'memail' => $MVPDetails->email,
                'tfirst' => $TRSDetails->first_name,
                'tlast' => $TRSDetails->last_name,
                'temail' => $TRSDetails->email,
                'sfirst' => $SECDetails->first_name,
                'slast' => $SECDetails->last_name,
                'semail' => $SECDetails->email,
                'conf' => $chConfId,
                'cc_fname' => $cc_fname,
                'cc_lname' => $cc_lname,
                'cc_pos' => $cc_pos,
                'cc_conf_desc' => $cc_conf_desc,
                'cc_email' => $cc_email,
            ];

            //Primary Coordinator Notification//
            $to_email = 'listadmin@momsclub.org';
            Mail::to($to_email)
                ->queue(new ChapterReAddListAdmin($mailData));

            // Commit the transaction
            DB::commit();

            $message = 'Chapter successfully unzapped';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on exception
            DB::rollback();
            Log::error($e);

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);
        }

    }

    /**
     *Add New Chapter
     */
    public function addChapterNew(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $allStates = State::all();  // Full List for Dropdown Menu
        $allRegions = Region::with('conference')  // Full List for Dropdown Menu based on Conference
            ->where('conference_id', $cdConfId)
            ->get();
        $allStatuses = Status::all();  // Full List for Dropdown Menu
        $chConfId = $cdConfId;

        $pcList = Coordinators::with(['displayPosition', 'secondaryPosition'])
            ->where('conference_id', $chConfId)
            ->whereBetween('position_id', [1, 7])
            ->where('is_active', 1)
            ->where('on_leave', '!=', '1')
            ->get();

        $pcDetails = $pcList->map(function ($coordinator) {
            $cpos = $coordinator->displayPosition->short_title ?? '';
            if (isset($coordinator->secondaryPosition->short_title)) {
                $cpos = "({$cpos}/{$coordinator->secondaryPosition->short_title})";
            } elseif ($cpos) {
                $cpos = "({$cpos})";
            }

            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'dpos' => $coordinator->displayPosition->short_title ?? '',
                'spos' => $coordinator->secondaryPosition->short_title ?? '',
                'cpos' => $cpos,
                'regid' => $coordinator->region_id,
            ];
        });

        $pcDetails = $pcDetails->unique('cid');  // Remove duplicates based on the 'cid' field

        $data = ['allRegions' => $allRegions, 'allStatuses' => $allStatuses, 'pcDetails' => $pcDetails, 'allStates' => $allStates,

        ];

        return view('chapters.addnew')->with($data);
    }

    /**
     *Save New Chapter
     */
    public function updateChapterNew(Request $request): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

        $conference = $cdConfId;
        $country = 'USA';
        $currentMonth = date('m');
        $currentYear = date('Y');

        $input = $request->all();

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultBoardCategories = $defaultCategories['boardCategories'];

        DB::beginTransaction();
        try {
            $chapterId = Chapters::create([
                'name' => $input['ch_name'],
                'state_id' => $input['ch_state'],
                'country_short_name' => $country,
                'conference_id' => $conference,
                'region_id' => $input['ch_region'],
                'ein' => $input['ch_ein'],
                'status_id' => $input['ch_status'],
                'territory' => $input['ch_boundariesterry'],
                'inquiries_contact' => $input['ch_inqemailcontact'],
                'start_month_id' => $currentMonth,
                'start_year' => $currentYear,
                'next_renewal_year' => $currentYear + 1,
                'primary_coordinator_id' => $input['ch_primarycor'],
                'founders_name' => $input['ch_pre_fname'].' '.$input['ch_pre_lname'],
                'last_updated_by' => $lastUpdatedBy,
                'last_updated_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'is_active' => 1,
            ])->id;

            $financialReport = FinancialReport::create([
                'chapter_id' => $chapterId,
            ]);

            $documents = Documents::create([
                'chapter_id' => $chapterId,
            ]);

            //President Info
            if (isset($input['ch_pre_fname']) && isset($input['ch_pre_lname']) && isset($input['ch_pre_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;

                $boardId = Boards::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'board_position_id' => 1,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_pre_street'],
                    'city' => $input['ch_pre_city'],
                    'state' => $input['ch_pre_state'],
                    'zip' => $input['ch_pre_zip'],
                    'country' => $country,
                    'phone' => $input['ch_pre_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1,
                ])->id;

                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            //AVP Info
            if (isset($input['ch_avp_fname']) && isset($input['ch_avp_lname']) && isset($input['ch_avp_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;

                $boardId = Boards::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'board_position_id' => 1,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_pre_street'],
                    'city' => $input['ch_pre_city'],
                    'state' => $input['ch_pre_state'],
                    'zip' => $input['ch_pre_zip'],
                    'country' => $country,
                    'phone' => $input['ch_pre_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1,
                ])->id;

                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            //MVP Info
            if (isset($input['ch_mvp_fname']) && isset($input['ch_mvp_lname']) && isset($input['ch_mvp_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;

                $boardId = Boards::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'board_position_id' => 1,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_pre_street'],
                    'city' => $input['ch_pre_city'],
                    'state' => $input['ch_pre_state'],
                    'zip' => $input['ch_pre_zip'],
                    'country' => $country,
                    'phone' => $input['ch_pre_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1,
                ])->id;

                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            //TREASURER Info
            if (isset($input['ch_trs_fname']) && isset($input['ch_trs_lname']) && isset($input['ch_trs_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;

                $boardId = Boards::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'board_position_id' => 1,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_pre_street'],
                    'city' => $input['ch_pre_city'],
                    'state' => $input['ch_pre_state'],
                    'zip' => $input['ch_pre_zip'],
                    'country' => $country,
                    'phone' => $input['ch_pre_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1,
                ])->id;

                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            //Secretary Info
            if (isset($input['ch_sec_fname']) && isset($input['ch_sec_lname']) && isset($input['ch_sec_email'])) {
                $userId = User::create([
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'password' => Hash::make('TempPass4You'),
                    'user_type' => 'board',
                    'is_active' => 1,
                ])->id;

                $boardId = Boards::create([
                    'user_id' => $userId,
                    'first_name' => $input['ch_pre_fname'],
                    'last_name' => $input['ch_pre_lname'],
                    'email' => $input['ch_pre_email'],
                    'board_position_id' => 1,
                    'chapter_id' => $chapterId,
                    'street_address' => $input['ch_pre_street'],
                    'city' => $input['ch_pre_city'],
                    'state' => $input['ch_pre_state'],
                    'zip' => $input['ch_pre_zip'],
                    'country' => $country,
                    'phone' => $input['ch_pre_phone'],
                    'last_updated_by' => $lastUpdatedBy,
                    'last_updated_date' => date('Y-m-d H:i:s'),
                    'is_active' => 1,
                ])->id;

                foreach ($defaultBoardCategories as $categoryId) {
                    ForumCategorySubscription::create([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                    ]);
                }
            }

            $chDetails = Chapters::with(['state', 'primaryCoordinator'])->find($chapterId);
            $stateShortName = $chDetails->state->state_short_name;
            $chConfId = $chDetails->conference->id;
            $pcFName = $chDetails->primaryCoordinator->first_name;
            $pcLName = $chDetails->primaryCoordinator->last_name;
            $pcEmail = $chDetails->primaryCoordinator->email;

            $mailData = [
                'chapter_name' => $chDetails->name,
                'chapter_state' => $stateShortName,
                'conf' => $chConfId,
                'cor_fname' => $pcFName,
                'cor_lname' => $pcLName,
                'updated_by' => date('Y-m-d H:i:s'),
                'pfirst' => $input['ch_pre_fname'],
                'plast' => $input['ch_pre_lname'],
                'pemail' => $input['ch_pre_email'],
                'afirst' => $input['ch_avp_fname'],
                'alast' => $input['ch_avp_lname'],
                'aemail' => $input['ch_avp_email'],
                'mfirst' => $input['ch_mvp_fname'],
                'mlast' => $input['ch_mvp_lname'],
                'memail' => $input['ch_mvp_email'],
                'tfirst' => $input['ch_trs_fname'],
                'tlast' => $input['ch_trs_lname'],
                'temail' => $input['ch_trs_email'],
                'sfirst' => $input['ch_sec_fname'],
                'slast' => $input['ch_sec_lname'],
                'semail' => $input['ch_sec_email'],
            ];

            //Primary Coordinator Notification//
            Mail::to($pcEmail)
                ->queue(new ChapterAddPrimaryCoor($mailData));

            //List Admin Notification//
            $listAdminEmail = 'listadmin@momsclub.org';
            Mail::to($listAdminEmail)
                ->queue(new ChapterAddListAdmin($mailData));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/chapter/chapterlist')->with('fail', 'Something went wrong, Please try again...');
        }

        return redirect()->to('/chapter/chapterlist')->with('success', 'Chapter created successfully');
    }

    /**
     *Edit Chapter Information
     */
    public function editChapterDetails(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chRegId = $baseQuery['chRegId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $reviewComplete = $baseQuery['reviewComplete'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $websiteLink = $baseQuery['websiteLink'];

        $allStatuses = $baseQuery['allStatuses'];
        $allWebLinks = $baseQuery['allWebLinks'];

        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];

        $pcDetails = $baseQuery['pcDetails'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'cdPositionid' => $cdPositionid, 'cdId' => $cdId, 'reviewComplete' => $reviewComplete,
            'emailListCoord' => $emailListCoord, 'emailListChap' => $emailListChap, 'chDetails' => $chDetails, 'websiteLink' => $websiteLink,
            'startMonthName' => $startMonthName, 'cdConfId' => $cdConfId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chapterStatus' => $chapterStatus,
            'chFinancialReport' => $chFinancialReport, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName,
            'conferenceDescription' => $conferenceDescription, 'allStatuses' => $allStatuses, 'allWebLinks' => $allWebLinks,
            'pcDetails' => $pcDetails,
        ];

        return view('chapters.edit')->with($data);
    }

    /**
     *Update Chapter Information
     */
    public function updateChapterDetails(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName = $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQueryPre = $this->baseChapterController->getChapterDetails($id);
        $chDetailsPre = $baseQueryPre['chDetails'];
        $PresDetailsPre = $baseQueryPre['PresDetails'];

        $input = $request->all();
        $chPcIdPre = $input['ch_hid_primarycor'];
        $chPcIdUpd = $input['ch_primarycor'];
        $webStatusPre = $input['ch_hid_webstatus'];
        $webStatusUpd = $input['ch_webstatus'];

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

        DB::beginTransaction();
        try {
            $chapter->name = $request->filled('ch_name') ? $request->input('ch_name') : $request->input('ch_hid_name');
            $chapter->notes = $request->input('ch_einnotes');
            $chapter->former_name = $request->filled('ch_preknown') ? $request->input('ch_preknown') : $request->input('ch_hid_preknown');
            $chapter->sistered_by = $request->filled('ch_sistered') ? $request->input('ch_sistered') : $request->input('ch_hid_sistered');
            $chapter->territory = $request->filled('ch_boundariesterry') ? $request->input('ch_boundariesterry') : $request->input('ch_hid_boundariesterry');
            $chapter->status_id = $request->filled('ch_status') ? $request->input('ch_status') : $request->input('ch_hid_status');
            $chapter->notes = $request->input('ch_notes');
            $chapter->inquiries_contact = $request->input('ch_inqemailcontact');
            $chapter->inquiries_note = $request->input('ch_inqnote');
            $chapter->email = $request->input('ch_email');
            $chapter->po_box = $request->input('ch_pobox');
            $chapter->additional_info = $request->input('ch_addinfo');
            $chapter->website_url = $website;
            $chapter->website_status = $request->input('ch_webstatus');
            $chapter->website_notes = $request->input('ch_webnotes');
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->primary_coordinator_id = $request->filled('ch_primarycor') ? $request->input('ch_primarycor') : $request->input('ch_hid_primarycor');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;

            $chapter->save();

            //Update Chapter MailData//
            $baseQueryUpd = $this->baseChapterController->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $chConfId = $baseQueryUpd['chConfId'];
            $chPcId = $baseQueryUpd['chPcId'];
            $PresDetailsUpd = $baseQueryUpd['PresDetails'];
            $emailListChap = $baseQueryUpd['emailListChap'];  // Full Board
            $emailListCoord = $baseQueryUpd['emailListCoord'];  // Full Coordinaor List
            $emailCC = $baseQueryUpd['emailCC'];  // CC Email
            $pcDetails = $baseQueryUpd['chDetails']->primaryCoordinator;
            $pcEmail = $pcDetails->email;  // PC Email
            $EINCordEmail = 'jackie.mchenry@momsclub.org';  // EIN Coor Email

            $mailData = [
                'chapterNameUpd' => $chDetailsUpd->name,
                'boundUpd' => $chDetailsUpd->territory,
                'chapstatusUpd' => $chDetailsUpd->status_id,
                'chapNoteUpd' => $chDetailsUpd->notes,
                'inConUpd' => $chDetailsUpd->inquiries_contact,
                'inNoteUpd' => $chDetailsUpd->inquiries_note,
                'chapemailUpd' => $chDetailsUpd->email,
                'poBoxUpd' => $chDetailsUpd->po_box,
                'addInfoUpd' => $chDetailsUpd->additional_info,
                'webUrlUpd' => $chDetailsUpd->website_url,
                'webStatusUpd' => $chDetailsUpd->website_status,
                'egroupUpd' => $chDetailsUpd->egroup,
                'cor_fnameUpd' => $PresDetailsUpd->cor_f_name,
                'cor_lnameUpd' => $PresDetailsUpd->cor_l_name,

                'chapterNamePre' => $chDetailsPre->name,
                'boundPre' => $chDetailsPre->territory,
                'chapstatusPre' => $chDetailsPre->status_id,
                'chapNotePre' => $chDetailsPre->notes,
                'inConPre' => $chDetailsPre->inquiries_contact,
                'inNotePre' => $chDetailsPre->inquiries_note,
                'chapemailPre' => $chDetailsPre->email,
                'poBoxPre' => $chDetailsPre->po_box,
                'addInfoPre' => $chDetailsPre->additional_info,
                'webUrlPre' => $chDetailsPre->website_url,
                'webStatusPre' => $chDetailsPre->website_status,
                'egroupPre' => $chDetailsPre->egroup,
                'cor_fnamePre' => $PresDetailsPre->cor_f_name,
                'cor_lnamePre' => $PresDetailsPre->cor_l_name,

                'chapter_name' => $chDetailsUpd->name,
                'chapter_state' => $stateShortName,
                'conference' => $chConfId,
                'updated_byUpd' => $lastupdatedDate,

                'ch_pre_fname' => $PresDetailsPre->first_name,
                'ch_pre_lname' => $PresDetailsPre->last_name,
                'ch_pre_email' => $PresDetailsPre->email,
                'name1' => $pcDetails->first_name,
                'name2' => $pcDetails->last_name,
                'email1' => $pcDetails->email,

                'ch_website_url' => $website,
            ];

            //Primary Coordinator Notification//
            if ($chDetailsUpd->name != $chDetailsPre->name || $chDetailsUpd->inquiries_contact != $chDetailsPre->inquiries_contact || $chDetailsUpd->inquiries_note != $chDetailsPre->inquiries_note ||
                    $chDetailsUpd->email != $chDetailsPre->email || $chDetailsUpd->po_box != $chDetailsPre->po_box || $chDetailsUpd->website_url != $chDetailsPre->website_url ||
                    $chDetailsUpd->website_status != $chDetailsPre->website_status || $chDetailsUpd->egroup != $chDetailsPre->egroup || $chDetailsUpd->territory != $chDetailsPre->territory ||
                    $chDetailsUpd->additional_info != $chDetailsPre->additional_info || $chDetailsUpd->status_id != $chDetailsPre->status_id || $chDetailsUpd->notes != $chDetailsPre->notes) {
                Mail::to($pcEmail)
                    ->queue(new ChaptersUpdatePrimaryCoorChapter($mailData));
            }

            //Name Change Notification//
            if ($chDetailsUpd->name != $chDetailsPre->name) {
                Mail::to($EINCordEmail)
                    ->queue(new ChapersUpdateEINCoor($mailData));
            }

            //PC Change Notification//
            if ($chPcIdUpd != $chPcIdPre) {
                Mail::to($emailListChap)
                    ->queue(new ChaptersPrimaryCoordinatorChange($mailData));

                Mail::to($pcEmail)
                    ->queue(new ChaptersPrimaryCoordinatorChangePCNotice($mailData));
            }

            //Website URL Change Notification//
            if ($webStatusUpd != $webStatusPre) {
                if ($webStatusUpd == 1) {
                    Mail::to($emailCC)
                        ->queue(new WebsiteAddNoticeAdmin($mailData));

                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new WebsiteAddNoticeChapter($mailData));
                }

                if ($webStatusUpd == 2) {
                    Mail::to($emailCC)
                        ->queue(new WebsiteReviewNotice($mailData));
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.view', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('chapters.view', ['id' => $id])->with('success', 'Chapter Details have been updated');
    }

    /**
     *Edit Chapter Board Information
     */
    public function editChapterBoard(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $reviewComplete = $baseQuery['reviewComplete'];

        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $baseQuery['chapterStatus'];
        $websiteLink = $baseQuery['websiteLink'];

        $PresDetails = $baseQuery['PresDetails'];
        $AVPDetails = $baseQuery['AVPDetails'];
        $MVPDetails = $baseQuery['MVPDetails'];
        $TRSDetails = $baseQuery['TRSDetails'];
        $SECDetails = $baseQuery['SECDetails'];

        $allStates = $baseQuery['allStates'];
        $websiteLink = $baseQuery['websiteLink'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'cdId' => $cdId, 'reviewComplete' => $reviewComplete, 'stateShortName' => $stateShortName,
            'chDetails' => $chDetails, 'websiteLink' => $websiteLink, 'SECDetails' => $SECDetails, 'TRSDetails' => $TRSDetails, 'MVPDetails' => $MVPDetails, 'AVPDetails' => $AVPDetails,
            'cdConfId' => $cdConfId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'allStates' => $allStates, 'PresDetails' => $PresDetails,
            'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
        ];

        return view('chapters.editboard')->with($data);
    }

    /**
     *Update Chapter Board Information
     */
    public function updateChapterBoard(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName = $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;
        $lastupdatedDate = date('Y-m-d H:i:s');

        $baseQueryPre = $this->baseChapterController->getChapterDetails($id);
        $chDetailsPre = $baseQueryPre['chDetails'];
        $PresDetailsPre = $baseQueryPre['PresDetails'];
        $AVPDetailsPre = $baseQueryPre['AVPDetails'];
        $MVPDetailsPre = $baseQueryPre['MVPDetails'];
        $TRSDetailsPre = $baseQueryPre['TRSDetails'];
        $SECDetailsPre = $baseQueryPre['SECDetails'];

        $chapter = Chapters::find($id);

        $defaultCategories = $this->forumSubscriptionController->defaultCategories();
        $defaultBoardCategories = $defaultCategories['boardCategories'];

        DB::beginTransaction();
        try {
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
                // Check if subscription already exists
                foreach ($defaultBoardCategories as $categoryId) {
                    $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                        ->where('category_id', $categoryId)
                        ->first();
                    if (!$existingSubscription) {
                        ForumCategorySubscription::create([
                            'user_id' => $user->id,
                            'category_id' => $categoryId,
                        ]);
                    }
                }
            }

            //AVP Info
            $chapter = Chapters::with('avp')->find($id);
            $avp = $chapter->avp;
            if ($avp) {
                $user = $avp->user;
                if ($request->input('AVPVacant') == 'on') {
                    $avp->delete();  // Delete board member and associated user if now Vacant
                    $user->delete();
                    ForumCategorySubscription::where('user_id', $user)->delete();
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
                        // Check if subscription already exists
                        foreach ($defaultBoardCategories as $categoryId) {
                            $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                                ->where('category_id', $categoryId)
                                ->first();
                            if (!$existingSubscription) {
                                ForumCategorySubscription::create([
                                    'user_id' => $user->id,
                                    'category_id' => $categoryId,
                                ]);
                            }
                        }
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
                    foreach ($defaultBoardCategories as $categoryId) {
                        ForumCategorySubscription::create([
                            'user_id' => $user->id,
                            'category_id' => $categoryId,
                        ]);
                    }
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
                    ForumCategorySubscription::where('user_id', $user)->delete();
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
                    // Check if subscription already exists
                    foreach ($defaultBoardCategories as $categoryId) {
                        $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                            ->where('category_id', $categoryId)
                            ->first();
                        if (!$existingSubscription) {
                            ForumCategorySubscription::create([
                                'user_id' => $user->id,
                                'category_id' => $categoryId,
                            ]);
                        }
                    }
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

                    foreach ($defaultBoardCategories as $categoryId) {
                        ForumCategorySubscription::create([
                            'user_id' => $user->id,
                            'category_id' => $categoryId,
                        ]);
                    }
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
                    ForumCategorySubscription::where('user_id', $user)->delete();
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
                    // Check if subscription already exists
                    foreach ($defaultBoardCategories as $categoryId) {
                        $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                            ->where('category_id', $categoryId)
                            ->first();
                        if (!$existingSubscription) {
                            ForumCategorySubscription::create([
                                'user_id' => $user->id,
                                'category_id' => $categoryId,
                            ]);
                        }
                    }
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

                    foreach ($defaultBoardCategories as $categoryId) {
                        ForumCategorySubscription::create([
                            'user_id' => $user->id,
                            'category_id' => $categoryId,
                        ]);
                    }
                }
            }

            //SEC Info
            $chapter = Chapters::with('secretary')->find($id);
            $secretary = $chapter->secretary;
            ForumCategorySubscription::where('user_id', $user)->delete();
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
                    // Check if subscription already exists
                    foreach ($defaultBoardCategories as $categoryId) {
                        $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                            ->where('category_id', $categoryId)
                            ->first();
                        if (!$existingSubscription) {
                            ForumCategorySubscription::create([
                                'user_id' => $user->id,
                                'category_id' => $categoryId,
                            ]);
                        }
                    }
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

                    foreach ($defaultBoardCategories as $categoryId) {
                        ForumCategorySubscription::create([
                            'user_id' => $user->id,
                            'category_id' => $categoryId,
                        ]);
                    }
                }
            }

            //Update Chapter MailData//
            $baseQueryUpd = $this->baseChapterController->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $chConfId = $baseQueryUpd['chConfId'];
            $chPcId = $baseQueryUpd['chPcId'];
            $emailPC = $baseQueryUpd['emailPC'];
            $PresDetailsUpd = $baseQueryUpd['PresDetails'];
            $AVPDetailsUpd = $baseQueryUpd['AVPDetails'];
            $MVPDetailsUpd = $baseQueryUpd['MVPDetails'];
            $TRSDetailsUpd = $baseQueryUpd['TRSDetails'];
            $SECDetailsUpd = $baseQueryUpd['SECDetails'];

            $mailDataPres = [
                'chapter_name' => $chDetailsUpd->name,
                'chapter_state' => $stateShortName,
                'conference' => $chConfId,
                'updated_byUpd' => $lastUpdatedBy,
                'updated_byPre' => $lastupdatedDate,

                'chapfnamePre' => $PresDetailsPre->first_name,
                'chaplnamePre' => $PresDetailsPre->last_name,
                'chapteremailPre' => $PresDetailsPre->email,
                'phonePre' => $PresDetailsPre->phone,
                'streetPre' => $PresDetailsPre->street,
                'cityPre' => $PresDetailsPre->city,
                'statePre' => $PresDetailsPre->state,
                'zipPre' => $PresDetailsPre->zip,

                'chapfnameUpd' => $PresDetailsUpd->first_name,
                'chaplnameUpd' => $PresDetailsUpd->last_name,
                'chapteremailUpd' => $PresDetailsUpd->email,
                'phoneUpd' => $PresDetailsUpd->phone,
                'streetUpd' => $PresDetailsUpd->street,
                'cityUpd' => $PresDetailsUpd->city,
                'stateUpd' => $PresDetailsUpd->state,
                'zipUpd' => $PresDetailsUpd->zip,
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
                    $PresDetailsUpd->zip != $PresDetailsPre->zip || $PresDetailsUpd->phone != $PresDetailsPre->phone || $PresDetailsUpd->inquiries_contact != $PresDetailsPre->inquiries_contact ||
                    $PresDetailsUpd->ein != $chDetailsPre->ein || $chDetailsUpd->ein_letter_path != $chDetailsPre->ein_letter_path || $PresDetailsUpd->inquiries_note != $PresDetailsPre->inquiries_note ||
                    $chDetailsUpd->email != $chDetailsPre->email || $chDetailsUpd->po_box != $chDetailsPre->po_box || $chDetailsUpd->website_url != $chDetailsPre->website_url ||
                    $chDetailsUpd->website_status != $chDetailsPre->website_status || $chDetailsUpd->egroup != $chDetailsPre->egroup || $chDetailsUpd->territory != $chDetailsPre->territory ||
                    $chDetailsUpd->additional_info != $chDetailsPre->additional_info || $chDetailsUpd->status_id != $chDetailsPre->status_id || $chDetailsUpd->notes != $chDetailsPre->notes ||
                    $mailDataAvpp['avpfnamePre'] != $mailDataAvp['avpfnameUpd'] || $mailDataAvpp['avplnamePre'] != $mailDataAvp['avplnameUpd'] || $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] ||
                    $mailDataMvpp['mvpfnamePre'] != $mailDataMvp['mvpfnameUpd'] || $mailDataMvpp['mvplnamePre'] != $mailDataMvp['mvplnameUpd'] || $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] ||
                    $mailDatatresp['tresfnamePre'] != $mailDatatres['tresfnameUpd'] || $mailDatatresp['treslnamePre'] != $mailDatatres['treslnameUpd'] || $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] ||
                    $mailDataSecp['secfnamePre'] != $mailDataSec['secfnameUpd'] || $mailDataSecp['seclnamePre'] != $mailDataSec['seclnameUpd'] || $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {

                Mail::to($emailPC)
                    ->queue(new ChaptersUpdatePrimaryCoorBoard($mailData));
            }

            // //List Admin Notification//
            $to_email2 = 'listadmin@momsclub.org';

            if ($PresDetailsUpd->email != $PresDetailsPre->email || $PresDetailsUpd->email != $PresDetailsPre->email || $mailDataAvpp['avpemailPre'] != $mailDataAvp['avpemailUpd'] ||
                        $mailDataMvpp['mvpemailPre'] != $mailDataMvp['mvpemailUpd'] || $mailDatatresp['tresemailPre'] != $mailDatatres['tresemailUpd'] ||
                        $mailDataSecp['secemailPre'] != $mailDataSec['secemailUpd']) {

                Mail::to($to_email2)
                    ->queue(new ChapersUpdateListAdmin($mailData));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.view', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('chapters.view', ['id' => $id])->with('success', 'Chapter Details have been updated');
    }

    /**
     *Edit Chapter EIN Notes
     */
    public function editChapterIRS(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $startMonthName = $baseQuery['startMonthName'];
        $chIsActive = $baseQuery['chIsActive'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];
        $chDocuments = $baseQuery['chDocuments'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'cdId' => $cdId, 'conferenceDescription' => $conferenceDescription,
            'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'regionLongName' => $regionLongName, 'startMonthName' => $startMonthName,
            'cdConfId' => $cdConfId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'chDocuments' => $chDocuments,
        ];

        return view('chapters.editirs')->with($data);
    }

    /**
     *Update Chapter EIN Notes
     */
    public function updateChapterIRS(Request $request, $id): RedirectResponse
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

            $documents->ein_letter = $request->has('ch_ein_letter') ? 1 : 0;
            $documents->irs_verified = $request->has('irs_verified') ? 1 : 0;
            $documents->irs_notes = $request->input('irs_notes');
            $documents->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.editirs', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('chapters.editirs', ['id' => $id])->with('success', 'Chapter IRS Information has been updated');
    }

    /**
     * Display the Website Details
     */
    public function showChapterWebsite(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $websiteList = $baseQuery['query']->get();

        $data = ['websiteList' => $websiteList];

        return view('chapters.chapwebsite')->with($data);
    }

    /**
     * Display the Social Media Information
     */
    public function showRptSocialMedia(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList, 'corId' => $cdId];

        return view('chapreports.chaprptsocialmedia')->with($data);
    }

    /**
     *Edit Website & Social Information
     */
    public function editChapterWebsite(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $chIsActive = $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $chConfId = $baseQuery['chConfId'];
        $chPcId = $baseQuery['chPcId'];

        $allWebLinks = Website::all();  // Full List for Dropdown Menu

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'cdId' => $cdId, 'stateShortName' => $stateShortName, 'conferenceDescription' => $conferenceDescription,
            'chDetails' => $chDetails, 'allWebLinks' => $allWebLinks,
            'cdConfId' => $cdConfId, 'chConfId' => $chConfId, 'chPcId' => $chPcId, 'regionLongName' => $regionLongName,
        ];

        return view('chapters.editwebsite')->with($data);
    }

    /**
     *Update Website & Social Media Information
     */
    public function updateChapterWebsite(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

        $baseQueryPre = $this->baseChapterController->getChapterDetails($id);
        $chDetailsPre = $baseQueryPre['chDetails'];

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

        DB::beginTransaction();
        try {
            $chapter->website_url = $website;
            $chapter->website_status = $request->input('ch_webstatus');
            $chapter->website_notes = $request->input('ch_webnotes');
            $chapter->egroup = $request->input('ch_onlinediss');
            $chapter->social1 = $request->input('ch_social1');
            $chapter->social2 = $request->input('ch_social2');
            $chapter->social3 = $request->input('ch_social3');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = date('Y-m-d H:i:s');

            $chapter->save();

            //Update Chapter MailData//
            $baseQueryUpd = $this->baseChapterController->getChapterDetails($id);
            $chDetailsUpd = $baseQueryUpd['chDetails'];
            $stateShortName = $baseQueryUpd['stateShortName'];
            $chPcId = $baseQueryUpd['chPcId'];
            $emailListChap = $baseQueryUpd['emailListChap'];  // Full Board
            $emailListCoord = $baseQueryUpd['emailListCoord']; // Full Coord List
            $emailCC = $baseQueryUpd['emailCC'];  // CC Email
            $emailPC = $baseQueryUpd['emailPC'];

            if ($request->input('ch_webstatus') != $request->input('ch_hid_webstatus')) {
                $mailData = [
                    'chapter_name' => $chDetailsUpd->name,
                    'chapter_state' => $stateShortName,
                    'ch_website_url' => $website,
                ];

                if ($request->input('ch_webstatus') == 1) {
                    Mail::to($emailCC)
                        ->queue(new WebsiteAddNoticeAdmin($mailData));

                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new WebsiteAddNoticeChapter($mailData));
                }

                if ($request->input('ch_webstatus') == 2) {
                    Mail::to($emailCC)
                        ->queue(new WebsiteReviewNotice($mailData));
                }
            }

            $mailData = [
                'chapter_name' => $chDetailsUpd->name,
                'chapter_state' => $stateShortName,
                'webUrlUpd' => $chDetailsUpd->website_url,
                'webStatusUpd' => $chDetailsUpd->website_status,
                'webUrlPre' => $chDetailsPre->website_url,
                'webStatusPre' => $chDetailsPre->website_status,
                'updated_byUpd' => $chDetailsPre->last_updated_date,
            ];

            if ($chDetailsUpd->website_url != $chDetailsPre->website_url || $chDetailsUpd->website_status != $chDetailsPre->website_status) {
                Mail::to($emailPC)
                    ->queue(new WebsiteUpdatePrimaryCoor($mailData));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.editwebsite', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('chapters.editwebsite', ['id' => $id])->with('success', 'Chapter Website & Social Meida has been updated');
    }

    /**
     * BoardList
     */
    public function showChapterBoardlist(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $activeChapterList = $baseQuery['query']->get();

        $countList = count($activeChapterList);
        $data = ['countList' => $countList, 'activeChapterList' => $activeChapterList];

        return view('chapters.chapboardlist')->with($data);
    }

    /**
     * ReRegistration List
     */
    public function showChapterReRegistration(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $currentYear = date('Y');
        $currentMonth = date('m');

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $checkBoxStatus = $baseQuery['checkBoxStatus'];
        $checkBox3Status = $baseQuery['checkBox3Status'];

        if ($checkBox3Status) {
            $reChapterList = $baseQuery['query']
                ->get();
        } else {
            $reChapterList = $baseQuery['query']
                ->where(function ($query) use ($currentYear, $currentMonth) {
                    $query->where('next_renewal_year', '<', $currentYear)
                        ->orWhere(function ($query) use ($currentYear, $currentMonth) {
                            $query->where('next_renewal_year', '=', $currentYear)
                                ->where('start_month_id', '<=', $currentMonth);
                        });
                })
                ->orderByDesc('start_month_id')
                ->orderByDesc('next_renewal_year')
                ->get();
        }

        $countList = count($reChapterList);
        $data = ['countList' => $countList, 'reChapterList' => $reChapterList, 'checkBoxStatus' => $checkBoxStatus, 'checkBox3Status' => $checkBox3Status, 'corId' => $cdId];

        return view('chapters.chapreregistration')->with($data);
    }

    /**
     * ReRegistration Reminders Auto Send
     */
    public function createChapterReRegistrationReminder(Request $request): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;

        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $monthInWords = $now->format('F');
        $rangeEndDate = $now->copy()->subMonth()->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        try {

            $chapters = Chapters::with(['state', 'conference', 'region'])
                ->where('conference_id', $cdConfId)
                ->where('start_month_id', $month)
                ->where('next_renewal_year', $year)
                ->where('is_active', 1)
                ->get();

            if ($chapters->isEmpty()) {
                return redirect()->back()->with('info', 'There are no Chapters with Registrations Due.');
            }

            $chapterIds = [];
            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapters as $chapter) {
                $chapterIds[] = $chapter->id;

                $chapterName = $chapter->name;
                $stateShortName = $chapter->state->state_short_name;

                if ($chapterName) {
                    $emailData = $this->userController->loadEmailDetails($chapter->id);
                    $emailListChap = $emailData['emailListChap'];
                    $emailListCoord = $emailData['emailListCoord'];

                    $chapterEmails[$chapterName] = $emailListChap;
                    $coordinatorEmails[$chapterName] = $emailListCoord;
                }

                $mailData[$chapterName] = [
                    'chapterName' => $chapterName,
                    'chapterState' => $stateShortName,
                    'startRange' => $rangeStartDateFormatted,
                    'endRange' => $rangeEndDateFormatted,
                    'startMonth' => $monthInWords,
                ];
            }

            foreach ($mailData as $chapterName => $data) {
                $to_email = $chapterEmails[$chapterName] ?? [];
                $cc_email = $coordinatorEmails[$chapterName] ?? [];

                if (! empty($to_email)) {
                    Mail::to($to_email)
                        ->cc($cc_email)
                        ->queue(new PaymentsReRegReminder($data));
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapter/reregistration')->with('success', 'Re-Registration Reminders have been successfully sent.');
    }

    /**
     * ReRegistration Late Notices Auto Send
     */
    public function createChapterReRegistrationLateReminder(Request $request): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;

        $now = Carbon::now();
        $month = $now->month;
        $lastMonth = $now->copy()->subMonth()->format('m');
        $year = $now->year;
        if ($now->format('m') == '01' && $lastMonth == '12') {
            $year = $now->year - 1;
        }
        $monthInWords = $now->format('F');
        $lastMonthInWords = $now->copy()->subMonth()->format('F');
        $rangeEndDate = $now->copy()->subMonths(2)->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        try {

            $chapters = Chapters::with(['state', 'conference', 'region'])
                ->where('chapters.conference_id', $cdConfId)
                ->where('chapters.start_month_id', $lastMonth)
                ->where('chapters.next_renewal_year', $year)
                ->where('chapters.is_active', 1)
                ->get();

            if ($chapters->isEmpty()) {
                return redirect()->back()->with('info', 'There are no Chapters with Late Registrations Due.');
            }

            $chapterIds = [];
            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapters as $chapter) {
                $chapterIds[] = $chapter->id;

                $chapterName = $chapter->name;
                $stateShortName = $chapter->state->state_short_name;

                if ($chapterName) {
                    $emailData = $this->userController->loadEmailDetails($chapter->id);
                    $emailListChap = $emailData['emailListChap'];
                    $emailListCoord = $emailData['emailListCoord'];

                    $chapterEmails[$chapterName] = $emailListChap;
                    $coordinatorEmails[$chapterName] = $emailListCoord;
                }

                $mailData[$chapterName] = [
                    'chapterName' => $chapterName,
                    'chapterState' => $stateShortName,
                    'startRange' => $rangeStartDateFormatted,
                    'endRange' => $rangeEndDateFormatted,
                    'startMonth' => $lastMonthInWords,
                    'dueMonth' => $monthInWords,
                ];
            }

            foreach ($mailData as $chapterName => $data) {
                $to_email = $chapterEmails[$chapterName] ?? [];
                $cc_email = $coordinatorEmails[$chapterName] ?? [];

                if (! empty($to_email)) {
                    Mail::to($to_email)
                        ->cc($cc_email)
                        ->queue(new PaymentsReRegLate($data));
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        }

        return redirect()->to('/chapter/reregistration')->with('success', 'Re-Registration Late Reminders have been successfully sent.');
    }

    /**
     * View Doantions List
     */
    public function showRptDonations(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $data = ['chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus, 'corId' => $cdId];

        return view('chapters.chapdonations')->with($data);
    }

    /**
     * View the International M2M Doantions
     */
    public function showIntdonation(Request $request): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $chapterList = Chapters::with(['state', 'conference', 'region', 'webLink', 'president', 'avp', 'mvp', 'treasurer', 'secretary', 'startMonth',
            'state', 'primaryCoordinator'])
            ->where('is_active', 1)
            ->orderBy(State::select('state_short_name')
                ->whereColumn('state.id', 'chapters.state_id'), 'asc')
            ->orderBy('chapters.name')
            ->get();

        $data = ['chapterList' => $chapterList];

        return view('international.intdonation')->with($data);
    }

    /**
     *Edit Chapter Information
     */
    public function editChapterPayment(Request $request, $id): View
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $startMonthName = $baseQuery['startMonthName'];
        $chapterStatus = $chDetails->status->chapter_status;
        $chIsActive = $baseQuery['chIsActive'];

        $data = ['id' => $id, 'chIsActive' => $chIsActive, 'stateShortName' => $stateShortName, 'startMonthName' => $startMonthName,
            'chDetails' => $chDetails, 'chapterStatus' => $chapterStatus, 'regionLongName' => $regionLongName, 'conferenceDescription' => $conferenceDescription,
        ];

        return view('chapters.editpayment')->with($data);
    }

    /**
     *Update Chapter Information
     */
    public function updateChapterPayment(Request $request, $id): RedirectResponse
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;
        $userName = $user->first_name.' '.$user->last_name;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $lastUpdatedBy = $cdDetails->first_name.' '.$cdDetails->last_name;

        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $nextRenewalYear = $baseQuery['chDetails']->next_renewal_year;
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];
        $emailPC = $baseQuery['emailPC'];

        $input = $request->all();
        $reg_notes = $input['ch_regnotes'];
        $dues_last_paid = $input['PaymentDate'];
        $members_paid_for = $input['MembersPaidFor'];
        $m2m_date = $input['M2MPaymentDate'];
        $m2m_payment = $input['M2MPayment'];
        $sustaining_date = $input['SustainingPaymentDate'];
        $sustaining_donation = $input['SustainingPayment'];

        $chapter = Chapters::find($id);

        DB::beginTransaction();
        try {
            $chapter->reg_notes = $reg_notes;
            $chapter->save();

            if ($dues_last_paid != null) {
                $chapter->dues_last_paid = $dues_last_paid;
                $chapter->members_paid_for = $members_paid_for;
                $chapter->next_renewal_year = $nextRenewalYear + 1;
                $chapter->last_updated_by = $lastUpdatedBy;
                $chapter->last_updated_date = date('Y-m-d H:i:s');
                $chapter->save();

                if ($request->input('ch_notify') == 'on') {
                    $mailData = [
                        'chapterName' => $chDetails->name,
                        'chapterState' => $stateShortName,
                        'chapterDate' => $dues_last_paid,
                        'chapterMembers' => $members_paid_for,
                    ];

                    // Payment Thank You Email
                    Mail::to($emailListChap)
                        ->cc($emailPC)
                        ->queue(new PaymentsReRegChapterThankYou($mailData));
                }
            }

            if ($m2m_date != null) {
                $chapter->m2m_date = $m2m_date;
                $chapter->m2m_payment = $m2m_payment;
                $chapter->last_updated_by = $lastUpdatedBy;
                $chapter->last_updated_date = date('Y-m-d H:i:s');
                $chapter->save();

                if ($request->input('ch_thanks') == 'on') {
                    $mailData = [
                        'chapterName' => $chDetails->name,
                        'chapterState' => $stateShortName,
                        'chapterAmount' => $m2m_payment,
                    ];

                    //M2M Donation Thank You Email//
                    Mail::to($emailListChap)
                        ->cc($emailPC)
                        ->queue(new PaymentsM2MChapterThankYou($mailData));
                }
            }

            if ($sustaining_date != null) {
                $chapter->sustaining_date = $sustaining_date;
                $chapter->sustaining_donation = $sustaining_donation;
                $chapter->last_updated_by = $lastUpdatedBy;
                $chapter->last_updated_date = date('Y-m-d H:i:s');
                $chapter->save();

                if ($request->input('ch_sustaining') == 'on') {
                    $mailData = [
                        'chapterName' => $chDetails->name,
                        'chapterState' => $stateShortName,
                        'chapterTotal' => $sustaining_donation,
                    ];

                    //Sustaining Chapter Thank You Email//
                    Mail::to($emailListChap)
                        ->cc($emailPC)
                        ->queue(new PaymentsSustainingChapterThankYou($mailData));
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return to_route('chapters.editpayment', ['id' => $id])->with('fail', 'Something went wrong, Please try again..');
        }

        return to_route('chapters.editpayment', ['id' => $id])->with('success', 'Chapter Payments/Donations have been updated');
    }
}
