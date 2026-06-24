<?php

namespace App\Http\Controllers;

use App\Mail\CampaignsOldBoardThankYou;
use App\Mail\NewBoardWelcome;
use App\Models\Resources;
use App\Services\PositionConditionsService;
use Illuminate\Support\Facades\Mail;

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
            ->queue(new NewBoardWelcome($mailData, $pdfPath));
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
        $boardReportRange = $reportYearOptions['boardReportRange'];

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
            ->queue(new CampaignsOldBoardThankYou($mailData));
    }

    public function sendElectionTimeline(int $chId): void
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
            ->queue(new CampaignsElectionTimeline($mailData, $pdfPath));
    }

    public function sendAnnualReportInfo(int $chId): void
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
            ->queue(new CampaignsAnnualReportInfo($mailData));
    }

    public function sendVolunteerPush(int $chId): void
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
            ->queue(new CampaignsVolunteerPush($mailData));
    }
}
