<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckCurrentPasswordBoardRequest;
use App\Http\Requests\UpdatePasswordBoardRequest;
use App\Mail\EOYElectionReportSubmitted;
use App\Mail\EOYElectionReportThankYou;
use App\Mail\EOYFinancialReportThankYou;
use App\Mail\EOYFinancialSubmitted;
use App\Mail\DisbandChecklistComplete;
use App\Mail\DisbandChecklistThankYou;
use App\Mail\DisbandFinancialReportThankYou;
use App\Mail\DisbandFinalReportSubmit;
use App\Models\Admin;
use App\Models\Boards;
use App\Models\Chapters;
use App\Models\Documents;
use App\Models\DisbandedChecklist;
use App\Models\FinancialReport;
use App\Models\incomingboard;
use App\Models\Resources;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FinancialReportController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseBoardController;

    protected $pdfController;

    protected $baseMailDataController;

    public function __construct(UserController $userController, BaseBoardController $baseBoardController, PDFController $pdfController,
        BaseMailDataController $baseMailDataController)
    {
        $this->userController = $userController;
        $this->pdfController = $pdfController;
        $this->baseBoardController = $baseBoardController;
        $this->baseMailDataController = $baseMailDataController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
        ];
    }

    /**
     * Show EOY Financial Report All Board Members
     */
    public function editFinancialReport(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userName = $loggedInName = $user['user_name'];
        $userEmail = $user['user_email'];
        $chId = $user['user_chapterId'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $chIsActive =  $baseQuery['chIsActive'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $awards = $baseQuery['awards'];
        $allAwards = $baseQuery['allAwards'];

        $resources = Resources::with('categoryName')->get();

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userType' => $userType,
            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
            'awards' => $awards, 'allAwards' => $allAwards, 'chIsActive' => $chIsActive
        ];

        return view('boards.financial')->with($data);

    }

    /**
     * Save EOY Financial Report Accordion
     */
    public function saveAccordionFields($financialReport, $input)
    {
        $financialReport->farthest_step_visited = $input['FurthestStep'];

        // CHAPTER DUES
        $financialReport->changed_dues = $input['optChangeDues'] ?? null;
        $financialReport->different_dues = $input['optNewOldDifferent'] ?? null;
        $financialReport->not_all_full_dues = $input['optNoFullDues'] ?? null;
        $financialReport->total_new_members = $input['TotalNewMembers'] ?? null;
        $financialReport->total_renewed_members = $input['TotalRenewedMembers'] ?? null;
        $financialReport->dues_per_member = $input['MemberDues'] ?? null;
        $financialReport->total_new_members_changed_dues = $input['TotalNewMembersNewFee'] ?? null;
        $financialReport->total_renewed_members_changed_dues = $input['TotalRenewedMembersNewFee'] ?? null;
        $financialReport->dues_per_member_renewal = $input['MemberDuesRenewal'] ?? null;
        $financialReport->dues_per_member_new_changed = $input['NewMemberDues'] ?? null;
        $financialReport->dues_per_member_renewal_changed = $input['NewMemberDuesRenewal'] ?? null;
        $financialReport->members_who_paid_no_dues = $input['MembersNoDues'] ?? null;
        $financialReport->members_who_paid_partial_dues = $input['TotalPartialDuesMembers'] ?? null;
        $financialReport->total_partial_fees_collected = $input['PartialDuesMemberDues'] ?? null;
        $financialReport->total_associate_members = $input['TotalAssociateMembers'] ?? null;
        $financialReport->associate_member_fee = $input['AssociateMemberDues'] ?? null;

        // MONTHLY MEETING EXPENSES
        $financialReport->manditory_meeting_fees_paid = $input['ManditoryMeetingFeesPaid'] ?? null;
        $financialReport->voluntary_donations_paid = $input['VoluntaryDonationsPaid'] ?? null;
        $financialReport->meeting_speakers = $input['MeetingSpeakers'] ?? null;
        $financialReport->meeting_speakers_array = $input['Speakers'] ?? null;
        $financialReport->discussion_topic_frequency = $input['SpeakerFrequency'] ?? null;
        $financialReport->childrens_room_sitters = $input['ChildrensRoom'] ?? null;
        $financialReport->paid_baby_sitters = $input['PaidBabySitters'] ?? null;

        // Children Room Expenses (serialized)
        $ChildrenRoomArray = null;
        $FieldCount = $input['ChildrensExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $ChildrenRoomArray[$i]['childrens_room_desc'] = $input['ChildrensRoomDesc'.$i] ?? null;
            $ChildrenRoomArray[$i]['childrens_room_supplies'] = $input['ChildrensRoomSupplies'.$i] ?? null;
            $ChildrenRoomArray[$i]['childrens_room_other'] = $input['ChildrensRoomOther'.$i] ?? null;
        }
        $financialReport->childrens_room_expenses = base64_encode(serialize($ChildrenRoomArray));

        // SERVICE PROJECTS
        $financialReport->at_least_one_service_project = $input['PerformServiceProject'] ?? null;
        $financialReport->at_least_one_service_project_explanation = $input['PerformServiceProjectExplanation'] ?? null;
        $financialReport->contributions_not_registered_charity = $input['ContributionsNotRegNP'] ?? null;
        $financialReport->contributions_not_registered_charity_explanation = $input['ContributionsNotRegNPExplanation'] ?? null;

        // Service Projects (serialized)
        $ServiceProjectFields = null;
        $FieldCount = $input['ServiceProjectRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $ServiceProjectFields[$i]['service_project_desc'] = $input['ServiceProjectDesc'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_income'] = $input['ServiceProjectIncome'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_supplies'] = $input['ServiceProjectSupplies'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_charity'] = $input['ServiceProjectDonatedCharity'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_m2m'] = $input['ServiceProjectDonatedM2M'.$i] ?? null;
        }
        $financialReport->service_project_array = base64_encode(serialize($ServiceProjectFields));

        // Party Expenses (serialized)
        $PartyExpenseFields = null;
        $FieldCount = $input['PartyExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $PartyExpenseFields[$i]['party_expense_desc'] = $input['PartyDesc'.$i] ?? null;
            $PartyExpenseFields[$i]['party_expense_income'] = $input['PartyIncome'.$i] ?? null;
            $PartyExpenseFields[$i]['party_expense_expenses'] = $input['PartyExpenses'.$i] ?? null;
        }
        $financialReport->party_expense_array = base64_encode(serialize($PartyExpenseFields));


        // OFFICE & OPERATING EXPENSES
        $financialReport->office_printing_costs = $input['PrintingCosts'] ?? null;
        $financialReport->office_postage_costs = $input['PostageCosts'] ?? null;
        $financialReport->office_membership_pins_cost = $input['MembershipPins'] ?? null;

        // Office Other Expenses (serialized)
        $OfficeOtherArray = null;
        $FieldCount = $input['OfficeExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $OfficeOtherArray[$i]['office_other_desc'] = $input['OfficeDesc'.$i] ?? null;
            $OfficeOtherArray[$i]['office_other_expense'] = $input['OfficeExpenses'.$i] ?? null;
        }
        $financialReport->office_other_expenses = base64_encode(serialize($OfficeOtherArray));

        // INTERNATIONAL EVENTS & RE-REGISTRATION
        $financialReport->annual_registration_fee = $input['AnnualRegistrationFee'] ?? null;
        $financialReport->international_event = $input['InternationalEvent'] ?? null;

        // International Events (serialized)
        $InternationalEventArray = null;
        $FieldCount = $input['InternationalEventRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $InternationalEventArray[$i]['intl_event_desc'] = $input['InternationalEventDesc'.$i] ?? null;
            $InternationalEventArray[$i]['intl_event_income'] = $input['InternationalEventIncome'.$i] ?? null;
            $InternationalEventArray[$i]['intl_event_expenses'] = $input['InternationalEventExpense'.$i] ?? null;
        }
        $financialReport->international_event_array = base64_encode(serialize($InternationalEventArray));

        // Donations to Chapte (serialized)
        $MonetaryDonation = null;
        $FieldCount = $input['MonDonationRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $MonetaryDonation[$i]['mon_donation_desc'] = $input['DonationDesc'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_info'] = $input['DonorInfo'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_date'] = $input['MonDonationDate'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_amount'] = $input['DonationAmount'.$i] ?? null;
        }
        $financialReport->monetary_donations_to_chapter = base64_encode(serialize($MonetaryDonation));

        $NonMonetaryDonation = null;
        $FieldCount = $input['NonMonDonationRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $NonMonetaryDonation[$i]['nonmon_donation_desc'] = $input['NonMonDonationDesc'.$i] ?? null;
            $NonMonetaryDonation[$i]['nonmon_donation_info'] = $input['NonMonDonorInfo'.$i] ?? null;
            $NonMonetaryDonation[$i]['nonmon_donation_date'] = $input['NonMonDonationDate'.$i] ?? null;
        }
        $financialReport->non_monetary_donations_to_chapter = base64_encode(serialize($NonMonetaryDonation));

        // OTHER INCOME & EXPENSES (seralized)
        $OtherOffice = null;
        $FieldCount = $input['OtherOfficeExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $OtherOffice[$i]['other_desc'] = $input['OtherOfficeDesc'.$i] ?? null;
            $OtherOffice[$i]['other_expenses'] = $input['OtherOfficeExpenses'.$i] ?? null;
            $OtherOffice[$i]['other_income'] = $input['OtherOfficeIncome'.$i] ?? null;
        }
        $financialReport->other_income_and_expenses_array = base64_encode(serialize($OtherOffice));

        // BANK RECONCILLIATION
        $financialReport->bank_statement_included = $input['BankStatementIncluded'] ?? null;
        $financialReport->bank_statement_included_explanation = $input['BankStatementIncludedExplanation'] ?? null;
        $financialReport->wheres_the_money = $input['WheresTheMoney'] ?? null;
        $amount_reserved_from_previous_year = $input['AmountReservedFromLastYear'];
        $amount_reserved_from_previous_year = str_replace(',', '', $amount_reserved_from_previous_year);
        $financialReport->amount_reserved_from_previous_year = $amount_reserved_from_previous_year === '' ? null : $amount_reserved_from_previous_year;
        $bank_balance_now = $input['BankBalanceNow'];
        $bank_balance_now = str_replace(',', '', $bank_balance_now);
        $financialReport->bank_balance_now = $bank_balance_now === '' ? null : $bank_balance_now;

        // Bank Reconciliation (serialized)
        $BankRecArray = null;
        $FieldCount = $input['BankRecRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $BankRecArray[$i]['bank_rec_date'] = $input['BankRecDate'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_check_no'] = $input['BankRecCheckNo'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_desc'] = $input['BankRecDesc'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_payment_amount'] = $input['BankRecPaymentAmount'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_desposit_amount'] = $input['BankRecDepositAmount'.$i] ?? null;
        }
        $financialReport->bank_reconciliation_array = base64_encode(serialize($BankRecArray));

        // 990 IRS FILING
        $financialReport->file_irs = $input['FileIRS'] ?? null;
        $financialReport->file_irs_explanation = $input['FileIRSExplanation'] ?? null;

        // CHPATER QUESTIONS
        // Question 1
        $financialReport->bylaws_available = $input['ByLawsAvailable'] ?? null;
        $financialReport->bylaws_available_explanation = $input['ByLawsAvailableExplanation'] ?? null;
        // Question 2
        $financialReport->vote_all_activities = $input['VoteAllActivities'] ?? null;
        $financialReport->vote_all_activities_explanation = $input['VoteAllActivitiesExplanation'] ?? null;
        // Question 3
        $financialReport->child_outings = $input['ChildOutings'] ?? null;
        $financialReport->child_outings_explanation = $input['ChildOutingsExplanation'] ?? null;
        // Question 4
        $financialReport->playgroups = $input['Playgroups'] ?? null;
        $financialReport->had_playgroups_explanation = $input['PlaygroupsExplanation'] ?? null;
        // Question 5
        $financialReport->park_day_frequency = $input['ParkDays'] ?? null;
        $financialReport->park_day_frequency_explanation = $input['ParkDaysExplanation'] ?? null;
        // Question 6
        $financialReport->mother_outings = $input['MotherOutings'] ?? null;
        $financialReport->mother_outings_explanation = $input['MotherOutingsExplanation'] ?? null;
        // Question 7
        $financialReport->activity_array = $input['Activity'] ?? null;
        $financialReport->activity_other_explanation = $input['ActivityOtherExplanation'] ?? null;
        // Question 8
        $financialReport->offered_merch = $input['OfferedMerch'] ?? null;
        $financialReport->offered_merch_explanation = $input['OfferedMerchExplanation'] ?? null;
        // Question 9
        $financialReport->bought_merch = $input['BoughtMerch'] ?? null;
        $financialReport->bought_merch_explanation = $input['BoughtMerchExplanation'] ?? null;
        // Question 10
        $financialReport->purchase_pins = $input['BoughtPins'] ?? null;
        $financialReport->purchase_pins_explanation = $input['BoughtPinsExplanation'] ?? null;
        // Question 11
        $financialReport->receive_compensation = $input['ReceiveCompensation'] ?? null;
        $financialReport->receive_compensation_explanation = $input['ReceiveCompensationExplanation'];
        // Question 12
        $financialReport->financial_benefit = $input['FinancialBenefit'] ?? null;
        $financialReport->financial_benefit_explanation = $input['FinancialBenefitExplanation'] ?? null;
        // Question 13
        $financialReport->influence_political = $input['InfluencePolitical'] ?? null;
        $financialReport->influence_political_explanation = $input['InfluencePoliticalExplanation'] ?? null;
        // Question 14
        $financialReport->sister_chapter = $input['SisterChapter'] ?? null;
        $financialReport->sister_chapter_explanation = $input['SisterChapterExplanation'] ?? null;

        // AWARDS
        $financialReport->outstanding_follow_bylaws = $input['OutstandingFollowByLaws'] ?? null;
        $financialReport->outstanding_well_rounded = $input['OutstandingWellRounded'] ?? null;
        $financialReport->outstanding_communicated = $input['OutstandingCommunicated'] ?? null;
        $financialReport->outstanding_support_international = $input['OutstandingSupportMomsClub'] ?? null;

        // Awards (seralized)
        $ChapterAwards = null;
        $FieldCount = $input['ChapterAwardsRowCount'] ?? null;
        for ($i = 0; $i < $FieldCount; $i++) {
            $ChapterAwards[$i]['awards_type'] = $input['ChapterAwardsType'.$i] ?? null;
            $ChapterAwards[$i]['awards_desc'] = $input['ChapterAwardsDesc'.$i] ?? null;
            $ChapterAwards[$i]['awards_approved'] = false;
        }
        $financialReport->chapter_awards = base64_encode(serialize($ChapterAwards));
    }


    /**
     * Save EOY Financial Report All Board Members
     */
    public function updateFinancialReport(Request $request, $chapterId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userName = $user['user_name'];
        $userEmail = $user['user_email'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $input = $request->all();
        $reportReceived = $input['submitted']?? null;

        $financialReport = FinancialReport::find($chapterId);
        $documents = Documents::find($chapterId);
        $chapter = Chapters::find($chapterId);

        if ($userType == 'disbanded') {
            $disbandChecklist = DisbandedChecklist::find($chapterId);
            $checklistComplete = ($disbandChecklist->final_payment == '1' && $disbandChecklist->donate_funds == '1' &&
                $disbandChecklist->destroy_manual == '1' && $disbandChecklist->remove_online == '1' &&
                $disbandChecklist->file_irs == '1' && $disbandChecklist->file_financial == '1');
        }
        else {
            $checklistComplete = null;
        }

        DB::beginTransaction();
        try{
            $this->saveAccordionFields($financialReport, $input);

            if ($reportReceived == 1) {
                $financialReport->completed_name = $userName;
                $financialReport->completed_email = $userEmail;
                $financialReport->submitted = $lastupdatedDate;
                $financialReport->save();

                $documents->financial_report_received = 1;
                $documents->report_received = $lastupdatedDate;
                $documents->save();
            }

            if ($reportReceived == 1 && $userType == 'disbanded') {
                $documents->final_report_received = 1;
                $documents->save();
            }

            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;

            $chapter->save();

            $baseQuery = $this->baseBoardController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chDocuments = $baseQuery['chDocuments'];
            $chFinancialReport = $baseQuery['chFinancialReport'];
            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];
            $pcDetails = $baseQuery['pcDetails'];
            $emailCC = $baseQuery['emailCC'];
            $cc_id = $baseQuery['cc_id'];
            $reviewerEmail = $baseQuery['reviewerEmail'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPCData($pcDetails),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport),
            );

            if ($reportReceived == 1) {
                $pdfPath = $this->pdfController->saveFinancialReport($request, $chapterId);   // Generate and Send the PDF

                if ($userType != 'disbanded'){
                    Mail::to($userEmail)
                        ->cc($emailListChap)
                        ->queue(new EOYFinancialReportThankYou($mailData, $pdfPath));

                    if ($chFinancialReport->reviewer_id == null) {
                        DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [$cc_id, $chapterId]);
                        Mail::to($emailCC)
                            ->queue(new EOYFinancialSubmitted($mailData, $pdfPath));
                    }

                    if ($chFinancialReport->reviewer_id != null) {
                        Mail::to($reviewerEmail)
                            ->queue(new EOYFinancialSubmitted($mailData, $pdfPath));
                    }
                }

                if ($userType == 'disbanded'){
                    Mail::to($userEmail)
                        ->cc($emailListChap)
                        ->queue(new DisbandFinancialReportThankYou($mailData, $pdfPath));

                    if ($chFinancialReport->reviewer_id == null) {
                        DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [$cc_id, $chapterId]);
                    }

                    Mail::to($emailCC)
                        ->queue(new DisbandFinalReportSubmit($mailData, $pdfPath));
                }
            }

            if ($documents->final_report_received == '1' && $checklistComplete){
                Mail::to($userEmail)
                    ->cc($emailListChap)
                    ->queue(new DisbandChecklistThankYou($mailData));

                Mail::to($emailCC)
                    ->queue(new DisbandChecklistComplete($mailData));
            }

            DB::commit();
            if ($reportReceived == 1) {
                return redirect()->back()->with('success', 'Report has been successfully Submitted');
            } else {
                return redirect()->back()->with('success', 'Report has been successfully updated');
            }

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong Please try again.');
        }
    }






    /**
     * Save EOY Financial Report Active Board Members
     */
    // public function updateFinancialReport(Request $request, $chapterId): RedirectResponse
    // {
    //     $user = $this->userController->loadUserInformation($request);
    //     $userName = $user['user_name'];
    //     $userEmail = $user['user_email'];
    //     $lastUpdatedBy = $user['user_name'];
    //     $lastupdatedDate = date('Y-m-d H:i:s');

    //     $input = $request->all();
    //     $reportReceived = $input['submitted']?? null;

    //     $financialReport = FinancialReport::find($chapterId);
    //     $documents = Documents::find($chapterId);
    //     $chapter = Chapters::find($chapterId);

    //     DB::beginTransaction();
    //     try{
    //         $this->saveAccordionFields($financialReport, $input);

    //         if ($reportReceived == 1) {
    //             $financialReport->completed_name = $userName;
    //             $financialReport->completed_email = $userEmail;
    //             $financialReport->submitted = $lastupdatedDate;
    //         }

    //         $financialReport->save();

    //         if ($reportReceived == 1) {
    //             $documents->financial_report_received = 1;
    //             $documents->report_received = $lastupdatedDate;

    //             $documents->save();
    //         }

    //         $chapter->last_updated_by = $lastUpdatedBy;
    //         $chapter->last_updated_date = $lastupdatedDate;

    //         $chapter->save();

    //         $baseQuery = $this->baseBoardController->getChapterDetails($chapterId);
    //         $chDetails = $baseQuery['chDetails'];
    //         $stateShortName = $baseQuery['stateShortName'];
    //         $chDocuments = $baseQuery['chDocuments'];
    //         $chFinancialReport = $baseQuery['chFinancialReport'];
    //         $emailListChap = $baseQuery['emailListChap'];
    //         $emailListCoord = $baseQuery['emailListCoord'];
    //         $pcDetails = $baseQuery['pcDetails'];
    //         $emailCC = $baseQuery['emailCC'];
    //         $cc_id = $baseQuery['cc_id'];
    //         $reviewerEmail = $baseQuery['reviewerEmail'];

    //         $mailData = array_merge(
    //             $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
    //             $this->baseMailDataController->getPCData($pcDetails),
    //             $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport),
    //         );

    //         if ($reportReceived == 1) {
    //             $pdfPath = $this->pdfController->saveFinancialReport($request, $chapterId);   // Generate and Send the PDF
    //             Mail::to($userEmail)
    //                 ->cc($emailListChap)
    //                 ->queue(new EOYFinancialReportThankYou($mailData, $pdfPath));

    //             if ($chFinancialReport->reviewer_id == null) {
    //                 DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [$cc_id, $chapterId]);
    //                 Mail::to($emailCC)
    //                     ->queue(new EOYFinancialSubmitted($mailData, $pdfPath));
    //             }

    //             if ($chFinancialReport->reviewer_id != null) {
    //                 Mail::to($reviewerEmail)
    //                     ->queue(new EOYFinancialSubmitted($mailData, $pdfPath));
    //             }
    //         }

    //         DB::commit();
    //         if ($reportReceived == 1) {
    //             return redirect()->back()->with('success', 'Report has been successfully Submitted');
    //         } else {
    //             return redirect()->back()->with('success', 'Report has been successfully updated');
    //         }

    //     } catch (\Exception $e) {
    //         DB::rollback();  // Rollback Transaction
    //         Log::error($e);  // Log the error

    //         return redirect()->back()->with('fail', 'Something went wrong Please try again.');
    //     }
    // }

    public function getRosterfile(): BinaryFileResponse
    {
        $filename = 'roster_template.xlsx';

        $file_path = '/home/momsclub/public_html/mimi/storage/app/public';

        return Response::download($file_path, $filename, [
            'Content-Length: '.filesize($file_path),
        ]);
    }

    /**
     * Save Financial Report for Disbanded Chapters
     */
    public function updateDisbandReport(Request $request, $chapterId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userName = $user['user_name'];
        $userEmail = $user['user_email'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $input = $request->all();
        $reportReceived = $input['submitted'] ?? null;

        $financialReport = FinancialReport::find($chapterId);
        $documents = Documents::find($chapterId);
        $chapter = Chapters::find($chapterId);

        $disbandChecklist = DisbandedChecklist::find($chapterId);
        $checklistComplete = ($disbandChecklist->final_payment == '1' && $disbandChecklist->donate_funds == '1' &&
            $disbandChecklist->destroy_manual == '1' && $disbandChecklist->remove_online == '1' &&
            $disbandChecklist->file_irs == '1' && $disbandChecklist->file_financial == '1');

        DB::beginTransaction();
        try{
            $this->saveAccordionFields($financialReport, $input);

            if ($reportReceived == 1) {
                $financialReport->completed_name = $userName;
                $financialReport->completed_email = $userEmail;
                $financialReport->submitted = $lastupdatedDate;
            }

            $financialReport->save();

            if ($reportReceived == 1) {
                $documents->financial_report_received = 1;
                $documents->final_report_received = 1;
                $documents->report_received = $lastupdatedDate;

                $documents->save();
            }

            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;

            $chapter->save();

            $baseQuery = $this->baseBoardController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chDocuments = $baseQuery['chDocuments'];
            $chFinancialReport = $baseQuery['chFinancialReport'];
            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];
            $pcDetails = $baseQuery['pcDetails'];
            $emailCC = $baseQuery['emailCC'];
            $cc_id = $baseQuery['cc_id'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPCData($pcDetails),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport),
            );

            if ($documents->final_report_received == '1') {
                $pdfPath = $this->pdfController->saveFinalFinancialReport($request, $chapterId);   // Generate and Send the PDF
                Mail::to($userEmail)
                    ->cc($emailListChap)
                    ->queue(new DisbandFinancialReportThankYou($mailData, $pdfPath));

                if ($chFinancialReport->reviewer_id == null) {
                    DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [$cc_id, $chapterId]);
                }

                Mail::to($emailCC)
                    ->queue(new DisbandFinalReportSubmit($mailData, $pdfPath));
            }

            if ($documents->final_report_received == '1' && $checklistComplete){
                Mail::to($userEmail)
                    ->cc($emailListChap)
                    ->queue(new DisbandChecklistThankYou($mailData));

                Mail::to($emailCC)
                    ->queue(new DisbandChecklistComplete($mailData));
            }

            DB::commit();
            if ($reportReceived == 1) {
                return redirect()->back()->with('success', 'Report has been successfully Submitted');
            } else {
                return redirect()->back()->with('success', 'Report has been successfully updated');
            }

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong Please try again.');
        }
    }

     /**
     * Save Disbanded Checklsit Questions
     */
    public function updateDisbandChecklist(Request $request, $chapterId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userName = $user['user_name'];
        $userEmail = $user['user_email'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $documents = Documents::find($chapterId);
        $chapter = Chapters::find($chapterId);
        $disbandChecklist = DisbandedChecklist::find($chapterId);

        DB::beginTransaction();
        try{
            $disbandChecklist->final_payment = $request->has('FinalPayment') ? 1 : 0;
            $disbandChecklist->donate_funds = $request->has('DonateFunds') ? 1 : 0;
            $disbandChecklist->destroy_manual = $request->has('DestroyManual') ? 1 : 0;
            $disbandChecklist->remove_online = $request->has('RemoveOnline') ? 1 : 0;
            $disbandChecklist->file_irs = $request->has('FileIRS') ? 1 : 0;
            $disbandChecklist->file_financial = $request->has('FileFinancial') ? 1 : 0;

            $disbandChecklist->save();

            $checklistComplete = ($disbandChecklist->final_payment == '1' && $disbandChecklist->donate_funds == '1' &&
                $disbandChecklist->destroy_manual == '1' && $disbandChecklist->remove_online == '1' &&
                $disbandChecklist->file_irs == '1' && $disbandChecklist->file_financial == '1');

            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;

            $chapter->save();

            $baseQuery = $this->baseBoardController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chDocuments = $baseQuery['chDocuments'];
            $chFinancialReport = $baseQuery['chFinancialReport'];
            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];
            $pcDetails = $baseQuery['pcDetails'];
            $emailCC = $baseQuery['emailCC'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPCData($pcDetails),
                $this->baseMailDataController->getFinancialReportData($chDocuments, $chFinancialReport),
            );


            if ($documents->final_financial_report_received == '1' && $checklistComplete) {
                Mail::to($userEmail)
                    ->cc($emailListChap)
                    ->queue(new DisbandChecklistThankYou($mailData));

                Mail::to($emailCC)
                    ->queue(new DisbandChecklistComplete($mailData));
            }

            DB::commit();
                return redirect()->back()->with('success', 'Checklist has been successfully updated');

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->back()->with('fail', 'Something went wrong Please try again.');
        }
    }

}
