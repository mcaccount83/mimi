<?php

namespace App\Http\Controllers;

use App\Mail\NameChangeEINNotice;
use App\Mail\NewChapterFaxCover;
use App\Mail\DisbandChapterLetter;
use App\Mail\ProbationChapNoPmtLetter;
use App\Mail\ProbationChapNoRptLetter;
use App\Mail\ProbationChapPartyLetter;
use App\Mail\ProbationChapReleaseLetter;
use App\Mail\ProbationChapWarningPartyLetter;
use App\Models\Chapters;
use App\Models\Documents;
use App\Models\GoogleDrive;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

class PDFController extends Controller
{
    protected $userController;

    protected $baseChapterController;

    protected $googleController;

    protected $baseMailDataController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController,
        BaseMailDataController $baseMailDataController, GoogleController $googleController)
    {
        $this->userController = $userController;
        $this->googleController = $googleController;
        $this->baseChapterController = $baseChapterController;
        $this->baseMailDataController = $baseMailDataController;
    }

    public $pdfData = [];

    private function token()
    {
        $client_id = config('services.google.client_id');
        $client_secret = config('services.google.client_secret');
        $refresh_token = config('services.google.refresh_token');
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
            'scope' => 'https://www.googleapis.com/auth/drive', // Add the necessary scope for Shared Drive access
        ]);

        $accessToken = json_decode((string) $response->getBody(), true)['access_token'];

        return $accessToken;
    }

    /**
     * Save & Send Fianncial Reprot
     */
public function saveFinancialReport(Request $request, $chapterId = null, $PresDetails = null)
// public function saveFinancialReport(Request $request)

