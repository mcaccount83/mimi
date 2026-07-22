<?php

namespace App\Http\Controllers;

use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use App\Mail\DisbandChecklistCompleteCCNotice;
use App\Mail\DisbandChecklistCompleteThankYou;
use App\Mail\DisbandReportCCNotice;
use App\Mail\DisbandReportThankYou;
use App\Mail\EOYFinancialReportThankYou;
use App\Mail\EOYFinancialSubmitted;
use App\Mail\NewBoardWelcome;
use App\Models\Boards;
use App\Models\BoardsIncoming;
use App\Models\BoardsOutgoing;
use App\Models\Chapters;
use App\Models\DisbandedChecklist;
use App\Models\DocumentsEOY;
use App\Models\FinancialReport;
use App\Models\FinancialReportQuestions;
use App\Models\FinancialReportFinal;
use App\Models\FinancialReportFinalQuestions;
use App\Models\ResourceCategory;
use App\Models\Resources;
use App\Models\User;
use App\Services\PositionConditionsService;
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
    public function __construct(
        protected UserController $userController,
        protected BaseChapterController $baseChapterController,
        protected BaseBoardController $baseBoardController,
        protected PDFController $pdfController,
        protected BaseMailDataController $baseMailDataController,
        protected PositionConditionsService $positionConditionsService,
        protected EmailCampaignController $emailCampaignController,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsBoardOrDisbanded::class,
            \App\Http\Middleware\SetViewAsSession::class,
        ];
    }

    /**
     * Show EOY Financial Report All Board Members
     */
    public function editFinancialReport(Request $request, int $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $userTypeId = $user['userTypeId'];
        $userName = $loggedInName = $user['userName'];
        $userEmail = $user['userEmail'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chIRSDocuments = $baseQuery['chIRSDocuments'];
        $chReportDocuments = $baseQuery['chReportDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReport'];
        $chFinancialReportQuestions = $baseQuery['chFinancialReportQuestions'];
        $chFinancialReportQuestions = $baseQuery['chFinancialReportQuestions'];
        $awards = $baseQuery['awards'];
        $allAwards = $baseQuery['allAwards'];

        $ownBoardDetails = Boards::with(['state', 'country', 'user'])
            ->where('chapter_id', $chId)
            ->where('user_id', $userId)
            ->first();

        $PresDetails = $baseQuery['PresDetails'];
        $bdData = $this->positionConditionsService->getViewAs($userTypeId, $userId, $PresDetails, $ownBoardDetails);
        $bdPositionId = $bdData['bdPositionId'];
        $borDetails = $bdData['bdDetails'];
        $bdTypeId = $bdData['bdTypeId'];

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        $data = ['chActiveId' => $chActiveId, 'chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userTypeId' => $userTypeId, 'userAdmin' => $userAdmin,
            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'stateShortName' => $stateShortName,
            'awards' => $awards, 'allAwards' => $allAwards, 'resourceCategories' => $resourceCategories, 'chEOYDocuments' => $chEOYDocuments,
            'bdPositionId' => $bdPositionId, 'borDetails' => $borDetails, 'bdTypeId' => $bdTypeId, 'PresDetails' => $PresDetails,
            'chIRSDocuments' => $chIRSDocuments, 'chReportDocuments' => $chReportDocuments, 'chFinancialReportQuestions' => $chFinancialReportQuestions
        ];

        return view('boards.editfinancialreport')->with($data);

    }

    public function editFinancialReportFinal(Request $request, int $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $userTypeId = $user['userTypeId'];
        $userName = $loggedInName = $user['userName'];
        $userEmail = $user['userEmail'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chIRSDocuments = $baseQuery['chIRSDocuments'];
        $chReportDocuments = $baseQuery['chReportDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReportFinal'];
        $chFinancialReportQuestions = $baseQuery['chFinancialReportQuestions'];

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        $chDisbanded = $baseQuery['chDisbanded'];

        $ownBoardDetails = Boards::with(['state', 'country', 'user'])
            ->where('chapter_id', $chId)
            ->where('user_id', $userId)
            ->first();

        $PresDetails = $baseQuery['PresDetails'];
        $bdData = $this->positionConditionsService->getViewAs($userTypeId, $userId, $PresDetails, $ownBoardDetails);
        $bdPositionId = $bdData['bdPositionId'];
        $borDetails = $bdData['bdDetails'];
        $bdTypeId = $bdData['bdTypeId'];

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userTypeId' => $userTypeId,
            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
            'chDisbanded' => $chDisbanded, 'chActiveId' => $chActiveId, 'resourceCategories' => $resourceCategories, 'userAdmin' => $userAdmin, 'chEOYDocuments' => $chEOYDocuments,
            'bdPositionId' => $bdPositionId, 'borDetails' => $borDetails, 'bdTypeId' => $bdTypeId, 'PresDetails' => $PresDetails,
            'chIRSDocuments' => $chIRSDocuments, 'chReportDocuments' => $chReportDocuments, 'chFinancialReportQuestions' => $chFinancialReportQuestions
        ];

        return view('boards.disband.editfinancialreportfinal')->with($data);

    }

    /**
     * Save EOY Financial Report Accordion
     */
    public function saveAccordionCalculations(FinancialReport|FinancialReportFinal $financialReport, array $input): void
    {
        // --- Membership counts & dues total ---
        $ChangedMeetingFees = ($input['optChangeDues'] ?? null) == '1';
        $ChargedMembersDifferently = ($input['optNewOldDifferent'] ?? null) == '1';

        $NewMembers = (int) ($input['TotalNewMembers'] ?? 0);
        $RenewedMembers = (int) ($input['TotalRenewedMembers'] ?? 0);
        $NewMembers2 = (int) ($input['TotalNewMembersNewFee'] ?? 0);
        $RenewedMembers2 = (int) ($input['TotalRenewedMembersNewFee'] ?? 0);
        $MembersNoDues = (int) ($input['MembersNoDues'] ?? 0);
        $PartialDuesMembers = (int) ($input['TotalPartialDuesMembers'] ?? 0);
        $AssociateMembers = (int) ($input['TotalAssociateMembers'] ?? 0);

        $MemberDues = (float) preg_replace('/[^0-9.-]/', '', $input['MemberDues'] ?? 0);
        $NewMemberDues = (float) preg_replace('/[^0-9.-]/', '', $input['NewMemberDues'] ?? 0);
        $MemberDuesRenewal = (float) preg_replace('/[^0-9.-]/', '', $input['MemberDuesRenewal'] ?? 0);
        $NewMemberDuesRenewal = (float) preg_replace('/[^0-9.-]/', '', $input['NewMemberDuesRenewal'] ?? 0);
        $PartialDuesMemberDues = (float) preg_replace('/[^0-9.-]/', '', $input['PartialDuesMemberDues'] ?? 0);
        $AssociateMemberDues = (float) preg_replace('/[^0-9.-]/', '', $input['AssociateMemberDues'] ?? 0);

        $financialReport->member_count_total = $NewMembers + $RenewedMembers + $MembersNoDues
            + $AssociateMembers + $PartialDuesMembers + $NewMembers2 + $RenewedMembers2;

        $newMembersDues = $NewMembers * $MemberDues;
        $renewalMembersDues = $RenewedMembers * $MemberDues;
        $renewalMembersDuesDiff = $RenewedMembers * $MemberDuesRenewal;
        $newMembersDuesNew = $NewMembers2 * $NewMemberDues;
        $renewMembersDuesNew = $RenewedMembers2 * $NewMemberDues;
        $renewMembersNewDuesDiff = $RenewedMembers2 * $NewMemberDuesRenewal;
        $partialMembersDues = $PartialDuesMemberDues;
        $associateMembersDues = $AssociateMembers * $AssociateMemberDues;

        if ($ChangedMeetingFees && $ChargedMembersDifferently) {
            $TotalFees = $newMembersDues + $renewalMembersDuesDiff + $newMembersDuesNew + $renewMembersNewDuesDiff + $associateMembersDues + $partialMembersDues;
        } elseif ($ChargedMembersDifferently) {
            $TotalFees = $newMembersDues + $renewalMembersDuesDiff + $associateMembersDues + $partialMembersDues;
        } elseif ($ChangedMeetingFees) {
            $TotalFees = $newMembersDues + $renewalMembersDues + $newMembersDuesNew + $renewMembersDuesNew + $associateMembersDues + $partialMembersDues;
        } else {
            $TotalFees = $newMembersDues + $renewalMembersDues + $associateMembersDues + $partialMembersDues;
        }

        $financialReport->member_dues_total = round($TotalFees, 2);
        $SumTotalDues = round($TotalFees, 2);

        // MONTHLY MEETING EXPENSES
        $financialReport->manditory_meeting_fees_paid = isset($input['ManditoryMeetingFeesPaid']) ? preg_replace('/[^\d.]/', '', $input['ManditoryMeetingFeesPaid']) : null;
        $financialReport->voluntary_donations_paid = isset($input['VoluntaryDonationsPaid']) ? preg_replace('/[^\d.]/', '', $input['VoluntaryDonationsPaid']) : null;
        $financialReport->paid_baby_sitters = isset($input['PaidBabySitters']) ? preg_replace('/[^\d.]/', '', $input['PaidBabySitters']) : null;

        // Children Room Expenses (serialized)
        $ChildrenRoomArray = null;
        $SumChildrensSuppliesExpense = 0;
        $SumChildrensOtherExpense = 0;
        $FieldCount = $input['ChildrensExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $children_supplies = (float) preg_replace('/[^\d.]/', '', $input['ChildrensRoomSupplies'.$i] ?? 0);
            $children_other = (float) preg_replace('/[^\d.]/', '', $input['ChildrensRoomOther'.$i] ?? 0);

            $ChildrenRoomArray[$i]['childrens_room_desc'] = $input['ChildrensRoomDesc'.$i] ?? null;
            $ChildrenRoomArray[$i]['childrens_room_supplies'] = $input['ChildrensRoomSupplies'.$i] ?? null;
            $ChildrenRoomArray[$i]['childrens_room_other'] = $input['ChildrensRoomOther'.$i] ?? null;

            $SumChildrensSuppliesExpense += $children_supplies;
            $SumChildrensOtherExpense += $children_other;
        }
        $financialReport->childrens_room_expenses = base64_encode(serialize($ChildrenRoomArray));

        $SumManditoryMeetingFees = (float) preg_replace('/[^\d.]/', '', $input['ManditoryMeetingFeesPaid'] ?? 0);
        $SumVoluntaryDonations = (float) preg_replace('/[^\d.]/', '', $input['VoluntaryDonationsPaid'] ?? 0);
        $SumMeetingRoomFees = round($SumManditoryMeetingFees + $SumVoluntaryDonations, 2);

        $financialReport->meeting_expenses_total = round($SumMeetingRoomFees, 2);

        $SumPaidSittersExpense = (float) preg_replace('/[^\d.]/', '', $input['PaidBabySitters'] ?? 0);
        $SumChildrensRoomFees = round($SumChildrensSuppliesExpense + $SumChildrensOtherExpense + $SumPaidSittersExpense, 2);

        $financialReport->children_expenses_supplies = round($SumChildrensSuppliesExpense, 2);
        $financialReport->children_expenses_other = round($SumChildrensOtherExpense, 2);
        $financialReport->children_expenses_total = round($SumChildrensRoomFees, 2);

        // Service Projects (serialized)
        $ServiceProjectFields = null;
        $SumServiceProjectIncome = 0;
        $SumServiceProjectSuppliesExpense = 0;
        $SumServiceProjectCharityExpense = 0;
        $SumServiceProjectM2MExpense = 0;
        $FieldCount = $input['ServiceProjectRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $service_income = (float) preg_replace('/[^\d.]/', '', $input['ServiceProjectIncome'.$i] ?? 0);
            $service_supplies = (float) preg_replace('/[^\d.]/', '', $input['ServiceProjectSupplies'.$i] ?? 0);
            $service_charity = (float) preg_replace('/[^\d.]/', '', $input['ServiceProjectDonatedCharity'.$i] ?? 0);
            $service_m2m = (float) preg_replace('/[^\d.]/', '', $input['ServiceProjectDonatedM2M'.$i] ?? 0);

            $ServiceProjectFields[$i]['service_project_desc'] = $input['ServiceProjectDesc'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_income'] = $input['ServiceProjectIncome'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_supplies'] = $input['ServiceProjectSupplies'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_charity'] = $input['ServiceProjectDonatedCharity'.$i] ?? null;
            $ServiceProjectFields[$i]['service_project_m2m'] = $input['ServiceProjectDonatedM2M'.$i] ?? null;

            $SumServiceProjectIncome += $service_income;
            $SumServiceProjectSuppliesExpense += $service_supplies;
            $SumServiceProjectCharityExpense += $service_charity;
            $SumServiceProjectM2MExpense += $service_m2m;
        }
        $financialReport->service_project_array = base64_encode(serialize($ServiceProjectFields));

        $SumServiceProjectExpense = round($SumServiceProjectSuppliesExpense + $SumServiceProjectCharityExpense + $SumServiceProjectM2MExpense, 2);

        $financialReport->service_project_income_total = round($SumServiceProjectIncome, 2);
        $financialReport->service_project_expenses_supplies = round($SumServiceProjectSuppliesExpense, 2);
        $financialReport->service_project_expenses_charity = round($SumServiceProjectCharityExpense, 2);
        $financialReport->service_project_expenses_m2m = round($SumServiceProjectM2MExpense, 2);
        $financialReport->service_project_expenses_total = round($SumServiceProjectExpense, 2);

        // Party Expenses (serialized)
        $PartyExpenseFields = null;
        $SumPartyIncome = 0;
        $SumPartyExpense = 0;
        $FieldCount = $input['PartyExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $party_income = (float) preg_replace('/[^\d.]/', '', $input['PartyIncome'.$i] ?? 0);
            $party_expense = (float) preg_replace('/[^\d.]/', '', $input['PartyExpenses'.$i] ?? 0);

            $PartyExpenseFields[$i]['party_expense_desc'] = $input['PartyDesc'.$i] ?? null;
            $PartyExpenseFields[$i]['party_expense_income'] = $input['PartyIncome'.$i] ?? null;
            $PartyExpenseFields[$i]['party_expense_expenses'] = $input['PartyExpenses'.$i] ?? null;

            $SumPartyIncome += $party_income;
            $SumPartyExpense += $party_expense;

        }
        $financialReport->party_expense_array = base64_encode(serialize($PartyExpenseFields));

        $financialReport->party_income_total = round($SumPartyIncome, 2);
        $financialReport->party_expense_total = round($SumPartyExpense, 2);
        $financialReport->party_percentage = $SumTotalDues > 0 ? round($SumPartyExpense / $SumTotalDues, 2) : 0;

        // OFFICE & OPERATING EXPENSES
        $financialReport->office_printing_costs = isset($input['PrintingCosts']) ? preg_replace('/[^\d.]/', '', $input['PrintingCosts']) : null;
        $financialReport->office_postage_costs = isset($input['PostageCosts']) ? preg_replace('/[^\d.]/', '', $input['PostageCosts']) : null;
        $financialReport->office_membership_pins_cost = isset($input['MembershipPins']) ? preg_replace('/[^\d.]/', '', $input['MembershipPins']) : null;

        // Office Other Expenses (serialized)
        $OfficeOtherArray = null;
        $SumOfficeOtherExpense = 0;
        $FieldCount = $input['OfficeExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $office_expense = (float) preg_replace('/[^\d.]/', '', $input['OfficeExpenses'.$i] ?? 0);

            $OfficeOtherArray[$i]['office_other_desc'] = $input['OfficeDesc'.$i] ?? null;
            $OfficeOtherArray[$i]['office_other_expense'] = $input['OfficeExpenses'.$i] ?? null;

            $SumOfficeOtherExpense += $office_expense;
        }
        $financialReport->office_other_expenses = base64_encode(serialize($OfficeOtherArray));

        $SumOfficePrinting = (float) preg_replace('/[^\d.]/', '', $input['PrintingCosts'] ?? 0);
        $SumOfficePostage = (float) preg_replace('/[^\d.]/', '', $input['PostageCosts'] ?? 0);
        $SumOfficeMembershipPins = (float) preg_replace('/[^\d.]/', '', $input['MembershipPins'] ?? 0);
        $SumOfficeExpense = round($SumOfficeOtherExpense + $SumOfficePrinting + $SumOfficePostage + $SumOfficeMembershipPins, 2);

        $financialReport->office_expenses_other = round($SumOfficeOtherExpense, 2);
        $financialReport->office_expenses_total = round($SumOfficeExpense, 2);

        // INTERNATIONAL EVENTS & RE-REGISTRATION
        $financialReport->annual_registration_fee = isset($input['AnnualRegistrationFee']) ? preg_replace('/[^\d.]/', '', $input['AnnualRegistrationFee']) : null;

        $SumReRegFee = (float) preg_replace('/[^\d.]/', '', $input['AnnualRegistrationFee'] ?? 0);

        // International Events (serialized)
        $InternationalEventArray = null;
        $SumInternationalEventIncome = 0;
        $SumInternationalEventExpense = 0;
        $FieldCount = $input['InternationalEventRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $international_event_income = (float) preg_replace('/[^\d.]/', '', $input['InternationalEventIncome'.$i] ?? 0);
            $international_event_expense = (float) preg_replace('/[^\d.]/', '', $input['InternationalEventExpense'.$i] ?? 0);

            $InternationalEventArray[$i]['intl_event_desc'] = $input['InternationalEventDesc'.$i] ?? null;
            $InternationalEventArray[$i]['intl_event_income'] = $input['InternationalEventIncome'.$i] ?? null;
            $InternationalEventArray[$i]['intl_event_expenses'] = $input['InternationalEventExpense'.$i] ?? null;

            $SumInternationalEventIncome += $international_event_income;
            $SumInternationalEventExpense += $international_event_expense;
        }
        $financialReport->international_event_array = base64_encode(serialize($InternationalEventArray));

        $financialReport->international_event_income_total = round($SumInternationalEventIncome, 2);
        $financialReport->international_event_expenses_total = round($SumInternationalEventExpense, 2);

        $MonetaryDonation = null;
        $SumDonationIncome = 0;
        $FieldCount = $input['MonDonationRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $donation_income = (float) preg_replace('/[^\d.]/', '', $input['DonationAmount'.$i] ?? 0);

            $MonetaryDonation[$i]['mon_donation_desc'] = $input['DonationDesc'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_info'] = $input['DonorInfo'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_amount'] = $input['DonationAmount'.$i] ?? null;
            $MonetaryDonation[$i]['mon_donation_date'] = $this->parseDateInput($input['MonDonationDate'.$i] ?? null);

            $SumDonationIncome += $donation_income;
        }
        $financialReport->monetary_donations_to_chapter = base64_encode(serialize($MonetaryDonation));

        $financialReport->donation_income_total = round($SumDonationIncome, 2);

        $NonMonetaryDonation = null;
        $FieldCount = $input['NonMonDonationRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $NonMonetaryDonation[$i]['nonmon_donation_desc'] = $input['NonMonDonationDesc'.$i] ?? null;
            $NonMonetaryDonation[$i]['nonmon_donation_info'] = $input['NonMonDonorInfo'.$i] ?? null;
            $NonMonetaryDonation[$i]['nonmon_donation_date'] = $this->parseDateInput($input['NonMonDonationDate'.$i] ?? null);
        }
        $financialReport->non_monetary_donations_to_chapter = base64_encode(serialize($NonMonetaryDonation));

        // OTHER INCOME & EXPENSES (seralized)
        $OtherOffice = null;
        $SumOtherIncome = 0;
        $SumOtherExpense = 0;
        $FieldCount = $input['OtherOfficeExpenseRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $other_income = (float) preg_replace('/[^\d.]/', '', $input['OtherOfficeIncome'.$i] ?? 0);
            $other_expenses = (float) preg_replace('/[^\d.]/', '', $input['OtherOfficeExpenses'.$i] ?? 0);

            $OtherOffice[$i]['other_desc'] = $input['OtherOfficeDesc'.$i] ?? null;
            $OtherOffice[$i]['other_expenses'] = $input['OtherOfficeExpenses'.$i] ?? null;
            $OtherOffice[$i]['other_income'] = $input['OtherOfficeIncome'.$i] ?? null;

            $SumOtherIncome += $other_income;
            $SumOtherExpense += $other_expenses;
        }
        $financialReport->other_income_and_expenses_array = base64_encode(serialize($OtherOffice));

        $financialReport->other_income_total = round($SumOtherIncome, 2);
        $financialReport->other_expense_total = round($SumOtherExpense, 2);

        // FINANCIAL SUMMARY
        $SumTotalIncome = round($SumTotalDues + $SumServiceProjectIncome + $SumPartyIncome +
            $SumInternationalEventIncome + $SumDonationIncome + $SumOtherIncome, 2);
        $SumTotalExpense = round($SumMeetingRoomFees + $SumChildrensRoomFees + $SumPartyExpense +
            $SumOfficeExpense + $SumReRegFee + $SumInternationalEventExpense + $SumOtherExpense, 2);
        $NetProfitLoss = round($SumTotalIncome - $SumTotalExpense, 2);

        $financialReport->sum_total_income = $SumTotalIncome;
        $financialReport->sum_total_expense = $SumTotalExpense;
        $financialReport->sum_total_net_income = $NetProfitLoss;

        // BANK RECONCILLIATION
        $financialReport->amount_reserved_from_previous_year = isset($input['AmountReservedFromLastYear']) ? preg_replace('/[^\d.]/', '', $input['AmountReservedFromLastYear']) : null;
        $financialReport->bank_balance_now = isset($input['BankBalanceNow']) ? preg_replace('/[^\d.]/', '', $input['BankBalanceNow']) : null;

        // Bank Reconciliation (serialized)
        $BankRecArray = null;
        $SumRecPayments = 0;
        $SumRecDeposits = 0;
        $FieldCount = $input['BankRecRowCount'];
        for ($i = 0; $i < $FieldCount; $i++) {
            $rec_payments = (float) preg_replace('/[^\d.]/', '', $input['BankRecPaymentAmount'.$i] ?? 0);
            $rec_deposits = (float) preg_replace('/[^\d.]/', '', $input['BankRecDepositAmount'.$i] ?? 0);

            $BankRecArray[$i]['bank_rec_date'] = $this->parseDateInput($input['BankRecDate'.$i] ?? null);
            $BankRecArray[$i]['bank_rec_check_no'] = $input['BankRecCheckNo'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_desc'] = $input['BankRecDesc'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_payment_amount'] = $input['BankRecPaymentAmount'.$i] ?? null;
            $BankRecArray[$i]['bank_rec_desposit_amount'] = $input['BankRecDepositAmount'.$i] ?? null;

            $SumRecPayments += $rec_payments;
            $SumRecDeposits += $rec_deposits;
        }
        $financialReport->bank_reconciliation_array = base64_encode(serialize($BankRecArray));

        $SumReconciliation = round($SumRecDeposits - $SumRecPayments, 2);

        $financialReport->bank_reconciliation = round($SumReconciliation, 2);

        $BeginningBalance = (float) preg_replace('/[^\d.]/', '', $input['AmountReservedFromLastYear'] ?? 0);
        $EndingBalance = round($BeginningBalance + $NetProfitLoss, 2);
        $StatementBalance = (float) preg_replace('/[^\d.]/', '', $input['BankBalanceNow'] ?? 0);
        $ReconciledBalance = round($StatementBalance + $SumReconciliation, 2);

        $financialReport->beginning_balance = $BeginningBalance;
        $financialReport->ending_balance = $EndingBalance;
        $financialReport->statement_balance = $StatementBalance;
        $financialReport->reconciled_balance = $ReconciledBalance;
    }

    public function saveAccordionQuestions(FinancialReport|FinancialReportFinal|FinancialReportQuestions $financialReportQuestions, array $input): void
    {
        // CHAPTER DUES
        $financialReportQuestions->changed_dues = $input['optChangeDues'] ?? null;
        $financialReportQuestions->different_dues = $input['optNewOldDifferent'] ?? null;
        $financialReportQuestions->not_all_full_dues = $input['optNoFullDues'] ?? null;
        $financialReportQuestions->not_full_dues_array = $input['Dues'] ?? null;
        $financialReportQuestions->total_new_members = $input['TotalNewMembers'] ?? null;
        $financialReportQuestions->total_renewed_members = $input['TotalRenewedMembers'] ?? null;
        $financialReportQuestions->dues_per_member = $input['MemberDues'] ?? null;
        $financialReportQuestions->total_new_members_changed_dues = $input['TotalNewMembersNewFee'] ?? null;
        $financialReportQuestions->total_renewed_members_changed_dues = $input['TotalRenewedMembersNewFee'] ?? null;
        $financialReportQuestions->dues_per_member_renewal = $input['MemberDuesRenewal'] ?? null;
        $financialReportQuestions->dues_per_member_new_changed = $input['NewMemberDues'] ?? null;
        $financialReportQuestions->dues_per_member_renewal_changed = $input['NewMemberDuesRenewal'] ?? null;
        $financialReportQuestions->members_who_paid_no_dues = $input['MembersNoDues'] ?? null;
        $financialReportQuestions->members_who_paid_partial_dues = $input['TotalPartialDuesMembers'] ?? null;
        $financialReportQuestions->total_partial_fees_collected = $input['PartialDuesMemberDues'] ?? null;
        $financialReportQuestions->total_associate_members = $input['TotalAssociateMembers'] ?? null;
        $financialReportQuestions->associate_member_fee = $input['AssociateMemberDues'] ?? null;

        // MONTHLY MEETING EXPENSES
        $financialReportQuestions->meeting_speakers = $input['MeetingSpeakers'] ?? null;
        $financialReportQuestions->meeting_speakers_array = $input['Speakers'] ?? null;
        $financialReportQuestions->discussion_topic_frequency = $input['SpeakerFrequency'] ?? null;
        $financialReportQuestions->childrens_room_sitters = $input['ChildrensRoom'] ?? null;

        // SERVICE PROJECTS
        $financialReportQuestions->at_least_one_service_project = $input['PerformServiceProject'] ?? null;
        $financialReportQuestions->at_least_one_service_project_explanation = $input['PerformServiceProjectExplanation'] ?? null;
        $financialReportQuestions->contributions_not_registered_charity = $input['ContributionsNotRegNP'] ?? null;
        $financialReportQuestions->contributions_not_registered_charity_explanation = $input['ContributionsNotRegNPExplanation'] ?? null;

        // INTERNATIONAL EVENTS & RE-REGISTRATION
        $financialReportQuestions->international_event = $input['InternationalEvent'] ?? null;

        // BANK RECONCILLIATION
        $financialReportQuestions->bank_statement_included = $input['BankStatementIncluded'] ?? null;
        $financialReportQuestions->bank_statement_included_explanation = $input['BankStatementIncludedExplanation'] ?? null;
        $financialReportQuestions->wheres_the_money = $input['WheresTheMoney'] ?? null;

        // 990 IRS FILING
        $financialReportQuestions->file_irs = $input['FileIRS'] ?? null;
        $financialReportQuestions->file_irs_explanation = $input['FileIRSExplanation'] ?? null;

        // CHAPTER QUESTIONS
        // Question 1
        $financialReportQuestions->bylaws_available = $input['ByLawsAvailable'] ?? null;
        $financialReportQuestions->bylaws_available_explanation = $input['ByLawsAvailableExplanation'] ?? null;
        // Question 2
        $financialReportQuestions->vote_all_activities = $input['VoteAllActivities'] ?? null;
        $financialReportQuestions->vote_all_activities_explanation = $input['VoteAllActivitiesExplanation'] ?? null;
        // Question 3
        $financialReportQuestions->child_outings = $input['ChildOutings'] ?? null;
        $financialReportQuestions->child_outings_explanation = $input['ChildOutingsExplanation'] ?? null;
        // Question 4
        $financialReportQuestions->playgroups = $input['Playgroups'] ?? null;
        $financialReportQuestions->had_playgroups_explanation = $input['PlaygroupsExplanation'] ?? null;
        // Question 5
        $financialReportQuestions->park_day_frequency = $input['ParkDays'] ?? null;
        $financialReportQuestions->park_day_frequency_explanation = $input['ParkDaysExplanation'] ?? null;
        // Question 6
        $financialReportQuestions->mother_outings = $input['MotherOutings'] ?? null;
        $financialReportQuestions->mother_outings_explanation = $input['MotherOutingsExplanation'] ?? null;
        // Question 7
        $financialReportQuestions->activity_array = $input['Activity'] ?? null;
        $financialReportQuestions->activity_other_explanation = $input['ActivityOtherExplanation'] ?? null;
        // Question 8
        $financialReportQuestions->offered_merch = $input['OfferedMerch'] ?? null;
        $financialReportQuestions->offered_merch_explanation = $input['OfferedMerchExplanation'] ?? null;
        // Question 9
        $financialReportQuestions->bought_merch = $input['BoughtMerch'] ?? null;
        $financialReportQuestions->bought_merch_explanation = $input['BoughtMerchExplanation'] ?? null;
        // Question 10
        $financialReportQuestions->purchase_pins = $input['BoughtPins'] ?? null;
        $financialReportQuestions->purchase_pins_explanation = $input['BoughtPinsExplanation'] ?? null;
        // Question 11
        $financialReportQuestions->receive_compensation = $input['ReceiveCompensation'] ?? null;
        $financialReportQuestions->receive_compensation_explanation = $input['ReceiveCompensationExplanation'];
        // Question 12
        $financialReportQuestions->financial_benefit = $input['FinancialBenefit'] ?? null;
        $financialReportQuestions->financial_benefit_explanation = $input['FinancialBenefitExplanation'] ?? null;
        // Question 13
        $financialReportQuestions->influence_political = $input['InfluencePolitical'] ?? null;
        $financialReportQuestions->influence_political_explanation = $input['InfluencePoliticalExplanation'] ?? null;
        // Question 14
        $financialReportQuestions->sister_chapter = $input['SisterChapter'] ?? null;
        $financialReportQuestions->sister_chapter_explanation = $input['SisterChapterExplanation'] ?? null;

        // AWARDS
        $financialReportQuestions->outstanding_follow_bylaws = $input['OutstandingFollowByLaws'] ?? null;
        $financialReportQuestions->outstanding_well_rounded = $input['OutstandingWellRounded'] ?? null;
        $financialReportQuestions->outstanding_communicated = $input['OutstandingCommunicated'] ?? null;
        $financialReportQuestions->outstanding_support_international = $input['OutstandingSupportMomsClub'] ?? null;

        // Awards (seralized)
        $ChapterAwards = null;
        $FieldCount = $input['ChapterAwardsRowCount'] ?? 0;
        for ($i = 0; $i < $FieldCount; $i++) {
            $ChapterAwards[$i]['awards_type'] = $input['ChapterAwardsType'.$i] ?? null;
            $ChapterAwards[$i]['awards_desc'] = $input['ChapterAwardsDesc'.$i] ?? null;
            $ChapterAwards[$i]['awards_approved'] = false;
        }
        $financialReportQuestions->chapter_awards = base64_encode(serialize($ChapterAwards));
    }

    private function parseDateInput(?string $rawDate): ?string
    {
        if (empty($rawDate)) {
            return null;
        }

        $rawDate = trim(str_replace('.', '/', $rawDate));

        // Try standard slash formats first
        foreach (['m/d/Y', 'm/d/y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $rawDate)->format('Y-m-d');
            } catch (\Exception $e) {
                // keep trying
            }
        }

        // Digit-only fallback: strip everything but digits, then try
        // plausible mm/dd/yyyy splits from the right (year is most reliable anchor)
        $digits = preg_replace('/\D/', '', $rawDate);
        $len = strlen($digits);

        $candidates = [];
        if ($len === 8) $candidates[] = ['mdY', $digits];           // 06292026
        if ($len === 6) {
            $candidates[] = ['mdy', $digits];                       // 062926
        }
        if ($len === 7) {
            $candidates[] = ['mdY', '0' . $digits];                 // dropped leading month zero
            $candidates[] = ['mdY', substr($digits,0,2).'0'.substr($digits,2)]; // dropped leading day zero
        }

        foreach ($candidates as [$fmt, $val]) {
            try {
                $date = Carbon::createFromFormat($fmt, $val);
                if ($date->year >= 2000 && $date->year <= 2099) {
                    return $date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // keep trying
            }
        }

        Log::warning("Date parse error: {$rawDate} - could not determine format, {$len} digits");
        return null;
    }

    /**
     * Save EOY Financial Report All Board Members
     */
    public function updateFinancialReport(Request $request, int $chapterId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userName = $user['userName'];
        $userEmail = $user['userEmail'];
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $input = $request->all();
        $reportReceived = $input['submitted'] ?? null;

        $chapter = Chapters::find($chapterId);
        $documentsEOY = DocumentsEOY::find($chapterId);
        $financialReport = FinancialReport::find($chapterId);
        $financialReportQuestions = FinancialReportQuestions::find($chapterId);
        $farthest_step_visited = max((int) $input['FurthestStep'], (int) $financialReport->farthest_step_visited_coord);

        DB::beginTransaction();
        try {
            $this->saveAccordionCalculations($financialReport, $input);
            $financialReport->farthest_step_visited = $farthest_step_visited;
            $financialReport->save();

            $this->saveAccordionQuestions($financialReportQuestions, $input);
            $financialReportQuestions->save();

            if ($reportReceived == 1) {
                $financialReport->completed_name = $userName;
                $financialReport->completed_email = $userEmail;
                $financialReport->submitted = Carbon::now();
                $financialReport->save();

                $documentsEOY->financial_report_received = 1;
                $documentsEOY->report_received = Carbon::now();
                $documentsEOY->report_extension = null;
                $documentsEOY->save();
            }

            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $baseQuery = $this->baseBoardController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chDocuments = $baseQuery['chDocuments'];
            $chEOYDocuments = $baseQuery['chEOYDocuments'];
            $chIRSDocuments = $baseQuery['chIRSDocuments'];
            $chReportDocuments = $baseQuery['chReportDocuments'];
            $chFinancialReport = $baseQuery['chFinancialReport'];
            $chFinancialReportQuestions = $baseQuery['chFinancialReportQuestions'];
            $chFinancialReportReview = $baseQuery['chFinancialReportReview'];
            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];
            $pcDetails = $baseQuery['pcDetails'];
            $emailCC = $baseQuery['emailCC'];
            $cc_id = $baseQuery['cc_id'];
            $reviewerEmail = $baseQuery['reviewerEmail'];

            $baseActiveBoardQuery = $this->baseChapterController->getActiveBoardDetails($chapterId);
            $PresDetails = $baseActiveBoardQuery['PresDetails'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPCData($pcDetails),
                $this->baseMailDataController->getPresData($PresDetails),
                $this->baseMailDataController->getFinancialReportData($chFinancialReport),
                $this->baseMailDataController->getFinancialDocumentsData($chDocuments, $chEOYDocuments, $chIRSDocuments, $chReportDocuments),
            );

            $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
            $reportYearRange = $reportYearOptions['reportYearRange'];

            if ($reportReceived == 1) {
                $pdfPath = $this->pdfController->saveFinancialReport($request, $chapterId, $PresDetails);   // Generate and Send the PDF
                Mail::to($userEmail)
                    ->cc($emailListChap)
                    ->queue(new EOYFinancialReportThankYou($mailData, $pdfPath, $reportYearRange));

                if ($chFinancialReportReview->reviewer_id == null) {
                    DB::update('UPDATE financial_report_review SET reviewer_id = ? where chapter_id = ?', [$cc_id, $chapterId]);
                    Mail::to($emailCC)
                        ->queue(new EOYFinancialSubmitted($mailData, $pdfPath, $reportYearRange));
                }

                if ($chFinancialReportReview->reviewer_id != null) {
                    Mail::to($reviewerEmail)
                        ->queue(new EOYFinancialSubmitted($mailData, $pdfPath, $reportYearRange));
                }

            }

            DB::commit();
            if ($reportReceived == 1) {
                return redirect()->back()->with('success', 'Report has been successfully Submitted');
            } else {
                return redirect()->back()->with('success', 'Report has been successfully updated');
            }
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('fail', 'Something went wrong Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function getRosterfile(): BinaryFileResponse
    {
        $filename = 'roster_template.xlsx';

        $file_path = '/home/momsclub/public_html/mimi/storage/app/public';

        return Response::download($file_path, $filename, [
            'Content-Length: '.filesize($file_path),
        ]);
    }

    /**
     * Show Disband Checklist andEOY Financial Report for Disbanded Chapters
     */
    public function editDisbandChecklist(Request $request, int $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $userTypeId = $user['userTypeId'];
        $userName = $loggedInName = $user['userName'];
        $userEmail = $user['userEmail'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $chActiveId = $baseQuery['chActiveId'];
        $stateShortName = $baseQuery['stateShortName'];
        $chDocuments = $baseQuery['chDocuments'];
        $chEOYDocuments = $baseQuery['chEOYDocuments'];
        $chIRSDocuments = $baseQuery['chIRSDocuments'];
        $chReportDocuments = $baseQuery['chReportDocuments'];
        $chFinancialReport = $baseQuery['chFinancialReportFinal'];
        $chFinancialReportQuestions = $baseQuery['chFinancialReportFinalQuestions'];

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        $chDisbanded = $baseQuery['chDisbanded'];

        $ownBoardDetails = Boards::with(['state', 'country', 'user'])
            ->where('chapter_id', $chId)
            ->where('user_id', $userId)
            ->first();

        $PresDetails = $baseQuery['PresDetails'];
        $bdData = $this->positionConditionsService->getViewAs($userTypeId, $userId, $PresDetails, $ownBoardDetails);
        $bdPositionId = $bdData['bdPositionId'];
        $borDetails = $bdData['bdDetails'];
        $bdTypeId = $bdData['bdTypeId'];

        $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userTypeId' => $userTypeId,
            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
            'chDisbanded' => $chDisbanded, 'chActiveId' => $chActiveId, 'resourceCategories' => $resourceCategories, 'userAdmin' => $userAdmin, 'chEOYDocuments' => $chEOYDocuments,
            'bdPositionId' => $bdPositionId, 'borDetails' => $borDetails, 'bdTypeId' => $bdTypeId, 'PresDetails' => $PresDetails,
            'chIRSDocuments' => $chIRSDocuments, 'chReportDocuments' => $chReportDocuments, 'chFinancialReportQuestions' => $chFinancialReportQuestions
        ];

        return view('boards.disband.editdisbandchecklist')->with($data);

    }

    /**
     * Save Financial Report for Disbanded Chapters
     */
    public function updateDisbandReport(Request $request, int $chapterId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userName = $user['userName'];
        $userEmail = $user['userEmail'];
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $input = $request->all();
        $reportReceived = $input['submitted'] ?? null;

        $financialReport = FinancialReportFinal::find($chapterId);
        $financialReportQuestions = FinancialReportFinalQuestions::find($chapterId);
        $farthest_step_visited = max((int) $input['FurthestStep'], (int) $financialReport->farthest_step_visited_coord);

        $documentsEOY = DocumentsEOY::find($chapterId);
        $chapter = Chapters::find($chapterId);
        $disbandChecklist = DisbandedChecklist::find($chapterId);

        DB::beginTransaction();
        try {
            $this->saveAccordionCalculations($financialReport, $input);
            $financialReport->farthest_step_visited = $farthest_step_visited;
            $financialReport->save();

            $this->saveAccordionQuestions($financialReportQuestions, $input);
            $financialReportQuestions->save();

            if ($reportReceived == 1) {
                $financialReport->completed_name = $userName;
                $financialReport->completed_email = $userEmail;
                $financialReport->submitted = Carbon::now();
                $financialReport->save();

                $documentsEOY->final_report_received = 1;
                $documentsEOY->save();

                $disbandChecklist->file_financial = 1;
                $disbandChecklist->save();
            }

            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $disbandChecklistUpd = DisbandedChecklist::find($chapterId);
            $checklistComplete = ($disbandChecklistUpd->final_payment == '1' && $disbandChecklistUpd->donate_funds == '1' &&
            $disbandChecklistUpd->destroy_manual == '1' && $disbandChecklistUpd->remove_online == '1' &&
            $disbandChecklistUpd->file_irs == '1' && $disbandChecklistUpd->file_financial == '1');

            $baseQuery = $this->baseBoardController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chDocuments = $baseQuery['chDocuments'];
            $chEOYDocuments = $baseQuery['chEOYDocuments'];
            $chIRSDocuments = $baseQuery['chIRSDocuments'];
            $chReportDocuments = $baseQuery['chReportDocuments'];
            $chFinancialReport = $baseQuery['chFinancialReportFinal'];
            $chFinancialReportQuestions = $baseQuery['chFinancialReportFinalQuestions'];
            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];
            $pcDetails = $baseQuery['pcDetails'];
            $emailCC = $baseQuery['emailCC'];
            $cc_id = $baseQuery['cc_id'];

            $baseDsibandedBoardQuery = $this->baseChapterController->getDisbandedBoardDetails($chapterId);
            $PresDetails = $baseDsibandedBoardQuery['PresDetails'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPCData($pcDetails),
                $this->baseMailDataController->getFinancialReportData($chFinancialReport),
                $this->baseMailDataController->getPresData($PresDetails),
            );

            if ($documentsEOY->final_report_received == '1') {
                $pdfPath = $this->pdfController->saveFinalFinancialReport($request, $chapterId, $PresDetails);   // Generate and Send the PDF
                Mail::to($userEmail)
                    ->cc($emailListChap)
                    ->queue(new DisbandReportThankYou($mailData, $pdfPath));

                if ($chFinancialReport->reviewer_id == null) {
                    DB::update('UPDATE financial_report SET reviewer_id = ? where chapter_id = ?', [$cc_id, $chapterId]);
                }

                Mail::to($emailCC)
                    ->queue(new DisbandReportCCNotice($mailData, $pdfPath));
            }

            if ($documentsEOY->final_report_received == '1' && $checklistComplete) {
                Mail::to($userEmail)
                    ->cc($emailListChap)
                    ->queue(new DisbandChecklistCompleteThankYou($mailData));

                Mail::to($emailCC)
                    ->queue(new DisbandChecklistCompleteCCNotice($mailData));
            }

            DB::commit();
            if ($reportReceived == 1) {
                return redirect()->back()->with('success', 'Report has been successfully Submitted');
            } else {
                return redirect()->back()->with('success', 'Report has been successfully updated');
            }
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('fail', 'Something went wrong Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Save Disbanded Checklsit Questions
     */
    public function updateDisbandChecklist(Request $request, int $chapterId): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userEmail = $user['userEmail'];
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        $chapter = Chapters::find($chapterId);
        $documentsEOY = DocumentsEOY::find($chapterId);
        $disbandChecklist = DisbandedChecklist::find($chapterId);

        DB::beginTransaction();
        try {
            $disbandChecklist->final_payment = $request->has('FinalPayment') ? 1 : 0;
            $disbandChecklist->donate_funds = $request->has('DonateFunds') ? 1 : 0;
            $disbandChecklist->destroy_manual = $request->has('DestroyManual') ? 1 : 0;
            $disbandChecklist->remove_online = $request->has('RemoveOnline') ? 1 : 0;
            $disbandChecklist->file_irs = $request->has('FileIRS') ? 1 : 0;
            $disbandChecklist->file_financial = $request->has('FileFinancial') ? 1 : 0;

            $disbandChecklist->save();

            $chapter->updated_by = $updatedBy;
            $chapter->updated_id = $updatedId;
            $chapter->save();

            $disbandChecklistUpd = DisbandedChecklist::find($chapterId);
            $checklistComplete = ($disbandChecklistUpd->final_payment == '1' && $disbandChecklistUpd->donate_funds == '1' &&
                $disbandChecklistUpd->destroy_manual == '1' && $disbandChecklistUpd->remove_online == '1' &&
                $disbandChecklistUpd->file_irs == '1' && $disbandChecklistUpd->file_financial == '1');

            $baseQuery = $this->baseBoardController->getChapterDetails($chapterId);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chDocuments = $baseQuery['chDocuments'];
            $chEOYDocuments = $baseQuery['chEOYDocuments'];
            $chIRSDocuments = $baseQuery['chIRSDocuments'];
            $chReportDocuments = $baseQuery['chReportDocuments'];
            $chFinancialReport = $baseQuery['chFinancialReport'];
            $chFinancialReportQuestions = $baseQuery['chFinancialReportQuestions'];
            $emailListChap = $baseQuery['emailListChap'];
            $emailListCoord = $baseQuery['emailListCoord'];
            $pcDetails = $baseQuery['pcDetails'];
            $emailCC = $baseQuery['emailCC'];

            $mailData = array_merge(
                $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                $this->baseMailDataController->getPCData($pcDetails),
                $this->baseMailDataController->getFinancialReportData($chFinancialReport),
            );

            if ($documentsEOY->final_financial_report_received == '1' && $checklistComplete) {
                Mail::to($userEmail)
                    ->cc($emailListChap)
                    ->queue(new DisbandChecklistCompleteThankYou($mailData));

                Mail::to($emailCC)
                    ->queue(new DisbandChecklistCompleteCCNotice($mailData));
            }

            DB::commit();

            return redirect()->back()->with('success', 'Checklist has been successfully updated');
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('fail', 'Something went wrong Please try again.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Update or create incoming board member
     */
    public function updateIncomingBoardMember(int $chapterId, int $positionId, string $positionPrefix, string $vacantField, string $idField, Request $request, string $updatedBy, int $userId)
    {
        $boardDetails = BoardsIncoming::where('chapter_id', $chapterId)
            ->where('board_position_id', $positionId)
            ->get();

        $isVacant = $request->input($vacantField) == 'on';
        $hasExisting = count($boardDetails) > 0;

        if ($hasExisting) {
            if ($isVacant) {
                // Delete board member if now vacant
                BoardsIncoming::where('chapter_id', $chapterId)
                    ->where('board_position_id', $positionId)
                    ->delete();
            } else {
                // Update existing board member
                $memberId = $request->input($idField);
                BoardsIncoming::where('id', $memberId)
                    ->update($this->getBoardMemberData($request, $positionPrefix, $updatedBy, $userId));
            }
        } else {
            if (! $isVacant) {
                // Create new board member
                BoardsIncoming::create(array_merge(
                    ['chapter_id' => $chapterId, 'board_position_id' => $positionId],
                    $this->getBoardMemberData($request, $positionPrefix, $updatedBy, $userId)
                ));
            }
        }
    }

    /**
     * Get board member data from request
     */
    public function getBoardMemberData(Request $request, string $prefix, string $updatedBy, int $updatedId): array
    {
        return [
            'first_name' => $request->input($prefix.'fname'),
            'last_name' => $request->input($prefix.'lname'),
            'email' => $request->input($prefix.'email'),
            'street_address' => $request->input($prefix.'street'),
            'city' => $request->input($prefix.'city'),
            'state_id' => $request->input($prefix.'state'),
            'zip' => $request->input($prefix.'zip'),
            'country_id' => $request->input($prefix.'country') ?? '198',
            'phone' => $request->input($prefix.'phone'),
            'updated_by' => $updatedBy,
            'updated_id' => $updatedId,
        ];
    }

    public function activateSingleBoardStandalone(Request $request, int $id): RedirectResponse
    {
        try {
            $status = $this->activateSingleBoard($request, $id);

            if ($status == 'success') {
                return redirect()->back()->with('success', 'Board activation successful');
            } else {
                return redirect()->back()->with('fail', 'Board activation failed');
            }
        } catch (\Exception $e) {
            Log::error('Board activation failed: '.$e->getMessage());

            return redirect()->back()->with('fail', 'Board activation failed');
        }
    }

    public function activateAllBoardsStandalone(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['cdId'];
        $confId = $user['confId'];
        $regId = $user['regId'];
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];

        $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        $reportYearEnd = $reportYearOptions['reportYearEnd'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']
            ->where(function ($query) use ($reportYearEnd) {
                $query->where(function ($q) use ($reportYearEnd) {
                    $q->where('start_year', '<', $reportYearEnd)
                        ->orWhere(function ($q) use ($reportYearEnd) {
                            $q->where('start_year', '=', $reportYearEnd)
                                ->where('start_month_id', '<', 7);
                        });
                });
            })
            ->get();

        $activationStatuses = [];

        foreach ($chapterList as $chapter) {
            $hasIncoming = BoardsIncoming::where('chapter_id', $chapter->id)->exists();

            if ($hasIncoming) {
                try {
                    $result = $this->activateSingleBoard($request, $chapter->id);
                    $activationStatuses[$chapter->id] = $result == 'success' ? 'success' : 'fail';
                } catch (\Exception $e) {
                    $activationStatuses[$chapter->id] = 'fail';
                    Log::error("Board activation unsuccessful for chapter {$chapter->id}: ".$e->getMessage());
                }
            }
        }

        $successCount = count(array_filter($activationStatuses, fn ($s) => $s == 'success'));
        $totalCount = count($activationStatuses);

        if ($totalCount == 0) {
            return redirect()->to('/eoyreports/boardreport')->with('info', 'No Incoming Board Members for Activation');
        } elseif ($successCount == $totalCount) {
            return redirect()->to('/eoyreports/boardreport')->with('success', 'All Board Info has been successfully activated');
        } elseif ($successCount > 0) {
            return redirect()->to('/eoyreports/boardreport')->with('warning', "Board activation completed: {$successCount}/{$totalCount} successful");
        } else {
            return redirect()->to('/eoyreports/boardreport')->with('fail', 'Board activation failed for all chapters');
        }
    }

    // Unified method that handles both single and batch activations
    public function activateSingleBoard(Request $request, int $id)
    {
        $user = $this->userController->loadUserInformation($request);
        $userId = $user['userId'];
        $updatedId = $user['userId'];
        $updatedBy = $user['userName'];

        // Calculate the fiscal year (current year - next year)
        // $reportYearOptions = $this->positionConditionsService->getReportYearOptions();
        // $reportYearRange = $reportYearOptions['reportYearRange'];

        // $resources = Resources::with('resourceCategory')->get();
        // $instructionsName = 'Officer Packet';
        // $matchingInstructions = $resources->where('name', $instructionsName)->first();
        // $pdfPath = $matchingInstructions ? 'https://drive.google.com/uc?export=download&id='.$matchingInstructions->file_path : null;

        $status = 'fail';
        $BoardsIncomingDetails = BoardsIncoming::where('chapter_id', $id)->get();

        if ($BoardsIncomingDetails && count($BoardsIncomingDetails) > 0) {
            DB::beginTransaction();
            try {
                $boardDetails = Boards::where('chapter_id', $id)->get();

                if ($boardDetails && count($boardDetails) > 0) {
                    $borDetails = Boards::with('user')->where('chapter_id', $id)->get();
                    foreach ($borDetails as $record) {
                        $user_id = $record->user_id;
                        $userDetails = User::find($user_id);

                        $userDetails->type_id = UserTypeEnum::OUTGOING;
                        $userDetails->save();

                        BoardsOutgoing::create([  // Create outgoing board details
                            'id' => $record->id,
                            'user_id' => $record->user_id,
                            'first_name' => $record->first_name,
                            'last_name' => $record->last_name,
                            'email' => $record->email,
                            'board_position_id' => $record->board_position_id,
                            'chapter_id' => $id,
                            'street_address' => $record->street_address,
                            'city' => $record->city,
                            'state_id' => $record->state_id,
                            'zip' => $record->zip,
                            'country_id' => $record->country_id,
                            'phone' => $record->phone,
                            'updated_by' => $updatedBy,
                            'updated_id' => $updatedId,
                        ]);

                    }

                    try {
                        $this->emailCampaignController->sendOldBoardThankYou($id);
                    } catch (\Exception $e) {
                        Log::warning("sendOldBoardThankYou failed for chapter {$id}: " . $e->getMessage());
                    }

                    Boards::where('chapter_id', $id)->delete();
                }

                foreach ($BoardsIncomingDetails as $incomingRecord) {
                    $existingUser = User::where('email', $incomingRecord->email)->first();
                    if ($existingUser) {
                        $existingUser->first_name = $incomingRecord->first_name;
                        $existingUser->last_name = $incomingRecord->last_name;
                        $existingUser->email = $incomingRecord->email;
                        $existingUser->type_id = UserTypeEnum::BOARD;
                        $existingUser->is_active = 1;
                        $existingUser->save();
                        $userId = $existingUser->id;

                    } else {
                        $newUser = User::create([  // Create user details if new
                            'first_name' => $incomingRecord->first_name,
                            'last_name' => $incomingRecord->last_name,
                            'email' => $incomingRecord->email,
                            'password' => Hash::make('TempPass4You'),
                            'type_id' => UserTypeEnum::BOARD,
                            'is_active' => UserStatusEnum::ACTIVE,
                        ]);
                        $userId = $newUser->id;
                    }

                    Boards::create([  // Create board details if new
                        'user_id' => $userId,
                        'first_name' => $incomingRecord->first_name,
                        'last_name' => $incomingRecord->last_name,
                        'email' => $incomingRecord->email,
                        'board_position_id' => $incomingRecord->board_position_id,
                        'chapter_id' => $id,
                        'street_address' => $incomingRecord->street_address,
                        'city' => $incomingRecord->city,
                        'state_id' => $incomingRecord->state_id,
                        'zip' => $incomingRecord->zip,
                        'country_id' => $incomingRecord->country_id,
                        'phone' => $incomingRecord->phone,
                        'updated_by' => $updatedBy,
                        'updated_id' => $updatedId,
                    ]);
                }

                try {
                    $this->emailCampaignController->sendNewBoardWelcome($id);
                } catch (\Exception $e) {
                    Log::warning("sendNewBoardWelcome failed for chapter {$id}: " . $e->getMessage());
                }

                $documentsEOY = DocumentsEOY::find($id);
                $documentsEOY->new_board_active = 1;
                $documentsEOY->save();

                BoardsIncoming::where('chapter_id', $id)->delete();

                // $baseQuery = $this->baseChapterController->getChapterDetails($id);
                // $chDetails = $baseQuery['chDetails'];
                // $pcDetails = $baseQuery['pcDetails'];
                // $chPcId = $chDetails->primary_coordinator_id;
                // $stateShortName = $baseQuery['stateShortName'];
                // $emailListChap = $baseQuery['emailListChap'];  // Full Board
                // $emailListCoord = $baseQuery['emailListCoord']; // Full Coord List
                // $emailCCData = $this->userController->loadConferenceCoord($chPcId);
                // // $emailPC = $baseQuery['emailPC'];  // PC Email

                // $adminyearOptions = $this->positionConditionsService->getReportYearOptions();
                // $boardReportRange = $adminyearOptions['boardReportRange'];

                // $mailData = array_merge(
                //     $this->baseMailDataController->getChapterData($chDetails, $stateShortName),
                //     $this->baseMailDataController->getPCData($pcDetails),
                //     $this->baseMailDataController->getCCData($emailCCData),
                //     // $this->baseMailDataController->getUserData($user),
                //     [
                //         'reportYearRange' => $reportYearRange,
                //         'boardReportRange' => $boardReportRange,
                //     ]
                // );

                // Mail::to($emailListChap)
                //     ->cc($emailListCoord)
                //     ->queue(new NewBoardWelcome($mailData, $pdfPath));

                DB::commit();

                return 'success';
            } catch (\Exception $e) {
                DB::rollback();
                throw $e; // always re-throws, caller decides what to do
            }

        }

        return $status;
    }
}
