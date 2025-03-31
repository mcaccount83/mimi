<?php

namespace App\Http\Controllers;

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

    public function getChapterData($chDetails, $stateShortName)
    {
        return [
            'chapterName' => $chDetails->name,
            'chapterNameSanitized' => $chDetails->sanitized_name,
            'chapterState' => $stateShortName,
            'chapterConf' => $chDetails->conference_id,
            'chapterEIN' => $chDetails->ein,
            'chapterBoundaries' => $chDetails->territory,
            'chapterStatus' => $chDetails->status_id,
            'chapterNotes' => $chDetails->notes,
            'chapterInquiriesContact' => $chDetails->inquiries_contact,
            'chapterInquiriesNotes' => $chDetails->inquiries_note,
            'chapterEmail' => $chDetails->email,
            'chapterPOBox' => $chDetails->po_box,
            'chapterAdditionalInfo' => $chDetails->additional_info,
            'chapterWebsiteURL' => $chDetails->website_url,
            'chapterWebsiteStatus' => $chDetails->website_status,
        ];
    }

    public function getPresData($PresDetails)
    {
        return [
            'presName' => $PresDetails->first_name.' '.$PresDetails->last_name,
            'presAddress' => $PresDetails->street_address,
            'presCity' => $PresDetails->city,
            'presState' => $PresDetails->state,
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
            'boardState' => $borDetails->state,
            'boardZip' => $borDetails->zip,
            'boardPhone' => $borDetails->phone,
            'boardEmail' => $borDetails->email,
        ];
    }

    public function getPCData($pcDetails)
    {
        return [
            'pcEmail' => $pcDetails->email,
            'pcName' => $pcDetails->first_name.' '.$pcDetails->last_name,
        ];
    }

    public function getCCData($emailCCData)
    {
        return [
            'ccEmail' => $emailCCData['cc_email'],
            'ccName' => $emailCCData['cc_fname'].' '. $emailCCData['cc_lname'],
            'ccPosition' => $emailCCData['cc_pos'],
            'ccConfName' => $emailCCData['cc_conf_name'],
            'ccConfDescription' => $emailCCData['cc_conf_desc'],
        ];
    }

    public function getChapterUpdatedData($chDetailsUpd, $pcDetailsUpd)
    {
        return [
            'chapterNameUpd' => $chDetailsUpd->name,
            'boundariesUpd' => $chDetailsUpd->territory,
            'statusUpd' => $chDetailsUpd->status_id,
            'notesUpd' => $chDetailsUpd->notes,
            'inquiriesContactUpd' => $chDetailsUpd->inquiries_contact,
            'inquiriesNotesUpd' => $chDetailsUpd->inquiries_note,
            'chapterEmailUpd' => $chDetailsUpd->email,
            'poBoxUpd' => $chDetailsUpd->po_box,
            'additionalInfoUpd' => $chDetailsUpd->additional_info,
            'websiteURLUpd' => $chDetailsUpd->website_url,
            'websiteStatusUpd' => $chDetailsUpd->website_status,
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
            'presStateUpd' => $PresDetailsUpd->state,
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
            'borStateUpd' => $borDetailsUpd->state,
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

    public function getPaymentData($chPayments, $input)
    {
        return [
            'chapterId' => $chPayments->chapter_id,
            'reregMembers' => $chPayments->rereg_members,
            'reregPayment' => $chPayments->rereg_payment,
            'reregPaid' => \Carbon\Carbon::parse($chPayments->rereg_date)->format('m/d/Y'),
            'reregInvoice' => $chPayments->rereg_invoice,
            'sustainingDonation' => $chPayments->sustaining_donation,
            'sustainingPaid' => \Carbon\Carbon::parse($chPayments->sustaining_date)->format('m/d/Y'),
            'm2mDonation' => $chPayments->m2m_donation,
            'm2mPaid' => \Carbon\Carbon::parse($chPayments->m2m_date)->format('m/d/Y'),
            'donationInvoice' => $chPayments->donation_invoice,
            'lateFee' => $input['late'] ?? null,
            'processingFee' => $input['fee'] ?? null,
            'totalPaid' => $input['total'] ?? null,
            'fname' => $input['first_name'] ?? null,
            'lname' => $input['last_name'] ?? null,
            'email' => $input['email'] ?? null,
        ];
    }

}
