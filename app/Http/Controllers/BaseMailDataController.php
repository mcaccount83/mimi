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

    /**
     *  Get Basic Chapter Mail Data Information
     */
    public function getChapterBasicData($chDetails, $stateShortName)
    {
        return [
            // 'chapterId' => $chDetails->id,
            'chapterName' => $chDetails->name,
            'chapterNameSanitized' => $chDetails->sanitized_name,
            'chapterState' => $stateShortName,
            'chapterConf' => $chDetails->conference_id,
            'chapterEIN' => $chDetails->ein,
            'chapterBoundaries' => $chDetails->territory,
            'chapterEmail' => $chDetails->email,
        ];
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

    public function getPresData($PresDetails)
    {
        return [
            'presName' => $PresDetails->first_name.' '.$PresDetails->last_name,
            'presAddress' => $PresDetails->street_address,
            'presCity' => $PresDetails->city,
            'presState' => $PresDetails->state,
            'presZip' => $PresDetails->zip,
            'presEmail' => $PresDetails->email,
        ];
    }

    public function getPCData($pcDetails)
    {
        return [
            'pcEmail' => $pcDetails->email,
            'pcName' => $pcDetails->first_name.' '.$pcDetails->last_name,
        ];
    }

    public function getChapterPreviousData($chDetailsPre, $pcDetailsPre)
    {
        return [
            'chapterNamePre' => $chDetailsPre->name,
            'boundariesPre' => $chDetailsPre->territory,
            'statusPre' => $chDetailsPre->status_id,
            'notesPre' => $chDetailsPre->notes,
            'inquiriesContactPre' => $chDetailsPre->inquiries_contact,
            'inquiriesNotesPre' => $chDetailsPre->inquiries_note,
            'chapterEmailPre' => $chDetailsPre->email,
            'poBoxPre' => $chDetailsPre->po_box,
            'additionalInfoPre' => $chDetailsPre->additional_info,
            'websiteURLPre' => $chDetailsPre->website_url,
            'websiteStatusPre' => $chDetailsPre->website_status,
            'pcNamePre' => $pcDetailsPre->first_name.' '.$pcDetailsPre->last_name,
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

    public function getPresPreviousData($PresDetailsPre)
    {
        return [
            'presNamePre' => $PresDetailsPre->first_name.' '.$PresDetailsPre->last_name,
            'presAddressPre' => $PresDetailsPre->street_address,
            'presCityPre' => $PresDetailsPre->city,
            'presStatePre' => $PresDetailsPre->state,
            'presZipPre' => $PresDetailsPre->zip,
            'presPhpnePre' => $PresDetailsPre->phone,
            'presEmailPre' => $PresDetailsPre->email,
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

    public function getBoardPreviousData($borDetailsPre)
    {
        return [
            'borNamePre' => $borDetailsPre->first_name.' '.$borDetailsPre->last_name,
            'borAddressPre' => $borDetailsPre->street_address,
            'borCityPre' => $borDetailsPre->city,
            'borStatePre' => $borDetailsPre->state,
            'borZipPre' => $borDetailsPre->zip,
            'borPhpnePre' => $borDetailsPre->phone,
            'borEmailPre' => $borDetailsPre->email,
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
}
