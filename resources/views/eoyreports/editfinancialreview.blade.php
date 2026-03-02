@extends('layouts.mimi_theme')

@section('page_title', 'Financial Report')
@section('breadcrumb', 'Financial Report Review')

@section('content')
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
            <form id="financial_report" name="financial_report" role="form" data-bs-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("eoyreports.updatefinancialreport", $chDetails->id) }}' novalidate>
                @csrf
                <input type="hidden" name="submitted" id="submitted" value="{{ $chEOYDocuments->financial_report_received ?? '' }}" />
                <input type="hidden" name="FurthestStep" id="FurthestStep" value="{{ $chFinancialReport->farthest_step_visited_coord > 0 ? $chFinancialReport->farthest_step_visited_coord : '0' }}" />
                <input type="hidden" name="submit_type" id="submit_type" value="" />

          <!-- Profile Image -->
          <div class="card card-primary card-outline">
          <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                    <h3 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                    <p class="mb-0">{{ $conferenceDescription }} Conference, {{ $conferenceDescription }} Region
                  </p>
                </div>

          <ul class="list-group list-group-flush mb-3">
            <li class="list-group-item">
               <label>Review Summary</label><br>
            Answers from questios in previous sections will show up here after they have been saved.<br>
            <br>
            @if ($chEOYDocuments->financial_report_received)
            @if ($chEOYDocuments->$yearColumnName != null)
                <div class="row mb-1">
                    <div class="col-auto fw-bold">Financial Report PDF:</div>
                    <div class="col text-end">
                        <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id={{ $chEOYDocuments->$yearColumnName }}">Download PDF</a>
                    </div>
                </div>
            @endif
                <div class="row mb-1">
                    <div class="col-auto fw-bold">Chapter Roster File:</div>
                    <div class="col text-end">
                        @if ($chEOYDocuments->roster_path != null)
                            <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id={{ $chEOYDocuments->roster_path }}">Chapter Roster</a>
                        @else
                           <span class="badge bg-secondary fs-7">No file attached</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-auto fw-bold">Primary Bank Statement:</div>
                    <div class="col text-end">
                    @if ($chEOYDocuments->statement_1_path != null)
                        <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id={{ $chEOYDocuments->statement_1_path }}">Primary Statement</a>
                    @else
                        <span class="badge bg-secondary fs-7">No file attached</span>
                    @endif
                </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Additional Bank Statement:</div>
                    <div class="col text-end">
                @if ($chEOYDocuments->statement_2_path != null)
                    <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id={{ $chEOYDocuments->statement_2_path }}">Additional Statement</a>
                @else
                    <span class="badge bg-secondary fs-7">No file attached</span>
                @endif
            </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">990N Filing:</div>
                    <div class="col text-end">
                @if ($chEOYDocuments->irs_path != null)
                    <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id={{ $chEOYDocuments->irs_path }}">990N Confirmation</a>
                @else
                    <span class="badge bg-secondary fs-7">No file attached</span>
                @endif
            </div>
            </div>
            @endif
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Excel roster attached and complete:</div>
                    <div class="col text-end">
                         @php $val = $chFinancialReportReview->roster_attached; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
            </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Number of members/dues seems right:</div>
                    <div class="col text-end">
                        @php $val = $chFinancialReportReview->renewal_seems_right; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
            </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">At least one service project completed:</div>
                    <div class="col text-end">
                         @php $val = $chFinancialReportReview->minimum_service_project; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
            </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Donation to M2M Fund:</div>
                    <div class="col text-end">
                         @php $val = $chFinancialReportReview->m2m_donation; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
            </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Party Percentage:</div>

                    @php
                        $newMembers = $chFinancialReport->total_new_members * $chFinancialReport->dues_per_member;
                        $renewalMembers = $chFinancialReport->total_renewed_members * $chFinancialReport->dues_per_member;
                        $renewalMembersDiff = $chFinancialReport->total_renewed_members * $chFinancialReport->dues_per_member_renewal;
                        $newMembersNew = $chFinancialReport->total_new_members_changed_dues * $chFinancialReport->dues_per_member_new_changed;
                        $renewMembersNew = $chFinancialReport->total_renewed_members_changed_dues * $chFinancialReport->dues_per_member_new_changed;
                        $renewMembersNewDiff = $chFinancialReport->total_renewed_members_changed_dues * $chFinancialReport->dues_per_member_renewal_changed;
                        $partialMembers = $chFinancialReport->members_who_paid_partial_dues * $chFinancialReport->total_partial_fees_collected;
                        $associateMembers = $chFinancialReport->total_associate_members * $chFinancialReport->associate_member_fee;

                        $totalMembers = $chFinancialReport->total_new_members + $chFinancialReport->total_renewed_members + $chFinancialReport->total_new_members_changed_dues + $chFinancialReport->total_renewed_members_changed_dues
                                + $chFinancialReport->members_who_paid_partial_dues + $chFinancialReport->total_associate_members + $chFinancialReport->members_who_paid_no_dues;

                        if ($chFinancialReport->different_dues == 1 && $chFinancialReport->changed_dues == 1) {
                            $totalDues = $newMembers + $renewalMembersDiff + $newMembersNew + $renewMembersNewDiff + $partialMembers + $associateMembers;
                        } elseif ($chFinancialReport->different_dues == 1) {
                            $totalDues = $newMembers + $renewalMembersDiff + $partialMembers + $associateMembers;
                        } elseif ($chFinancialReport->changed_dues == 1) {
                            $totalDues = $newMembers + $renewalMembers + $newMembersNew + $renewMembersNew + $partialMembers + $associateMembers;
                        } else {
                            $totalDues = $newMembers + $renewalMembers + $partialMembers + $associateMembers;
                        }

                        $party_expenses = null;
                        $totalPartyIncome = 0;
                        $totalPartyExpense = 0;
                        $partyPercentage = 0;

                        if (isset($chFinancialReport['party_expense_array'])) {  // ← Keep bracket notation for isset() on serialized data
                            $blobData = base64_decode($chFinancialReport['party_expense_array']);  // ← Keep bracket notation here too
                            $party_expenses = unserialize($blobData);

                            if ($party_expenses != false) {
                                foreach ($party_expenses as $row) {
                                    $income = is_numeric(str_replace(',', '', $row['party_expense_income']))
                                        ? floatval(str_replace(',', '', $row['party_expense_income'])) : 0;
                                    $expense = is_numeric(str_replace(',', '', $row['party_expense_expenses']))
                                        ? floatval(str_replace(',', '', $row['party_expense_expenses'])) : 0;

                                    $totalPartyIncome += $income;
                                    $totalPartyExpense += $expense;
                                }

                                if (!empty($totalDues) && $totalDues != 0) {
                                    $partyPercentage = ($totalPartyExpense - $totalPartyIncome) / $totalDues;
                                }
                            }
                        }

                    $badgeClass = match(true) {
                        is_null($chFinancialReportReview->party_percentage) => 'bg-secondary',
                        $chFinancialReportReview->party_percentage == 0     => 'bg-danger',
                        $chFinancialReportReview->party_percentage == 1     => 'bg-warning text-dark',
                        $chFinancialReportReview->party_percentage == 2     => 'bg-success',
                        default                                             => 'bg-secondary',
                    };
                @endphp

                <div class="col text-end">
                    <span class="badge {{ $badgeClass }} fs-7">{{ number_format($partyPercentage * 100, 2) }}%</span>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-auto fw-bold">Party Percentage less than 15%:</div>
                    <div class="col text-end">
                    @if(is_null($chFinancialReportReview->party_percentage))
                        <span class="badge bg-secondary fs-7">Please Review</span>
                    @elseif($chFinancialReportReview->party_percentage == 0)
                        <span class="badge bg-danger fs-7">They are over 20%</span>
                    @elseif($chFinancialReportReview->party_percentage == 1)</span>
                        <span class="badge bg-warning text-dark fs-7">They are between 15-20%
                    @elseif($chFinancialReportReview->party_percentage == 2)
                        <span class="badge bg-success fs-7">They are under 15%</span>
                    @else
                        <span class="badge bg-secondary fs-7">Please Review</span>
                    @endif
            </div>
            </div>
             <div class="row mb-1">
                    <div class="col-auto fw-bold">Attended International Event:</div>
                    <div class="col text-end">
                        @php $val = $chFinancialReportReview->attended_training; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
            </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Total income/revenue less than $50,000:</div>
                    <div class="col text-end">
                          @php $val = $chFinancialReportReview->total_income_less; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
            </div>
            </div>
            <div class="row mb-1">
                <div class="col-auto fw-bold">Beginning Balance Match:</div>
                <div class="col text-end">
                    @php $val = $chFinancialReportReview->beginning_balance; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-auto fw-bold">Current bank statement included:</div>
                <div class="col text-end">
                    @php $val = $chFinancialReportReview->bank_statement_included; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
                </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Treasury & Reconciled Balances Match:</div>
                    <div class="col text-end">
                    @if(is_null($chFinancialReportReview->bank_statement_matches))
                        <span class="badge bg-secondary fs-7">Please Review</span>
                    @elseif($chFinancialReportReview->bank_statement_matches == 1)
                        <span class="badge bg-success fs-7">In Balance</span>
                    @elseif($chFinancialReportReview->bank_statement_matches == 0)
                        <span class="badge bg-danger fs-7">Out of balance</span>
                    @else
                        <span class="badge bg-secondary fs-7">Please Review</span>
                    @endif
            </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Proof of 990N Filing <small>(7/1/{{ $lastYear}} - 6/30/{{ $currentYear }})</small>:</div>
                    <div class="col text-end">
                    @if(is_null($chFinancialReportReview->current_990N_included))
                        <span class="badge bg-secondary fs-7">Please Review</span>
                    @elseif($chFinancialReportReview->current_990N_included == 1)
                        <span class="badge bg-success fs-7">990N is filed</span>
                    @elseif($chFinancialReportReview->current_990N_included == 0)
                        <span class="badge bg-danger fs-7">990N has not been filed</span>
                    @else
                        <span class="badge bg-secondary fs-7">Please Review</span>
                    @endif
               </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Purchased membership pins or had stock:</div>
                    <div class="col text-end">
                        @php $val = $chFinancialReportReview->purchased_pins; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
            </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Purchased MOMS Club merchandise:</div>
                    <div class="col text-end">
                        @php $val = $chFinancialReportReview->purchased_mc_merch; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
            </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Offered MC merch or info to members:</div>
                    <div class="col text-end">
                         @php $val = $chFinancialReportReview->offered_merch; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
            </div>
            </div>
            <div class="row mb-1">
                    <div class="col-auto fw-bold">Manual/by-laws made available to members:</div>
                    <div class="col text-end">
                         @php $val = $chFinancialReportReview->bylaws_available; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
            </div>
            </div>
                <div class="row mb-1">
                    <div class="col-auto fw-bold">Sistered another chapter:</div>
                    <div class="col text-end">
                         @php $val = $chFinancialReportReview->sistered_another_chapter; @endphp
                    <span class="badge {{ is_null($val) ? 'bg-secondary' : ($val == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                        {{ is_null($val) ? 'Please Review' : ($val == 1 ? 'YES' : 'NO') }}
                    </span>
            </div>
            </div>
            <div class="row">
                    <div class="col-auto fw-bold">Chapter Awards:</div>
                    <div class="col text-end">
                    <button type="button" id="back-eoy" class="btn btn-primary bg-gradient btn-xs keep-enabled" onclick="window.location.href='{{ route('eoyreports.editawards', ['id' => $chDetails->id]) }}'">View/Update Award Information</button>
              </div>
            </div>

            </li>
            <li class="list-group-item">
                <div class="col-auto fw-bold">Reviewer Notes Logged for this Report (not visible to chapter):</div>
                <div class="col-auto">
                    @php
                        $financial_report_notes = [];
                        for ($i = 1; $i <= 13; $i++) {
                            $key = 'step_' . $i . '_notes_log';
                            if (isset($chFinancialReport[$key])) {
                                $notes = explode("\n", $chFinancialReport[$key]);
                                $financial_report_notes = array_merge($financial_report_notes, $notes);
                            }
                        }
                    @endphp
                    {!! empty($financial_report_notes) ? 'No notes logged for this report.' : implode('<br>', $financial_report_notes) !!}
                </div>
            </li>

            <li class="list-group-item">
                <div class="row mb-1">
                    <div class="col-auto fw-bold">Report Completed By:</div>
                    <div class="col text-end">
                        {{ !is_null($chFinancialReport) ? $chFinancialReport->completed_name : '' }}
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-auto fw-bold">Contact Email:</div>
                    <div class="col text-end">
                        <a href="mailto:{{ !is_null($chFinancialReport) ? $chFinancialReport->completed_email : '' }}">{{ !is_null($chFinancialReport) ? $chFinancialReport->completed_email : '' }}</a>
                    </div>
                </div>
                <div class="row mb-1">
                    @if ($chEOYDocuments->financial_report_received == 1 && $chFinancialReport->reviewer_id == null)
                        <div class="col-12">
                            <span style="color: red;">No Reviewer Assigned - Select Reviewer before continuing to prevent errors.</span>
                        </div>
                    @endif
                    <label class="col-auto fw-bold" for="AssignedReviewer">Assigned Reviewer:</label>
                    <div class="col text-end">
                        <select class="form-select" name="AssignedReviewer" id="AssignedReviewer" required>
                            <option value="" style="display:none" disabled selected>Select Reviewer</option>
                            @foreach($rrList as $coordinator)
                                <option value="{{ $coordinator['cid'] }}"
                                    {{ isset($chFinancialReport->reviewer_id) && $chFinancialReport->reviewer_id == $coordinator['cid'] ? 'selected' : '' }}>
                                    {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <input type="hidden" id="ch_reportrev" value="{{ $chFinancialReport->reviewer_id }}">
                <div id="emailMessageGroup" style="display: none;">
                    <label for="reviewer_email_message">Additional Email Message for Reviewer:</label>
                    <textarea class="form-control" rows="8" name="reviewer_email_message" id="reviewer_email_message">{{ $chFinancialReport->reviewer_email_message }}</textarea>
                </div>
            </li>

        <li class="list-group-item">
            <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
            <div class="row mb-2">
            <span id="display_corlist"></span>
            </div>
        </li>
         <li class="list-group-item">
                 <div class="card-body text-center">
            <button type="submit" id="btn-step-14" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-floppy-fill me-2"></i>Save Report Review</button>
            <button class="btn btn-primary bg-gradient mb-2" type="button" id="email-chapter" onclick="showChapterEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}', 'Financial Report Review')">
                <i class="bi bi-envelope-fill me-2"></i>Email Board</button>
            <br>
            @if ($chEOYDocuments->financial_review_complete != "" && $chEOYDocuments->financial_report_received)
                @if ($regionalCoordinatorCondition)
                    <button type="button" class="btn btn-success bg-gradient mb-2" id="review-clear"><i class="bi bi-ban me-2"></i>Clear Review Complete</button>
                @else
                    <button type="button" class="btn btn-success bg-gradient mb-2 disabled" disabled><i class="bi bi-ban me-2"></i>Clear Review Complete</button>
                @endif
            @else
                <button type="button" class="btn btn-success bg-gradient mb-2" id="review-complete"><i class="bi bi-check-lg me-2"></i>Mark as Review Complete</button>
            @endif
                <button type="button" class="btn btn-danger bg-gradient mb-2" id="unsubmit"><i class="bi bi-arrow-counterclockwise me-2"></i>UnSubmit Report</button>
            @if ($chEOYDocuments->financial_review_complete != 1)
                <br>
                <span style="color:red;"><b>"Mark as Review Complete" is for FINAL REVIEWER USE ONLY!</b></span>
            @endif
            <br><br>
            @if ($chEOYDocuments->$yearColumnName != null)
                <button class="btn btn-primary bg-gradient mb-2" type="button" id="financial-pdf" onclick="openPdfViewer('{{ $chEOYDocuments->$yearColumnName }}')"><i class="bi bi-file-earmark-pdf-fill me-2"></i>View/Download Financial Report PDF</button>
            @else
                <button class="btn btn-primary bg-gradient mb-2 disabled" type="button" id="financial-pdf" disabled><i class="bi bi-file-earmark-pdf-fill me-2"></i>No PDF Report Available</button>
            @endif
            @if ($chEOYDocuments->$yearColumnName != null && $chEOYDocuments->financial_report_received)
                <br>
                <button type="button" id="generate-pdf" class="btn btn-primary bg-gradient btn-sm" onclick="generateFinancialReport()"><i class="bi bi-arrow-repeat me-2"></i>Regenerate Financial PDF</button>
            @elseif ($chEOYDocuments->$yearColumnName == null && $chEOYDocuments->financial_report_received)
                <br>
                <button type="button" id="generate-pdf" class="btn btn-primary bg-gradient btn-sm" onclick="generateFinancialReport()"><i class="bi bi-arrow-repeat me-2"></i>Generate Financial PDF</button>
            @endif
        </div>
        </li>
    </ul>

  </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
</div>
<!-- /.col -->

  <div class="col-md-8">
    <div class="card card-primary card-outline">
        <div class="card-body">
            <div class="card-header bg-transparent border-0">
                <h3>{{ $financialReportName}} Review</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="col-md-12">
                    @if($chEOYDocuments->financial_report_received)
                        @if ($chFinancialReport->reviewer_id != null)
                            <label>Assigned Reviewer:</label>&nbsp;&nbsp;{{ $chDetails->reportReviewer->first_name }} {{ $chDetails->reportReviewer->last_name }}
                        @else
                            <span style="color:red">No Reviewer Assigned - Select Reviewer before saving report review to prevent errors</span>
                        @endif
                    @else
                        <span style="color:red">REPORT NOT YET SUBMITTED FOR REVIEW</span>
                    @endif
                <p>Have some questions about reviewing?<br>
                    <a href="https://momsclub.org/reviewing-reports-faq/"   target="_blank">Check out our FAQ!</a></p>
                </div>

                    <div class="card-body">
                         @include('eoyreports.editfinancialreview_accordion', [ ])
                    </div>
                <!-- end of accordion -->
                </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
              <div class="col-md-12">
                <div class="card-body text-center mt-3">
                    @if ($confId == $chConfId)
                        <button type="button" id="back-eoy" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyfinancialreport') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-calculator-fill me-2"></i>Back to Financial Report</button>
                    @elseif ($confId != $chConfId && $ITCondition)
                        <button type="button" id="back-eoy" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyfinancialreport', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-calculator-fill me-2"></i>Back to International Financial Report</button>
                    @endif
                    <button type="button" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Back to EOY Details</button>
                </div>
            </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
    @include('layouts.scripts.disablefieldseoy')

<script>
   document.addEventListener('DOMContentLoaded', function() {
    const unsubmitButton = document.getElementById('unsubmit');
    if (!unsubmitButton) return;

    unsubmitButton.addEventListener('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "Unsubmitting this report will make it editable by the chapter again and will disable coordinator editing until the chapter has resubmitted - any unsaved changes will be lost.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Unsubmit',
            cancelButtonText: 'Cancel',
            customClass: { confirmButton: 'btn-sm btn-success', cancelButton: 'btn-sm btn-danger' },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('/eoyreports/unsubmit/' . $chDetails->id) }}";
            }
        });
    });

    document.getElementById('review-clear')?.addEventListener('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will clear the 'review complete' flag and coordinators will be able to edit the report again. Do you wish to continue?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Clear Review',
            cancelButtonText: 'Cancel',
            customClass: { confirmButton: 'btn-sm btn-success', cancelButton: 'btn-sm btn-danger' },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('/eoyreports/clearreview/' . $chDetails->id) }}";
            }
        });
    });

    document.getElementById('review-complete')?.addEventListener('click', function() {
        if (!CheckMembers()) return false;
        if (!CheckService()) return false;
        if (!CheckParties()) return false;
        if (!CheckFinancial()) return false;
        if (!CheckReconciliation()) return false;
        if (!CheckQuestions()) return false;
        var post_balance = document.getElementById('post_balance').value;
        if (post_balance == null || post_balance == '') {
            customWarningAlert('Please enter Ending Balance in the Bank Reconciliation Section');
            document.getElementById('post_balance').focus();
            return false;
        }
        var result = confirm("This will finalize this report and flag it as 'review complete'. Do you wish to continue?");
        if (result) {
            document.getElementById('submit_type').value = 'review_complete';
            document.getElementById('FurthestStep').value = '14';
            document.getElementById('financial_report').submit();
        }
    });

    document.getElementById('AssignedReviewer')?.addEventListener('change', function() {
        var emailMessageGroup = document.getElementById('emailMessageGroup');
        if (this.value != '') {
            emailMessageGroup.style.display = 'block';
        } else {
            emailMessageGroup.style.display = 'none';
        }
    });

    function submitFormWithStep(step) {
        document.getElementById('FurthestStep').value = step;
        document.getElementById('financial_report').submit();
    }

    document.getElementById('btn-step-1')?.addEventListener('click', function() { if (!CheckMembers()) return false; submitFormWithStep(1); });
    document.getElementById('btn-step-2')?.addEventListener('click', function() { submitFormWithStep(2); });
    document.getElementById('btn-step-3')?.addEventListener('click', function() { if (!CheckService()) return false; submitFormWithStep(3); });
    document.getElementById('btn-step-4')?.addEventListener('click', function() { if (!CheckParties()) return false; submitFormWithStep(4); });
    document.getElementById('btn-step-5')?.addEventListener('click', function() { submitFormWithStep(5); });
    document.getElementById('btn-step-6')?.addEventListener('click', function() { submitFormWithStep(6); });
    document.getElementById('btn-step-7')?.addEventListener('click', function() { submitFormWithStep(7); });
    document.getElementById('btn-step-8')?.addEventListener('click', function() { submitFormWithStep(8); });
    document.getElementById('btn-step-9')?.addEventListener('click', function() { if (!CheckFinancial()) return false; submitFormWithStep(9); });
    document.getElementById('btn-step-10')?.addEventListener('click', function() {
        var post_balance = document.getElementById('post_balance').value;
        if (post_balance == null || post_balance == '') {
            customWarningAlert('Please enter Ending Balance');
            document.getElementById('post_balance').focus();
            return false;
        }
        if (!CheckReconciliation()) return false;
        submitFormWithStep(10);
    });
    document.getElementById('btn-step-11')?.addEventListener('click', function() { submitFormWithStep(11); });
    document.getElementById('btn-step-12')?.addEventListener('click', function() { if (!CheckQuestions()) return false; submitFormWithStep(12); });
    document.getElementById('btn-step-13')?.addEventListener('click', function() { submitFormWithStep(13); });
    document.getElementById('btn-step-14')?.addEventListener('click', function() {
        var assignedReviewer = document.getElementById('AssignedReviewer').value;
        if (assignedReviewer == null || assignedReviewer == '') {
            customWarningAlert('Please select a Reviewer');
            document.getElementById('AssignedReviewer').focus();
            return false;
        }
        submitFormWithStep(14);
    });
});
</script>
<script>
    /* Disable fields and buttons  */


    function EnableNoteLogButton(NoteNumber){
        var noteValue = document.getElementById("Step" + NoteNumber + "_Note").value.trim();
        var button = document.getElementById("AddNote" + NoteNumber);

        if(noteValue !== ""){
            button.disabled = false;
            button.classList.remove('disabled');
        } else {
            button.disabled = true;
            button.classList.add('disabled');
        }
    }

    function AddNote(NoteNumber){
        // Validate note is not empty FIRST
        var noteValue = document.getElementById("Step" + NoteNumber + "_Note").value.trim();
        if(noteValue === ""){
            return false; // Exit if empty, don't add the note
        }

        var Note = "";
        var Log = "";
        var d = new Date();
        var now = "";
        var SummaryNote="";

        now = d.toString();

        var noteText ={
            1: 'Dues',
            2: 'Meetings',
            3: 'Service Projects',
            4: 'Parties',
            5 :'Operating',
            6: 'International',
            7: 'Donations',
            8: 'Other',
            9: 'Financials',
            10: 'Reconciliation',
            11: '990N',
            12: 'Questions',
        }

        var noteTextValue = noteText[NoteNumber] || NoteNumber;

        Note=document.getElementById("Step" + NoteNumber + "_Note").value;
        Log += "\n" + noteTextValue + " Section, {{ date('m/d/Y') }}, {{ $loggedInName }}, " + Note;

        document.getElementById("Step" + NoteNumber + "_Log").value += Log;
        document.getElementById("Step" + NoteNumber + "_Note").value = "";
        document.getElementById("AddNote" + NoteNumber).disabled = true;
        document.getElementById("AddNote" + NoteNumber).classList.add('disabled');

        for(i=1;i<12;i++){
            Note=document.getElementById("Step" + i + "_Log").value;
            SummaryNote += Note;
        }
        document.getElementById("Summary_Log").value = SummaryNote;
    }

    function CheckMembers() {
        var checkRosterAttached = document.querySelector('input[name="checkRosterAttached"]:checked');
        var checkRenewalSeemsRight = document.querySelector('input[name="checkRenewalSeemsRight"]:checked');

        if (!checkRosterAttached || !checkRenewalSeemsRight) {
            customWarningAlert("Answer Review Questions in CHAPTER DUES section to Continue.");
            accordion.openAccordionItem('accordion-header-members');
            return false;
        }
        return true;
    }

    function CheckService() {
        var checkServiceProject = document.querySelector('input[name="checkServiceProject"]:checked');
        var checkM2MDonation = document.querySelector('input[name="checkM2MDonation"]:checked');

        if (!checkServiceProject || !checkM2MDonation) {
            customWarningAlert("Answer Review Questions in SERVICE PROJECTS section to Continue.");
            accordion.openAccordionItem('accordion-header-service');
            return false;
        }
        return true;
    }

    function CheckParties() {
        var check_party_percentage = document.querySelector('input[name="check_party_percentage"]:checked');

        if (!check_party_percentage) {
            customWarningAlert("Answer Review Questions in PARTIES & MEMBER BENEFITS section to Continue.");
            accordion.openAccordionItem('accordion-header-parties');
            return false;
        }
        return true;
    }

    function CheckFinancial() {
        var checkTotalIncome = document.querySelector('input[name="checkTotalIncome"]:checked');

        if (!checkTotalIncome) {
            customWarningAlert("Answer Review Questions in FINANCIAL SUMMARY section to Continue.");
            accordion.openAccordionItem('accordion-header-financial');
            return false;
        }
        return true;
    }

    function CheckReconciliation() {
        var check_beginning_balance = document.querySelector('input[name="check_beginning_balance"]:checked');
        var checkBankStatementIncluded = document.querySelector('input[name="checkBankStatementIncluded"]:checked');
        var checkBankStatementMatches = document.querySelector('input[name="checkBankStatementMatches"]:checked');
        var post_balance = document.getElementById('post_balance');

        if (!check_beginning_balance || !checkBankStatementIncluded || !checkBankStatementMatches || !post_balance) {
            customWarningAlert("Answer Review Questions in RECONCILIATION section to Continue.");
            accordion.openAccordionItem('accordion-header-reconciliation');
            return false;
        }
        return true;
    }

    function CheckQuestions() {
        var checkPurchasedPins = document.querySelector('input[name="checkPurchasedPins"]:checked');
        var checkPurchasedMCMerch = document.querySelector('input[name="checkPurchasedMCMerch"]:checked');
        var checkOfferedMerch = document.querySelector('input[name="checkOfferedMerch"]:checked');
        var checkBylawsMadeAvailable = document.querySelector('input[name="checkBylawsMadeAvailable"]:checked');
        var checkSisteredAnotherChapter = document.querySelector('input[name="checkSisteredAnotherChapter"]:checked');
        var checkAttendedTraining = document.querySelector('input[name="checkAttendedTraining"]:checked');
        var checkCurrent990NAttached = document.querySelector('input[name="checkCurrent990NAttached"]:checked');

        if (!checkPurchasedPins || !checkPurchasedMCMerch || !checkOfferedMerch || !checkBylawsMadeAvailable
            || !checkSisteredAnotherChapter || !checkAttendedTraining || !checkCurrent990NAttached) {
                customWarningAlert("Answer Review Questions in CHAPTER QUESTIONS section to Continue.");
            accordion.openAccordionItem('accordion-header-questions');
            return false;
        }
        return true;
    }

</script>
@endsection
