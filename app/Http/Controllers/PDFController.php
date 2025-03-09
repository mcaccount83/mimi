<?php

namespace App\Http\Controllers;

use App\Mail\ProbationNoPmtLetter;
use App\Mail\ProbationNoRptLetter;
use App\Mail\ProbationPartyLetter;
use App\Mail\ProbationReleaseLetter;
use App\Mail\WarningPartyLetter;
use App\Mail\ChapterDisbandLetter;
use App\Models\GoogleDrive;
use App\Models\User;
use App\Models\Documents;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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

    /*/ Base Chapter Controller /*/
    //  $this->baseChapterController->getChapterDetails($chId)

    /**
     * Save & Send Fianncial Reprot
     */
    public function saveFinancialReport(Request $request, $chapterId)
    {
        // $user = User::find($request->user()->id);
        // $userId = $user->id;
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];

        $googleDrive = GoogleDrive::first();
        $eoyDrive = $googleDrive->eoy_uploads;
        $year = $googleDrive->eoy_uploads_year;
        $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

        // $eoyDrive = GoogleDrive::value('eoy_uploads');
        // $sharedDriveId = $eoyDrive;
        // $year = GoogleDrive::value('eoy_uploads_year');

        // $baseQuery = $this->getChapterDetails($chapterId);
        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId, $userId);
        $chDetails = $baseQuery['chDetails'];
        $chDocuments = $baseQuery['chDocuments'];
        $conf = $chDetails->conference_id;
        $state = $baseQuery['stateShortName'];
        $chapterName = $chDetails->name;
        $name = date('Y') - 1 .'-'.date('Y').'_'.$state.'_'.$chapterName.'_FinancialReport';

        $result = $this->generateFinancialReport($chapterId);
        $pdf = $result['pdf'];
        $filename = $result['filename'];

        $pdfPath = storage_path('app/pdf_reports/'.$filename);
        $pdf->save($pdfPath);

        $file = $pdfPath;

        if ($file_id = $this->googleController->uploadToEOYGoogleDrive($file, $name, $sharedDriveId, $year, $conf, $state, $chapterName)) {
            $existingDocRecord = Documents::where('chapter_id', $chapterId)->first();
            $existingDocRecord->update([
                'financial_pdf_path' => $file_id,
            ]);

            // $chDocuments->financial_pdf_path = $file_id;
            // $chDocuments->save();

            return $pdfPath;  // Return the full local stored path
        }
    }

     /**
     * Generate Financial Report
     */
    public function generateFinancialReport($chapterId)
    {
        // $user = User::find($request->user()->id);
        // $userId = $user->id;

        // $userName = $user->first_name.' '.$user->last_name;
        // $userEmail = $user->email;

        // $baseQuery = $this->getChapterDetails($chapterId);
        $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
        $chDetails = $baseQuery['chDetails'];
        $chId = $baseQuery['chId'];
        $sanitizedChapterName = str_replace(['/', '\\'], '-', $chDetails->name);
        $stateShortName = $baseQuery['stateShortName'];
        $chFinancialReport = $chDetails->financialReport;
        $submitted = $chFinancialReport->submitted;
        $PresDetails = $baseQuery['PresDetails'];
        $cc_fname = $baseQuery['cc_fname'];
        $cc_lname = $baseQuery['cc_lname'];
        $cc_pos = $baseQuery['cc_pos'];
        $cc_conf_name = $baseQuery['cc_conf_name'];
        $cc_conf_desc = $baseQuery['cc_conf_desc'];

        $pdfData = [
            'chapter_name' => $chDetails->name,
            'state' => $stateShortName,
            'ein' => $chDetails->ein,
            'boundaries' => $chDetails->territory,
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
            'submitted' => $submitted,
            'ch_name' => $sanitizedChapterName,
        ];

        $pdf = Pdf::loadView('pdf.financialreport', compact('pdfData'));

        $filename = date('Y') - 1 .'-'.date('Y').'_'.$pdfData['state'].'_'.$pdfData['ch_name'].'_FinancialReport.pdf';

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
        $goodStandingDrive = GoogleDrive::value('good_standing_letter');
        $sharedDriveId = $goodStandingDrive;

        $result = $this->generateGoodStanding($chapterId, false);
        $pdf = $result['pdf'];
        $filename = $result['filename'];

        $pdfPath = storage_path('app/pdf_reports/'.$filename);
        $pdf->save($pdfPath);

        if ($this->uploadToGoogleDrive($pdfPath, $pdfFileId, $sharedDriveId)) {
            $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $chDocuments = $baseQuery['chDocuments'];

            $chDocuments->good_standing_letter = $pdfFileId;
            $chDocuments->save();

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
        $sanitizedChapterName = str_replace(['/', '\\'], '-', $chDetails->name);
        $stateShortName = $baseQuery['stateShortName'];
        $reRegMonth = $chDetails->start_month_id;
        $reRegYear = $chDetails->next_renewal_year;
        $PresDetails = $baseQuery['PresDetails'];
        $cc_fname = $baseQuery['cc_fname'];
        $cc_lname = $baseQuery['cc_lname'];
        $cc_pos = $baseQuery['cc_pos'];
        $cc_conf_name = $baseQuery['cc_conf_name'];
        $cc_conf_desc = $baseQuery['cc_conf_desc'];

        $pdfData = [
            'chapter_name' => $chDetails->name,
            'state' => $stateShortName,
            'ein' => $chDetails->ein,
            'pres_fname' => $PresDetails->first_name,
            'pres_lname' => $PresDetails->last_name,
            'pres_addr' => $PresDetails->street_address,
            'pres_city' => $PresDetails->city,
            'pres_state' => $PresDetails->state,
            'pres_zip' => $PresDetails->zip,
            'cc_fname' => $cc_fname,
            'cc_lname' => $cc_lname,
            'cc_pos' => $cc_pos,
            'conf_name' => $cc_conf_name,
            'conf_desc' => $cc_conf_desc,
            'ch_name' => $sanitizedChapterName,
        ];

        $pdf = Pdf::loadView('pdf.chapteringoodstanding', compact('pdfData'));

        $filename = $pdfData['state'].'_'.$pdfData['ch_name']."_ChapterInGoodStanding.pdf";

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
        // $chapterId = $request->chapterId;
        // $letterType = $request->letterType;
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
        $filename = $result['filename'];

        $pdfPath = storage_path('app/pdf_reports/'.$filename);
        $pdf->save($pdfPath);

        if ($this->uploadToGoogleDrive($pdfPath, $pdfFileId, $sharedDriveId)) {
            $baseQuery = $this->baseChapterController->getChapterDetails($chapterId, $request);
            $chDetails = $baseQuery['chDetails'];
            $chDocuments = $baseQuery['chDocuments'];
            $stateShortName = $baseQuery['stateShortName'];

            $chDocuments->disband_letter_path = $pdfFileId;
            $chDocuments->save();

            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];

            //  Load User Information for Signing Email & PDFs
            $user = $this->userController->loadUserInformation($request);

            $mailData = array_merge(
                $this->baseMailDataController->getChapterBasicData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
            );

            switch ($letterType) {
                case 'general':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ChapterDisbandLetter($mailData, $pdfPath));
                    break;
                case 'did_not_start':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ChapterDisbandLetter($mailData, $pdfPath));
                    break;
                case 'no_report':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ChapterDisbandLetter($mailData, $pdfPath));
                    break;
                case 'no_payment':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ChapterDisbandLetter($mailData, $pdfPath));
                    break;
                case 'no_communication':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ChapterDisbandLetter($mailData, $pdfPath));
                    break;
                default:
                    return response()->json(['message' => 'Invalid letter type selected'], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Letter emailed successfully.',
                'pdf_path' => $pdfPath,
                'google_drive_id' => $pdfFileId,
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
        $sanitizedChapterName = str_replace(['/', '\\'], '-', $chDetails->name);
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];
        $PresDetails = $baseQuery['PresDetails'];

        //  Load User Information for Signing Email & PDFs
        $user = $this->userController->loadUserInformation($request);

        $date = Carbon::now();
        $dateFormatted = $date->format('m-d-Y');
        $nextMonth = $date->copy()->addMonth()->endOfMonth();
        $nextMonthFormatted = $nextMonth->format('m-d-Y');

        $pdfData = array_merge(
            $this->baseMailDataController->getChapterBasicData($chDetails, $stateShortName),
            $this->baseMailDataController->getUserData($user),
            $this->baseMailDataController->getPresData($PresDetails),
            [
            'ch_name' => $sanitizedChapterName,
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

        $filename = $pdfData['chapterState'].'_'.$pdfData['ch_name']."_{$type}_Letter.pdf";

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
        $filename = $result['filename'];

        $pdfPath = storage_path('app/pdf_reports/'.$filename);
        $pdf->save($pdfPath);

        if ($this->uploadToGoogleDrive($pdfPath, $pdfFileId, $sharedDriveId)) {

            $baseQuery = $this->baseChapterController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $chDocuments = $baseQuery['chDocuments'];
            $stateShortName = $baseQuery['stateShortName'];

            if ($letterType === 'probation_release') {
                $chDocuments->probation_release_path = $pdfFileId;
            } else {
                $chDocuments->probation_path = $pdfFileId;
            }

            $chDocuments->save();

            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];

            //  Load User Information for Signing Email & PDFs
            $user = $this->userController->loadUserInformation($request);

            $mailData = array_merge(
                $this->baseMailDataController->getChapterBasicData($chDetails, $stateShortName),
                $this->baseMailDataController->getUserData($user),
            );

            switch ($letterType) {
                case 'no_report':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ProbationNoRptLetter($mailData, $pdfPath));
                    break;
                case 'no_payment':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ProbationNoPmtLetter($mailData, $pdfPath));
                    break;
                case 'probation_party':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ProbationPartyLetter($mailData, $pdfPath));
                    break;
                case 'warning_party':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new WarningPartyLetter($mailData, $pdfPath));
                    break;
                case 'probation_release':
                    Mail::to($emailListChap)
                        ->cc($emailListCoord)
                        ->queue(new ProbationReleaseLetter($mailData, $pdfPath));
                    break;
                default:
                    return response()->json(['message' => 'Invalid letter type selected'], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Letter emailed successfully.',
                'pdf_path' => $pdfPath,
                'google_drive_id' => $pdfFileId,
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
        $sanitizedChapterName = str_replace(['/', '\\'], '-', $chDetails->name);
        $stateShortName = $baseQuery['stateShortName'];
        $startMonthName = $baseQuery['startMonthName'];
        $PresDetails = $baseQuery['PresDetails'];

        //  Load User Information for Signing Email & PDFs
        $user = $this->userController->loadUserInformation($request);

        $date = Carbon::now();
        $dateFormatted = $date->format('m-d-Y');
        $nextMonth = $date->copy()->addMonth()->endOfMonth();
        $nextMonthFormatted = $nextMonth->format('m-d-Y');

        $pdfData = array_merge(
            $this->baseMailDataController->getChapterBasicData($chDetails, $stateShortName),
            $this->baseMailDataController->getUserData($user),
            $this->baseMailDataController->getPresData($PresDetails),
            [
            'ch_name' => $sanitizedChapterName,
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

        $filename = $pdfData['chapterState'].'_'.$pdfData['ch_name']."_{$type}_Letter.pdf";

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
     * Save & Send Disband Letter  -------  OLD DISBAND LETTER
     */
    // public function generateAndSaveDisbandLetter($id)
    // {
    //     $baseQuery = $this->getChapterDetails($id);
    //     $chDetails = $baseQuery['chDetails'];
    //     $chId = $baseQuery['chId'];
    //     $sanitizedChapterName = str_replace(['/', '\\'], '-', $chDetails->name);
    //     $stateShortName = $baseQuery['stateShortName'];
    //     $reRegMonth = $chDetails->start_month_id;
    //     $reRegYear = $chDetails->next_renewal_year;
    //     $PresDetails = $baseQuery['PresDetails'];

    //     $emailListChap = $baseQuery['emailListChap'];
    //     $emailListCoord = $baseQuery['emailListCoord'];
    //     $emailCC = $baseQuery['emailCC'];
    //     $cc_fname = $baseQuery['cc_fname'];
    //     $cc_lname = $baseQuery['cc_lname'];
    //     $cc_pos = $baseQuery['cc_pos'];
    //     $cc_conf_name = $baseQuery['cc_conf_name'];
    //     $cc_conf_desc = $baseQuery['cc_conf_desc'];

    //     $disbandDrive = GoogleDrive::value('disband_letter');
    //     $sharedDriveId = $disbandDrive;

    //     $date = Carbon::now();
    //     $dateFormatted = $date->format('m-d-Y');
    //     $nextMonth = $date->copy()->addMonth()->endOfMonth();
    //     $nextMonthFormatted = $nextMonth->format('m-d-Y');

    //     $pdfData = [
    //         'ch_name' => $sanitizedChapterName,
    //         'today' => $dateFormatted,
    //         'nextMonth' => $nextMonthFormatted,
    //         'chapter_name' => $chDetails->name,
    //         'state' => $stateShortName,
    //         'pres_fname' => $PresDetails->first_name,
    //         'pres_lname' => $PresDetails->last_name,
    //         'pres_addr' => $PresDetails->street_address,
    //         'pres_city' => $PresDetails->city,
    //         'pres_state' => $PresDetails->state,
    //         'pres_zip' => $PresDetails->zip,
    //         're_reg_month' => $reRegMonth,
    //         're_reg_year' => $reRegYear,
    //         'cc_fname' => $cc_fname,
    //         'cc_lname' => $cc_lname,
    //         'cc_pos' => $cc_pos,
    //         'conf_name' => $cc_conf_name,
    //         'conf_desc' => $cc_conf_desc,
    //     ];

    //     $pdf = Pdf::loadView('pdf.disbandletter', compact('pdfData'));

    //     $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
    //     $filename = $pdfData['state'].'_'.$chapterName.'_Disband_Letter.pdf'; // Use sanitized chapter name

    //     $pdfPath = storage_path('app/pdf_reports/'.$filename);
    //     $pdf->save($pdfPath);

    //     if ($this->uploadToGoogleDrive($pdfPath, $pdfFileId, $sharedDriveId)){
    //         $documents = Documents::find($id);
    //         $documents->disband_letter_path = $pdfFileId;
    //         $documents->save();

    //         return $pdfPath;  // Return the full local stored path
    //     }
    // }

     /**
     * Generate Financial Report PDF
     */
    // public function generatePdf(Request $request, $chapterId)
    // {
    //     $user = User::find($request->user()->id);
    //     $userId = $user->id;
    //     $userName = $user->first_name.' '.$user->last_name;
    //     $userEmail = $user->email;

    //     $baseQuery = $this->getChapterDetails($chapterId);
    //     $chDetails = $baseQuery['chDetails'];
    //     $chId = $baseQuery['chId'];
    //     $sanitizedChapterName = str_replace(['/', '\\'], '-', $chDetails->name);
    //     $stateShortName = $baseQuery['stateShortName'];
    //     $chFinancialReport = $chDetails->financialReport;
    //     $submitted = $chFinancialReport->submitted;
    //     $submittedFormatted = $submitted->format('m-d-Y');

    //     $pdfData = [
    //         'chapter_name' => $chDetails->name,
    //         'state' => $stateShortName,
    //         'ein' => $chDetails->ein,
    //         'boundaries' => $chDetails->territory,
    //         'changed_dues' => $chFinancialReport->changed_dues,
    //         'different_dues' => $chFinancialReport->different_dues,
    //         'not_all_full_dues' => $chFinancialReport->not_all_full_dues,
    //         'total_new_members' => $chFinancialReport->total_new_members,
    //         'total_renewed_members' => $chFinancialReport->total_renewed_members,
    //         'dues_per_member' => $chFinancialReport->dues_per_member,
    //         'total_new_members_changed_dues' => $chFinancialReport->total_new_members_changed_dues,
    //         'total_renewed_members_changed_dues' => $chFinancialReport->total_renewed_members_changed_dues,
    //         'dues_per_member_renewal' => $chFinancialReport->dues_per_member_renewal,
    //         'dues_per_member_new_changed' => $chFinancialReport->dues_per_member_new_changed,
    //         'dues_per_member_renewal_changed' => $chFinancialReport->dues_per_member_renewal_changed,
    //         'members_who_paid_no_dues' => $chFinancialReport->members_who_paid_no_dues,
    //         'members_who_paid_partial_dues' => $chFinancialReport->members_who_paid_partial_dues,
    //         'total_partial_fees_collected' => $chFinancialReport->total_partial_fees_collected,
    //         'total_associate_members' => $chFinancialReport->total_associate_members,
    //         'associate_member_fee' => $chFinancialReport->associate_member_fee,
    //         'manditory_meeting_fees_paid' => $chFinancialReport->manditory_meeting_fees_paid,
    //         'voluntary_donations_paid' => $chFinancialReport->voluntary_donations_paid,
    //         'paid_baby_sitters' => $chFinancialReport->paid_baby_sitters,
    //         'childrens_room_expenses' => $chFinancialReport->childrens_room_expenses,
    //         'service_project_array' => $chFinancialReport->service_project_array,
    //         'party_expense_array' => $chFinancialReport->party_expense_array,
    //         'office_printing_costs' => $chFinancialReport->office_printing_costs,
    //         'office_postage_costs' => $chFinancialReport->office_postage_costs,
    //         'office_membership_pins_cost' => $chFinancialReport->office_membership_pins_cost,
    //         'office_other_expenses' => $chFinancialReport->office_other_expenses,
    //         'international_event_array' => $chFinancialReport->international_event_array,
    //         'annual_registration_fee' => $chFinancialReport->annual_registration_fee,
    //         'monetary_donations_to_chapter' => $chFinancialReport->monetary_donations_to_chapter,
    //         'non_monetary_donations_to_chapter' => $chFinancialReport->non_monetary_donations_to_chapter,
    //         'other_income_and_expenses_array' => $chFinancialReport->other_income_and_expenses_array,
    //         'amount_reserved_from_previous_year' => $chFinancialReport->amount_reserved_from_previous_year,
    //         'bank_balance_now' => $chFinancialReport->bank_balance_now,
    //         'bank_reconciliation_array' => $chFinancialReport->bank_reconciliation_array,
    //         'receive_compensation' => $chFinancialReport->receive_compensation,
    //         'receive_compensation_explanation' => $chFinancialReport->receive_compensation_explanation,
    //         'financial_benefit' => $chFinancialReport->financial_benefit,
    //         'financial_benefit_explanation' => $chFinancialReport->financial_benefit_explanation,
    //         'influence_political' => $chFinancialReport->influence_political,
    //         'influence_political_explanation' => $chFinancialReport->influence_political_explanation,
    //         'vote_all_activities' => $chFinancialReport->vote_all_activities,
    //         'vote_all_activities_explanation' => $chFinancialReport->vote_all_activities_explanation,
    //         'purchase_pins' => $chFinancialReport->purchase_pins,
    //         'purchase_pins_explanation' => $chFinancialReport->purchase_pins_explanation,
    //         'bought_merch' => $chFinancialReport->bought_merch,
    //         'bought_merch_explanation' => $chFinancialReport->bought_merch_explanation,
    //         'offered_merch' => $chFinancialReport->offered_merch,
    //         'offered_merch_explanation' => $chFinancialReport->offered_merch_explanation,
    //         'bylaws_available' => $chFinancialReport->bylaws_available,
    //         'bylaws_available_explanation' => $chFinancialReport->bylaws_available_explanation,
    //         'childrens_room_sitters' => $chFinancialReport->childrens_room_sitters,
    //         'childrens_room_sitters_explanation' => $chFinancialReport->childrens_room_sitters_explanation,
    //         'had_playgroups' => $chFinancialReport->had_playgroups,
    //         'playgroups' => $chFinancialReport->playgroups,
    //         'had_playgroups_explanation' => $chFinancialReport->had_playgroups_explanation,
    //         'child_outings' => $chFinancialReport->child_outings,
    //         'child_outings_explanation' => $chFinancialReport->child_outings_explanation,
    //         'mother_outings' => $chFinancialReport->mother_outings,
    //         'mother_outings_explanation' => $chFinancialReport->mother_outings_explanation,
    //         'meeting_speakers' => $chFinancialReport->meeting_speakers,
    //         'meeting_speakers_explanation' => $chFinancialReport->meeting_speakers_explanation,
    //         'meeting_speakers_array' => $chFinancialReport->meeting_speakers_array,
    //         'discussion_topic_frequency' => $chFinancialReport->discussion_topic_frequency,
    //         'park_day_frequency' => $chFinancialReport->park_day_frequency,
    //         'activity_array' => $chFinancialReport->activity_array,
    //         'contributions_not_registered_charity' => $chFinancialReport->contributions_not_registered_charity,
    //         'contributions_not_registered_charity_explanation' => $chFinancialReport->contributions_not_registered_charity_explanation,
    //         'at_least_one_service_project' => $chFinancialReport->at_least_one_service_project,
    //         'at_least_one_service_project_explanation' => $chFinancialReport->at_least_one_service_project_explanation,
    //         'sister_chapter' => $chFinancialReport->sister_chapter,
    //         'international_event' => $chFinancialReport->international_event,
    //         'file_irs' => $chFinancialReport->file_irs,
    //         'file_irs_explanation' => $chFinancialReport->file_irs_explanation,
    //         'bank_statement_included' => $chFinancialReport->bank_statement_included,
    //         'bank_statement_included_explanation' => $chFinancialReport->bank_statement_included_explanation,
    //         'wheres_the_money' => $chFinancialReport->wheres_the_money,
    //         'completed_name' => $userName,
    //         'completed_email' => $userEmail,
    //         'submitted' => $submittedFormatted,
    //         'ch_name' => $sanitizedChapterName,
    //     ];

    //     $pdf = Pdf::loadView('pdf.financialreport', compact('pdfData'));

    //     $filename = date('Y') - 1 .'-'.date('Y').'_'.$pdfData['state'].'_'.$pdfData['ch_name'].'_FinancialReport.pdf';

    //     return $pdf->stream($filename, ['Attachment' => 0]); // Stream the PDF

    // }



}
