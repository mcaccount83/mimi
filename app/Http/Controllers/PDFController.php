<?php

namespace App\Http\Controllers;

use App\Models\FinancialReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PDFController extends Controller
{
    public function __construct()
    {
        //$this->middleware('preventBackHistory');
        $this->middleware('auth')->except('logout');
    }

    public $pdfData = [];

    /**
     * Show Financial Report PDF All Board Members
     */
    public function generatePdf($chapterId)
    {
        // Load financial report data, chapter details, and any other data you need
        $financial_report_array = FinancialReport::find($chapterId);

        $chapterDetails = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.name as chapter_name', 'chapters.ein as ein', 'chapters.territory as boundaries',
                'chapters.financial_report_received as financial_report_received', 'st.state_short_name as state',
                'chapters.conference as conf', 'chapters.primary_coordinator_id as pcid')
            ->leftJoin('state as st', 'chapters.state', '=', 'st.id')
            ->where('chapters.is_active', '=', '1')
            ->where('chapters.id', '=', $chapterId)
            ->get();

        $pdfData = [
            'chapter_name' => $chapterDetails[0]->chapter_name,
            'state' => $chapterDetails[0]->state,
            'ein' => $chapterDetails[0]->ein,
            'boundaries' => $chapterDetails[0]->boundaries,
            'changed_dues' => $financial_report_array->changed_dues,
            'different_dues' => $financial_report_array->different_dues,
            'not_all_full_dues' => $financial_report_array->not_all_full_dues,
            'total_new_members' => $financial_report_array->total_new_members,
            'total_renewed_members' => $financial_report_array->total_renewed_members,
            'dues_per_member' => $financial_report_array->dues_per_member,
            'total_new_members_changed_dues' => $financial_report_array->total_new_members_changed_dues,
            'total_renewed_members_changed_dues' => $financial_report_array->total_renewed_members_changed_dues,
            'dues_per_member_renewal' => $financial_report_array->dues_per_member_renewal,
            'dues_per_member_new_changed' => $financial_report_array->dues_per_member_new_changed,
            'dues_per_member_renewal_changed' => $financial_report_array->dues_per_member_renewal_changed,
            'members_who_paid_no_dues' => $financial_report_array->members_who_paid_no_dues,
            'members_who_paid_partial_dues' => $financial_report_array->members_who_paid_partial_dues,
            'total_partial_fees_collected' => $financial_report_array->total_partial_fees_collected,
            'total_associate_members' => $financial_report_array->total_associate_members,
            'associate_member_fee' => $financial_report_array->associate_member_fee,
            'manditory_meeting_fees_paid' => $financial_report_array->manditory_meeting_fees_paid,
            'voluntary_donations_paid' => $financial_report_array->voluntary_donations_paid,
            'paid_baby_sitters' => $financial_report_array->paid_baby_sitters,
            'childrens_room_expenses' => $financial_report_array->childrens_room_expenses,
            'service_project_array' => $financial_report_array->service_project_array,
            'party_expense_array' => $financial_report_array->party_expense_array,
            'office_printing_costs' => $financial_report_array->office_printing_costs,
            'office_postage_costs' => $financial_report_array->office_postage_costs,
            'office_membership_pins_cost' => $financial_report_array->office_membership_pins_cost,
            'office_other_expenses' => $financial_report_array->office_other_expenses,
            'international_event_array' => $financial_report_array->international_event_array,
            'annual_registration_fee' => $financial_report_array->annual_registration_fee,
            'monetary_donations_to_chapter' => $financial_report_array->monetary_donations_to_chapter,
            'non_monetary_donations_to_chapter' => $financial_report_array->non_monetary_donations_to_chapter,
            'other_income_and_expenses_array' => $financial_report_array->other_income_and_expenses_array,
            'amount_reserved_from_previous_year' => $financial_report_array->amount_reserved_from_previous_year,
            'bank_balance_now' => $financial_report_array->bank_balance_now,
            'petty_cash' => $financial_report_array->petty_cash,
            'bank_reconciliation_array' => $financial_report_array->bank_reconciliation_array,
            'receive_compensation' => $financial_report_array->receive_compensation,
            'receive_compensation_explanation' => $financial_report_array->receive_compensation_explanation,
            'financial_benefit' => $financial_report_array->financial_benefit,
            'financial_benefit_explanation' => $financial_report_array->financial_benefit_explanation,
            'influence_political' => $financial_report_array->influence_political,
            'influence_political_explanation' => $financial_report_array->influence_political_explanation,
            'vote_all_activities' => $financial_report_array->vote_all_activities,
            'vote_all_activities_explanation' => $financial_report_array->vote_all_activities_explanation,
            'purchase_pins' => $financial_report_array->purchase_pins,
            'purchase_pins_explanation' => $financial_report_array->purchase_pins_explanation,
            'bought_merch' => $financial_report_array->bought_merch,
            'bought_merch_explanation' => $financial_report_array->bought_merch_explanation,
            'offered_merch' => $financial_report_array->offered_merch,
            'offered_merch_explanation' => $financial_report_array->offered_merch_explanation,
            'bylaws_available' => $financial_report_array->bylaws_available,
            'bylaws_available_explanation' => $financial_report_array->bylaws_available_explanation,
            'childrens_room_sitters' => $financial_report_array->childrens_room_sitters,
            'childrens_room_sitters_explanation' => $financial_report_array->childrens_room_sitters_explanation,
            'had_playgroups' => $financial_report_array->had_playgroups,
            'playgroups' => $financial_report_array->playgroups,
            'had_playgroups_explanation' => $financial_report_array->had_playgroups_explanation,
            'child_outings' => $financial_report_array->child_outings,
            'child_outings_explanation' => $financial_report_array->child_outings_explanation,
            'mother_outings' => $financial_report_array->mother_outings,
            'mother_outings_explanation' => $financial_report_array->mother_outings_explanation,
            'meeting_speakers' => $financial_report_array->meeting_speakers,
            'meeting_speakers_explanation' => $financial_report_array->meeting_speakers_explanation,
            'meeting_speakers_array' => $financial_report_array->meeting_speakers_array,
            'discussion_topic_frequency' => $financial_report_array->discussion_topic_frequency,
            'park_day_frequency' => $financial_report_array->park_day_frequency,
            'activity_array' => $financial_report_array->activity_array,
            'contributions_not_registered_charity' => $financial_report_array->contributions_not_registered_charity,
            'contributions_not_registered_charity_explanation' => $financial_report_array->contributions_not_registered_charity_explanation,
            'at_least_one_service_project' => $financial_report_array->at_least_one_service_project,
            'at_least_one_service_project_explanation' => $financial_report_array->at_least_one_service_project_explanation,
            'sister_chapter' => $financial_report_array->sister_chapter,
            'international_event' => $financial_report_array->international_event,
            'file_irs' => $financial_report_array->file_irs,
            'file_irs_explanation' => $financial_report_array->file_irs_explanation,
            'bank_statement_included' => $financial_report_array->bank_statement_included,
            'bank_statement_included_explanation' => $financial_report_array->bank_statement_included_explanation,
            'wheres_the_money' => $financial_report_array->wheres_the_money,
            'completed_name' => $financial_report_array->completed_name,
            'completed_email' => $financial_report_array->completed_email,
            'submitted' => $financial_report_array->submitted,
        ];

        $pdf = Pdf::loadView('pdf.financialreport', compact('pdfData'));

        $filename = date('Y') - 1 .'-'.date('Y').'_'.$pdfData['state'].'_'.$pdfData['chapter_name'].'_FinancialReport.pdf';

        return $pdf->stream($filename, ['Attachment' => 0]); // Stream the PDF

    }
}
