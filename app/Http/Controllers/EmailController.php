<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBugsAdminRequest;
use App\Http\Requests\AddResourcesAdminRequest;
use App\Http\Requests\AddToolkitAdminRequest;
use App\Http\Requests\UpdateBugsAdminRequest;
use App\Http\Requests\UpdateResourcesAdminRequest;
use App\Http\Requests\UpdateToolkitAdminRequest;
use App\Mail\ChapterSetup;
use App\Mail\ChapterEIN;
use App\Mail\NewChapterWelcome;
use App\Models\Resources;
use App\Models\Chapters;
use App\Models\EmailFields;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use TeamTeaTime\Forum\Models\Category as ForumCategory;

class EmailController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $pdfController;

    protected $baseMailDataController;

    protected $baseChapterController;

    public function __construct(UserController $userController, PDFController $pdfController, BaseMailDataController $baseMailDataController,
        BaseChapterController $baseChapterController)
    {
        $this->userController = $userController;
        $this->pdfController = $pdfController;
        $this->baseMailDataController = $baseMailDataController;
        $this->baseChapterController = $baseChapterController;
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
                $this->baseMailDataController->getFounderData($input),
                $this->baseMailDataController->getCCData($emailCCData),
            );

            Mail::to($founderEmail)
                ->cc($userEmail)
                ->queue(new ChapterSetup($mailData, $pdfPath, $pdfPath2));

            // Commit the transaction
            DB::commit();

            $message = 'Chapter Setup Email successful sent';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('chapters.chaplist'),
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
                'redirect' => route('chapters.chaplist'),
            ]);
        }
    }

    /**
     * Send Chapter EIN Number Notification Email
     */
    public function sendChapterEIN(Request $request, $chapterid): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);

        $baseQuery = $this->baseChapterController->getChapterDetails($chapterid);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];  // Full Board
        $emailListCoord = $baseQuery['emailListCoord']; // Full Coord List

        try {
            DB::beginTransaction();

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
            );

            Mail::to($emailListChap)
                ->cc($emailListCoord)
                ->queue(new ChapterEIN($mailData));

            // Commit the transaction
            DB::commit();

            $message = 'Chapter Setup Email successful sent';

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
            $PresDetails = $baseQuery['PresDetails'];
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


}
