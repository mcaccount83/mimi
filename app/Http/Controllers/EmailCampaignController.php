<?php

namespace App\Http\Controllers;

use App\Mail\CampaignsAnnualReport;
use App\Mail\CampaignsElectionsTimeline;
use App\Mail\CampaignsOldBoardThankYou;
use App\Mail\CampaignsVolunteerPush;
use App\Mail\CampaignsBudgetMeeting;
use App\Mail\CampaignsCodeOfConduct;
use App\Mail\CampaignsRecordsRetention;
use App\Mail\CampaignsServiceProjects;
use App\Mail\CampaignsMemberBenefits;
use App\Mail\CampaignsHolidayBreak;
use App\Mail\CampaignsProcessingReimbursements;
use App\Mail\CampaignsSummary;
use App\Mail\CampaignsBoardReport;
use App\Mail\CampaignsFinancialReport;
use App\Mail\CampaignsNewBoardWelcome;
use App\Models\Chapters;
use App\Models\EmailCampaign;
use App\Models\Resources;
use App\Services\PositionConditionsService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailCampaignController extends Controller
{
    public function __construct(
        protected UserController $userController,
        protected BaseChapterController $baseChapterController,
        protected BaseMailDataController $baseMailDataController,
        protected PositionConditionsService $positionConditionsService,
        ) {}

    /**
     * After adding a Campaign to the Database, the following still needs to be done manually --
     *  -  Create NEW Function HERE for sending
     *  -  Create NEW Route in web.php for the route
     *  -  Create NEW Mailable for message content
     *  -  Add Preview slug to buildCampaignMailable function HERE to enable preview
     *  -  Pull PDF attachment if needed
     *  -  Add Custom entries if needed
     */

    private function getResourcePdfPath(string $resourceName): ?string
    {
        $resource = Resources::with('resourceCategory')->get()->where('name', $resourceName)->first();
        return $resource ? 'https://drive.google.com/uc?export=download&id=' . $resource->file_path : null;
    }

    public function previewCampaign(Request $request, string $campaignKey): Response
    {
        $user = $this->userController->loadUserInformation($request);
        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();

        $baseQuery = $this->baseChapterController->getBaseQuery(
            1, $user['cdId'], $user['confId'], $user['regId'], $user['cdPositionId'], $user['cdSecPositionId']
        );
        $sampleChapter = $baseQuery['query']->first();

        if (! $sampleChapter) {
            abort(404, 'No sample chapter available to preview.');
        }

        $mailable = $this->buildCampaignMailable($campaignKey, $sampleChapter, $user, $reportYearOptions, $request);

        if (! $mailable) {
            abort(404, 'Unknown campaign.');
        }

        return response($mailable->render())->header('Content-Type', 'text/html');
    }

    private function sendCampaignSummary(array $user, array $coordinatorSummary, string $campaignLabel, ?string $pdfPath = null): void
    {
        foreach ($coordinatorSummary as $coordEmail => $summary) {
            $summaryData = array_merge(
                $this->baseMailDataController->getUserData($user),
                [
                    'campaignLabel' => $campaignLabel,
                    'chapterNames' => $summary['chapterNames'],
                    'campaignMessage' => $summary['campaignMessage'],
                ]
            );
            Mail::to($coordEmail)
                ->queue(new CampaignsSummary($summaryData, $pdfPath));
        }
    }

    private function buildCampaignMailable(string $campaignKey, Chapters $chapter, array $user, array $reportYearOptions, Request $request): ?\Illuminate\Mail\Mailable
    {
        $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
        $chDetails = $emailDetails['chDetails'];
        $stateShortName = $emailDetails['stateShortName'];

        $baseData = array_merge(
            $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            $this->baseMailDataController->getUserData($user),
        );

        // A few campaigns also merge in report year data
        $withReportYear = array_merge($baseData, $this->baseMailDataController->getReportYearData($reportYearOptions));

        return match ($campaignKey) {
            'elections-timeline'         => new CampaignsElectionsTimeline($withReportYear, $this->getResourcePdfPath('Election Timetable')),
            'annual-report'              => new CampaignsAnnualReport($withReportYear),
            'budget-meeting'             => new CampaignsBudgetMeeting($baseData),
            'code-of-conduct'            => new CampaignsCodeOfConduct($baseData),
            'records-retention'          => new CampaignsRecordsRetention($baseData),
            'holiday-break'              => new CampaignsHolidayBreak(array_merge($baseData, [
                                                'fallBreak'   => $request->input('fallBreak', '[Fall Break dates]'),
                                                'winterBreak' => $request->input('winterBreak', '[Winter Break dates]'),
                                            ])),
            'processing-reimbursements'  => new CampaignsProcessingReimbursements($baseData),
            'volunteer-push'             => new CampaignsVolunteerPush($baseData),
            'service-projects'           => new CampaignsServiceProjects($baseData),
            'member-benefits'            => new CampaignsMemberBenefits($baseData, $this->getResourcePdfPath('Party Expenses & 15% Rule')),
            'board-report'               => new CampaignsBoardReport($withReportYear),
            'financial-report'           => new CampaignsFinancialReport($withReportYear),
            default => null,
        };
    }

    private function markCampaignSent(): void
    {
        $routeName = \Illuminate\Support\Facades\Route::currentRouteName();

        if (! $routeName) {
            return;
        }

        EmailCampaign::where('route_name', $routeName)->update([
            'sent' => true,
            'sent_date' => now(),
        ]);
    }

    private function sendCampaignToChapters(
        array $user, int $coorId, int $confId, int $regId, int $positionId, array $secPositionId,
        string $viewPartial,
        \Closure $buildMailData,   // fn(array $baseData) => array  (lets caller merge extra fields, e.g. Holiday Break dates)
        \Closure $buildMailable,   // fn(array $data) => Mailable
    ): array
    {
        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();

        $chapterEmails = [];
        $coordinatorSummary = [];
        $mailData = [];

        foreach ($chapterList as $chapter) {
            $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
            $chDetails = $emailDetails['chDetails'];
            $stateShortName = $emailDetails['stateShortName'];
            $emailListChap = $emailDetails['emailListChap'];
            $emailListCoord = $emailDetails['emailListCoord'];

            $baseData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
            );

            $mailData[$chDetails->name] = $buildMailData($baseData);

            $campaignMessage = \Illuminate\Support\Facades\View::make(
                $viewPartial,
                ['mailData' => $mailData[$chDetails->name]]
            )->render();

            $chapterEmails[$chDetails->name] = $emailListChap;

            foreach ($emailListCoord as $coordEmail) {
                if (!isset($coordinatorSummary[$coordEmail])) {
                    $coordinatorSummary[$coordEmail] = [
                        'chapterNames' => [],
                        'campaignMessage' => $campaignMessage,
                    ];
                }
                $coordinatorSummary[$coordEmail]['chapterNames'][] = [
                    'name' => $chDetails->name,
                    'state' => $stateShortName,
                ];
            }
        }

        $delay = 0;
        foreach ($mailData as $chapterName => $data) {
            if (! empty($chapterName)) {
                Mail::to($chapterEmails[$chapterName] ?? [])
                    ->later(now()->addSeconds($delay), $buildMailable($data));
                $delay += 15;
            }
        }

        return $coordinatorSummary;
    }

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
        $pdfPath = $this->getResourcePdfPath('Officer Packet');

        // $resources = Resources::with('resourceCategory')->get();
        // $instructionsName = 'Officer Packet';
        // $matchingInstructions = $resources->where('name', $instructionsName)->first();
        // $pdfPath = $matchingInstructions ? 'https://drive.google.com/uc?export=download&id='.$matchingInstructions->file_path : null;

        $mailData = array_merge(
            $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            $this->baseMailDataController->getReportYearData($reportYearOptions),
            $this->baseMailDataController->getPCData($pcDetails),
            $this->baseMailDataController->getCCData($emailCCData),
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

        $mailData = array_merge(
            $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            $this->baseMailDataController->getReportYearData($reportYearOptions),
            $this->baseMailDataController->getPCData($pcDetails),
            $this->baseMailDataController->getCCData($emailCCData),
        );

        Mail::to($emailListChap)
            ->cc($emailListCoord)
            ->queue(new CampaignsOldBoardThankYou($mailData));
    }

    public function sendElectionsTimelineCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];
            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $pdfPath = $this->getResourcePdfPath('Election Timetable');

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.electionstimeline_body',
                fn(array $baseData) => array_merge($baseData, $this->baseMailDataController->getReportYearData($reportYearOptions)),
                fn(array $data) => new CampaignsElectionsTimeline($data, $pdfPath),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Election Timeline', $pdfPath);
            $this->markCampaignSent();

            return response()->json(['message' => 'Election Timeline emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function sendAnnualReportCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];
            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.annualreport_body',
                fn(array $baseData) => array_merge($baseData, $this->baseMailDataController->getReportYearData($reportYearOptions)),
                fn(array $data) => new CampaignsAnnualReport($data),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'EOY Reports');
            $this->markCampaignSent();

            return response()->json(['message' => 'EOY Reports emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function sendBudgetMeetingCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.budgetmeeting_body',
                fn(array $baseData) => array_merge($baseData),
                fn(array $data) => new CampaignsBudgetMeeting($data),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Budget & Meeting');
            $this->markCampaignSent();

            return response()->json(['message' => 'Budget & Meeting emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function sendCodeOfConductCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.codeofconduct_body',
                fn(array $baseData) => array_merge($baseData),
                fn(array $data) => new CampaignsCodeOfConduct($data),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Code of Conduct');
            $this->markCampaignSent();

            return response()->json(['message' => 'Code of Conduct emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function sendRecordsRetentionCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.recordsretention_body',
                fn(array $baseData) => array_merge($baseData),
                fn(array $data) => new CampaignsRecordsRetention($data),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Records Retention');
            $this->markCampaignSent();

            return response()->json(['message' => 'Records Retention emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function sendHolidayBreakCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];
            $fallBreak = $request->input('fallBreak');
            $winterBreak = $request->input('winterBreak');

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.holidaybreak_body',
                fn(array $baseData) => array_merge($baseData, ['fallBreak' => $fallBreak, 'winterBreak' => $winterBreak]),
                fn(array $data) => new CampaignsHolidayBreak($data),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Holiday Break');
            $this->markCampaignSent();

            return response()->json(['message' => 'Holiday Break emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function sendProcessingReimbursementsCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.processingreimbursements_body',
                fn(array $baseData) => array_merge($baseData),
                fn(array $data) => new CampaignsProcessingReimbursements($data),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Processing Reimbursements');
            $this->markCampaignSent();

            return response()->json(['message' => 'Processing Reimbursements emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function sendVolunteerPushCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.volunteerpush_body',
                fn(array $baseData) => array_merge($baseData),
                fn(array $data) => new CampaignsVolunteerPush($data),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Volunteer Push');
            $this->markCampaignSent();

            return response()->json(['message' => 'Volunteer Push emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function sendServiceProjectsCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.serviceprojects_body',
                fn(array $baseData) => array_merge($baseData),
                fn(array $data) => new CampaignsServiceProjects($data),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Service Projects');
            $this->markCampaignSent();

            return response()->json(['message' => 'Service Projects emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function sendMemberBenefitsCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];
            $pdfPath = $this->getResourcePdfPath('Party Expenses & 15% Rule');

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.memberbenefits_body',
                fn(array $baseData) => array_merge($baseData),
                fn(array $data) => new CampaignsMemberBenefits($data, $pdfPath),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Member Benefits', $pdfPath);
            $this->markCampaignSent();

            return response()->json(['message' => 'Member Benefits emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function sendBoardReportCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];
            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.boardreport_body',
                fn(array $baseData) => array_merge($baseData, $this->baseMailDataController->getReportYearData($reportYearOptions)),
                fn(array $data) => new CampaignsBoardReport($data),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Board Report');
            $this->markCampaignSent();

            return response()->json(['message' => 'Board Report Reminder emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function sendFinancialReportCampaign(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $confId = $user['confId'];
            $regId = $user['regId'];
            $positionId = $user['cdPositionId'];
            $secPositionId = $user['cdSecPositionId'];
            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();

            $coordinatorSummary = $this->sendCampaignToChapters(
                $user, $coorId, $confId, $regId, $positionId, $secPositionId,
                'emails.campaigns.partials.financialreport_body',
                fn(array $baseData) => array_merge($baseData, $this->baseMailDataController->getReportYearData($reportYearOptions)),
                fn(array $data) => new CampaignsFinancialReport($data),
            );

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Financial Report');
            $this->markCampaignSent();

            return response()->json(['message' => 'Financial Report emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
    }
}
