<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;

class BaseMailDataController extends Controller
{
    protected $userController;

    protected $baseChapterController;

    protected $baseCoordinatorController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseCoordinatorController $baseCoordinatorController)
    {
        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
    }

    public function getUserData($user)
    {
        return [
            'userName' => $user['user_name'],
            'userPosition' => $user['user_position'],
            'userConfName' => $user['user_conf_name'],
            'userConfDesc' => $user['user_conf_desc'],
            'userEmail' => $user['user_email'],
        ];
    }

    public function getMessageData($input)
    {
        return [
            'founderEmail' => $input['founderEmail'] ?? null,
            'founderFirstName' => $input['founderFirstName'] ?? null,
            'founderLastName' => $input['founderLastName'] ?? null,
            'boundaryDetails' => $input['boundaryDetails'] ?? null,
            'nameDetails' => $input['nameDetails'] ?? null,
            'message' => $input['message'] ?? null,
            'subject' => $input['subject'] ?? null,
        ];
    }

    public function getChapterData($chDetails, $stateShortName)
    {
        return [
            'chapterName' => $chDetails->name,
            'chapterNameSanitized' => $chDetails->sanitized_name,
            // 'stateId' => (int)$chDetails->state_id,
            'chapterState' => $stateShortName,
            // 'chapterState' => $chDetails->state->state_short_name,
            // 'chapterCountry' => $chDetails->country->short_name,
            'chapterConf' => $chDetails->conference_id,
            'chapterEIN' => $chDetails->ein,
            'chapterBoundaries' => $chDetails->territory,
            'chapterStatus' => $chDetails->status->chapter_status,
            'chapterNotes' => $chDetails->notes,
            'chapterInquiriesContact' => $chDetails->inquiries_contact,
            'chapterInquiriesNotes' => $chDetails->inquiries_note,
            'chapterEmail' => $chDetails->email,
            'chapterPOBox' => $chDetails->po_box,
            'chapterAdditionalInfo' => $chDetails->additional_info,
            'chapterWebsiteURL' => $chDetails->website_url,
            'chapterWebsiteStatus' => $chDetails->website?->link_status,
            'egroup' => $chDetails->egroup,
            'facebook' => $chDetails->social1,
            'twitter' => $chDetails->social2,
            'instagram' => $chDetails->social3,
        ];
    }

    public function getPresData($PresDetails)
    {
        return [
            'presName' => $PresDetails->first_name.' '.$PresDetails->last_name,
            'presAddress' => $PresDetails->street_address,
            'presCity' => $PresDetails->city,
            'stateId' => (int)$PresDetails->state_id,
            'presState' => $PresDetails->state->state_short_name,
            'presCountry' => $PresDetails->country->short_name,
            'presZip' => $PresDetails->zip,
            'presPhone' => $PresDetails->phone,
            'presEmail' => $PresDetails->email,
        ];
    }

    public function getBoardData($borDetails)
    {
        return [
            'boardName' => $borDetails->first_name.' '.$borDetails->last_name,
            'boardAddress' => $borDetails->street_address,
            'boardCity' => $borDetails->city,
            'stateId' => (int)$borDetails->state_id,
            'boardState' => $borDetails->state->state_short_name,
            'boardCountry' => $borDetails->country->short_name,
            'boardZip' => $borDetails->zip,
            'boardPhone' => $borDetails->phone,
            'boardEmail' => $borDetails->email,
        ];
    }

    public function getBoardEmail($PresDetails, $AVPDetails, $MVPDetails, $TRSDetails, $SECDetails)
    {
         return [
            'presName' => $PresDetails->first_name.' '.$PresDetails->last_name,
            'presEmail' => $PresDetails->email,
            'avpName' => $AVPDetails->first_name.' '.$AVPDetails->last_name,
            'avpEmail' => $AVPDetails->email,
            'mvpName' => $MVPDetails->first_name.' '.$MVPDetails->last_name,
            'mvpEmail' => $MVPDetails->email,
            'trsName' => $TRSDetails->first_name.' '.$TRSDetails->last_name,
            'trsEmail' => $TRSDetails->email,
            'secName' => $SECDetails->first_name.' '.$SECDetails->last_name,
            'secEmail' => $SECDetails->email,
        ];
    }

    public function getBoardUpdEmail($PresDetailsUpd, $AVPDetailsUpd, $MVPDetailsUpd, $TRSDetailsUpd, $SECDetailsUpd)
    {
         return [
            'presNameUpd' => $PresDetailsUpd->first_name.' '.$PresDetailsUpd->last_name,
            'presEmailUpd' => $PresDetailsUpd->email,
            'avpNameUpd' => $AVPDetailsUpd->first_name.' '.$AVPDetailsUpd->last_name,
            'avpEmailUpd' => $AVPDetailsUpd->email,
            'mvpNameUpd' => $MVPDetailsUpd->first_name.' '.$MVPDetailsUpd->last_name,
            'mvpEmailUpd' => $MVPDetailsUpd->email,
            'trsNameUpd' => $TRSDetailsUpd->first_name.' '.$TRSDetailsUpd->last_name,
            'trsEmailUpd' => $TRSDetailsUpd->email,
            'secNameUpd' => $SECDetailsUpd->first_name.' '.$SECDetailsUpd->last_name,
            'secEmailUpd' => $SECDetailsUpd->email,
        ];
    }

    public function getPCData($pcDetails)
    {
        return [
            'pcEmail' => $pcDetails->email,
            'pcName' => $pcDetails->first_name.' '.$pcDetails->last_name,
        ];
    }

    public function getRCData($rcDetails)
    {
        return [
            'rcEmail' => $rcDetails->email,
            'rcName' => $rcDetails->first_name.' '.$rcDetails->last_name,
            // 'rcPosition' => $rcDetails->position->position,
        ];
    }

     public function getCDData($cdDetails)
    {
        return [
            'cdEmail' => $cdDetails->email,
            'cdName' => $cdDetails->first_name.' '.$cdDetails->last_name,
            // 'cdPosition' => $cdDetails->position->position,
        ];
    }

    public function getCCData($emailCCData)
    {
        return [
            'ccEmail' => $emailCCData['cc_email'],
            'ccName' => $emailCCData['cc_fname'].' '.$emailCCData['cc_lname'],
            'ccPosition' => $emailCCData['cc_pos'],
            'ccConfName' => $emailCCData['cc_conf_name'],
            'ccConfDescription' => $emailCCData['cc_conf_desc'],
            'ccPhone' => $emailCCData['cc_phone'],
        ];
    }

    public function getEINCoorData($emailEINCoorData)
    {
        return [
            'einEmail' => $emailEINCoorData['ein_email'],
            'einName' => $emailEINCoorData['ein_fname'].' '.$emailEINCoorData['ein_lname'],
            'einPhone' => $emailEINCoorData['ein_phone'],
        ];
    }

    public function getChapterUpdatedData($chDetailsUpd, $pcDetailsUpd)
    {
        return [
            'chapterNameUpd' => $chDetailsUpd->name,
            'boundariesUpd' => $chDetailsUpd->territory,
            'statusUpd' => $chDetailsUpd->status->chapter_status,
            'notesUpd' => $chDetailsUpd->notes,
            'inquiriesContactUpd' => $chDetailsUpd->inquiries_contact,
            'inquiriesNotesUpd' => $chDetailsUpd->inquiries_note,
            'chapterEmailUpd' => $chDetailsUpd->email,
            'poBoxUpd' => $chDetailsUpd->po_box,
            'additionalInfoUpd' => $chDetailsUpd->additional_info,
            'websiteURLUpd' => $chDetailsUpd->website_url,
            'websiteStatusUpd' => $chDetailsUpd->website?->link_status,
            'egroupUpd' => $chDetailsUpd->egroup,
            'facebookUpd' => $chDetailsUpd->social1,
            'twitterUpd' => $chDetailsUpd->social2,
            'instagramUpd' => $chDetailsUpd->social3,
            'pcNameUpd' => $pcDetailsUpd->first_name.' '.$pcDetailsUpd->last_name,
            'pcEmailUpd' => $pcDetailsUpd->email,
        ];
    }

    public function getPresUpdatedData($PresDetailsUpd)
    {
        return [
            'presNameUpd' => $PresDetailsUpd->first_name.' '.$PresDetailsUpd->last_name,
            'presAddressUpd' => $PresDetailsUpd->street_address,
            'presCityUpd' => $PresDetailsUpd->city,
            'stateId' => (int)$PresDetailsUpd->state_id,
            'presState' => $PresDetailsUpd->state->state_short_name,
            'presCountry' => $PresDetailsUpd->country->short_name,
            'presZipUpd' => $PresDetailsUpd->zip,
            'presPhoneUpd' => $PresDetailsUpd->phone,
            'presEmailUpd' => $PresDetailsUpd->email,
        ];
    }

    public function getBoardUpdatedData($borDetailsUpd)
    {
        return [
            'borPosition' => $borDetailsUpd->position->position,
            'borNameUpd' => $borDetailsUpd->first_name.' '.$borDetailsUpd->last_name,
            'borAddressUpd' => $borDetailsUpd->street_address,
            'borCityUpd' => $borDetailsUpd->city,
            'stateId' => (int)$borDetailsUpd->state_id,
            'borState' => $borDetailsUpd->state->state_short_name,
            'borCountry' => $borDetailsUpd->country->short_name,
            'borZipUpd' => $borDetailsUpd->zip,
            'borPhoneUpd' => $borDetailsUpd->phone,
            'borEmailUpd' => $borDetailsUpd->email,
        ];
    }

    public function getPCUpdatedData($pcDetailsUpd)
    {
        return [
            'pcEmailUpd' => $pcDetailsUpd->email,
            'pcNameUpd' => $pcDetailsUpd->first_name.' '.$pcDetailsUpd->last_name,
        ];
    }

    public function getFinancialReportData($chDocuments, $chFinancialReport)
    {
        return [
            'completedName' => $chFinancialReport->completed_name,
            'completedEmail' => $chFinancialReport->completed_email,
            'boardElectionReportReceived' => $chDocuments->new_board_submitted,
            'financialReportReceived' => $chDocuments->financial_report_received,
            '990NSubmissionReceived' => $chDocuments->irs_verified,
            'einLetterCopyReceived' => $chDocuments->ein_letter_path,
            'rosterPath' => $chDocuments->roster_path,
            'irsPath' => $chDocuments->irs_path,
            'statement1Path' => $chDocuments->statement_1_path,
            'statement2Path' => $chDocuments->statement_2_path,
            'financialPdfPath' => $chDocuments->financial_pdf_path,
            'reviewerEmailMessage' => $chFinancialReport->reviewer_email_message,
            'final_report_received' => $chDocuments->final_report_received,
            'financialFinalPdfPath' => $chDocuments->final_financial_pdf_path,
        ];
    }

    public function getPaymentData($chPayments, $input, $paymentType)
    {
        return [
            'chapterId' => $chPayments->chapter_id,
            'reregMembers' => $input['members'] ?? null,
            'reregPayment' => $input['rereg'] ?? null,
            'reregPaid' => \Carbon\Carbon::parse($chPayments->rereg_date)->format('m/d/Y') ?? null,
            'reregInvoice' => $chPayments->rereg_invoice,
            'sustainingDonation' => $input['sustaining'] ?? null,
            'donationInvoice' => $chPayments->donation_invoice,
            'm2mDonation' => $input['m2m'] ?? null,
            'donationInvoice' => $chPayments->m2m_invoice,
            'manualOrder' => $input['manual'] ?? null,
            'manualInvoice' => $chPayments->manual_invoice,
            'lateFee' => $input['late'] ?? null,
            'processingFee' => $input['fee'] ?? null,
            'totalPaid' => $input['total'] ?? null,
            'fname' => $input['first_name'] ?? null,
            'lname' => $input['last_name'] ?? null,
            'email' => $input['email'] ?? null,
            'paymentType' => $paymentType,
        ];
    }

    public function getShippingData($input, $shippingCountry, $shippingState)
    {
        return [
            'ship_fname' => $input['ship_fname'] ?? null,
            'ship_lname' => $input['ship_lname'] ?? null,
            'ship_street' => $input['ship_street'] ?? null,
            'ship_city' => $input['ship_city'] ?? null,
            'ship_state' => $shippingState,
            'ship_country' => $shippingCountry,
            'ship_zip' => $input['ship_zip'] ?? null,
            'ship_company' => $input['ship_company'] ?? null,
            'ship_email' => $input['ship_email'] ?? null,
            'ship_phone' => $input['ship_phone'] ?? null,
        ];
    }

    public function getPublicPaymentData($input, $invoice, $paymentType)
    {
        return [
            'invoice' => $invoice,
            'newchap' => $input['newchap'] ?? null,
            'sustainingDonation' => $input['sustaining'] ?? null,
            'm2mDonation' => $input['m2m'] ?? null,
            'processingFee' => $input['fee'] ?? null,
            'totalPaid' => $input['total'] ?? null,
            'fname' => $input['first_name'] ?? null,
            'lname' => $input['last_name'] ?? null,
            'email' => $input['email'] ?? null,
            'paymentType' => $paymentType,
        ];
    }

    public function getReRegData($startMonthId)
    {
        $startDate = Carbon::createFromDate(null, $startMonthId, 1);
        $year = $startDate->year;
        $monthInWords = $startDate->format('F');

        $rangeEndDate = $startDate->copy()->subMonth()->endOfMonth();
        $rangeStartDate = $rangeEndDate->copy()->startOfMonth()->subYear()->addMonth();

        $rangeStartDateFormatted = $rangeStartDate->format('m-d-Y');
        $rangeEndDateFormatted = $rangeEndDate->format('m-d-Y');

        $now = Carbon::now();
        $currentMonthInWords = $now->format('F');

        return [
            'startRange' => $rangeStartDateFormatted,
            'endRange' => $rangeEndDateFormatted,
            'startMonth' => $monthInWords,
            'dueMonth' => $currentMonthInWords,
        ];
    }

    public function getProbationData($input)
    {
        return [
            'q1_dues' => $input['q1_dues'] ?? null,
            'q1_benefit' => $input['q1_benefit'] ?? null,
            'q2_dues' => $input['q2_dues'] ?? null,
            'q2_benefit' => $input['q2_benefit'] ?? null,
            'q3_dues' => $input['q3_dues'] ?? null,
            'q3_benefit' => $input['q3_benefit'] ?? null,
            'q4_dues' => $input['q4_dues'] ?? null,
            'q4_benefit' => $input['q4_benefit'] ?? null,

            'q1_percentage' => $input['q1_percentage'] ?? null,
            'q2_percentage' => $input['q2_percentage'] ?? null,
            'q3_percentage' => $input['q3_percentage'] ?? null,
            'q4_percentage' => $input['q4_percentage'] ?? null,

            'TotalDues' => $input['TotalDues'] ?? null,
            'TotalBenefit' => $input['TotalBenefit'] ?? null,
            'TotalPercentage' => $input['TotalPercentage'] ?? null,
        ];
    }

     public function getNewChapterAppData($input, $sisteredWords)
    {
        return [
            'hear_about' => $input['ch_hearabout'] ?? null,
            'sistered' => $sisteredWords,
            'sistered_by' => $input['ch_sisteredby'] ?? null,
        ];
    }

    public function getNewChapterData($chDetails)
    {
        return [
            'chapterName' => $chDetails->name,
            'chapterNameSanitized' => $chDetails->sanitized_name,
            'stateId' => (int)$chDetails->state_id,
            'chapterState' => $chDetails->state->state_short_name,
            'chapterCountry' => $chDetails->country->short_name,
            'chapterConf' => $chDetails->conference_id,
            'chapterBoundaries' => $chDetails->territory,
            'chapterInquiriesContact' => $chDetails->inquiries_contact,
            'chapterStatus' => $chDetails->status->chapter_status,
            'founderName' => $chDetails->pendingPresident->first_name.' '.$chDetails->pendingPresident->last_name,
            'founderEmail' => $chDetails->pendingPresident->email,
            'founderPhone' => $chDetails->pendingPresident->phone,
            'founderAddress' => $chDetails->pendingPresident->street_address,
            'founderCity' => $chDetails->pendingPresident->city,
            'founderState' => $chDetails->pendingPresident->state->state_short_name,
            'founderZip' => $chDetails->pendingPresident->zip,
            'founderCountry' => $chDetails->pendingPresident->country->short_name,
        ];
    }

    public function getNewCoordinatorData($cdDetails)
    {
        return [
            'conference_id' => $cdDetails->conference_id,
            'region' => $cdDetails->region->short_name,
            'first_name' => $cdDetails->first_name,
            'last_name' => $cdDetails->last_name,
            'position' => $cdDetails->mimiPosition->position,
            'display_position' => $cdDetails->displayPosition->position,
            'email' => $cdDetails->email,
            'sec_email' => $cdDetails->sec_email,
            'report_id' => $cdDetails->reportsTo->first_name.' '.$cdDetails->reportsTo->last_name,
            'address' => $cdDetails->address,
            'city' => $cdDetails->city,
            'state' => $cdDetails->state->state_short_name,
            'zip' => $cdDetails->zip,
            'country' => $cdDetails->country->short_name,
            'phone' => $cdDetails->phone,
            'alt_phone' => $cdDetails->alt_phone,
            'coordinator_start_date' => $cdDetails->coordinator_start_date,
        ];
    }

     public function getNewCoordinatorAppData($input)
    {
        return [
            'home_chapter' => $input['home_chapter'].', '.$input['home_state'],
            'start_date' => $input['start_date'],
            'jobs_programs' => $input['jobs_programs'],
            'helped_me' => $input['helped_me'],
            'problems' => $input['problems'],
            'why_volunteer' => $input['why_volunteer'],
            'other_volunteer' => $input['other_volunteer'],
            'special_skills' => $input['special_skills'],
            'enjoy_volunteering' => $input['enjoy_volunteering'],
            'referred_by' => $input['referred_by'] ?? null,
        ];
    }
}
