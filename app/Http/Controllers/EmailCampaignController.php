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
use App\Models\Resources;
use App\Services\PositionConditionsService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
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

        $resources = Resources::with('resourceCategory')->get();
        $instructionsName = 'Officer Packet';
        $matchingInstructions = $resources->where('name', $instructionsName)->first();
        $pdfPath = $matchingInstructions ? 'https://drive.google.com/uc?export=download&id='.$matchingInstructions->file_path : null;

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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $resources = Resources::with('resourceCategory')->get();
            $instructionsName = 'Election Timetable';
            $matchingInstructions = $resources->where('name', $instructionsName)->first();
            $pdfPath = $matchingInstructions ? 'https://drive.google.com/uc?export=download&id='.$matchingInstructions->file_path : null;

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getReportYearData($reportYearOptions),
                    $this->baseMailDataController->getUserData($user),
                );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.electionstimeline_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsElectionsTimeline($data, $pdfPath));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Election Timeline', $pdfPath);

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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getReportYearData($reportYearOptions),
                    $this->baseMailDataController->getUserData($user),
                );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.annualreport_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsAnnualReport($data));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'EOY Reports');

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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getUserData($user),
                );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.budgetmeeting_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsBudgetMeeting($data));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Budget & Meeting');

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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getUserData($user),
                );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.codeofconduct_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsCodeOfConduct($data));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Code of Conduct');

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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getUserData($user),
                  );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.recordsretention_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsRecordsRetention($data));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Records Retention');

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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getUserData($user),
                    [
                        'fallBreak' => $fallBreak,
                        'winterBreak' => $winterBreak,
                    ]
                );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.holidaybreak_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsHolidayBreak($data));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Holiday Break');

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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getUserData($user),
                );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.processingreimbursements_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsProcessingReimbursements($data));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Processing Reimbursements');

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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getUserData($user),
                );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.volunteerpush_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsVolunteerPush($data));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Volunteer Push');

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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getUserData($user),
                );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.serviceprojects_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsServiceProjects($data));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Service Projects');

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

            $resources = Resources::with('resourceCategory')->get();
            $instructionsName = 'Party Expenses & 15% Rule';
            $matchingInstructions = $resources->where('name', $instructionsName)->first();
            $pdfPath = $matchingInstructions ? 'https://drive.google.com/uc?export=download&id='.$matchingInstructions->file_path : null;

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getUserData($user),
                );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.memberbenefits_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsMemberBenefits($data, $pdfPath));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Member Benefits');

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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getReportYearData($reportYearOptions),
                    $this->baseMailDataController->getUserData($user),
                );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.boardreport_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsBoardReport($data));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Board Report');

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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorSummary = [];
            $mailData = [];

            foreach ($chapterList as $chapter) {
                $emailDetails = $this->baseChapterController->getChapterDetails($chapter->id);
                $chDetails = $emailDetails['chDetails'];
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getReportYearData($reportYearOptions),
                    $this->baseMailDataController->getUserData($user),
                );

                $campaignMessage = \Illuminate\Support\Facades\View::make(
                    'emails.campaigns.partials.financialreport_body',
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

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->queue(new CampaignsFinancialReport($data));
                }
            }

            $this->sendCampaignSummary($user, $coordinatorSummary, 'Financial Report');

            return response()->json(['message' => 'Financial Report emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
        }
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
}
