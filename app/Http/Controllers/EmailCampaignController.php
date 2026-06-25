<?php

namespace App\Http\Controllers;

use App\Mail\CampaignsAnnualReport;
use App\Mail\CampaignsElectionTimeline;
use App\Mail\CampaignsOldBoardThankYou;
use App\Mail\CampaignsVolunteerPush;
use App\Mail\CampaignsBudgetMeeting;
use App\Mail\CampaignsCodeOfConduct;
use App\Mail\CampaignsRecordsRetention;
use App\Mail\CampaignsHappyHolidays;
use App\Mail\CampaignsProcessingReimbursements;
use App\Mail\CampaignsBoardReport;
use App\Mail\CampaignsFinancialReport;
use App\Mail\CampaignsNewBoardWelcome;
use App\Models\Resources;
use App\Services\PositionConditionsService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class EmailCampaignController extends Controller
{
    public function __construct(
        protected UserController $userController,
        protected BaseChapterController $baseChapterController,
        protected BaseMailDataController $baseMailDataController,
        protected PositionConditionsService $positionConditionsService,
        ) {}

    public function sendNewBoardWelcome(int $chId): void
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $pcDetails = $baseQuery['pcDetails'];
        $chPcId = $chDetails->primary_coordinator_id;
        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];  // Full Board
        $emailListCoord = $baseQuery['emailListCoord']; // Full Coord List
        $emailCCData = $this->userController->loadConferenceCoord($chPcId);

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $boardReportRange = $reportYearOptions['boardReportRange'];

        $resources = Resources::with('resourceCategory')->get();
        $instructionsName = 'Officer Packet';
        $matchingInstructions = $resources->where('name', $instructionsName)->first();
        $pdfPath = $matchingInstructions ? 'https://drive.google.com/uc?export=download&id='.$matchingInstructions->file_path : null;

        $mailData = array_merge(
            $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            $this->baseMailDataController->getPCData($pcDetails),
            $this->baseMailDataController->getCCData($emailCCData),
            [
                'boardReportRange' => $boardReportRange,
            ]
        );

        Mail::to($emailListChap)
            ->cc($emailListCoord)
            ->queue(new CampaignsNewBoardWelcome($mailData, $pdfPath));
    }

    public function sendOldBoardThankYou(int $chId): void
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $pcDetails = $baseQuery['pcDetails'];
        $chPcId = $chDetails->primary_coordinator_id;
        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];  // Full Board
        $emailListCoord = $baseQuery['emailListCoord']; // Full Coord List
        $emailCCData = $this->userController->loadConferenceCoord($chPcId);

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearRange = $reportYearOptions['reportYearRange'];

        $mailData = array_merge(
            $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            $this->baseMailDataController->getPCData($pcDetails),
            $this->baseMailDataController->getCCData($emailCCData),
            [
                'reportYearRange' => $reportYearRange,
            ]
        );

        Mail::to($emailListChap)
            ->cc($emailListCoord)
            ->queue(new CampaignsOldBoardThankYou($mailData));
    }

    public function sendElectionsTimelineCampaign(Request $request): RedirectResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $boardReportRange = $reportYearOptions['boardReportRange'];
            $reportYearStart = $reportYearOptions['reportYearStart'];
            $boardReportEnd = $reportYearOptions['boardReportEnd'];

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $resources = Resources::with('resourceCategory')->get();
            $instructionsName = 'Election Timetable';
            $matchingInstructions = $resources->where('name', $instructionsName)->first();
            $pdfPath = $matchingInstructions ? 'https://drive.google.com/uc?export=download&id='.$matchingInstructions->file_path : null;

            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                // $pcDetails = $emailDetails['pcDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                // $chPcId = $chDetails->primary_coordinator_id;
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];
                // $emailCCData = $this->userController->loadConferenceCoord($chPcId);

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    // $this->baseMailDataController->getPCData($pcDetails),
                    $this->baseMailDataController->getUserData($user),
                    // $this->baseMailDataController->getCCData($emailCCData),
                    [
                        'boardReportRange' => $boardReportRange,
                        'reportYearStart' => $reportYearStart,
                        'boardReportEnd' => $boardReportEnd,
                    ]
                );

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsElectionTimeline($data, $pdfPath));
                }
            }

            return redirect()->back()->with('success', 'Election Timeline emails have been queued.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'Something went wrong. Please try again.');
        }
    }

    public function sendAnnualReportCampaign(Request $request): RedirectResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $boardReportRange = $reportYearOptions['boardReportRange'];

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $pcDetails = $emailDetails['pcDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chPcId = $chDetails->primary_coordinator_id;
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];
                $emailCCData = $this->userController->loadConferenceCoord($chPcId);

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getPCData($pcDetails),
                    $this->baseMailDataController->getCCData($emailCCData),
                );

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsAnnualReport($data));
                }
            }

            return redirect()->back()->with('success', 'Annual Report emails have been queued.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'Something went wrong. Please try again.');
        }
    }

    public function sendBudgetMeetingCampaign(Request $request): RedirectResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $boardReportRange = $reportYearOptions['boardReportRange'];

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $pcDetails = $emailDetails['pcDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chPcId = $chDetails->primary_coordinator_id;
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];
                $emailCCData = $this->userController->loadConferenceCoord($chPcId);

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getPCData($pcDetails),
                    $this->baseMailDataController->getCCData($emailCCData),
                );

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsBudgetMeeting($data));
                }
            }

            return redirect()->back()->with('success', 'Budget & Meeting emails have been queued.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'Something went wrong. Please try again.');
        }
    }

    public function sendCodeOfConductCampaign(Request $request): RedirectResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $boardReportRange = $reportYearOptions['boardReportRange'];

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $pcDetails = $emailDetails['pcDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chPcId = $chDetails->primary_coordinator_id;
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];
                $emailCCData = $this->userController->loadConferenceCoord($chPcId);

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getPCData($pcDetails),
                    $this->baseMailDataController->getCCData($emailCCData),
                );

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsCodeOfConduct($data));
                }
            }

            return redirect()->back()->with('success', 'Code of Conduct emails have been queued.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'Something went wrong. Please try again.');
        }
    }

    public function sendRecordsRetentionCampaign(Request $request): RedirectResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $boardReportRange = $reportYearOptions['boardReportRange'];

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $pcDetails = $emailDetails['pcDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chPcId = $chDetails->primary_coordinator_id;
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];
                $emailCCData = $this->userController->loadConferenceCoord($chPcId);

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getPCData($pcDetails),
                    $this->baseMailDataController->getCCData($emailCCData),
                );

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsRecordsRetention($data));
                }
            }

            return redirect()->back()->with('success', 'Records Retention emails have been queued.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'Something went wrong. Please try again.');
        }
    }

    public function sendHappyHolidaysCampaign(Request $request): RedirectResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $boardReportRange = $reportYearOptions['boardReportRange'];

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $pcDetails = $emailDetails['pcDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chPcId = $chDetails->primary_coordinator_id;
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];
                $emailCCData = $this->userController->loadConferenceCoord($chPcId);

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getPCData($pcDetails),
                    $this->baseMailDataController->getCCData($emailCCData),
                );

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsHappyHolidays($data));
                }
            }

            return redirect()->back()->with('success', 'Happy Holidays emails have been queued.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'Something went wrong. Please try again.');
        }
    }

    public function sendProcessingReimbursementsCampaign(Request $request): RedirectResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $boardReportRange = $reportYearOptions['boardReportRange'];

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $pcDetails = $emailDetails['pcDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chPcId = $chDetails->primary_coordinator_id;
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];
                $emailCCData = $this->userController->loadConferenceCoord($chPcId);

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getPCData($pcDetails),
                    $this->baseMailDataController->getCCData($emailCCData),
                );

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsProcessingReimbursements($data));
                }
            }

            return redirect()->back()->with('success', 'Processing Reimbursement emails have been queued.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'Something went wrong. Please try again.');
        }
    }

    public function sendVolunteerPushCampaign(Request $request): RedirectResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $boardReportRange = $reportYearOptions['boardReportRange'];

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $pcDetails = $emailDetails['pcDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chPcId = $chDetails->primary_coordinator_id;
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];
                $emailCCData = $this->userController->loadConferenceCoord($chPcId);

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getPCData($pcDetails),
                    $this->baseMailDataController->getCCData($emailCCData),
                );

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsVolunteerPush($data));
                }
            }

            return redirect()->back()->with('success', 'Volunteer Push emails have been queued.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'Something went wrong. Please try again.');
        }
    }

    public function sendBoardReportCampaign(Request $request): RedirectResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $boardReportRange = $reportYearOptions['boardReportRange'];

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $pcDetails = $emailDetails['pcDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chPcId = $chDetails->primary_coordinator_id;
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];
                $emailCCData = $this->userController->loadConferenceCoord($chPcId);

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getPCData($pcDetails),
                    $this->baseMailDataController->getCCData($emailCCData),
                );

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsBoardReport($data));
                }
            }

            return redirect()->back()->with('success', 'Board Report Reminder emails have been queued.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'Something went wrong. Please try again.');
        }
    }


    public function sendFinancialReportCampaign(Request $request): RedirectResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $boardReportRange = $reportYearOptions['boardReportRange'];

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $pcDetails = $emailDetails['pcDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $chPcId = $chDetails->primary_coordinator_id;
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];
                $emailCCData = $this->userController->loadConferenceCoord($chPcId);

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getPCData($pcDetails),
                    $this->baseMailDataController->getCCData($emailCCData),
                );

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsFinancialReport($data));
                }
            }

            return redirect()->back()->with('success', 'Financial Report Reminder emails have been queued.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'Something went wrong. Please try again.');
        }
    }
}