{
        // Prefer route parameters if provided, else fallback to request
    $chapterId = $chapterId ?? $request->chapterId;
    $chActiveId = $request->chActiveId ?? null;

    // Only calculate PresDetails if not passed in
    if ($PresDetails === null) {
        if ($chActiveId === '1') {
            $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($chapterId);
            $PresDetails = $baseActiveBoardQuery['PresDetails'];
        } elseif ($chActiveId === '0') {
            $baseDisbandedBoardQuery = $this->baseChapterController->getDisbandedBoardDetails($chapterId);
            $PresDetails = $baseDisbandedBoardQuery['PresDisbandedDetails'];
        }
    }

        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];

        $googleDrive = GoogleDrive::first();
        $eoyDrive = $googleDrive->eoy_uploads;
        $year = $googleDrive->eoy_uploads_year;
        $sharedDriveId = $eoyDrive;  // Shared Drive -> EOY Uploads

        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId, $userId);
        $chDetails = $baseQuery['chDetails'];
        $chDocuments = $baseQuery['chDocuments'];
        $conf = $chDetails->conference_id;
        $state = $baseQuery['stateShortName'];
        $chapterName = $chDetails->name;

        $result = $this->generateFinancialReport($chapterId, $PresDetails);
        $pdf = $result['pdf'];
        $name = $result['filename'];

        $pdfPath = storage_path('app/pdf_reports/'.$name);
        $pdf->save($pdfPath);
        $filename = basename($pdfPath);
        $mimetype = 'application/pdf';
        $filecontent = file_get_contents($pdfPath);

        if ($file_id = $this->googleController->uploadToEOYGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId, $year, $conf, $state, $chapterName)) {
            $existingDocRecord = Documents::where('chapter_id', $chapterId)->first();
            if ($existingDocRecord) {
                $existingDocRecord->financial_pdf_path = $file_id;
                $existingDocRecord->save();
            } else {
                Log::error("Expected document record for chapter_id {$chapterId} not found");
                $newDocData = ['chapter_id' => $chapterId];
                $newDocData['financial_pdf_path'] = $file_id;
                Documents::create($newDocData);
            }

            return $pdfPath;  // Return the full local stored path
        }
    }

    /**
     * Save & Send Fianncial Reprot
     */
    // public function saveFinalFinancialReport(Request $request, $chapterId, $PresDetails)
        public function saveFinalFinancialReport(Request $request)

    {

        $chapterId = $request->chapterId;
        $PresDetails = $request->PresDetails;

        $googleDrive = GoogleDrive::first();
        $finalFinancialDrive = $googleDrive->final_financial_report;
        $sharedDriveId = $finalFinancialDrive;  // Shared Drive -> EOY Uploads

        $result = $this->generateFinancialReport($chapterId, $PresDetails);
        $pdf = $result['pdf'];
        $name = $result['filename'];

        $pdfPath = storage_path('app/pdf_reports/'.$name);
        $pdf->save($pdfPath);
        $filename = basename($pdfPath);
        $mimetype = 'application/pdf';
        $filecontent = file_get_contents($pdfPath);

        if ($file_id = $this->googleController->uploadToGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId)) {
            $existingDocRecord = Documents::where('chapter_id', $chapterId)->first();
            if ($existingDocRecord) {
                $existingDocRecord->final_financial_pdf_path = $file_id;
                $existingDocRecord->save();
            } else {
                Log::error("Expected document record for chapter_id {$chapterId} not found");
                $newDocData = ['chapter_id' => $chapterId];
                $newDocData['final_financial_pdf_path'] = $file_id;
                Documents::create($newDocData);
            }

            return $pdfPath;  // Return the full local stored path
        }
    }

    /**
     * Generate Financial Report
     */
    public function generateFinancialReport($chapterId, $PresDetails)
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $chActiveId = $baseQuery['chActiveId'];

        if ($chActiveId == 1){
        $chFinancialReport = $baseQuery['chFinancialReport'];
        }
        if ($chActiveId == 0){
        $chFinancialReport = $baseQuery['chFinancialReportFinal'];
        }

        // $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($chapterId);
        // $PresDetails = $baseActiveBoardQuery['PresDetails'];

        $pdfData = array_merge(
            $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            $this->baseMailDataController->getPresData($PresDetails),
            $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport, $reviewer_email_message=null),
            [
                'changed_dues' => $chFinancialReport->changed_dues,
                'different_dues' => $chFinancialReport->different_dues,
                'not_all_full_dues' => $chFinancialReport->not_all_full_dues,
                'total_new_members' => $chFinancialReport->total_new_members,
                'total_renewed_members' => $chFinancialReport->total_renewed_members,
                'dues_per_member' => $chFinancialReport->dues_per_member,
                'total_new_members_changed_dues' => $chFinancialReport->total_new_members_changed_dues,
                'total_renewed_members_changed_dues' => $chFinancialReport->total_renewed_members_changed_dues,
                'dues_per_member_renewal' => $chFinancialReport->dues_per_member_renewal,
                'dues_per_member_new_changed' => $chFinancialReport->dues_per_member_new_changed,
                'dues_per_member_renewal_changed' => $chFinancialReport->dues_per_member_renewal_changed,
                'members_who_paid_no_dues' => $chFinancialReport->members_who_paid_no_dues,
                'members_who_paid_partial_dues' => $chFinancialReport->members_who_paid_partial_dues,
                'total_partial_fees_collected' => $chFinancialReport->total_partial_fees_collected,
                'total_associate_members' => $chFinancialReport->total_associate_members,
                'associate_member_fee' => $chFinancialReport->associate_member_fee,
                'manditory_meeting_fees_paid' => $chFinancialReport->manditory_meeting_fees_paid,
                'voluntary_donations_paid' => $chFinancialReport->voluntary_donations_paid,
                'paid_baby_sitters' => $chFinancialReport->paid_baby_sitters,
                'childrens_room_expenses' => $chFinancialReport->childrens_room_expenses,
                'service_project_array' => $chFinancialReport->service_project_array,
                'party_expense_array' => $chFinancialReport->party_expense_array,
                'office_printing_costs' => $chFinancialReport->office_printing_costs,
                'office_postage_costs' => $chFinancialReport->office_postage_costs,
                'office_membership_pins_cost' => $chFinancialReport->office_membership_pins_cost,
                'office_other_expenses' => $chFinancialReport->office_other_expenses,
                'international_event_array' => $chFinancialReport->international_event_array,
                'annual_registration_fee' => $chFinancialReport->annual_registration_fee,
                'monetary_donations_to_chapter' => $chFinancialReport->monetary_donations_to_chapter,
                'non_monetary_donations_to_chapter' => $chFinancialReport->non_monetary_donations_to_chapter,
                'other_income_and_expenses_array' => $chFinancialReport->other_income_and_expenses_array,
                'amount_reserved_from_previous_year' => $chFinancialReport->amount_reserved_from_previous_year,
                'bank_balance_now' => $chFinancialReport->bank_balance_now,
                'bank_reconciliation_array' => $chFinancialReport->bank_reconciliation_array,
                'receive_compensation' => $chFinancialReport->receive_compensation,
                'receive_compensation_explanation' => $chFinancialReport->receive_compensation_explanation,
                'financial_benefit' => $chFinancialReport->financial_benefit,
                'financial_benefit_explanation' => $chFinancialReport->financial_benefit_explanation,
                'influence_political' => $chFinancialReport->influence_political,
                'influence_political_explanation' => $chFinancialReport->influence_political_explanation,
                'vote_all_activities' => $chFinancialReport->vote_all_activities,
                'vote_all_activities_explanation' => $chFinancialReport->vote_all_activities_explanation,
                'purchase_pins' => $chFinancialReport->purchase_pins,
                'purchase_pins_explanation' => $chFinancialReport->purchase_pins_explanation,
                'bought_merch' => $chFinancialReport->bought_merch,
                'bought_merch_explanation' => $chFinancialReport->bought_merch_explanation,
                'offered_merch' => $chFinancialReport->offered_merch,
                'offered_merch_explanation' => $chFinancialReport->offered_merch_explanation,
                'bylaws_available' => $chFinancialReport->bylaws_available,
                'bylaws_available_explanation' => $chFinancialReport->bylaws_available_explanation,
                'childrens_room_sitters' => $chFinancialReport->childrens_room_sitters,
                'childrens_room_sitters_explanation' => $chFinancialReport->childrens_room_sitters_explanation,
                'had_playgroups' => $chFinancialReport->had_playgroups,
                'playgroups' => $chFinancialReport->playgroups,
                'had_playgroups_explanation' => $chFinancialReport->had_playgroups_explanation,
                'child_outings' => $chFinancialReport->child_outings,
                'child_outings_explanation' => $chFinancialReport->child_outings_explanation,
                'mother_outings' => $chFinancialReport->mother_outings,
                'mother_outings_explanation' => $chFinancialReport->mother_outings_explanation,
                'meeting_speakers' => $chFinancialReport->meeting_speakers,
                'meeting_speakers_explanation' => $chFinancialReport->meeting_speakers_explanation,
                'meeting_speakers_array' => $chFinancialReport->meeting_speakers_array,
                'discussion_topic_frequency' => $chFinancialReport->discussion_topic_frequency,
                'park_day_frequency' => $chFinancialReport->park_day_frequency,
                'activity_array' => $chFinancialReport->activity_array,
                'contributions_not_registered_charity' => $chFinancialReport->contributions_not_registered_charity,
                'contributions_not_registered_charity_explanation' => $chFinancialReport->contributions_not_registered_charity_explanation,
                'at_least_one_service_project' => $chFinancialReport->at_least_one_service_project,
                'at_least_one_service_project_explanation' => $chFinancialReport->at_least_one_service_project_explanation,
                'sister_chapter' => $chFinancialReport->sister_chapter,
                'international_event' => $chFinancialReport->international_event,
                'file_irs' => $chFinancialReport->file_irs,
                'file_irs_explanation' => $chFinancialReport->file_irs_explanation,
                'bank_statement_included' => $chFinancialReport->bank_statement_included,
                'bank_statement_included_explanation' => $chFinancialReport->bank_statement_included_explanation,
                'wheres_the_money' => $chFinancialReport->wheres_the_money,
                'completed_name' => $chFinancialReport->completed_name,
                'completed_email' => $chFinancialReport->completed_email,
                'submitted' => $chFinancialReport->submitted,
            ],
        );

        $pdf = Pdf::loadView('pdf.financialreport', compact('pdfData'));

        $filename = date('Y') - 1 .'-'.date('Y').'_'.$pdfData['chapterState'].'_'.$pdfData['chapterNameSanitized'].'_FinancialReport.pdf';

        // if ($streamResponse) {
        //     return $pdf->stream($filename, ['Attachment' => 0]);
        // }

        return [
            'pdf' => $pdf,
            'filename' => $filename,
        ];
    }

    /**
     * Save & Send Good Standing Letter
     */
    public function saveGoodStandingLetter($chapterId)
    {
        $googleDrive = GoogleDrive::first();
        $goodStandingDrive = $googleDrive->good_standing_letter;
        $sharedDriveId = $goodStandingDrive;  // Shared Drive -> Good Standing Letter

        $result = $this->generateGoodStanding($chapterId, false);
        $pdf = $result['pdf'];
        $name = $result['filename'];

        $pdfPath = storage_path('app/pdf_reports/'.$name);
        $pdf->save($pdfPath);
        $filename = basename($pdfPath);
        $mimetype = 'application/pdf';
        $filecontent = file_get_contents($pdfPath);

        if ($file_id = $this->googleController->uploadToGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId)) {
            $existingDocRecord = Documents::where('chapter_id', $chapterId)->first();
            if ($existingDocRecord) {
                $existingDocRecord->good_standing_letter = $file_id;
                $existingDocRecord->save();
            } else {
                Log::error("Expected document record for chapter_id {$chapterId} not found");
                $newDocData = ['chapter_id' => $chapterId];
                $newDocData['good_standing_letter'] = $file_id;
                Documents::create($newDocData);
            }

            return $pdfPath;  // Return the full local stored path
        }
    }

    /**
     * Generate Chaper in Good Standing PDF All Board Members
     */
    public function generateGoodStanding($chapterId, $streamResponse = true)
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $stateShortName = $baseQuery['stateShortName'];
        $reRegMonth = $chDetails->start_month_id;
        $reRegYear = $chDetails->next_renewal_year;
        $emailCCData = $baseQuery['emailCCData'];

        $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($chapterId);
        $PresDetails = $baseActiveBoardQuery['PresDetails'];

        $pdfData = array_merge(
            $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            $this->baseMailDataController->getPresData($PresDetails),
            $this->baseMailDataController->getCCData($emailCCData),
        );

        $pdf = Pdf::loadView('pdf.chapteringoodstanding', compact('pdfData'));

        $filename = $pdfData['chapterState'].'_'.$pdfData['chapterNameSanitized'].'_ChapterInGoodStanding.pdf';

        if ($streamResponse) {
            return $pdf->stream($filename, ['Attachment' => 0]);
        }

        return [
            'pdf' => $pdf,
            'filename' => $filename,
        ];
    }

    /**
     * Save & Send Disband Letter
     */
    public function saveDisbandLetter(Request $request, $chapterId, $type): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId, $userId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];

        $letterType = $type;
        $disbandDrive = DB::table('google_drive')->value('disband_letter');
        $sharedDriveId = $disbandDrive;

        switch ($letterType) {
            case 'general':
                $type = 'general';
                break;
            case 'did_not_start':
                $type = 'did_not_start';
                break;
            case 'no_report':
                $type = 'no_report';
                break;
            case 'no_payment':
                $type = 'no_payment';
                break;
            case 'no_communication':
                $type = 'no_communication';
                break;
            default:
                return response()->json(['message' => 'Invalid letter type selected'], 400);
        }

        $result = $this->generateDisbandLetter($request, $chapterId, $type);
        $pdf = $result['pdf'];
        $name = $result['filename'];

        $pdfPath = storage_path('app/pdf_reports/'.$name);
        $pdf->save($pdfPath);

        $filename = basename($pdfPath);
        $mimetype = 'application/pdf';
        $filecontent = file_get_contents($pdfPath);

        if ($file_id = $this->googleController->uploadToGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId)) {
            $existingDocRecord = Documents::where('chapter_id', $chapterId)->first();
            if ($existingDocRecord) {
                $existingDocRecord->disband_letter_path = $file_id;
                $existingDocRecord->save();
            } else {
                Log::error("Expected document record for chapter_id {$chapterId} not found");
                $newDocData = ['chapter_id' => $chapterId];
                $newDocData['disband_letter_path'] = $file_id;
                Documents::create($newDocData);
            }

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
            );

            switch ($letterType) {
                case 'general':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new DisbandChapterLetter($mailData, $pdfPath));
                    break;
                case 'did_not_start':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new DisbandChapterLetter($mailData, $pdfPath));
                    break;
                case 'no_report':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new DisbandChapterLetter($mailData, $pdfPath));
                    break;
                case 'no_payment':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new DisbandChapterLetter($mailData, $pdfPath));
                    break;
                case 'no_communication':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new DisbandChapterLetter($mailData, $pdfPath));
                    break;
                default:
                    return response()->json(['message' => 'Invalid letter type selected'], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Letter emailed successfully.',
                'pdf_path' => $pdfPath,
                'google_drive_id' => $file_id,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to successfully generate letter.',
        ], 500);
    }

    /**
     * Generate Disband Letter
     */
    public function generateDisbandLetter(Request $request, $chapterId, $type)
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId, $request);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];

        $baseDisbandedBoardQuery = $this->baseChapterController->getDisbandedBoardDetails($chapterId);
        $PresDetails = $baseDisbandedBoardQuery['PresDisbandedDetails'];

        //  Load User Information for Signing Email & PDFs
        $user = $this->userController->loadUserInformation($request);

        $date = Carbon::now();
        $dateFormatted = $date->format('m-d-Y');
        $nextMonth = $date->copy()->addMonth()->endOfMonth();
        $nextMonthFormatted = $nextMonth->format('m-d-Y');

        $pdfData = array_merge(
            $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            $this->baseMailDataController->getUserData($user),
            $this->baseMailDataController->getPresData($PresDetails),
            [
                'today' => $dateFormatted,
                'nextMonth' => $nextMonthFormatted,
                'startMonth' => $startMonthName,
            ]
        );

        $type = strtolower($type);
        $view = match ($type) {
            'general' => 'pdf.disbandgeneral',
            'did_not_start' => 'pdf.disbandnotstarted',
            'no_report' => 'pdf.disbandreport',
            'no_payment' => 'pdf.disbandpayment',
            'no_communication' => 'pdf.disbandcommunication',
        };

        $pdf = Pdf::loadView($view, compact('pdfData'));

        $filename = $pdfData['chapterState'].'_'.$pdfData['chapterNameSanitized']."_{$type}_Letter.pdf";

        return [
            'pdf' => $pdf,
            'filename' => $filename,
        ];
    }

    /**
     * Save & Send Probation Letter
     */
    public function saveProbationLetter(Request $request): JsonResponse
    {
        $chapterId = $request->chapterId;
        $letterType = $request->letterType;

        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];

        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId, $userId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $emailListChap = $baseQuery['emailListChap'];
        $emailListCoord = $baseQuery['emailListCoord'];

        $probationDrive = GoogleDrive::value('probation_letter');
        $sharedDriveId = $probationDrive;

        switch ($letterType) {
            case 'no_report':
                $type = 'no_report';
                break;
            case 'no_payment':
                $type = 'no_payment';
                break;
            case 'probation_party':
                $type = 'probation_party';
                break;
            case 'warning_party':
                $type = 'warning_party';
                break;
            case 'probation_release':
                $type = 'probation_release';
                break;
            default:
                return response()->json(['message' => 'Invalid letter type selected'], 400);
        }

        $result = $this->generateProbationLetter($request, $chapterId, $type);
        $pdf = $result['pdf'];
        $name = $result['filename'];

        $pdfPath = storage_path('app/pdf_reports/'.$name);
        $pdf->save($pdfPath);

        $filename = basename($pdfPath);
        $mimetype = 'application/pdf';
        $filecontent = file_get_contents($pdfPath);

        if ($file_id = $this->googleController->uploadToGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId)) {
            $existingDocRecord = Documents::where('chapter_id', $chapterId)->first();
            if ($existingDocRecord) {
                if ($letterType === 'probation_release') {
                    $existingDocRecord->probation_release_path = $file_id;
                    $existingDocRecord->save();
                } else {
                    $existingDocRecord->probation_path = $file_id;
                    $existingDocRecord->save();
                }
            } else {
                Log::error("Expected document record for chapter_id {$chapterId} not found");
                $newDocData = ['chapter_id' => $chapterId];
                if ($letterType === 'probation_release') {
                    $newDocData['probation_release_path'] = $file_id;
                } else {
                    $newDocData['probation_path'] = $file_id;
                }
                Documents::create($newDocData);
            }

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
            );

            switch ($letterType) {
                case 'no_report':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ProbationChapNoRptLetter($mailData, $pdfPath));
                    break;
                case 'no_payment':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ProbationChapNoPmtLetter($mailData, $pdfPath));
                    break;
                case 'probation_party':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ProbationChapPartyLetter($mailData, $pdfPath));
                    break;
                case 'warning_party':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ProbationChapWarningPartyLetter($mailData, $pdfPath));
                    break;
                case 'probation_release':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ProbationChapReleaseLetter($mailData, $pdfPath));
                    break;
                default:
                    return response()->json(['message' => 'Invalid letter type selected'], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Letter emailed successfully.',
                'pdf_path' => $pdfPath,
                'google_drive_id' => $file_id,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to successfully generate letter.',
        ], 500);
    }

    /**
     * Generate Probation Letter
     */
    public function generateProbationLetter(Request $request, $chapterId, $type)
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];

        $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($chapterId);
        $PresDetails = $baseActiveBoardQuery['PresDetails'];

        //  Load User Information for Signing Email & PDFs
        $user = $this->userController->loadUserInformation($request);

        $date = Carbon::now();
        $dateFormatted = $date->format('m-d-Y');
        $nextMonth = $date->copy()->addMonth()->endOfMonth();
        $nextMonthFormatted = $nextMonth->format('m-d-Y');

        $pdfData = array_merge(
            $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            $this->baseMailDataController->getUserData($user),
            $this->baseMailDataController->getPresData($PresDetails),
            [
                'today' => $dateFormatted,
                'nextMonth' => $nextMonthFormatted,
                'startMonth' => $startMonthName,
            ]
        );

        $type = strtolower($type);
        $view = match ($type) {
            'no_report' => 'pdf.probationreport',
            'no_payment' => 'pdf.probationpayment',
            'probation_party' => 'pdf.probationparty',
            'warning_party' => 'pdf.warningparty',
            'probation_release' => 'pdf.probationrelease',
        };

        $pdf = Pdf::loadView($view, compact('pdfData'));

        $filename = $pdfData['chapterState'].'_'.$pdfData['chapterNameSanitized']."_{$type}_Letter.pdf";

        return [
            'pdf' => $pdf,
            'filename' => $filename,
        ];
    }

    /**
     * Save & Send IRS Name Change Letter
     */
    public function saveNameChangeLetter(Request $request, $chapterId, $chNamePrev): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];

        $baseQueryUpd = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetailsUpd = $baseQueryUpd['chDetails'];
        $pcDetailsUpd = $baseQueryUpd['chDetails']->primaryCoordinator;
        $stateShortName = $baseQueryUpd['stateShortName'];

        $irsDrive = DB::table('google_drive')->value('irs_letter');
        $sharedDriveId = $irsDrive;

        // $result = $this->generateNameChangeLetter($request, $chapterId, $chNamePrev, $chDetailsUpd, $pcDetailsUpd);
        $result = $this->generateCombinedNameChangeLetter($chapterId, $chNamePrev, false);
        $pdfPath = $result['pdf'];
        $name = $result['filename'];

        $filename = basename($pdfPath);
        $mimetype = 'application/pdf';
        $filecontent = file_get_contents($pdfPath);

        if ($file_id = $this->googleController->uploadToGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId)) {
            $existingDocRecord = Documents::where('chapter_id', $chapterId)->first();
            if ($existingDocRecord) {
                $existingDocRecord->name_change_letter_path = $file_id;
                $existingDocRecord->save();
            } else {
                Log::error("Expected document record for chapter_id {$chapterId} not found");
                $newDocData = ['chapter_id' => $chapterId];
                $newDocData['name_change_letter_path'] = $file_id;
                Documents::create($newDocData);
            }

            $emailEINCoorData = $this->userController->loadEINCoord();
            $eincoorEmail = $emailEINCoorData['ein_email'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetailsUpd, $stateShortName),
            );

            Mail::to($eincoorEmail)
                ->queue(new NameChangeEINNotice($mailData, $pdfPath));

            return response()->json([
                'status' => 'success',
                'message' => 'Letter emailed successfully.',
                'pdf_path' => $pdfPath,
                'google_drive_id' => $file_id,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to successfully generate letter.',
        ], 500);
    }

    // /**
    //  * Generate Combined Name Change Letter (Cover Sheet & Report)
    //  */
    public function generateCombinedNameChangeLetter($chapterId, $chNamePrev, $streamResponse = false)
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $chapterName = $chDetails->name;
        $chapterState = $baseQuery['stateShortName'];

        $title = $chapterName.', '.$chapterState.' | Chapter Name Change';
        $message = "Name change for subordinate.";
        $pages = '2';

        // 1. Generate both DOMPDFs
        $cover = $this->generateEODeptFaxCover(false, $title, $message, $pages);
        $report = $this->generateNameChangeLetter($chapterId, $chNamePrev);
        $filename = $report['filename'];

        // 2. Save to temporary files
        $coverPath = storage_path('app/temp_faxcover.pdf');
        $reportPath = storage_path('app/temp_irsupdates.pdf');

        file_put_contents($coverPath, $cover['pdf']->output());
        file_put_contents($reportPath, $report['pdf']->output());

        // 3. Merge with FPDI (don't confuse with your PDF alias)
        $merger = new Fpdi();

        $addFile = function ($filePath) use ($merger) {
            $pageCount = $merger->setSourceFile($filePath);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl = $merger->importPage($i);
                $size = $merger->getTemplateSize($tpl);
                $merger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merger->useTemplate($tpl);
            }
        };

        $addFile($coverPath);
        $addFile($reportPath);

        // 4. Output final merged PDF
        $mergedPdfPath = storage_path('app/pdf_reports/' . $filename);
        $merger->Output($mergedPdfPath, 'F'); // ✅ Save to file


        // 5. Clean up
        @unlink($coverPath);
        @unlink($reportPath);

        return [
            'pdf' => $mergedPdfPath, // the FPDI object
            'filename' => $filename,
        ];
    }

    /**
     * Generate IRS Name Change Letter
     */
    // public function generateNameChangeLetter(Request $request, $chapterId, $chNamePrev)

     public function generateNameChangeLetter($chapterId, $chNamePrev)
    {
        $baseQueryUpd = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetailsUpd = $baseQueryUpd['chDetails'];
        $pcDetailsUpd = $baseQueryUpd['chDetails']->primaryCoordinator;
        $chId = $baseQueryUpd['chId'];
        $stateShortName = $baseQueryUpd['stateShortName'];

        $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($chapterId);
        $PresDetails = $baseActiveBoardQuery['PresDetails'];

        $emailEINCoorData = $this->userController->loadEINCoord();

        $todayDate = date('F j, Y');
        $twoMonthsDate = date('F j, Y', strtotime('+2 months'));

        $pdfData = array_merge(
            $this->baseMailDataController->getChapterData($chDetailsUpd, $stateShortName),
            $this->baseMailDataController->getChapterUpdatedData($chDetailsUpd, $pcDetailsUpd),
            $this->baseMailDataController->getPresData($PresDetails),
            $this->baseMailDataController->getEINCoorData($emailEINCoorData),
            [
                'todayDate' => $todayDate,
                'twoMonthsDate' => $twoMonthsDate,
                'chNamePrev' => $chNamePrev,
            ]
        );

        $pdf = Pdf::loadView('pdf.irsnamenamechangeletter', compact('pdfData'));

        $filename = $pdfData['chapterState'].'_'.$pdfData['chapterNameSanitized'].'_NameChangeLetter.pdf';

        return [
            'pdf' => $pdf,
            'filename' => $filename,
        ];
    }

    /**
     * Generate New Chapter IRS Fax Coversheet
     */
     public function generateNewChapterFaxCover($chapterId, $streamResponse = true)
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $emailCCData = $baseQuery['emailCCData'];

        $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($chapterId);
        $PresDetails = $baseActiveBoardQuery['PresDetails'];

        $emailEINCoorData = $this->userController->loadEINCoord();

        $date = Carbon::now();
        $dateFormatted = $date->format('F j, Y');

        $pdfData = array_merge(
            $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
            $this->baseMailDataController->getCCData($emailCCData),
            $this->baseMailDataController->getPresData($PresDetails),
            [
                'todayDate' => $dateFormatted,
            ]
        );

        $pdf = Pdf::loadView('pdf.faxcoverirsnewchapter', compact('pdfData'));

        $filename = $pdfData['chapterState'].'_'.$pdfData['chapterNameSanitized'].'_NewChapterFaxCover.pdf';

        if ($streamResponse) {
            return $pdf->stream($filename, ['Attachment' => 0]);
        }

        return [
            'pdf' => $pdf,
            'filename' => $filename,
        ];
    }

     // /**
    //  * Generate Combined IRS Subordinate Filing (Cover Sheet & Report)
    //  */
    public function generateCombinedIRSSubordinateFiling(Request $request)
    {
        $startFormatted = Carbon::parse($request->query('date') ?? now())->format('F Y');
        $todayFormatted = now()->format('F Y');

        $title = 'IRS Subordinate Filing';
        $message = "Full subordinate list of updates, additions and deletions from $startFormatted - $todayFormatted.<br><br>Some additions or deletions may have been included in a previous submission, but are included here to ensure a full and complete report.";
        $pages = request()->query('pages') ?? request()->input('pages') ?? 1;

        // 1. Generate both DOMPDFs
        $cover = $this->generateEODeptFaxCover(false, $title, $message, $pages);
        $filing = $this->generateSubordinateFiling($request, false);
        $filename = $filing['filename'];

        // 2. Save to temporary files
        $coverPath = storage_path('app/temp_faxcover.pdf');
        $filingPath = storage_path('app/temp_subordinatefiling.pdf');

        file_put_contents($coverPath, $cover['pdf']->output());
        file_put_contents($filingPath, $filing['pdf']->output());

        // 3. Merge with FPDI (don't confuse with your PDF alias)
        $merger = new Fpdi();

        $addFile = function ($filePath) use ($merger) {
            $pageCount = $merger->setSourceFile($filePath);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl = $merger->importPage($i);
                $size = $merger->getTemplateSize($tpl);
                $merger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merger->useTemplate($tpl);
            }
        };

        $addFile($coverPath);
        $addFile($filingPath);

        // 4. Output final merged PDF
        $mergedPdfContent = $merger->Output('S'); // Uppercase Output() from FPDI/FPDF

        // 5. Clean up
        @unlink($coverPath);
        @unlink($filingPath);

        return response($mergedPdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"$filename\"",
        ]);
    }

    // /**
    //  * Generate Subordinate Filing
    //  */
      public function generateSubordinateFiling(Request $request, $streamResponse = true)
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $dateInput = request()->query('date') ?? request()->input('date');
        $date = $dateInput ? Carbon::parse($dateInput) : Carbon::now();
        $startFormatted = $date->format('F Y');
        $todayDate = Carbon::now();
        $todayFormatted = $todayDate->format('F Y');
        $year = $todayDate->format('Y');

        // Get the update, add and zap lists
        $chapterUpdateList = $this->generateIRSUpdateList($coorId, $date);
        $chapterAddList = $this->generateIRSAddList($coorId, $date);
        $chapterZapList = $this->generateIRSZapList($coorId, $date);

        $chapterList = collect();

        foreach ($chapterUpdateList as $chapter) {
            $chapter->notes_column = null;
            $chapterList->push($chapter);
        }

        foreach ($chapterAddList as $chapter) {
            $chapter->notes_column = 'ADD';
            $chapterList->push($chapter);
        }

        foreach ($chapterZapList as $chapter) {
            $chapter->notes_column = 'DELETE';
            $chapterList->push($chapter);
        }

        $emailEINCoorData = $this->userController->loadEINCoord();

        $pdfData = array_merge(
            $this->baseMailDataController->getEINCoorData($emailEINCoorData),
            [
                'chapterList' => $chapterList,
                'startFormatted' => $startFormatted,
                'todayFormatted' => $todayFormatted
            ]
        );

        $pdf = Pdf::loadView('pdf.irssubordinatefiling', $pdfData);

        $filename = $year.'_IRSSubordinateFiling.pdf';

        if ($streamResponse) {
            return $pdf->stream($filename, ['Attachment' => 0]);
        }

        return [
            'pdf' => $pdf,
            'filename' => $filename,
        ];
    }

     // /**
    //  * Generate Combined IRS Updates (Cover Sheet & Report)
    //  */
    public function generateCombinedIRSUpdates(Request $request)
    {
        $startFormatted = Carbon::parse($request->query('date') ?? now())->format('F Y');
        $todayFormatted = now()->format('F Y');

        $title = 'IRS Updates';
        $message = "Subordinate corrections. Includes any additions and deletions as follows.";
        $pages = request()->query('pages') ?? request()->input('pages') ?? 1;

        // 1. Generate both DOMPDFs
        $cover = $this->generateEODeptFaxCover(false, $title, $message, $pages);
        $report = $this->generateIRSUpdates($request, false);
        $filename = $report['filename'];

        // 2. Save to temporary files
        $coverPath = storage_path('app/temp_faxcover.pdf');
        $reportPath = storage_path('app/temp_irsupdates.pdf');

        file_put_contents($coverPath, $cover['pdf']->output());
        file_put_contents($reportPath, $report['pdf']->output());

        // 3. Merge with FPDI (don't confuse with your PDF alias)
        $merger = new Fpdi();

        $addFile = function ($filePath) use ($merger) {
            $pageCount = $merger->setSourceFile($filePath);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl = $merger->importPage($i);
                $size = $merger->getTemplateSize($tpl);
                $merger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merger->useTemplate($tpl);
            }
        };

        $addFile($coverPath);
        $addFile($reportPath);

        // 4. Output final merged PDF
        $mergedPdfContent = $merger->Output('S'); // Uppercase Output() from FPDI/FPDF

        // 5. Clean up
        @unlink($coverPath);
        @unlink($reportPath);

        return response($mergedPdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"$filename\"",
        ]);
    }

     // /**
    //  * Generate IRS Updates other than Subordinate Filing
    //  */
     public function generateIRSUpdates(Request $request, $streamResponse = true)
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $pages = request()->query('pages') ?? request()->input('pages') ?? 1;
        $totalPages = (int) $pages;
        $followPages = $totalPages - 1;
        $dateInput = request()->query('date') ?? request()->input('date');
        $date = $dateInput ? Carbon::parse($dateInput) : Carbon::now();
        $startFormatted = $date->format('m-d-Y');

        $todayDate = Carbon::now();
        $sartFormatted2 = $date->format('F Y');
        $todayFormatted = $todayDate->format('F Y');
        $dateFormatted = $todayDate->format('F j, Y');

        // Get the add and zap lists
        $chapterAddList = $this->generateIRSAddList2($coorId, $date);
        $chapterZapList = $this->generateIRSZapList($coorId, $date);

        $emailEINCoorData = $this->userController->loadEINCoord();

        $pdfData = array_merge(
            $this->baseMailDataController->getEINCoorData($emailEINCoorData),
            [
                'todayDate' => $dateFormatted,
                'startDate' => $startFormatted,
                'startFormatted' => $sartFormatted2,
                'todayFormatted' => $todayFormatted,
                'totalPages' => $totalPages,
                'followPages' => $followPages,
                'chapterAddList' => $chapterAddList,
                'chapterZapList' => $chapterZapList,
            ]
        );

        $pdf = Pdf::loadView('pdf.irsupdates', ['pdfData' => $pdfData]);

        $filename = $date.'_IRSUpdates.pdf';

        if ($streamResponse) {
            return $pdf->stream($filename, ['Attachment' => 0]);
        }

        return [
            'pdf' => $pdf,
            'filename' => $filename,
        ];
    }

    // /**
    //  * Generate IRS list of Updated Chapters
    //  */
    private function generateIRSUpdateList($coorId, $date)
    {
        $baseQueryActive = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);

        return $baseQueryActive['query']
            ->select([
                'chapters.*',
                'bd_active.first_name as pres_first_name',
                'bd_active.last_name as pres_last_name',
                'bd_active.street_address as pres_address',
                'bd_active.city as pres_city',
                'state.state_short_name as pres_state',
                'bd_active.zip as pres_zip'
            ])
            ->whereNotNull('chapters.ein')
            ->where('chapters.ein', '!=', '*********')
            ->where('chapters.country_id', '=', '198')
            ->where(function($query) use ($date) {
                $query->where('chapters.start_year', '<', $date->year)
                    ->orWhere(function($query) use ($date) {
                        $query->where('chapters.start_year', '=', $date->year)
                            ->where('chapters.start_month_id', '<=', $date->month);
                    });
            })
            ->leftJoin('boards as bd_active', function($join) {
                $join->on('chapters.id', '=', 'bd_active.chapter_id')
                    ->where('bd_active.board_position_id', '=', 1);
            })
            ->leftJoin('state', 'bd_active.state_id', '=', 'state.id')
            ->get()
            ->sortBy('ein');
    }

    // /**
    //  * Generate IRS list of Added Chapters
    //  */
    private function generateIRSAddList($coorId, $date)
    {
        $baseQueryActive = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);

        return $baseQueryActive['query']
            ->select([
                'chapters.*',
                'bd_active.first_name as pres_first_name',
                'bd_active.last_name as pres_last_name',
                'bd_active.street_address as pres_address',
                'bd_active.city as pres_city',
                'state.state_short_name as pres_state',
                'bd_active.zip as pres_zip'
            ])
            ->whereNotNull('chapters.ein')
            ->where('chapters.ein', '!=', '*********')
            ->where('chapters.country_id', '=', '198')
            ->where(function($query) use ($date) {
                $query->where('chapters.start_year', '>', $date->year)
                    ->orWhere(function($query) use ($date) {
                        $query->where('chapters.start_year', '=', $date->year)
                                ->where('chapters.start_month_id', '>', $date->month);
                    });
            })
            ->leftJoin('boards as bd_active', function($join) {
                $join->on('chapters.id', '=', 'bd_active.chapter_id')
                    ->where('bd_active.board_position_id', '=', 1);
            })
            ->leftJoin('state', 'bd_active.state_id', '=', 'state.id')
            ->get()
            ->sortBy('ein');
    }

    // /**
    //  * Generate IRS list of Added Chapters that have not been reported
    //  */
    private function generateIRSAddList2($coorId, $date)
    {
        $baseQueryActive = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);

        return $baseQueryActive['query']
            ->select([
                'chapters.*',
                'bd_active.first_name as pres_first_name',
                'bd_active.last_name as pres_last_name',
                'bd_active.street_address as pres_address',
                'bd_active.city as pres_city',
                'state.state_short_name as pres_state',
                'bd_active.zip as pres_zip'
            ])
            ->whereNotNull('chapters.ein')
            ->where('chapters.ein', '!=', '*********')
            ->where('chapters.country_id', '=', '198')
            ->leftJoin('documents as doc', 'chapters.id', '=', 'doc.chapter_id')
            ->leftJoin('boards as bd_active', function($join) {
                $join->on('chapters.id', '=', 'bd_active.chapter_id')
                    ->where('bd_active.board_position_id', '=', 1);
            })
            ->leftJoin('state', 'bd_active.state_id', '=', 'state.id')
            ->where(function($query) {
                $query->whereNull('doc.ein_sent')
                    ->orWhere('doc.ein_sent', '!=', 1);
            })
            ->get()
            ->sortBy('ein');
    }

    // /**
    //  * Generate IRS list of Zapped Chapters
    //  */
    private function generateIRSZapList($coorId, $date)
    {
        $baseQueryZapped = $this->baseChapterController->getZappedInternationalBaseQuery($coorId);

        return $baseQueryZapped['query']
            ->select([
                'chapters.*'
            ])
            ->whereNotNull('chapters.ein')
            ->where('chapters.ein', '!=', '*********')
            ->where('chapters.country_id', '=', '198')
            ->where('chapters.zap_date', '>', $date)
            ->get()
            ->sortBy('ein');
    }

     // /**
    //  * Generate Combined 990N Filing Corrections (Cover Sheet & Report)
    //  */
    public function generateCombinedIRSFilingCorrections(Request $request)
    {
        $startFormatted = Carbon::parse($request->query('date') ?? now())->format('F Y');
        $todayFormatted = now()->format('F Y');

        $title = 'IRS Updates';
        $message = "Subordinate corrections. Chapters could not file 990N.";
        $pages = request()->query('pages') ?? request()->input('pages') ?? 1;

        // 1. Generate both DOMPDFs
        $cover = $this->generateEODeptFaxCover(false, $title, $message, $pages);
        $report = $this->generateIRSFilingCorrections($request, false);
        $filename = $report['filename'];

        // 2. Save to temporary files
        $coverPath = storage_path('app/temp_faxcover.pdf');
        $reportPath = storage_path('app/temp_irsupdates.pdf');

        file_put_contents($coverPath, $cover['pdf']->output());
        file_put_contents($reportPath, $report['pdf']->output());

        // 3. Merge with FPDI (don't confuse with your PDF alias)
        $merger = new Fpdi();

        $addFile = function ($filePath) use ($merger) {
            $pageCount = $merger->setSourceFile($filePath);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl = $merger->importPage($i);
                $size = $merger->getTemplateSize($tpl);
                $merger->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $merger->useTemplate($tpl);
            }
        };

        $addFile($coverPath);
        $addFile($reportPath);

        // 4. Output final merged PDF
        $mergedPdfContent = $merger->Output('S'); // Uppercase Output() from FPDI/FPDF

        // 5. Clean up
        @unlink($coverPath);
        @unlink($reportPath);

        return response($mergedPdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"$filename\"",
        ]);
    }

      // /**
    //  * Generate 990N Filing Corrections
    //  */
     public function generateIRSFilingCorrections(Request $request, $streamResponse = true)
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $pages = request()->query('pages') ?? request()->input('pages') ?? 1;
        $totalPages = (int) $pages;
        $followPages = $totalPages - 1;

        $todayDate = Carbon::now();
        $date = $todayDate ? Carbon::parse($todayDate) : Carbon::now();
        $dateFormatted = $todayDate->format('F j, Y');

        // Get the add and zap lists
        $wrongDateList = $this->generateIRSWrongDateList($coorId);
        $notFoundList = $this->generateIRSNotFoundList($coorId);

        $emailEINCoorData = $this->userController->loadEINCoord();

        $pdfData = array_merge(
            $this->baseMailDataController->getEINCoorData($emailEINCoorData),
            [
                'todayDate' => $dateFormatted,
                'totalPages' => $totalPages,
                'followPages' => $followPages,
                'wrongDateList' => $wrongDateList,
                'notFoundList' => $notFoundList,
            ]
        );

        $pdf = Pdf::loadView('pdf.irsfilingcorrections', ['pdfData' => $pdfData]);

        $filename = $date.'_IRSFilingCorrections.pdf';

        if ($streamResponse) {
            return $pdf->stream($filename, ['Attachment' => 0]);
        }

        return [
            'pdf' => $pdf,
            'filename' => $filename,
        ];
    }

     // /**
    //  * Generate IRS list of 990N Filing Corrections with the Wrong Dates
    //  */
    private function generateIRSWrongDateList($coorId)
{
    $baseQueryActive = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);

    return $baseQueryActive['query']
        ->select([
            'chapters.*',
            'bd_active.first_name as pres_first_name',
            'bd_active.last_name as pres_last_name',
            'bd_active.street_address as pres_address',
            'bd_active.city as pres_city',
            'state.state_short_name as pres_state',
            'bd_active.zip as pres_zip'
        ])
        ->leftJoin('documents', 'chapters.id', '=', 'documents.chapter_id')
        ->leftJoin('boards as bd_active', function($join) {
            $join->on('chapters.id', '=', 'bd_active.chapter_id')
                ->where('bd_active.board_position_id', '=', 1);
        })
        ->leftJoin('state', 'bd_active.state_id', '=', 'state.id')
        ->where('documents.irs_wrongdate', 1)
        ->where(function($query) {
            $query->whereNull('documents.irs_notified')
                  ->orWhere('documents.irs_notified', '!=', '1');
        })
        ->get()
        ->sortBy('ein');
}

     // /**
    //  * Generate IRS list of 990N Filing Corrections where chapter was not found
    //  */
    private function generateIRSNotFoundList($coorId)
{
    $baseQueryActive = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);

    return $baseQueryActive['query']
        ->select([
            'chapters.*',
            'bd_active.first_name as pres_first_name',
            'bd_active.last_name as pres_last_name',
            'bd_active.street_address as pres_address',
            'bd_active.city as pres_city',
            'state.state_short_name as pres_state',
            'bd_active.zip as pres_zip'
        ])
        ->leftJoin('documents', 'chapters.id', '=', 'documents.chapter_id')
        ->leftJoin('boards as bd_active', function($join) {
            $join->on('chapters.id', '=', 'bd_active.chapter_id')
                ->where('bd_active.board_position_id', '=', 1);
        })
        ->leftJoin('state', 'bd_active.state_id', '=', 'state.id')
        ->where('documents.irs_notfound', 1)
        ->where(function($query) {
            $query->whereNull('documents.irs_notified')
                  ->orWhere('documents.irs_notified', '!=', '1');
        })
        ->get()
        ->sortBy('ein');
}


    // /**
    //  * EO Dept IRS Fax Coversheet
    //  */
     public function generateEODeptFaxCover($streamResponse = true, $title = null, $message = null, $pages = null)
    {
        $pages = $pages ?? request()->query('pages') ?? request()->input('pages') ?? 1;
        $message = $message ?? request()->query('message') ?? request()->input('message') ?? '';
        $title = $title ?? request()->query('title') ?? request()->input('title') ?? 'Fax Cover Sheet';

        $totalPages = (int) $pages;
        $followPages = $totalPages - 1;

        $date = Carbon::now();
        $dateToday = $date->format('m-d-Y');
        $dateFormatted = $date->format('F j, Y');

        $emailEINCoorData = $this->userController->loadEINCoord();

        $pdfData = array_merge(
            $this->baseMailDataController->getEINCoorData($emailEINCoorData),
            [
                'todayDate' => $dateFormatted,
                'totalPages' => $totalPages,
                'followPages' => $followPages,
                'title' => $title,
                'message' => $message,
            ]
        );

        $pdf = Pdf::loadView('pdf.irseodeptfaxcover', compact('pdfData'));

        $filename = $dateToday.'_IRSEODeptFaxCover.pdf';

        if ($streamResponse) {
            return $pdf->stream($filename, ['Attachment' => 0]);
        }

        return [
            'pdf' => $pdf,
            'filename' => $filename,
        ];
    }

    /**
     * Upload PDF to Google Drive
     */
    private function uploadToGoogleDrive($pdfPath, &$pdfFileId, $sharedDriveId)
    {
        $googleClient = new Client;
        $accessToken = $this->token();

        $filename = basename($pdfPath);
        $fileMetadata = [
            'name' => $filename,
            'mimeType' => 'application/pdf',
            'parents' => [$sharedDriveId],
        ];

        $fileContent = file_get_contents($pdfPath);
        $fileContentBase64 = base64_encode($fileContent);
        $metadataJson = json_encode($fileMetadata);

        $response = $googleClient->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
            ],
            'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
        ]);

        if ($response->getStatusCode() === 200) {
            $responseData = json_decode($response->getBody()->getContents(), true);
            $pdfFileId = $responseData['id'] ?? null; // Extract file ID

            return true;
        }

        return false;
    }

    /**
     * Upload to EOY Google Drive -- To create folder/sub folder system.
     */
    private function uploadToEOYGoogleDrive($pdfPath, &$pdfFileId, $sharedDriveId, $year, $conf, $state, $chapterName)
    {
        $googleClient = new Client;
        $accessToken = $this->token();

        $chapterFolderId = $this->googleController->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

        $filename = basename($pdfPath);
        $fileMetadata = [
            'name' => $filename,
            'mimeType' => 'application/pdf',
            'parents' => [$chapterFolderId],
            'driveId' => $sharedDriveId,
        ];

        $fileContent = file_get_contents($pdfPath);
        $fileContentBase64 = base64_encode($fileContent);
        $metadataJson = json_encode($fileMetadata);

        $response = $googleClient->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
            ],
            'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
        ]);

        $bodyContents = $response->getBody()->getContents();
        $jsonResponse = json_decode($bodyContents, true);

        if ($response->getStatusCode() === 200) {
            return $jsonResponse['id'];  // Return just the ID string instead of an array
        }

        return null; // Return null if upload fails
    }
}
