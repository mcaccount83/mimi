<?php

namespace App\Http\Controllers;

use App\Mail\ProbationNoPmtLetter;
use App\Mail\ProbationNoRptLetter;
use App\Mail\ProbationPartyLetter;
use App\Mail\ProbationReleaseLetter;
use App\Mail\WarningPartyLetter;
use App\Models\Chapters;
use App\Models\Documents;
use App\Models\FinancialReport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class PDFController extends Controller
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->middleware('auth')->except('logout');
        $this->userController = $userController;
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
     * Generate Financial Report PDF
     */
    public function generatePdf($chapterId, $user_id)
    {
        // Load financial report data, chapter details, and any other data you need
        $financial_report = FinancialReport::find($chapterId);

        $user = User::find($user_id);
        $userName = $user['first_name'].' '.$user['last_name'];
        $userEmail = $user['email'];

        $chapterDetails = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.ein as ein', 'chapters.territory as boundaries',
                'chapters.financial_report_received as financial_report_received', 'st.state_short_name as state',
                'chapters.conference_id as conf', 'chapters.primary_coordinator_id as pcid')
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('chapters.id', '=', $chapterId)
            ->get();

        $submitted = $financial_report[0]->submitted;
        $submittedFormatted = $submitted->format('m-d-Y');

        $sanitizedChapterName = str_replace(['/', '\\'], '-', $chapterDetails[0]->chapter_name);

        $pdfData = [
            'chapter_name' => $chapterDetails[0]->chapter_name,
            'state' => $chapterDetails[0]->state,
            'ein' => $chapterDetails[0]->ein,
            'boundaries' => $chapterDetails[0]->boundaries,
            'changed_dues' => $financial_report->changed_dues,
            'different_dues' => $financial_report->different_dues,
            'not_all_full_dues' => $financial_report->not_all_full_dues,
            'total_new_members' => $financial_report->total_new_members,
            'total_renewed_members' => $financial_report->total_renewed_members,
            'dues_per_member' => $financial_report->dues_per_member,
            'total_new_members_changed_dues' => $financial_report->total_new_members_changed_dues,
            'total_renewed_members_changed_dues' => $financial_report->total_renewed_members_changed_dues,
            'dues_per_member_renewal' => $financial_report->dues_per_member_renewal,
            'dues_per_member_new_changed' => $financial_report->dues_per_member_new_changed,
            'dues_per_member_renewal_changed' => $financial_report->dues_per_member_renewal_changed,
            'members_who_paid_no_dues' => $financial_report->members_who_paid_no_dues,
            'members_who_paid_partial_dues' => $financial_report->members_who_paid_partial_dues,
            'total_partial_fees_collected' => $financial_report->total_partial_fees_collected,
            'total_associate_members' => $financial_report->total_associate_members,
            'associate_member_fee' => $financial_report->associate_member_fee,
            'manditory_meeting_fees_paid' => $financial_report->manditory_meeting_fees_paid,
            'voluntary_donations_paid' => $financial_report->voluntary_donations_paid,
            'paid_baby_sitters' => $financial_report->paid_baby_sitters,
            'childrens_room_expenses' => $financial_report->childrens_room_expenses,
            'service_project_array' => $financial_report->service_project_array,
            'party_expense_array' => $financial_report->party_expense_array,
            'office_printing_costs' => $financial_report->office_printing_costs,
            'office_postage_costs' => $financial_report->office_postage_costs,
            'office_membership_pins_cost' => $financial_report->office_membership_pins_cost,
            'office_other_expenses' => $financial_report->office_other_expenses,
            'international_event_array' => $financial_report->international_event_array,
            'annual_registration_fee' => $financial_report->annual_registration_fee,
            'monetary_donations_to_chapter' => $financial_report->monetary_donations_to_chapter,
            'non_monetary_donations_to_chapter' => $financial_report->non_monetary_donations_to_chapter,
            'other_income_and_expenses_array' => $financial_report->other_income_and_expenses_array,
            'amount_reserved_from_previous_year' => $financial_report->amount_reserved_from_previous_year,
            'bank_balance_now' => $financial_report->bank_balance_now,
            'petty_cash' => $financial_report->petty_cash,
            'bank_reconciliation_array' => $financial_report->bank_reconciliation_array,
            'receive_compensation' => $financial_report->receive_compensation,
            'receive_compensation_explanation' => $financial_report->receive_compensation_explanation,
            'financial_benefit' => $financial_report->financial_benefit,
            'financial_benefit_explanation' => $financial_report->financial_benefit_explanation,
            'influence_political' => $financial_report->influence_political,
            'influence_political_explanation' => $financial_report->influence_political_explanation,
            'vote_all_activities' => $financial_report->vote_all_activities,
            'vote_all_activities_explanation' => $financial_report->vote_all_activities_explanation,
            'purchase_pins' => $financial_report->purchase_pins,
            'purchase_pins_explanation' => $financial_report->purchase_pins_explanation,
            'bought_merch' => $financial_report->bought_merch,
            'bought_merch_explanation' => $financial_report->bought_merch_explanation,
            'offered_merch' => $financial_report->offered_merch,
            'offered_merch_explanation' => $financial_report->offered_merch_explanation,
            'bylaws_available' => $financial_report->bylaws_available,
            'bylaws_available_explanation' => $financial_report->bylaws_available_explanation,
            'childrens_room_sitters' => $financial_report->childrens_room_sitters,
            'childrens_room_sitters_explanation' => $financial_report->childrens_room_sitters_explanation,
            'had_playgroups' => $financial_report->had_playgroups,
            'playgroups' => $financial_report->playgroups,
            'had_playgroups_explanation' => $financial_report->had_playgroups_explanation,
            'child_outings' => $financial_report->child_outings,
            'child_outings_explanation' => $financial_report->child_outings_explanation,
            'mother_outings' => $financial_report->mother_outings,
            'mother_outings_explanation' => $financial_report->mother_outings_explanation,
            'meeting_speakers' => $financial_report->meeting_speakers,
            'meeting_speakers_explanation' => $financial_report->meeting_speakers_explanation,
            'meeting_speakers_array' => $financial_report->meeting_speakers_array,
            'discussion_topic_frequency' => $financial_report->discussion_topic_frequency,
            'park_day_frequency' => $financial_report->park_day_frequency,
            'activity_array' => $financial_report->activity_array,
            'contributions_not_registered_charity' => $financial_report->contributions_not_registered_charity,
            'contributions_not_registered_charity_explanation' => $financial_report->contributions_not_registered_charity_explanation,
            'at_least_one_service_project' => $financial_report->at_least_one_service_project,
            'at_least_one_service_project_explanation' => $financial_report->at_least_one_service_project_explanation,
            'sister_chapter' => $financial_report->sister_chapter,
            'international_event' => $financial_report->international_event,
            'file_irs' => $financial_report->file_irs,
            'file_irs_explanation' => $financial_report->file_irs_explanation,
            'bank_statement_included' => $financial_report->bank_statement_included,
            'bank_statement_included_explanation' => $financial_report->bank_statement_included_explanation,
            'wheres_the_money' => $financial_report->wheres_the_money,
            'completed_name' => $userName,
            'completed_email' => $userEmail,
            'submitted' => $submittedFormatted,
            'ch_name' => $sanitizedChapterName,
        ];

        $pdf = Pdf::loadView('pdf.financialreport', compact('pdfData'));

        $filename = date('Y') - 1 .'-'.date('Y').'_'.$pdfData['state'].'_'.$pdfData['ch_name'].'_FinancialReport.pdf';

        return $pdf->stream($filename, ['Attachment' => 0]); // Stream the PDF

    }

    /**
     * Generate Chaper in Good Standing PDF All Board Members
     */
    public function generateGoodStanding($chapterId)
    {
        $chapterDetails = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.ein as ein', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name',
                'st.state_short_name as state', 'bd.first_name as pres_fname', 'bd.last_name as pres_lname', 'chapters.conference_id as conf',
                'cf.conference_name as conf_name', 'cf.conference_description as conf_desc', 'chapters.primary_coordinator_id as pcid')
            ->leftJoin('coordinators as cd', 'cd.id', '=', 'chapters.primary_coordinator_id')
            ->leftJoin('boards as bd', 'bd.chapter_id', '=', 'chapters.id')
            ->leftJoin('conference as cf', 'chapters.conference_id', '=', 'cf.id')
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('bd.board_position_id', '=', '1')
            ->where('chapters.id', '=', $chapterId)
            ->get();

        // Load Conference Coordinators for Signing Letter
        $chName = $chapterDetails[0]->chapter_name;
        $chState = $chapterDetails[0]->state;
        $chConf = $chapterDetails[0]->conf;
        $chPcid = $chapterDetails[0]->pcid;

        $coordinatorData = $this->userController->loadConferenceCoord($chPcid);
        $cc_fname = $coordinatorData['cc_fname'];
        $cc_lname = $coordinatorData['cc_lname'];
        $cc_pos = $coordinatorData['cc_pos'];

        $sanitizedChapterName = str_replace(['/', '\\'], '-', $chapterDetails[0]->chapter_name);

        $pdfData = [
            'chapter_name' => $chapterDetails[0]->chapter_name,
            'state' => $chapterDetails[0]->state,
            'conf_name' => $chapterDetails[0]->conf_name,
            'conf_desc' => $chapterDetails[0]->conf_desc,
            'ein' => $chapterDetails[0]->ein,
            'pres_fname' => $chapterDetails[0]->pres_fname,
            'pres_lname' => $chapterDetails[0]->pres_lname,
            'cc_fname' => $cc_fname,
            'cc_lname' => $cc_lname,
            'cc_pos' => $cc_pos,
            'ch_name' => $sanitizedChapterName,
        ];

        $pdf = Pdf::loadView('pdf.chapteringoodstanding', compact('pdfData'));

        $filename = date('Y') - 1 .'-'.date('Y').'_'.$pdfData['state'].'_'.$pdfData['ch_name'].'_ChapterInGoodStanding.pdf';

        return $pdf->stream($filename, ['Attachment' => 0]); // Stream the PDF

    }

    /**
     * Save & Send Disband Letter
     */
    public function generateAndSaveGoodStandingLetter($chapterId)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'documents', 'financialReport', 'startMonth', 'boards', 'primaryCoordinator'])->find($chapterId);
        $stateShortName = $chDetails->state->state_short_name;
        $chPcId = $chDetails->primary_coordinator_id;

        $sanitizedChapterName = str_replace(['/', '\\'], '-', $chDetails->name);
        $goodStandingDrive = DB::table('google_drive')->value('good_standing_letter');
        $sharedDriveId = $goodStandingDrive;

        $boards = $chDetails->boards()->get();
        $borDetails = $boards->groupBy('board_position_id');
        $presDetails = $borDetails->get(1)->first(); // President

        // Load Board and Coordinators for Sending Email
        $emailData = $this->userController->loadEmailDetails($chapterId);
        $emailListChap = $emailData['emailListChap'];
        $emailListCoord = $emailData['emailListCoord'];

        // Load Conference Coordinators for Sending Email
        $ccData = $this->userController->loadConferenceCoord($chPcId);
        $emailCC = $ccData['cc_email'];
        $cc_fname = $ccData['cc_fname'];
        $cc_lname = $ccData['cc_lname'];
        $cc_pos = $ccData['cc_pos'];

        $pdfData = [
            'chapter_name' => $chDetails->chapter_name,
            'state' => $stateShortName,
            'ein' => $chDetails->ein,
            'pres_fname' => $presDetails->first_name,
            'pres_lname' => $presDetails->last_name,
            'pres_addr' => $presDetails->street_address,
            'pres_city' => $presDetails->city,
            'pres_state' => $presDetails->state,
            'pres_zip' => $presDetails->zip,
            'cc_fname' => $ccData['cc_fname'],
            'cc_lname' => $ccData['cc_lname'],
            'cc_pos' => $ccData['cc_pos'],
            'conf_name' => $ccData['cc_conf_name'],
            'conf_desc' => $ccData['cc_conf_desc'],
            'ch_name' => $sanitizedChapterName,
        ];


        $pdf = Pdf::loadView('pdf.chapteringoodstanding', compact('pdfData'));

        $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
        $filename = $pdfData['state'].'_'.$chapterName.'_ChapterInGoodStanding.pdf'; // Use sanitized chapter name

        $pdfPath = storage_path('app/pdf_reports/'.$filename);
        $pdf->save($pdfPath);

        if ($this->uploadToGoogleDrive($pdfPath, $pdfFileId, $sharedDriveId)){
            $documents = Documents::find($chapterId);
            $documents->good_standing_letter = $pdfFileId;
            $documents->save();

            return $pdfPath;  // Return the full local stored path
        }
    }

    /**
     * Save & Send Disband Letter
     */
    public function generateAndSaveDisbandLetter($chapterId)
    {
        $chDetails = Chapters::with(['country', 'state', 'conference', 'region', 'documents', 'financialReport', 'startMonth', 'boards', 'primaryCoordinator'])->find($chapterId);
        $stateShortName = $chDetails->state->state_short_name;
        $chPcId = $chDetails->primary_coordinator_id;

        $sanitizedChapterName = str_replace(['/', '\\'], '-', $chDetails->name);
        $disbandDrive = DB::table('google_drive')->value('disband_letter');
        $sharedDriveId = $disbandDrive;

        $boards = $chDetails->boards()->get();
        $borDetails = $boards->groupBy('board_position_id');
        $presDetails = $borDetails->get(1)->first(); // President

        // Load Board and Coordinators for Sending Email
        $emailData = $this->userController->loadEmailDetails($chapterId);
        $emailListChap = $emailData['emailListChap'];
        $emailListCoord = $emailData['emailListCoord'];

        // Load Conference Coordinators for Sending Email
        $ccData = $this->userController->loadConferenceCoord($chPcId);
        $emailCC = $ccData['cc_email'];
        $cc_fname = $ccData['cc_fname'];
        $cc_lname = $ccData['cc_lname'];
        $cc_pos = $ccData['cc_pos'];

        $pdfData = [
            'chapter_name' => $chDetails->chapter_name,
            'state' => $stateShortName,
            'pres_fname' => $presDetails->first_name,
            'pres_lname' => $presDetails->last_name,
            'pres_addr' => $presDetails->street_address,
            'pres_city' => $presDetails->city,
            'pres_state' => $presDetails->state,
            'pres_zip' => $presDetails->zip,
            'cc_fname' => $ccData['cc_fname'],
            'cc_lname' => $ccData['cc_lname'],
            'cc_pos' => $ccData['cc_pos'],
            'conf_name' => $ccData['cc_conf_name'],
            'conf_desc' => $ccData['cc_conf_desc'],
            'ch_name' => $sanitizedChapterName,
        ];

        $pdf = Pdf::loadView('pdf.disbandletter', compact('pdfData'));

        $chapterName = str_replace('/', '', $pdfData['chapter_name']); // Remove any slashes from chapter name
        $filename = $pdfData['state'].'_'.$chapterName.'_Disband_Letter.pdf'; // Use sanitized chapter name

        $pdfPath = storage_path('app/pdf_reports/'.$filename);
        $pdf->save($pdfPath);

        if ($this->uploadToGoogleDrive($pdfPath, $pdfFileId, $sharedDriveId)){
            $documents = Documents::find($chapterId);
            $documents->disband_letter_path = $pdfFileId;
            $documents->save();

            return $pdfPath;  // Return the full local stored path
        }
    }

    /**
     * Save & Send Probation Letter
     */
    public function saveProbationLetter(Request $request): JsonResponse
    {
        $chapterId = $request->chapterId;
        $letterType = $request->letterType;
        $probationDrive = DB::table('google_drive')->value('probation_letter');
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

        $result = $this->generateProbationLetter($chapterId, $type);
        $pdf = $result['pdf'];
        $filename = $result['filename'];

        $pdfPath = storage_path('app/pdf_reports/'.$filename);
        $pdf->save($pdfPath);

        if ($this->uploadToGoogleDrive($pdfPath, $pdfFileId, $sharedDriveId)) {
            $chDetails = Chapters::with(['state', 'documents'])->find($chapterId);
            $document = $chDetails->documents;
            $stateShortName = $chDetails->state->state_short_name;

            if ($letterType === 'probation_release') {
                $document->probation_release_path = $pdfFileId;
            } else {
                $document->probation_path = $pdfFileId;
            }

            $document->save();

            $emailData = $this->userController->loadEmailDetails($chapterId, true);
            $emailListChap = $emailData['emailListChap'];
            $emailListCoord = $emailData['emailListCoord'];

            $ccData = $this->userController->loadConferenceCoord(
                $chDetails->primary_coordinator_id
            );

            $mailData = [
                'chapterName' => $chDetails->name,
                'chapterEmail' => $chDetails->email,
                'chapterState' => $stateShortName,
                'cc_fname' => $ccData['cc_fname'],
                'cc_lname' => $ccData['cc_lname'],
                'cc_pos' => $ccData['cc_pos'],
                'cc_conf_name' => $ccData['cc_conf_name'],
                'cc_conf_desc' => $ccData['cc_conf_desc'],
                'cc_email' => $ccData['cc_email'],
            ];

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
    public function generateProbationLetter(Request $chapterId, $type)
    {
        $chDetails = Chapters::with(['state', 'boards'])->find($chapterId);
        $stateShortName = $chDetails->state->state_short_name;

        $sanitizedChapterName = str_replace(['/', '\\'], '-', $chDetails->name);

        $chConfId = $chDetails->conference;
        $chPcId = $chDetails->primary_coordinator_id;

        $ccData = $this->userController->loadConferenceCoord($chPcId);

        $boards = $chDetails->boards()->get();
        $borDetails = $boards->groupBy('board_position_id');
        $presDetails = $borDetails->get(1)->first(); // President

        $pdfData = [
            'chapter_name' => $chDetails->name,
            'state' => $stateShortName,
            'pres_fname' => $presDetails->first_name,
            'pres_lname' => $presDetails->last_name,
            'pres_addr' => $presDetails->street_address,
            'pres_city' => $presDetails->city,
            'pres_state' => $presDetails->state,
            'pres_zip' => $presDetails->zip,
            'cc_fname' => $ccData['cc_fname'],
            'cc_lname' => $ccData['cc_lname'],
            'cc_pos' => $ccData['cc_pos'],
            'cc_conf_name' => $ccData['cc_conf_name'],
            'cc_conf_desc' => $ccData['cc_conf_desc'],
            'ch_name' => $sanitizedChapterName,
        ];

        $type = strtolower($type);
        $view = match ($type) {
            'no_report' => 'pdf.probationreport',
            'no_payment' => 'pdf.probationpayment',
            'probation_party' => 'pdf.probationparty',
            'warning_party' => 'pdf.warningparty',
            'probation_release' => 'pdf.probationrelease',
        };

        $pdf = Pdf::loadView($view, compact('pdfData'));

        $filename = $pdfData['state'].'_'.$pdfData['ch_name']."_{$type}_Letter.pdf";

        // if ($request->has('stream')) {
        //     return $pdf->stream($filename, ['Attachment' => 0]);
        // }

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
}
