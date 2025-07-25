<?php

namespace App\Http\Controllers;

use App\Mail\NewChapEIN;
use App\Mail\ChapterEmail;
use App\Mail\CoordEmail;
use App\Mail\NewChapterSetup;
use App\Mail\NewChapterWelcome;
use App\Mail\PaymentsReRegLate;
use App\Mail\PaymentsReRegReminder;
use App\Mail\EOYElectionReportReminder;
use App\Mail\EOYFinancialReportReminder;
use App\Mail\EOYLateReportReminder;
use App\Models\EmailFields;
use App\Models\Resources;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $pdfController;

    protected $baseMailDataController;

    protected $baseChapterController;

    protected $baseCoordinatorController;

    public function __construct(UserController $userController, PDFController $pdfController, BaseMailDataController $baseMailDataController,
        BaseChapterController $baseChapterController, BaseCoordinatorController $baseCoordinatorController)
    {
        $this->userController = $userController;
        $this->pdfController = $pdfController;
        $this->baseMailDataController = $baseMailDataController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * Update Email Data and Send Chapter Setup Email
     */
    public function sendChapterStartup(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $userEmail = $user['user_email'];
        $userConfId = $user['user_confId'];

        $emailCCData = $this->userController->loadConferenceCoord($userId);

        $resources = Resources::with('resourceCategory')->get();
        $applicationName = 'EIN Application Conference '.$userConfId;
        $matchingApplication = $resources->where('name', $applicationName)->first();
        $pdfPath = 'https://drive.google.com/uc?export=download&id='.$matchingApplication->file_path;
        $instructionsName = 'EIN Application Instructions';
        $matchingInstructions = $resources->where('name', $instructionsName)->first();
        $pdfPath2 = 'https://drive.google.com/uc?export=download&id='.$matchingInstructions->file_path;

        $input = $request->all();
        $chapterId = $input['chapterId'];
        $boundaryDetails = $input['boundaryDetails'];
        $nameDetails = $input['nameDetails'];

        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $founderEmail = $chDetails->pendingPresident->email;

        try {
            DB::beginTransaction();

            EmailFields::create([
                'boundary_details' => $boundaryDetails,
                'name_details' => $nameDetails,
            ]);

            $mailData = array_merge(
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getMessageData($input),
                $this->baseMailDataController->getCCData($emailCCData),
            );

            Mail::to($founderEmail)
                ->cc($userEmail)
                ->queue(new NewChapterSetup($mailData, $pdfPath, $pdfPath2));

            // Commit the transaction
            DB::commit();

            $message = 'Email successful sent';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.chaplist'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.chaplist'),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function sendChapterStartupOLD(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $userEmail = $user['user_email'];
        $userConfId = $user['user_confId'];

        $emailCCData = $this->userController->loadConferenceCoord($userId);

        $resources = Resources::with('resourceCategory')->get();
        $applicationName = 'EIN Application Conference '.$userConfId;
        $matchingApplication = $resources->where('name', $applicationName)->first();
        $pdfPath = 'https://drive.google.com/uc?export=download&id='.$matchingApplication->file_path;
        $instructionsName = 'EIN Application Instructions';
        $matchingInstructions = $resources->where('name', $instructionsName)->first();
        $pdfPath2 = 'https://drive.google.com/uc?export=download&id='.$matchingInstructions->file_path;

        $input = $request->all();
        $founderEmail = $input['founderEmail'];
        $founderFirstName = $input['founderFirstName'];
        $founderLastName = $input['founderLastName'];
        $boundaryDetails = $input['boundaryDetails'];
        $nameDetails = $input['nameDetails'];

        try {
            DB::beginTransaction();

            EmailFields::create([
                'to_email' => $founderEmail,
                'founder_first_name' => $founderFirstName,
                'founder_last_name' => $founderLastName,
                'boundary_details' => $boundaryDetails,
                'name_details' => $nameDetails,
            ]);

            $mailData = array_merge(
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getMessageData($input),
                $this->baseMailDataController->getCCData($emailCCData),
            );

            Mail::to($founderEmail)
                ->cc($userEmail)
                ->queue(new NewChapterSetup($mailData, $pdfPath, $pdfPath2));

            // Commit the transaction
            DB::commit();

            $message = 'Email successful sent';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.chaplist'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.chaplist'),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Send Chapter EIN Number Notification Email
     */
    public function sendChapterEIN(Request $request, $chapterid): JsonResponse
{
    $user = $this->userController->loadUserInformation($request);

    try {
        $baseQuery = $this->baseChapterController->getChapterDetails($chapterid);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];  // Full Board
        $emailListCoord = $baseQuery['emailListCoord']; // Full Coord List

        // Log::info('Email lists retrieved', [
        //     'chapterid' => $chapterid,
        //     'emailListChap' => $emailListChap,
        //     'emailListCoord' => $emailListCoord
        // ]);

        // Check if we have valid email addresses
        if (empty($emailListChap)) {
            Log::warning('No chapter email addresses found', ['chapterid' => $chapterid]);
            return response()->json([
                'status' => 'error',
                'message' => 'No chapter email addresses found',
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);
        }

        $mailData = array_merge(
            $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            $this->baseMailDataController->getUserData($user),
        );

        // Log::info('Attempting to send email', [
        //     'chapterid' => $chapterid,
        //     'mailData' => array_keys($mailData) // Just log the keys, not sensitive data
        // ]);

        // Try sending the email
        Mail::to($emailListChap)
            ->cc($emailListCoord)
            ->queue(new NewChapEIN($mailData));

        // Log::info('Email queued successfully', ['chapterid' => $chapterid]);

        $message = 'Email successfully sent';

        // Return JSON response
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'redirect' => route('chapters.view', ['id' => $chapterid]),
        ]);

    } catch (\Exception $e) {
        Log::error('SendChapterEIN error', [
            'chapterid' => $chapterid,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        $message = 'Something went wrong sending the email: ' . $e->getMessage();

        // Return JSON error response
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'redirect' => route('chapters.view', ['id' => $chapterid]),
        ]);
    }
}
    /**
     * Function for sending New Chapter Email with Attachments
     */
    public function sendNewChapterEmail(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);

        $input = $request->all();
        $chapterid = $input['chapterid'];

        try {
            DB::beginTransaction();

            // Load Chapter MailData//
            $baseQuery = $this->baseChapterController->getChapterDetails($chapterid);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $pcDetails = $baseQuery['pcDetails'];

            $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($chapterid);
        $PresDetails = $baseActiveBoardQuery['PresDetails'];

            // $PresDetails = $baseQuery['PresDetails'];
            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getPresData($PresDetails),
                $this->baseMailDataController->getPCData($pcDetails),
            );

            $pdfPath2 = 'https://drive.google.com/uc?export=download&id=1A3Z-LZAgLm_2dH5MEQnBSzNZEhKs5FZ3';
            $pdfPath = $this->pdfController->saveGoodStandingLetter($chapterid);   // Generate and save the PDF
            Mail::to($emailListChap)
                ->cc($emailListCoord)
                ->queue(new NewChapterWelcome($mailData, $pdfPath, $pdfPath2));

            // Commit the transaction
            DB::commit();

            $message = 'Email successfully sent';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterid]),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Send Chapter EIN Number Notification Email
     */
    public function sendChapterEmail(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);

        $input = $request->all();
        $emailSubject = $input['subject'];
        $emailMessage = $input['message'];
        $chapterId = $input['chapterId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];  // Full Board
        $emailListCoord = $baseQuery['emailListCoord']; // Full Coord List

        try {
            DB::beginTransaction();

            // EmailFields::create([
            //     'message' => $message,
            //     'chapter_id' => $chapterId,
            // ]);

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getMessageData($input),
            );

            Mail::to($emailListChap)
                ->cc($emailListCoord)
                ->queue(new ChapterEmail($mailData));

            // Commit the transaction
            DB::commit();

            $message = 'Email successful sent';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterId]),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterId]),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function sendCoordEmail(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);

        $input = $request->all();
        $emailSubject = $input['subject'];
        $emailMessage = $input['message'];
        $coordId = $input['coordId'];

        $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($coordId);
        $cdDetails = $baseQuery['cdDetails'];
        $cdList = $this->userController->loadCoordEmailDetails($coordId);
        $toCoordEmail = $cdList['toCoordEmail'];
        $ccCoordEmailList = $cdList['ccCoordEmailList'];

        try {
            DB::beginTransaction();

            $mailData = array_merge(
                $this->baseMailDataController->getNewCoordinatorData($cdDetails),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getMessageData($input),
            );

            Mail::to($toCoordEmail)
                ->cc($ccCoordEmailList)
                ->queue(new CoordEmail($mailData));

            // Commit the transaction
            DB::commit();

            $message = 'Email successful sent';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('coordinators.view', ['id' => $coordId]),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('coordinators.view', ['id' => $coordId]),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function sendCoordUplineEmail(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userCoordId = $user['user_coorId'];

        $input = $request->all();
        $emailSubject = $input['subject'];
        $emailMessage = $input['message'];
        $userCoordId = $input['userCoordId'];

        $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($userCoordId);
        $cdDetails = $baseQuery['cdDetails'];
        $cdList = $this->userController->loadCoordEmailDownlineDetails($userCoordId);
        $emailListCoord = $cdList['emailListCoord'];

        try {
            DB::beginTransaction();

            $mailData = array_merge(
                // $this->baseMailDataController->getNewCoordinatorData($cdDetails),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getMessageData($input),
                [
                    'first_name' => ' ',
                    'last_name' => ' ',
                ]
            );

            Mail::to($emailListCoord)
                ->queue(new CoordEmail($mailData));

            // Commit the transaction
            DB::commit();

            $message = 'Email successful sent';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('coordinators.view', ['id' => $userCoordId]),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('coordinators.view', ['id' => $userCoordId]),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }


    /**
     * Send Chapter Re-Registration Reminder
     */
    public function sendChapterReReg(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);

        $input = $request->all();
        $chapterId = $input['chapterId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthId = $chDetails->start_month_id;
        $emailListChap = $baseQuery['emailListChap'];  // Full Board
        $emailListCoord = $baseQuery['emailListCoord']; // Full Coord List

        try {
            DB::beginTransaction();

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getReRegData($startMonthId),
            );

            Mail::to($emailListChap)
                ->cc($emailListCoord)
                ->queue(new PaymentsReRegReminder($mailData));

            // Commit the transaction
            DB::commit();

            $message = 'Email successful sent';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterId]),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterId]),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Send Chapter Re-Registration Late Notice
     */
    public function sendChapterReRegLate(Request $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);

        $input = $request->all();
        $chapterId = $input['chapterId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthId = $chDetails->start_month_id;
        $emailListChap = $baseQuery['emailListChap'];  // Full Board
        $emailListCoord = $baseQuery['emailListCoord']; // Full Coord List

        try {
            DB::beginTransaction();

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
                $this->baseMailDataController->getReRegData($startMonthId),
            );

            Mail::to($emailListChap)
                ->cc($emailListCoord)
                ->queue(new PaymentsReRegLate($mailData));

            // Commit the transaction
            DB::commit();

            $message = 'Email successful sent';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterId]),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('chapters.view', ['id' => $chapterId]),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
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
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport, $reviewer_email_message=null)
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

            return redirect()->to('/eoy/boardreport')->with('success', 'Board Election Reminders have been successfully sent.');
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
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport, $reviewer_email_message=null)
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

            return redirect()->to('/eoy/financialreport')->with('success', 'Financial Report Reminders have been successfully sent.');
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
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport, $reviewer_email_message=null)
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

            return redirect()->to('/eoy/status')->with('success', 'EOY Late Notices have been successfully sent.');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong, Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }


}
