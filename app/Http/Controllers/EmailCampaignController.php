<?php

namespace App\Http\Controllers;

use App\Mail\CampaignsAnnualReport;
use App\Mail\CampaignsElectionTimeline;
use App\Mail\CampaignsOldBoardThankYou;
use App\Mail\CampaignsVolunteerPush;
use App\Mail\CampaignsBudgetMeeting;
use App\Mail\CampaignsCodeOfConduct;
use App\Mail\CampaignsRecordsRetention;
use App\Mail\CampaignsServiceProjects;
use App\Mail\CampaignsMemberBenefits;
use App\Mail\CampaignsHolidayBreak;
use App\Mail\CampaignsProcessingReimbursements;
use App\Mail\CampaignsBoardReport;
use App\Mail\CampaignsFinancialReport;
use App\Mail\CampaignsNewBoardWelcome;
use App\Models\Resources;
use App\Services\PositionConditionsService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
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
                $stateShortName = $emailDetails['stateShortName'];
                $emailListChap = $emailDetails['emailListChap'];
                $emailListCoord = $emailDetails['emailListCoord'];

                $mailData[$chDetails->name] = array_merge(
                    $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                    $this->baseMailDataController->getReportYearData($reportYearOptions),
                    $this->baseMailDataController->getUserData($user),
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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
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
            $coordinatorEmails = [];
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

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsHolidayBreak($data));
                }
            }

            return response()->json(['message' => 'Happy Holidays emails have been queued.']);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'Something went wrong. Please try again.'], 500);
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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
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

    public function sendServiceProjectsCampaign(Request $request): RedirectResponse
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
            $coordinatorEmails = [];
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

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsServiceProjects($data));
                }
            }

            return redirect()->back()->with('success', 'Service Projects emails have been queued.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'Something went wrong. Please try again.');
        }
    }

    public function sendMemberBenefitsCampaign(Request $request): RedirectResponse
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
            $coordinatorEmails = [];
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

                $chapterEmails[$chDetails->name] = $emailListChap;
                $coordinatorEmails[$chDetails->name] = $emailListCoord;
            }

            foreach ($mailData as $chapterName => $data) {
                if (! empty($chapterName)) {
                    Mail::to($chapterEmails[$chapterName] ?? [])
                        ->cc($coordinatorEmails[$chapterName] ?? [])
                        ->queue(new CampaignsMemberBenefits($data));
                }
            }

            return redirect()->back()->with('success', 'Member Benefits emails have been queued.');
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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
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

            $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
            $chapterList = $baseQuery['query']
                ->get();

            $chapterEmails = [];
            $coordinatorEmails = [];
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
