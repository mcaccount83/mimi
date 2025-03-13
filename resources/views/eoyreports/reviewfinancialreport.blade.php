@extends('layouts.coordinator_theme')

@section('page_title', 'Financial Report')
@section('breadcrumb', 'Financial Report Review')

<style>
    .flex-container2 {
        display: flex;
        flex-wrap: wrap;
        gap: 0px;
        width: 100%;
        overflow-x: auto;
        margin-top: 20px;

    }

    .flex-item2 {
        flex: 0 0 calc(48% - 10px);
        box-sizing: border-box;
    }
</style>

@section('content')
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
            <form id="financial_report" name="financial_report" role="form" data-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("eoyreports.updatefinancialreport", $chDetails->id) }}' novalidate>
                @csrf
                <input type="hidden" name="submitted" id="submitted" value="{{ $chFinancialReport['financial_report_received'] }}" />
                <input type="hidden" name="FurthestStep" id="FurthestStep" value="<?php if($chFinancialReport['farthest_step_visited_coord'] != null) echo $chFinancialReport['farthest_step_visited_coord']; else echo '14'; ?>" />
                <input type="hidden" name="submit_type" id="submit_type" value="" />

          <!-- Profile Image -->
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>

          <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
               <h5>Review Summary</h5>
            Answers from questios in previous sections will show up here after they have been saved.<br>
            <br>
            @if ($chDocuments['financial_report_received'])
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Financial Report PDF:</b> <span class="float-right"><a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $chDocuments['financial_pdf_path']; ?>">Download PDF</a></span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Chapter Roster File:</b> <span class="float-right">
                @if ($chDocuments['roster_path'] != null)
                <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $chDocuments['roster_path']; ?>">Chapter Roster</a></span>
                @else
                No file attached</span>
                @endif
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Primary Bank Statement:</b> <span class="float-right">
                @if ($chDocuments['statement_1_path'] != null)
                <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $chDocuments['statement_1_path']; ?>">Primary Statement</a></span>
                @else
                No file attached</span>
                @endif
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Additional Bank Statement:</b> <span class="float-right">
                    @if ($chDocuments['statement_2_path'] != null)
                    <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $chDocuments['statement_2_path']; ?>">Additional Statement</a></span>
                    @else
                    No file attached</span>
                    @endif
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>990N Filing:</b> <span class="float-right">
                    @if ($chDocuments['irs_path'] != null)
                    <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $chDocuments['irs_path']; ?>">990N Confirmation</a></span>
                    @else
                    No file attached</span>
                    @endif
            </div>
            @endif
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Excel roster attached and complete:</b> <span class="float-right">{{ is_null($chFinancialReport['check_roster_attached']) ? 'Please Review'
                    : ($chFinancialReport ['check_roster_attached'] == 0 ? 'NO' : ($chFinancialReport ['check_roster_attached'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Number of members/dues/renewal "seem right":</b> <span class="float-right">{{ is_null($chFinancialReport['check_renewal_seems_right']) ? 'Please Review'
                    : ($chFinancialReport ['check_renewal_seems_right'] == 0 ? 'NO' : ($chFinancialReport ['check_renewal_seems_right'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Minimum of one service project completed:</b> <span class="float-right">{{ is_null($chFinancialReport['check_minimum_service_project']) ? 'Please Review'
                    : ( $chFinancialReport ['check_minimum_service_project'] == 0 ? 'NO' : ($chFinancialReport ['check_minimum_service_project'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Donation to M2M Fund:</b> <span class="float-right">{{ is_null($chFinancialReport['check_m2m_donation']) ? 'Please Review'
                    : ($chFinancialReport ['check_m2m_donation'] == 0 ? 'NO' : ($chFinancialReport ['check_m2m_donation'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Party Percentage:</b> <span class="float-right">
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b> Party Percentage less than 15%:</b> <span class="float-right" style="
                    @if(is_null($chFinancialReport['check_party_percentage']))
                        background-color: #FFFFFF; color: #000000;
                    @elseif($chFinancialReport['check_party_percentage'] == 2)
                        background-color: #28a745; color: #FFFFFF;
                    @elseif($chFinancialReport['check_party_percentage'] == 1)
                        background-color: #ffc107; color: #000000;
                    @elseif($chFinancialReport['check_party_percentage'] == 0)
                        background-color: #dc3545; color: #FFFFFF;
                    @else
                        background-color: #FFFFFF; color: #000000;
                    @endif
                        ">
                    @if(is_null($chFinancialReport['check_party_percentage']))
                        Please Review
                    @elseif($chFinancialReport['check_party_percentage'] == 0)
                        They are over 20%
                    @elseif($chFinancialReport['check_party_percentage'] == 1)
                        They are between 15-20%
                    @elseif($chFinancialReport['check_party_percentage'] == 2)
                        They are under 15%
                    @else
                        Please Review
                    @endif
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Total income/revenue less than $50,000:</b> <span class="float-right">{{ is_null($chFinancialReport['check_total_income_less']) ? 'Please Review'
                    : ( $chFinancialReport ['check_total_income_less'] == 0 ? 'NO' : ($chFinancialReport ['check_total_income_less'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Current bank statement included:</b> <span class="float-right">{{ is_null($chFinancialReport['check_bank_statement_included']) ? 'Please Review'
                    : ( $chFinancialReport ['check_bank_statement_included'] == 0 ? 'NO' : ($chFinancialReport ['check_bank_statement_included'] == 1 ? 'YES' : 'Please Review')) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Treasury & Reconciled Balances Match:</b> <span class="float-right" style="
                    @if(is_null($chFinancialReport['check_bank_statement_matches']))
                        background-color: #FFFFFF; color: #000000;
                    @elseif($chFinancialReport['check_bank_statement_matches'] == 1)
                        background-color: #28a745; color: #FFFFFF;
                    @elseif($chFinancialReport['check_bank_statement_matches'] == 0)
                        background-color: #dc3545; color: #FFFFFF;
                    @else
                        background-color: #FFFFFF; color: #000000;
                    @endif
                        ">
                    @if(is_null($chFinancialReport['check_bank_statement_matches']))
                        Please Review
                    @elseif($chFinancialReport['check_bank_statement_matches'] == 1)
                        In Balance
                    @elseif($chFinancialReport['check_bank_statement_matches'] == 0)
                        Out of balance
                    @else
                        Please Review
                    @endif
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Proof of 990N Filing for 7/1/<?php echo date('Y')-1 .' - 6/30/'.date('Y');?> :</b> <span class="float-right" style="
                    @if(is_null($chFinancialReport['check_current_990N_included']))
                        background-color: #FFFFFF; color: #000000;
                    @elseif($chFinancialReport['check_current_990N_included'] == 1)
                        background-color: #28a745; color: #FFFFFF;
                    @elseif($chFinancialReport['check_current_990N_included'] == 0)
                        background-color: #dc3545; color: #FFFFFF;
                    @else
                        background-color: #FFFFFF; color: #000000;
                    @endif
                        ">
                    @if(is_null($chFinancialReport['check_current_990N_included']))
                       Please Review
                    @elseif($chFinancialReport['check_current_990N_included'] == 1)
                        990N is filed
                    @elseif($chFinancialReport['check_current_990N_included'] == 0)
                        990N has not been filed
                    @else
                        Please Review
                    @endif
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Purchased membership pins or had leftovers:</b> <span class="float-right">{{ is_null($chFinancialReport['check_purchased_pins']) ? 'Please Review'
                    : ( $chFinancialReport ['check_purchased_pins'] == 0 ? 'NO' : ($chFinancialReport ['check_purchased_pins'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Purchased MOMS Club merchandise:</b> <span class="float-right">{{ is_null($chFinancialReport['check_purchased_mc_merch']) ? 'Please Review'
                    : ($chFinancialReport ['check_purchased_mc_merch'] == 0 ? 'NO' : ($chFinancialReport ['check_purchased_mc_merch'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Offered MOMS Club merchandise or info to members:</b> <span class="float-right">{{ is_null($chFinancialReport['check_offered_merch']) ? 'Please Review'
                    : ( $chFinancialReport ['check_offered_merch'] == 0 ? 'NO' : ( $chFinancialReport ['check_offered_merch'] == 1 ? 'YES' : 'Pleae Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Manual/by-laws made available to members:</b> <span class="float-right">{{ is_null($chFinancialReport['check_bylaws_available']) ? 'Please Review'
                    : ( $chFinancialReport ['check_bylaws_available'] == 0 ? 'NO' : ($chFinancialReport ['check_bylaws_available'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Attended International Event:</b> <span class="float-right">{{ is_null($chFinancialReport['check_attended_training']) ? 'Please Review'
                    : ($chFinancialReport ['check_attended_training'] == 0 ? 'NO' : ($chFinancialReport ['check_attended_training'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Sistered another chapter:</b> <span class="float-right">{{ is_null($chFinancialReport['check_sistered_another_chapter']) ? 'Please Review'
                    : ($chFinancialReport ['check_sistered_another_chapter'] == 0 ? 'NO' : ($chFinancialReport ['check_sistered_another_chapter'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>

            @php
                $yesBackground = '#28a745';  // Green background for "YES"
                $noBackground = '#dc3545';   // Red background for "NO"
            @endphp

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Award #1 Status:</b> <span class="float-right" style="background-color: {{ is_null($chFinancialReport['check_award_1_approved']) ? '#FFFFFF' : ($chFinancialReport['check_award_1_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($chFinancialReport['check_award_1_approved']) ? '#000000' : '#FFFFFF' }};">
                    {{ is_null($chFinancialReport['award_1_nomination_type']) ? 'No Award Selected' : ($chFinancialReport['award_1_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                    : ($chFinancialReport['award_1_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($chFinancialReport['award_1_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($chFinancialReport['award_1_nomination_type'] == 4 ? 'Outstanding Spirit'
                    : ($chFinancialReport['award_1_nomination_type'] == 5 ? 'Outstanding Chapter' : ($chFinancialReport['award_1_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($chFinancialReport['award_1_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Award #2 Status:</b> <span class="float-right" style="background-color: {{ is_null($chFinancialReport['check_award_2_approved']) ? '#FFFFFF' : ($chFinancialReport['check_award_2_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($chFinancialReport['check_award_2_approved']) ? '#000000' : '#FFFFFF' }};">
                    {{ is_null($chFinancialReport['award_2_nomination_type']) ? 'No Award Selected' : ($chFinancialReport['award_2_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                        : ($chFinancialReport['award_2_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($chFinancialReport['award_2_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($chFinancialReport['award_2_nomination_type'] == 4 ? 'Outstanding Spirit'
                        : ($chFinancialReport['award_2_nomination_type'] == 5 ? 'Outstanding Chapter' : ($chFinancialReport['award_2_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($chFinancialReport['award_2_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Award #3 Status:</b> <span class="float-right" style="background-color: {{ is_null($chFinancialReport['check_award_3_approved']) ? '#FFFFFF' : ($chFinancialReport['check_award_3_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($chFinancialReport['check_award_3_approved']) ? '#000000' : '#FFFFFF' }};">
                    {{ is_null($chFinancialReport['award_3_nomination_type']) ? 'No Award Selected' : ($chFinancialReport['award_3_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                        : ($chFinancialReport['award_3_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($chFinancialReport['award_3_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($chFinancialReport['award_3_nomination_type'] == 4 ? 'Outstanding Spirit'
                        : ($chFinancialReport['award_3_nomination_type'] == 5 ? 'Outstanding Chapter' : ($chFinancialReport['award_3_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($chFinancialReport['award_3_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Award #4 Status:</b> <span class="float-right" style="background-color: {{ is_null($chFinancialReport['check_award_4_approved']) ? '#FFFFFF' : ($chFinancialReport['check_award_4_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($chFinancialReport['check_award_4_approved']) ? '#000000' : '#FFFFFF' }};">
                    {{ is_null($chFinancialReport['award_4_nomination_type']) ? 'No Award Selected' : ($chFinancialReport['award_4_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                        : ($chFinancialReport['award_4_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($chFinancialReport['award_4_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($chFinancialReport['award_4_nomination_type'] == 4 ? 'Outstanding Spirit'
                        : ($chFinancialReport['award_4_nomination_type'] == 5 ? 'Outstanding Chapter' : ($chFinancialReport['award_4_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($chFinancialReport['award_4_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Award #5 Status:</b> <span class="float-right" style="background-color: {{ is_null($chFinancialReport['check_award_5_approved']) ? '#FFFFFF' : ($chFinancialReport['check_award_5_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($chFinancialReport['check_award_5_approved']) ? '#000000' : '#FFFFFF' }};">
                    {{ is_null($chFinancialReport['award_5_nomination_type']) ? 'No Award Selected' : ($chFinancialReport['award_5_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                        : ($chFinancialReport['award_5_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($chFinancialReport['award_5_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($chFinancialReport['award_5_nomination_type'] == 4 ? 'Outstanding Spirit'
                        : ($chFinancialReport['award_5_nomination_type'] == 5 ? 'Outstanding Chapter' : ($chFinancialReport['award_5_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($chFinancialReport['award_5_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                </span>
            </div>

            </li>
            <li class="list-group-item">

               <strong>Reviewer Notes Logged for this Report (not visible to chapter):</strong><br>
                    <?php
                    $financial_report_notes = [];
                    for ($i = 1; $i <= 13; $i++) {
                        $key = 'step_' . $i . '_notes_log';
                        if (isset($chFinancialReport[$key])) {
                            $notes = explode("\n", $chFinancialReport[$key]);
                            $financial_report_notes = array_merge($financial_report_notes, $notes);
                        }
                    }

                    echo empty($financial_report_notes) ? 'No notes logged for this report.' : implode('<br>', $financial_report_notes);
                    ?>
            </li>
            <li class="list-group-item">

        <div class="d-flex justify-content-between w-100">
            <b>Report Completed By:</b> <span class="float-right"><?php if (!is_null($chFinancialReport)) {echo $chFinancialReport['completed_name'];}?>
        </div>
        <div class="d-flex justify-content-between w-100">
            <b>Contact Email:</b> <span class="float-right"><a href="mailto:<?php if (!is_null($chFinancialReport)) {echo $chFinancialReport['completed_email'];}?>"><?php if (!is_null($chFinancialReport)) {echo $chFinancialReport['completed_email'];}?></a></p>
        </div>

        <div class="d-flex align-items-center justify-content-between w-100">
            <?php if ($chDocuments->financial_report_received == 1 && $chFinancialReport['reviewer_id'] == null): ?>
                    <span style="display: inline; color: red;">No Reviewer Assigned - Select Reviewer before continuing to prevent errors.<br></span>
                <?php endif; ?>
                <label for="AssignedReviewer"><strong>Assigned Reviewer:</strong></label>
                <select class="form-control" name="AssignedReviewer" id="AssignedReviewer" style="width: 250px;"  required>
                    <option value="" style="display:none" disabled selected>Select a reviewer</option>
                        @foreach($rrList as $coordinator)
                            <option value="{{ $coordinator['cid'] }}"
                                {{ isset($chFinancialReport->reviewer_id) && $chFinancialReport->reviewer_id == $coordinator['cid'] ? 'selected' : '' }}>
                                {{ $coordinator['cname'] }} ({{ $coordinator['cpos'] }})
                            </option>
                        @endforeach
                </select>
            </div>
            <div class="form-group" id="emailMessageGroup" style="display: none;">
                <label for="AssignedReviewer"><strong>Additional Email Message for Reviewer:</strong></label>
                <textarea class="form-control" style="width:100%" rows="8" name="reviewer_email_message" id="reviewer_email_message"><?php echo $chFinancialReport['reviewer_email_message']; ?></textarea>
            </div>

        <div class="card-body text-center">
            <br>
            <button type="submit" id="btn-step-14" class="btn bg-gradient-primary mb-2"><i class="fas fa-save mr-2"></i>Save Report Review</button>
            <br>
            @if ($chDocuments['financial_review_complete'] != "" && $chDocuments['financial_report_received'])
                @if ($regionalCoordinatorCondition)
                    <button type="button" class="btn bg-gradient-success" id="review-clear"><i class="fas fa-minus-circle mr-2"></i>Clear Review Complete</button>
                @else
                    <button type="button" class="btn bg-gradient-success disabled"><i class="fas fa-minus-circle mr-2"></i>Clear Review Complete</button>
                @endif
            @else
                <button type="button" class="btn bg-gradient-success" id="review-complete"><i class="fas fa-check mr-2"></i>Mark as Review Complete</button>
            @endif
                <button type="button" class="btn bg-gradient-danger" id="unsubmit"><i class="fas fa-undo mr-2"></i>UnSubmit Report</button>
            <br>
            <span style="color:red;"><b>"Mark as Review Complete" is for FINAL REVIEWER USE ONLY!</b></span>
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
        <div class="card-body box-profile">
        <h3 class="profile-username">Finanical Report Review</h3>
            <!-- /.card-header -->
            <div class="row">
                <div class="col-md-12">
                    @if($chDocuments->financial_report_received)
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
            </div>

                <div class="col-12"  id="accordion">
                    	<!------Start Step 1 ------>
                    <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='1') echo "active";?>">
                        <div class="card-header" id="accordion-header-members">
                            <h4 class="card-title w-100">
                                <a class="d-block" data-toggle="collapse" href="#collapseOne" style="width: 100%;">CHAPTER DUES</a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='1') echo 'show'; ?>" data-parent="#accordion">
                            <div class="card-body">
						<section>
                            Did your chapter change dues this year?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($chFinancialReport['changed_dues']) ? 'Not Answered' : ($chFinancialReport['changed_dues'] == 0 ? 'NO'
                                : ($chFinancialReport ['changed_dues'] == 1 ? 'YES' : 'Not Answered' )) }}</strong><br>
                            Did your chapter charge different amounts for new and returning members?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($chFinancialReport['different_dues']) ? 'Not Answered' : ($chFinancialReport['different_dues'] == 0 ? 'NO'
                                :( $chFinancialReport ['different_dues'] == 1 ? 'YES' : 'Not Answered' )) }}</strong><br>
                            Did your chapter have any members who didn't pay full dues?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($chFinancialReport['not_all_full_dues']) ? 'Not Answered' : ($chFinancialReport['not_all_full_dues'] == 0 ? 'NO'
                                : ( $chFinancialReport ['not_all_full_dues'] == 1 ? 'YES' : 'Not Answered' )) }}</strong><br>
                            <br>
                            <style>
                                .flex-container {
                                    display: flex;
                                    flex-wrap: wrap;
                                    gap: 0px;
                                    width: 75%;
                                    overflow-x: auto;
                                }

                                .flex-item {
                                    flex: 0 0 calc(48% - 10px);
                                    box-sizing: border-box;
                                }
                            </style>

                            <div class="flex-container">
                                @if ($chFinancialReport['changed_dues'] != 1)
                                    <div class="flex-item">
                                        New Members:&nbsp;&nbsp;&nbsp;<strong>{{ $chFinancialReport['total_new_members'] }}</strong>
                                    </div>
                                    @if ($chFinancialReport['different_dues'] != 1)
                                        <div class="flex-item">
                                            Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['dues_per_member'], 2) }}</strong>
                                        </div>
                                    @endif
                                    @if ($chFinancialReport['different_dues'] == 1)
                                        <div class="flex-item">
                                            New Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['dues_per_member'], 2) }}</strong>
                                        </div>
                                    @endif
                                    <div class="flex-item">
                                        Renewed Members:&nbsp;&nbsp;&nbsp;<strong>{{ $chFinancialReport['total_renewed_members'] }}</strong>
                                    </div>
                                    @if ($chFinancialReport['different_dues'] != 1)
                                        <div class="flex-item">
                                           &nbsp;&nbsp;&nbsp;
                                        </div>
                                    @endif
                                    @if ($chFinancialReport['different_dues'] == 1)
                                        <div class="flex-item">
                                            Renewal Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['dues_per_member_renewal'], 2) }}</strong>
                                        </div>
                                    @endif
                                @endif

                                @if ($chFinancialReport['changed_dues'] == 1)
                                    <div class="flex-item">
                                        New Members (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ $chFinancialReport['total_new_members'] }}</strong>
                                    </div>
                                    @if ($chFinancialReport['different_dues'] != 1)
                                        <div class="flex-item">
                                            Dues (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['dues_per_member'], 2) }}</strong>
                                        </div>
                                    @endif
                                    @if ($chFinancialReport['different_dues'] == 1)
                                        <div class="flex-item">
                                            New Dues (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['dues_per_member'], 2) }}</strong>
                                        </div>
                                    @endif
                                    <div class="flex-item">
                                        Renewed Members (OLD dues amount): <strong>{{ $chFinancialReport['total_renewed_members'] }}</strong>
                                    </div>
                                    @if ($chFinancialReport['different_dues'] == 1)
                                        <div class="flex-item">
                                            Renewal Dues (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['dues_per_member_renewal'], 2) }}</strong>
                                        </div>
                                    @endif

                                    <div class="flex-item">
                                        New Members (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ $chFinancialReport['total_new_members_changed_dues'] }}</strong>
                                    </div>
                                    @if ($chFinancialReport['different_dues'] != 1)
                                        <div class="flex-item">
                                            Dues (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['dues_per_member_new_changed'], 2) }}</strong>
                                        </div>
                                    @endif
                                    @if ($chFinancialReport['different_dues'] == 1)
                                        <div class="flex-item">
                                            New Dues (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['dues_per_member_new_changed'], 2) }}</strong>
                                        </div>
                                    @endif
                                    <div class="flex-item">
                                        Renewed Members (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ $chFinancialReport['total_renewed_members_changed_dues'] }}</strong>
                                    </div>
                                    @if ($chFinancialReport['different_dues'] == 1)
                                        <div class="flex-item">
                                            Renewal Dues (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['dues_per_member_renewal_changed'], 2) }}</strong>
                                        </div>
                                    @endif
                                @endif

                                @if ($chFinancialReport['not_all_full_dues'] == 1)
                                    <div class="flex-item">
                                        Members Who Paid No Dues:&nbsp;&nbsp;&nbsp;<strong>{{ $chFinancialReport['members_who_paid_no_dues'] }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        &nbsp;&nbsp;&nbsp;
                                    </div>
                                    <div class="flex-item">
                                        Members Who Paid Partial Dues:&nbsp;&nbsp;&nbsp;<strong>{{ $chFinancialReport['members_who_paid_partial_dues'] }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        Partial Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['total_partial_fees_collected'], 2) }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        Assiciate Members:&nbsp;&nbsp;&nbsp;<strong>{{ $chFinancialReport['total_associate_members'] }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        Associate Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['associate_member_fee'], 2) }}</strong>
                                    </div>
                                @endif
                            </div>

                            <?php
                                $newMembers = $chFinancialReport['total_new_members'] * $chFinancialReport['dues_per_member'];
                                $renewalMembers = $chFinancialReport['total_renewed_members'] * $chFinancialReport['dues_per_member'];
                                $renewalMembersDiff = $chFinancialReport['total_renewed_members'] * $chFinancialReport['dues_per_member_renewal'];
                                $newMembersNew = $chFinancialReport['total_new_members_changed_dues'] * $chFinancialReport['dues_per_member_new_changed'];
                                $renewMembersNew = $chFinancialReport['total_renewed_members_changed_dues'] * $chFinancialReport['dues_per_member_new_changed'];
                                $renewMembersNewDiff = $chFinancialReport['total_renewed_members_changed_dues'] * $chFinancialReport['dues_per_member_renewal_changed'];
                                $partialMembers = $chFinancialReport['members_who_paid_partial_dues'] * $chFinancialReport['total_partial_fees_collected'];
                                $associateMembers = $chFinancialReport['total_associate_members'] * $chFinancialReport['associate_member_fee'];

                                $totalMembers = $chFinancialReport['total_new_members'] +$chFinancialReport['total_renewed_members'] + $chFinancialReport['total_new_members_changed_dues'] + $chFinancialReport['total_renewed_members_changed_dues']
                                        + $chFinancialReport['members_who_paid_partial_dues'] + $chFinancialReport['total_associate_members']+ $chFinancialReport['members_who_paid_no_dues'];

                                if ($chFinancialReport['different_dues'] == 1 && $chFinancialReport['changed_dues'] == 1) {
                                    $totalDues = $newMembers + $renewalMembersDiff + $newMembersNew + $renewMembersNewDiff + $partialMembers + $associateMembers;
                                } elseif ($chFinancialReport['different_dues'] == 1) {
                                    $totalDues = $newMembers + $renewalMembersDiff + $partialMembers + $associateMembers;
                                } elseif ($chFinancialReport['changed_dues'] == 1) {
                                    $totalDues = $newMembers + $renewalMembers + $newMembersNew + $renewMembersNew + $partialMembers + $associateMembers;
                                } else {
                                    $totalDues = $newMembers + $renewalMembers + $partialMembers + $associateMembers;
                                }
                            ?>

                        <br><strong>Total Members:&nbsp;&nbsp;&nbsp;{{ $totalMembers }}</strong></td><br>
                            <strong>Total Dues Collected:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($totalDues, 2) }}</strong></td><br>
						<hr style="border-bottom: 2px solid #007bff">
						<!-- start:report_review -->
						<div class="form-row report_review" >
                            <div class="card-header col-md-12">
                                <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                            </div>
							<div class="card-body form-row">
                                <div class="col-12">
                                @if (!is_null($chDocuments['roster_path']))
                                        <div class="col-12">
                                            <label>Chapter Roster Uploaded:</label><a href="https://drive.google.com/uc?export=download&id={{ $chDocuments['roster_path'] }}">&nbsp; View Chapter Roster</a><br>
                                        </div>
                                        <div class="col-12" id="RosterBlock">
                                            <strong style="color:red">Please Note</strong><br>
                                                This will refresh the screen - be sure to save all work before clicking button to Replace Roster File.<br>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Roster File</button>
                                    </div>
                                @else
                                    <div class="col-12" id="RosterBlock">
                                            <strong style="color:red">Please Note</strong><br>
                                                This will refresh the screen - be sure to save all work before clicking button to Upload Roster File.<br>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Roster File</button>
                                    </div>
                                @endif
                                <input type="hidden" name="RosterPath" id="RosterPath" value="<?php echo $chDocuments['roster_path']; ?>">
                                <div class="clearfix"></div>
                                <div class="col-12"><br></div>
                                <div class="col-12">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <label>Excel roster attached and complete:<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkRosterAttached" value="1" {{ $chFinancialReport['check_roster_attached'] === 1 ? 'checked' : '' }} required>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkRosterAttached" value="0" {{ $chFinancialReport['check_roster_attached'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label>Number of members listed, dues received, and renewal paid "seem right":<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkRenewalSeemsRight" value="1" {{ $chFinancialReport['check_renewal_seems_right'] === 1 ? 'checked' : '' }} required>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkRenewalSeemsRight" value="0" {{ $chFinancialReport['check_renewal_seems_right'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
										<label for="Step1_Note">Add New Note:</label>
										<textarea class="form-control" style="width:100%" rows="3" name="Step1_Note" id="Step1_Note" oninput="EnableNoteLogButton(1)" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                                    <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
										<button type="button" id="AddNote1" class="btn btn-sm bg-gradient-success" onclick="AddNote(1)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                                    <div class="col-12">
										<label for="Step1_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
									</div>
									<div class="col-12">
										<textarea class="form-control" rows="8" name="Step1_Log" id="Step1_Log" readonly style="width:100%"><?php echo $chFinancialReport['step_1_notes_log']; ?></textarea>
									</div>
                                    <div class="col-12"><br></div>

                            <!-- end:report_review -->
                            <div class="col-12 text-center">
								  <button type="submit" id="btn-step-1" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
							</div>
                        </div>
                    </div>
						</section>
					</div>
				</div>
            </div>
				<!------End Step 1 ------>

				<!------Start Step 2 ------>
                <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='2') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseTwo" style="width: 100%;">MONTHLY MEETING EXPENSES</a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='2') echo 'show'; ?>" data-parent="#accordion" data-parent="#accordion">
                        <div class="card-body">
						<section>
                            Meeting Room Fees:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['manditory_meeting_fees_paid'], 2) }}</strong><br>
                            Voluntary Donations Paid:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['voluntary_donations_paid'], 2) }}</strong><br>
                            <strong>Total Meeting Room Expenses:&nbsp;&nbsp;&nbsp;
                                {{ '$'.number_format($chFinancialReport['manditory_meeting_fees_paid'] + $chFinancialReport['voluntary_donations_paid']) }}</strong><br>
                            <br>
                            Did you have speakers at any meetings?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($chFinancialReport['meeting_speakers']) ? 'Not Answered' : ($chFinancialReport['meeting_speakers'] == 0 ? 'NO'
                                : ($chFinancialReport ['meeting_speakers'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport ['meeting_speakers_explanation']}}</strong><br>
                                @php
                                    $meetingSpeakersArray = json_decode($chFinancialReport['meeting_speakers_array']);
                                    $meetingSpeakersMapping = [
                                        '0' => 'N/A',
                                        '1' => 'Child Rearing',
                                        '2' => 'Schools/Education',
                                        '3' => 'Home Management',
                                        '4' => 'Politics',
                                        '5' => 'Other Non-Profit',
                                        '6' => 'Other',
                                    ];
                                @endphp

                                @if (!empty($meetingSpeakersArray))
                                    {{ implode(', ', array_map(function($value) use ($meetingSpeakersMapping) {
                                        // Check if the key exists in the mapping array before accessing it
                                        return isset($meetingSpeakersMapping[$value]) ? $meetingSpeakersMapping[$value] : 'Not Answered';
                                    }, $meetingSpeakersArray)) }}
                                @else
                                    N/A
                                @endif
                            Did you have any discussion topics at your meetings?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($chFinancialReport['discussion_topic_frequency']) ? 'Not Answered' : ($chFinancialReport['discussion_topic_frequency'] == 0 ? 'NO'
                                : ( $chFinancialReport['discussion_topic_frequency'] == 1 ? '1-3 Times' : ($chFinancialReport['discussion_topic_frequency'] == 2 ? '4-6 Times' :
                                ($chFinancialReport['discussion_topic_frequency'] == 3 ? '7-9 Times' : ($chFinancialReport['discussion_topic_frequency'] == 4 ? '10+ Times' : 'Not Answered'))))) }}</strong><br>
                            Did you have a children's room with babysitters?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($chFinancialReport['childrens_room_sitters']) ? 'Not Answered' : ($chFinancialReport['childrens_room_sitters'] == 0 ? 'NO'
                                : ( $chFinancialReport ['childrens_room_sitters'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport['childrens_room_sitters_explanation']}}</strong><br>
                            <br>
                            Paid Babysitter Expense:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['paid_baby_sitters'], 2) }}</strong><br>
                            <br>
                            Children's Room Miscellaneous:
                            <table width="75%" style="border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #333;">
                                        <td>Description</td>
                                        <td>Supplies</td>
                                        <td>Other Expenses</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $childrens_room = null;
                                        $totalChildrenSupplies = 0;
                                        $totalChildrenOther = 0;

                                        if (isset($chFinancialReport['childrens_room_expenses']) && $chFinancialReport['childrens_room_expenses'] !== null) {
                                            $blobData = base64_decode($chFinancialReport['childrens_room_expenses']);
                                            $childrens_room = unserialize($blobData);

                                            if ($childrens_room === false) {
                                                echo "Error: Failed to unserialize data.";
                                            } else {
                                                if (is_array($childrens_room) && count($childrens_room) > 0) {
                                                    foreach ($childrens_room as $row) {
                                                        echo "<tr>";
                                                        echo "<td>" . $row['childrens_room_desc'] . "</td>";
                                                        echo "<td>" . ($row['childrens_room_supplies'] ? "$" . number_format($row['childrens_room_supplies'], 2) : "$0.00") . "</td>";
                                                        echo "<td>" . ($row['childrens_room_other'] ? "$" . number_format($row['childrens_room_other'], 2) : "$0.00") . "</td>";
                                                        echo "</tr>";

                                                        $totalChildrenSupplies += floatval($row['childrens_room_supplies']);
                                                        $totalChildrenOther += floatval($row['childrens_room_other']);
                                                    }

                                                    // Total row
                                                    echo "<tr style='border-top: 1px solid #333;'>";
                                                    echo "<td><strong>Total</strong></td>";
                                                    echo "<td><strong>$" . number_format($totalChildrenSupplies, 2) . "</strong></td>";
                                                    echo "<td><strong>$" . number_format($totalChildrenOther, 2) . "</strong></td>";
                                                    echo "</tr>";
                                                } else {
                                                    echo "<tr style='border-top: 1px solid #333;'>";
                                                    echo "<td colspan='3'>No Children's Room Expenses Entered.</td>";
                                                    echo "</tr>";
                                                }
                                            }
                                        } else {
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td colspan='3'>No Children's Room Expenses Entered.</td>";
                                            echo "</tr>";
                                        }

                                        $totalChildrensRoomExpenses = $totalChildrenSupplies + $totalChildrenOther;
                                        ?>

                                </tbody>
                                </table>
                            <br>
                            <strong>Total Children's Room Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($chFinancialReport['paid_baby_sitters'] + $totalChildrensRoomExpenses, 2) }}</strong><br>
                            <hr style="border-bottom: 2px solid #007bff">
							<!-- start:report_review -->
								<div class="form-row report_review" >
									<div class="card-header col-md-12">
                                        <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                                    </div>
									<div class="card-body form-row">
                                        <div class="col-12">

                                            <div class="col-12">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <label for="Step2_Note">Add New Note:</label>
                                                    <textarea class="form-control" style="width:100%" rows="3" name="Step2_Note" id="Step2_Note" oninput="EnableNoteLogButton(2)" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                                                    <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
                                                        <button type="button" id="AddNote2" class="btn btn-sm bg-gradient-success" onclick="AddNote(2)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
                                                </div>
                                                </div>
                                            </div>
                                            </div>

											<div class="col-12">
												<label for="Step2_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
											</div>
											<div class="col-12">
                                                <textarea class="form-control" rows="8" name="Step2_Log" id="Step2_Log" readonly style="width:100%"><?php echo $chFinancialReport['step_2_notes_log']; ?></textarea>
                                            </div>
                                            <div class="col-12"><br></div>

								<!-- end:report_review -->

                                <div class="col-12 text-center">
									<button type="submit" id="btn-step-2" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
                                </div>
                            </div>
                        </div>
						</section>
					</div>
				</div>
            </div>
				<!------End Step 2 ------>

				<!------Start Step 3 ------>
                <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='3') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseThree" style="width: 100%;">SERVICE PROJECTS</a>
                        </h4>
                    </div>
                    <div id="collapseThree" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='3') echo 'show'; ?>" data-parent="#accordion">
                        <div class="card-body">
					<section>
                        Did your chapter perform at least one service project to benefit mothers or children?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($chFinancialReport['at_least_one_service_project']) ? 'Not Answered' : ($chFinancialReport['at_least_one_service_project'] == 0 ? 'NO'
                                : ( $chFinancialReport ['at_least_one_service_project'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport['at_least_one_service_project_explanation']}}</strong><br>
                        Did your chapter make any contributions to any organization or individual that is not registered with the government as a charity?&nbsp;&nbsp;&nbsp;
                        <strong>{{ is_null($chFinancialReport['contributions_not_registered_charity']) ? 'Not Answered' : ($chFinancialReport['contributions_not_registered_charity'] == 0 ? 'NO'
                            : ( $chFinancialReport ['contributions_not_registered_charity'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport['contributions_not_registered_charity_explanation']}}</strong><br>
                        <br>
                            <table width="100%" style="border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #333;">
                                    <td>Project Description</td>
                                    <td>Project Income</td>
                                    <td>Supplies/Expenses</td>
                                    <td>Charity Donation</td>
                                    <td>M2M Donation</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $service_projects = null;
                                $totalServiceIncome = 0;
                                $totalServiceSupplies = 0;
                                $totalServiceCharity = 0;
                                $totalServiceM2M = 0;

                                if (isset($chFinancialReport['service_project_array'])) {
                                    $blobData = base64_decode($chFinancialReport['service_project_array']);
                                    $service_projects = unserialize($blobData);

                                    if ($service_projects === false) {
                                        echo "Error: Failed to unserialize data.";
                                    } else {
                                        foreach ($service_projects as $row) {
                                            // Sanitize and remove commas before converting to float
                                            $income = is_numeric(str_replace(',', '', $row['service_project_income'])) ? floatval(str_replace(',', '', $row['service_project_income'])) : 0;
                                            $supplies = is_numeric(str_replace(',', '', $row['service_project_supplies'])) ? floatval(str_replace(',', '', $row['service_project_supplies'])) : 0;
                                            $charity = is_numeric(str_replace(',', '', $row['service_project_charity'])) ? floatval(str_replace(',', '', $row['service_project_charity'])) : 0;
                                            $m2m = is_numeric(str_replace(',', '', $row['service_project_m2m'])) ? floatval(str_replace(',', '', $row['service_project_m2m'])) : 0;

                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['service_project_desc']) . "</td>";
                                            echo "<td>$" . number_format($income, 2) . "</td>";
                                            echo "<td>$" . number_format($supplies, 2) . "</td>";
                                            echo "<td>$" . number_format($charity, 2) . "</td>";
                                            echo "<td>$" . number_format($m2m, 2) . "</td>";
                                            echo "</tr>";

                                            // Totals
                                            $totalServiceIncome += $income;
                                            $totalServiceSupplies += $supplies;
                                            $totalServiceCharity += $charity;
                                            $totalServiceM2M += $m2m;
                                        }
                                        // Total row
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td><strong>Total</strong></td>";
                                        echo "<td><strong>$" . number_format($totalServiceIncome, 2) . "</strong></td>";
                                        echo "<td><strong>$" . number_format($totalServiceSupplies, 2) . "</strong></td>";
                                        echo "<td><strong>$" . number_format($totalServiceCharity, 2) . "</strong></td>";
                                        echo "<td><strong>$" . number_format($totalServiceM2M, 2) . "</strong></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr style='border-top: 1px solid #333;'>";
                                    echo "<td colspan='5'>No Service Projects Entered.</td>";
                                    echo "</tr>";
                                }
                                $totalServiceProjectExpenses = $totalServiceSupplies + $totalServiceCharity + $totalServiceM2M;
                                ?>
                            </tbody>
                        </table>
                        <br>
                        <strong>Total Service Project Income:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($totalServiceIncome, 2) }}</strong><br>
                        <strong>Total Service Project Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($totalServiceProjectExpenses, 2) }}</strong><br>
						<hr style="border-bottom: 2px solid #007bff">
					<!-- start:report_review -->
						<div class="form-row report_review" >
							<div class="card-header col-md-12">
                                <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                            </div>
							<div class="card-body form-row">
                                <div class="col-md-12">

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <label>Minimum of one service project completed:<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-md-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkServiceProject" value="1" {{ $chFinancialReport['check_minimum_service_project'] === 1 ? 'checked' : '' }} required>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkServiceProject" value="0" {{ $chFinancialReport['check_minimum_service_project'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label>Made a donation to the M2M Fund:<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkM2MDonation" value="1" {{ $chFinancialReport['check_m2m_donation'] === 1 ? 'checked' : '' }} required>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkM2MDonation" value="0" {{ $chFinancialReport['check_m2m_donation'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>

                                    <div class="col-12">
                                        <div class="form-group row">
										<label for="Step3_Note">Add New Note:</label>
									    <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(3)" name="Step3_Note" id="Step3_Note" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                                        <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
                                            <button type="button" id="AddNote3" class="btn btn-sm btn-success" onclick="AddNote(3)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
								        </div>
                                        </div>
                                    </div>
                                </div>
									<div class="col-12">
										<label for="Step3_Log"><strong>Reviewer Notes Logged for this Section (not visible to chapter):</strong></label>
									</div>
									<div class="col-12">
                                        <textarea class="form-control" style="width:100%" rows="8" name="Step3_Log" id="Step3_Log" readonly><?php echo $chFinancialReport['step_3_notes_log']; ?></textarea>
									</div>
                               <div class="col-12"><br></div>

						<!-- end:report_review -->
                        <div class="col-12 text-center">
						  <button type="submit" id="btn-step-3" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
					    </div>
                    </div>
                </div>
				    </section>
				</div>
				</div>
            </div>
				<!------End Step 3 ------>

				<!------Start Step 4 ------>
                <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='4') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseFour" style="width: 100%;">PARTIES & MEMBER BENEFITS</a>
                        </h4>
                    </div>
                    <div id="collapseFour" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='4') echo 'show'; ?>" data-parent="#accordion">
                        <div class="card-body">
				    <section>
                       <table width="75%" style="border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #333;">
                                <td>Party/Member Benefit Description</td>
                                <td>Benefit Income</td>
                                <td>Benefit Expenses</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $party_expenses = null;
                            $totalPartyIncome = 0;
                            $totalPartyExpense = 0;

                            if (isset($chFinancialReport['party_expense_array'])) {
                                $blobData = base64_decode($chFinancialReport['party_expense_array']);
                                $party_expenses = unserialize($blobData);

                                if ($party_expenses === false) {
                                    echo "Error: Failed to unserialize data.";
                                } else {
                                    foreach ($party_expenses as $row) {
                                        // Sanitize inputs
                                        $income = is_numeric(str_replace(',', '', $row['party_expense_income'])) ? floatval(str_replace(',', '', $row['party_expense_income'])) : 0;
                                        $expense = is_numeric(str_replace(',', '', $row['party_expense_expenses'])) ? floatval(str_replace(',', '', $row['party_expense_expenses'])) : 0;

                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['party_expense_desc']) . "</td>";
                                        echo "<td>$" . number_format($income, 2) . "</td>";
                                        echo "<td>$" . number_format($expense, 2) . "</td>";
                                        echo "</tr>";

                                        // Totals
                                        $totalPartyIncome += $income;
                                        $totalPartyExpense += $expense;
                                    }
                                    // Total row
                                    echo "<tr style='border-top: 1px solid #333;'>";
                                    echo "<td><strong>Total</strong></td>";
                                    echo "<td><strong>$" . number_format($totalPartyIncome, 2) . "</strong></td>";
                                    echo "<td><strong>$" . number_format($totalPartyExpense, 2) . "</strong></td>";
                                    echo "</tr>";

                                    // Calculate party percentage
                                    if ($totalDues == 0) {
                                        $partyPercentage = 0;
                                    } else {
                                        $partyPercentage = ($totalPartyExpense - $totalPartyIncome) / $totalDues;
                                    }
                                }
                            } else {
                                echo "<tr style='border-top: 1px solid #333;'>";
                                echo "<td colspan='3'>No Parties or Member Benefits Entered.</td>";
                                echo "</tr>";

                                $partyPercentage = 0;
                            }
                            ?>
                        </tbody>
                    </table>
                    <br>
                    <strong>Total Member Benefit Income:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($totalPartyIncome, 2) }}</strong><br>
                    <strong>Total Member Benefit Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($totalPartyExpense, 2) }}</strong><br>
                    <strong>Member Benefit/Dues Income Percentage:&nbsp;&nbsp;&nbsp;{{ number_format($partyPercentage * 100, 2) }}%</strong><br>
                    <hr style="border-bottom: 2px solid #007bff">
					<!-- start:report_review -->
					<div class="form-row report_review" >
						<div class="card-header col-md-12">
                            <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                        </div>
						<div class="card-body form-row">
                            <div class="col-12">

                                <div class="col-12">
                                    <div class="form-group row">
                                        <label>Is the Chapter's Party Expense under 15%?<span class="field-required">*&nbsp;</span></label>
                                        <div class="col-12 row">
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" name="check_party_percentage" value="2" {{ $chFinancialReport['check_party_percentage'] === 2 ? 'checked' : '' }} required>
                                                <label class="form-check-label">They are under 15%</label>
                                            </div>
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" name="check_party_percentage" value="1" {{ $chFinancialReport['check_party_percentage'] === 1 ? 'checked' : '' }}>
                                                <label class="form-check-label">They are between 15-20%</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="check_party_percentage" value="0" {{ $chFinancialReport['check_party_percentage'] === 0 ? 'checked' : '' }}>
                                                <label class="form-check-label">They are over 20%</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group row">
                                        <label for="Step4_Note">Add New Note:</label>
									<textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(4)" name="Step4_Note" id="Step4_Note" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                                    <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
                                        <button type="button" id="AddNote4" class="btn btn-sm bg-gradient-success" onclick="AddNote(4)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
                                    </div>
                                </div>
                            </div>
                            </div>

								<div class="col-12">
									<label for="Step4_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
								</div>
								<div class="col-12">
									<textarea class="form-control" style="width:100%" rows="8" name="Step4_Log" id="Step4_Log" readonly><?php echo $chFinancialReport['step_4_notes_log']; ?></textarea>
								</div>
                                <div class="col-12"><br></div>

					<!-- end:report_review -->
                    <div class="col-12 text-center">
                        <button type="submit" id="btn-step-4" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
                    </div>
                </div>
            </div>
				</section>
				</div>
				</div>
            </div>
				<!------End Step 4 ------>

				<!------Start Step 5 ------>
                <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='5') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseFive" style="width: 100%;">OFFICE & OPERATING EXPENSES</a>
                        </h4>
                    </div>
                    <div id="collapseFive" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='5') echo 'show'; ?>" data-parent="#accordion">
                        <div class="card-body">
                <section>
                    Printing Costs:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['office_printing_costs'], 2) }}</strong><br>
                    Postage Costs:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['office_postage_costs'], 2) }}</strong><br>
                    Membership Pins:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport['office_membership_pins_cost'], 2) }}</strong><br>
                <br>
                Other Office/Operating Expenses:
                    <table width="50%" >
                        <tbody>
                            <?php
                            $other_office_expenses = null;
                            $totalOfficeExpense = 0;

                            if (isset($chFinancialReport['office_other_expenses']) && $chFinancialReport['office_other_expenses'] !== null) {
                                $blobData = base64_decode($chFinancialReport['office_other_expenses']);
                                $other_office_expenses = unserialize($blobData);

                                if ($other_office_expenses === false) {
                                    echo "Error: Failed to unserialize data.";
                                } else {
                                    if (is_array($other_office_expenses) && count($other_office_expenses) > 0) {
                                        foreach ($other_office_expenses as $row) {
                                            // Sanitize inputs
                                            $expense = is_numeric(str_replace(',', '', $row['office_other_expense'])) ? floatval(str_replace(',', '', $row['office_other_expense'])) : 0;
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['office_other_desc']) . "</td>";
                                            echo "<td>$" . number_format($expense, 2) . "</td>";
                                            echo "</tr>";

                                            // Totals
                                            $totalOfficeExpense += $expense;
                                        }
                                        // Total row
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td><strong>Total</strong></td>";
                                        echo "<td><strong>$" . number_format($totalOfficeExpense, 2) . "</strong></td>";
                                        echo "</tr>";
                                    } else {
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td colspan='2'>No Other Office/Operating Expenses Entered.</td>";
                                        echo "</tr>";
                                    }
                                }
                            } else {
                                echo "<tr style='border-top: 1px solid #333;'>";
                                echo "<td colspan='2'>No Other Office/Operating Expenses Entered.</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <br>
                    <strong>Total Office/Operating Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($chFinancialReport['office_printing_costs'] + $chFinancialReport['office_postage_costs'] +
                            $chFinancialReport['office_membership_pins_cost'] + $totalOfficeExpense, 2) }}</strong><br>
						<hr style="border-bottom: 2px solid #007bff">
                        <!-- start:report_review -->
				<div  class="form-row report_review">
					<div class="card-header col-md-12">
                        <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                    </div>
					<div class="card-body form-row">
                        <div class="col-12">

                            <div class="col-12">
                                <div class="col-12">
                                    <div class="form-group row">
								<label for="Step5_Note">Add New Note:</label>
								<textarea class="form-control" rows="3" style="width:100%" oninput="EnableNoteLogButton(5)" name="Step5_Note" id="Step5_Note" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                                <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
								<button type="button" id="AddNote5" class="btn btn-sm bg-gradient-success" onclick="AddNote(5)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
						    </div>
                        </div>
                    </div>
                    </div>

							<div class="col-12">
								<label for="Step5_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
							</div>
							<div class="col-12">
								<textarea class="form-control" style="width:100%" rows="8" name="Step5_Log" id="Step5_Log" readonly><?php echo $chFinancialReport['step_5_notes_log']; ?></textarea>
							</div>
                            <div class="col-12"><br></div>

                    <!-- end:report_review -->
                    <div class="col-12 text-center">
                        <button type="submit" id="btn-step-5" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
                    </div>
                </div>
            </div>
                </section>
			</div>
			</div>
        </div>
			<!------End Step 5 ------>

            <!------Start Step 6 ------>
            <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='6') echo "active";?>">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseSix" style="width: 100%;">INTERNATIONAL EVENTS & RE-REGISTRATION</a>
                    </h4>
                </div>
                <div id="collapseSix" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='6') echo 'show'; ?>" data-parent="#accordion">
                    <div class="card-body">
                    <section>
                        <strong>Chapter Re-Registration:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($chFinancialReport['annual_registration_fee'], 2) }}</strong><br>
                        <br>
                        Did your chapter attend an International Event?&nbsp;&nbsp;&nbsp;
                        <strong>{{ is_null($chFinancialReport['international_event']) ? 'Not Answered' : ($chFinancialReport['international_event'] == 0 ? 'NO'
                            : ( $chFinancialReport ['international_event'] == 1 ? 'YES' : 'Not Answered' )) }}</strong><br>
                        <br>
                        <table width="75%" style="border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #333;">
                                    <td>Description</td>
                                    <td>Income</td>
                                    <td>Expenses</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $international_event_array = null;
                                $totalEventIncome = 0;
                                $totalEventExpense = 0;

                                if (isset($chFinancialReport['international_event_array']) && $chFinancialReport['international_event_array'] !== null) {
                                    $blobData = base64_decode($chFinancialReport['international_event_array']);
                                    $international_event_array = unserialize($blobData);

                                    if ($international_event_array === false) {
                                        echo "Error: Failed to unserialize data.";
                                    } else {
                                        if (is_array($international_event_array) && count($international_event_array) > 0) {
                                            foreach ($international_event_array as $row) {
                                                // Sanitize and validate inputs
                                                $income = is_numeric(str_replace(',', '', $row['intl_event_income'])) ? floatval(str_replace(',', '', $row['intl_event_income'])) : 0;
                                                $expense = is_numeric(str_replace(',', '', $row['intl_event_expenses'])) ? floatval(str_replace(',', '', $row['intl_event_expenses'])) : 0;
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['intl_event_desc']) . "</td>";
                                                echo "<td>$" . number_format($income, 2) . "</td>";
                                                echo "<td>$" . number_format($expense, 2) . "</td>";
                                                echo "</tr>";

                                                // Totals
                                                $totalEventIncome += $income;
                                                $totalEventExpense += $expense;
                                            }
                                            // Total row
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td><strong>Total</strong></td>";
                                            echo "<td><strong>$" . number_format($totalEventIncome, 2) . "</strong></td>";
                                            echo "<td><strong>$" . number_format($totalEventExpense, 2) . "</strong></td>";
                                            echo "</tr>";
                                        } else {
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td colspan='3'>No International Events Entered.</td>";
                                            echo "</tr>";
                                        }
                                    }
                                } else {
                                    echo "<tr style='border-top: 1px solid #333;'>";
                                    echo "<td colspan='3'>No International Events Entered.</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <br>
                        <strong>Total Events Income:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($totalEventIncome, 2) }}</strong><br>
                        <strong>Total Events Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($totalEventExpense, 2) }}</strong><br>
						<hr style="border-bottom: 2px solid #007bff">
                <!-- start:report_review -->
                    <div  class="form-row report_review">
                        <div class="card-header col-md-12">
                            <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                        </div>
                        <div class="card-body form-row">
                            <div class="col-12">

                                <div class="col-12">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <label>Did they attended an in person or virtual International Event?<span class="field-required">*&nbsp;</span></label>
                                        <div class="col-12 row">
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" name="checkAttendedTraining" value="1" {{ $chFinancialReport['check_attended_training'] === 1 ? 'checked' : '' }}>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="checkAttendedTraining" value="0" {{ $chFinancialReport['check_attended_training'] === 0 ? 'checked' : '' }}>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                    <label for="Step6_Note">Add New Note:</label>
                                    <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(6)" name="Step6_Note" id="Step6_Note" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                                    <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
                                        <button type="button" id="AddNote6" class="btn btn-sm bg-gradient-success" onclick="AddNote(6)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
                                </div>
                            </div>
                        </div>
                        </div>

					<div class="col-12">
						<label for="Step6_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
					</div>
					<div class="col-12">
						<textarea class="form-control" style="width:100%" rows="8" name="Step6_Log" id="Step6_Log" readonly><?php echo $chFinancialReport['step_6_notes_log']; ?></textarea>
					</div>
                    <div class="col-12"><br></div>

                    <!-- end:report_review -->
                    <div class="col-12 text-center">
                          <button type="submit" id="btn-step-6" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
                    </div>
                </div>
            </div>
                    </section>
                </div>
                </div>
            </div>
                <!------End Step 6 ------>

			<!------Start Step 7 ------>
            <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='7') echo "active";?>">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseSeven" style="width: 100%;">DONATIONS TO YOUR CHAPTER</a>
                    </h4>
                </div>
                <div id="collapseSeven" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='7') echo 'show'; ?>" data-parent="#accordion">
                    <div class="card-body">
				<section>
                    Monetary Donations:
                    <table width="100%" style="border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #333;">
                                <td>Purpose of Donation</td>
                                <td>Donor Name/Address</td>
                                <td>Date</td>
                                <td>Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $monetary_donations_to_chapter = null;
                            $totalDonationAmount = 0;

                            if (isset($chFinancialReport['monetary_donations_to_chapter']) && $chFinancialReport['monetary_donations_to_chapter'] !== null) {
                                $blobData = base64_decode($chFinancialReport['monetary_donations_to_chapter']);
                                $monetary_donations_to_chapter = unserialize($blobData);

                                if ($monetary_donations_to_chapter === false) {
                                    echo "Error: Failed to unserialize data.";
                                } else {
                                    if (is_array($monetary_donations_to_chapter) && count($monetary_donations_to_chapter) > 0) {
                                        foreach ($monetary_donations_to_chapter as $row) {
                                            // Sanitize and validate inputs
                                            $donationDate = $row['mon_donation_date'] ? date('m/d/Y', strtotime($row['mon_donation_date'])) : '';

                                            $donationAmount = is_numeric(str_replace(',', '', $row['mon_donation_amount'])) ? floatval(str_replace(',', '', $row['mon_donation_amount'])) : 0;
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['mon_donation_desc']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['mon_donation_info']) . "</td>";
                                            echo "<td>" . htmlspecialchars($donationDate) . "</td>";
                                            echo "<td>$" . number_format($donationAmount, 2) . "</td>";
                                            echo "</tr>";

                                            // Totals
                                            $totalDonationAmount += $donationAmount;
                                        }
                                        // Total row
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td><strong>Total</strong></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td><strong>$" . number_format($totalDonationAmount, 2) . "</strong></td>";
                                        echo "</tr>";
                                    } else {
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td colspan='4'>No Monetary Donations Entered.</td>";
                                        echo "</tr>";
                                    }
                                }
                            } else {
                                echo "<tr style='border-top: 1px solid #333;'>";
                                echo "<td colspan='4'>No Monetary Donations Entered.</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <br>
                    <strong>Total Monetary Donations:&nbsp;&nbsp;&nbsp;{{ '$'.number_format( $totalDonationAmount, 2) }}</strong><br>
                    <br>
					Non-Monetary Donations:
                    <table width="75%" style="border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #333;">
                                <td>Purpose of Donation</td>
                                <td>Donor Name/Address</td>
                                <td>Date</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $non_monetary_donations_to_chapter = null;

                                if (isset($chFinancialReport['non_monetary_donations_to_chapter']) && $chFinancialReport['non_monetary_donations_to_chapter'] !== null) {
                                    $blobData = base64_decode($chFinancialReport['non_monetary_donations_to_chapter']);
                                    $non_monetary_donations_to_chapter = unserialize($blobData);

                                    if ($non_monetary_donations_to_chapter === false) {
                                        echo "Error: Failed to unserialize data.";
                                    } else {
                                        if (is_array($non_monetary_donations_to_chapter) && count($non_monetary_donations_to_chapter) > 0) {
                                            foreach ($non_monetary_donations_to_chapter as $row) {
                                                echo "<tr style='border-top: 1px solid #333;'>";
                                                echo "<td>" . $row['nonmon_donation_desc'] . "</td>";
                                                echo "<td>" . $row['nonmon_donation_info'] . "</td>";
                                                echo "<td>" . ($row['nonmon_donation_date'] ? date('m/d/Y', strtotime($row['nonmon_donation_date'])) : '') . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td colspan='3'>No Non-Monetary Donations Entered.</td>";
                                            echo "</tr>";
                                        }
                                    }
                                } else {
                                    echo "<tr style='border-top: 1px solid #333;'>";
                                    echo "<td colspan='3'>No Non-Monetary Donations Entered.</td>";
                                    echo "</tr>";
                                }
                            ?>

                        </tbody>
                    </table>
                    <br>
                    <hr style="border-bottom: 2px solid #007bff">
					<!-- start:report_review -->
						<div  class="form-row report_review">
							<div class="card-header col-md-12">
                                <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                            </div>
							<div class="card-body form-row">
                                <div class="col-12">

                                    <div class="col-12">
                                    <div class="col-12">
                                        <div class="form-group row">
										<label for="Step7_Note">Add New Note:</label>
										<textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(7)" name="Step7_Note" id="Step7_Note" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                                        <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
										<button type="button" id="AddNote7" class="btn btn-sm bg-gradient-success" onclick="AddNote(7)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
								</div>
                            </div>
                        </div>
                        </div>

                                <div class="col-12">
										<label for="Step7_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
									</div>
									<div class="col-12">
										<textarea class="form-control" style="width:100%" rows="8" name="Step7_Log" id="Step7_Log" readonly><?php echo $chFinancialReport['step_7_notes_log']; ?></textarea>
									</div>
                                    <div class="col-12"><br></div>

						<!-- end:report_review -->
                        <div class="col-12 text-center">
							  <button type="submit" id="btn-step-7" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
                        <div>
                        </div>
                    </div>
                    </section>
				</div>
				</div>
            </div>
				<!------End Step 7 ------>

				<!------Start Step 8 ------>
                <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='8') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseEight" style="width: 100%;">OTHER INCOME & EXPENSES</a>
                        </h4>
                    </div>
                    <div id="collapseEight" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='8') echo 'show'; ?>" data-parent="#accordion">
                        <div class="card-body">
					<section>
                        <table width="75%" style="border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #333;">
                                    <td>Description</td>
                                    <td>Income</td>
                                    <td>Expenses</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $other_income_and_expenses_array = null;
                                $totalOtherIncome = 0;
                                $totalOtherExpenses = 0;

                                if (isset($chFinancialReport['other_income_and_expenses_array'])) {
                                    $blobData = base64_decode($chFinancialReport['other_income_and_expenses_array']);
                                    $other_income_and_expenses_array = unserialize($blobData);

                                    if ($other_income_and_expenses_array === false) {
                                        echo "Error: Failed to unserialize data.";
                                    } else {
                                        if (is_array($other_income_and_expenses_array) && count($other_income_and_expenses_array) > 0) {
                                            foreach ($other_income_and_expenses_array as $row) {
                                                // Sanitize and validate inputs
                                                $otherIncome = is_numeric(str_replace(',', '', $row['other_income'])) ? floatval(str_replace(',', '', $row['other_income'])) : 0;
                                                $otherExpenses = is_numeric(str_replace(',', '', $row['other_expenses'])) ? floatval(str_replace(',', '', $row['other_expenses'])) : 0;
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['other_desc']) . "</td>";
                                                echo "<td>$" . number_format($otherIncome, 2) . "</td>";
                                                echo "<td>$" . number_format($otherExpenses, 2) . "</td>";
                                                echo "</tr>";

                                                // Accumulate totals
                                                $totalOtherIncome += $otherIncome;
                                                $totalOtherExpenses += $otherExpenses;
                                            }
                                            // Total row
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td><strong>Total</strong></td>";
                                            echo "<td><strong>$" . number_format($totalOtherIncome, 2) . "</strong></td>";
                                            echo "<td><strong>$" . number_format($totalOtherExpenses, 2) . "</strong></td>";
                                            echo "</tr>";
                                        } else {
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td colspan='3'>No Other Income or Expenses Entered.</td>";
                                            echo "</tr>";
                                        }
                                    }
                                } else {
                                    echo "<tr style='border-top: 1px solid #333;'>";
                                    echo "<td colspan='3'>No Other Income or Expenses Entered.</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <br>
                        <strong>Total Other Income:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($totalOtherIncome, 2) }}</strong><br>
                        <strong>Total Other Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($totalOtherExpenses, 2) }}</strong><br>
						<hr style="border-bottom: 2px solid #007bff">
					<!-- start:report_review -->
					<div  class="form-row report_review">
						<div class="card-header col-md-12">
                            <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                        </div>
						<div class="card-body form-row">
                            <div class="col-12">

                                <div class="col-12">
                                <div class="col-12">
                                    <div class="form-group row">
									<label for="Step8_Note">Add New Note:</label>
									<textarea class="form-control" style="width:100%" rows="3" oninput="EnableNoteLogButton(8)"  name="Step8_Note" id="Step8_Note" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                                    <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
									<button type="button" id="AddNote8" class="btn btn-sm bg-gradient-success" onclick="AddNote(8)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
							</div>
                        </div>
                    </div>
                    </div>
                            <div class="col-12">
									<label for="Step8_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
								</div>
								<div class="col-12">
									<textarea class="form-control" style="width:100%" rows="8" name="Step8_Log" id="Step8_Log" readonly><?php echo $chFinancialReport['step_8_notes_log']; ?></textarea>
								</div>
                                <div class="col-12"><br></div>

					<!-- end:report_review -->
                    <div class="col-12 text-center">
							  <button type="submit" id="btn-step-8" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
					</div>
                </div>
            </div>
				</section>
			  </div>
			  </div>
            </div>
				<!------End Step 8 ------>

                <!------Start Step 9 ------>
                <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='9') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseNine" style="width: 100%;">FINANCIAL SUMMARY</a>
                        </h4>
                    </div>
                    <div id="collapseNine" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='9') echo 'show'; ?>" data-parent="#accordion">
                        <div class="card-body">
				<section>
                    <?php
                        $totalIncome = $totalDues + $totalServiceIncome + $totalPartyIncome + $totalDonationAmount + $totalEventIncome + $totalOtherIncome;
                        $totalExpenses = $chFinancialReport['manditory_meeting_fees_paid'] + $chFinancialReport['voluntary_donations_paid'] + $chFinancialReport['paid_baby_sitters'] + $totalChildrensRoomExpenses + $totalServiceProjectExpenses
                                + $totalPartyExpense + $chFinancialReport['office_printing_costs'] + $chFinancialReport['office_postage_costs'] +
                                $chFinancialReport['office_membership_pins_cost'] + $totalOfficeExpense + $chFinancialReport['annual_registration_fee'] + $totalEventExpense + $totalOtherExpenses;
                        $treasuryBalance = $chFinancialReport ['amount_reserved_from_previous_year'] + $totalIncome - $totalExpenses
                    ?>
                    <table width="50%" style="border-collapse: collapse;">
                        <tbody>
                            <tr><td><strong>INCOME</strong></td></tr>
                            <tr><td style="border-top: 1px solid #333;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Membership Dues Income</td>
                            <td style="border-top: 1px solid #333;">{{ '$'.number_format($totalDues, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Service Project Income</td>
                            <td>{{ '$'.number_format($totalServiceIncome, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Party/Member Benefit Income</td>
                            <td>{{ '$'.number_format($totalPartyIncome, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Monetary Donations to Chapter</td>
                            <td>{{ '$'.number_format($totalDonationAmount, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;International Event Registration</td>
                            <td>{{ '$'.number_format($totalEventIncome, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other Income</td>
                            <td>{{ '$'.number_format($totalOtherIncome, 2) }}</td></tr>
                            <tr><td style="border-top: 1px solid #333;"><strong>TOTAL INCOME:</strong></td>
                            <td style="border-top: 1px solid #333;"><strong>{{ '$'.number_format($totalIncome, 2) }}</strong></td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td><strong>EXPENSES<strong></td></tr>
                            <tr><td style="border-top: 1px solid #333;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Meeting Room Expenses</td>
                            <td style="border-top: 1px solid #333;">{{ '$'.number_format($chFinancialReport['manditory_meeting_fees_paid'] + $chFinancialReport['voluntary_donations_paid'], 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Children's Room Expenses:</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Supplies</td>
                            <td>{{ '$'.number_format($totalChildrenSupplies, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paid Sitters</td>
                            <td>{{ '$'.number_format($chFinancialReport['paid_baby_sitters'], 2)  }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other</td>
                            <td>{{ '$'.number_format($totalChildrenOther, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Children's Room Expense Total</td>
                            <td>{{ '$'.number_format($chFinancialReport['paid_baby_sitters'] + $totalChildrensRoomExpenses, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Service Project Expenses</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Supplies:</td>
                            <td>{{ '$'.number_format($totalServiceSupplies, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Charitable Donations</td>
                            <td>{{ '$'.number_format($totalServiceCharity, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;M2M fund Donation</td>
                            <td>{{ '$'.number_format($totalServiceM2M, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Service Project Expense Total</td>
                            <td>{{ '$'.number_format($totalServiceProjectExpenses, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Party/Member Benefit Expenses</td>
                            <td> {{ '$'.number_format($totalPartyExpense, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Office/Operating Expenses</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Printing</td>
                            <td>{{ '$'.number_format($chFinancialReport['office_printing_costs'], 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Postage</td>
                            <td>{{ '$'.number_format($chFinancialReport['office_postage_costs'], 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Membership Pins</td>
                            <td>{{ '$'.number_format($chFinancialReport['office_membership_pins_cost'], 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other</td>
                            <td>{{ '$'.number_format($totalOfficeExpense, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Office/Operating Expense Total</td>
                            <td>{{ '$'.number_format($chFinancialReport['office_printing_costs'] + $chFinancialReport['office_postage_costs'] +
                                $chFinancialReport['office_membership_pins_cost'] + $totalOfficeExpense, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Annual Chapter Re-registration Fee</td>
                            <td>{{ '$'.number_format($chFinancialReport['annual_registration_fee'], 2)  }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;International Event Registration</td>
                            <td>{{ '$'.number_format($totalEventExpense, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other Expenses</td>
                            <td>{{ '$'.number_format($totalOtherExpenses, 2) }}</td></tr>
                            <tr><td style="border-top: 1px solid #333;"><strong>TOTAL EXPENSES</strong></td>
                            <td style="border-top: 1px solid #333;"><strong>{{ '$'.number_format($totalExpenses, 2) }}</strong></td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td style="border-top: 1px solid #333; border-bottom: 1px solid #333;"><strong>PROFIT (LOSS)</strong></td>
                            <td style="border-top: 1px solid #333; border-bottom: 1px solid #333;"><strong>
                            @php
                                $netAmount = $totalIncome - $totalExpenses;
                                $formattedAmount = ($netAmount < 0) ? '($' . number_format(abs($netAmount), 2) . ')' : '$' . number_format($netAmount, 2);
                            @endphp
                            {{ $formattedAmount }}</strong></td></tr>
                        </tbody>
                    </table>
                    <br>
                    <hr style="border-bottom: 2px solid #007bff">
         <!-- start:report_review -->
		<div  class="form-row report_review">
			<div class="card-header col-md-12">
                <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
            </div>
			<div class="card-body form-row">
                <div class="col-12">

                    <div class="col-12">
                        <div class="form-group row">
                            <label>Total Income/Revenue:</label>
                        <div class="col-12">
                            <strong>{{ '$'.number_format($totalIncome, 2) }}</strong>
                        </div>
                    </div>
                        <div class="form-group row">
                            <label>Is the Total Income/Revenue less than $50,000?<span class="field-required">*&nbsp;</span></label>
                            <div class="col-12 row">
                                <div class="form-check" style="margin-right: 20px;">
                                    <input class="form-check-input" type="radio" name="checkTotalIncome" value="1" {{ $chFinancialReport['check_total_income_less'] === 1 ? 'checked' : '' }}>
                                    <label class="form-check-label">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="checkTotalIncome" value="0" {{ $chFinancialReport['check_total_income_less'] === 0 ? 'checked' : '' }}>
                                    <label class="form-check-label">No</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group row">
                            <label for="Step9_Note">Add New Note:</label>
                            <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(9)" name="Step9_Note" id="Step9_Note" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                            <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
                            <button type="button" id="AddNote9" class="btn btn-sm bg-gradient-success" onclick="AddNote(9)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
                    </div>
                </div>
            </div>
            </div>
					<div class="col-12">
						<label for="Step9_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
					</div>
					<div class="col-12">
						<textarea class="form-control" style="width:100%" rows="8" name="Step9_Log" id="Step9_Log" readonly><?php echo $chFinancialReport['step_9_notes_log']; ?></textarea>
					</div>
                                            <div class="col-12"><br></div>
		    <!-- end:report_review -->
                    <div class="col-12 text-center">
                        <button type="submit" id="btn-step-9" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
                        </div>
                    </div>
                </div>
				</section>
			</div>
			</div>
        </div>
			<!------End Step 9 ------>

                <!------Start Step 10 ------>
                <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='10') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseTen" style="width: 100%;">BANK RECONCILIATION</a>
                        </h4>
                    </div>
                    <div id="collapseTen" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='10') echo 'show'; ?>" data-parent="#accordion">
                        <div class="card-body">
					<section>
                        <div class="flex-container">
                            Is a copy of your chapter’s most recent bank statement included?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($chFinancialReport['bank_statement_included']) ? 'Not Answered' : ($chFinancialReport['bank_statement_included'] == 0 ? 'NO'
                                : ( $chFinancialReport ['bank_statement_included'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport ['bank_statement_included_explanation']}}{{ $chFinancialReport ['wheres_the_money'] }}</strong><br>
                            <br>
                            <div class="flex-item">
                                Beginning Balance&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport ['amount_reserved_from_previous_year'], 2)}}</strong><br>
                            </div>
                            <div class="flex-item">
                                Ending Bank Statement Balance&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport ['bank_balance_now'], 2)}}</strong><br>
                            </div>
                            <div class="flex-item">
                                Profit (Loss)&nbsp;&nbsp;&nbsp;<strong>
                                    @php
                                        $netAmount = $totalIncome - $totalExpenses;
                                        $formattedAmount = ($netAmount < 0) ? '($' . number_format(abs($netAmount), 2) . ')' : '$' . number_format($netAmount, 2);
                                    @endphp
                                    {{ $formattedAmount }}</strong><br>
                            </div>
                            <div class="flex-item">
                                &nbsp;&nbsp;&nbsp;
                            </div>
                            <div class="flex-item">
                                Ending Balance (Treasury Balance Now)&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($treasuryBalance, 2)}}</strong><br>
                            </div>
                            <div class="flex-item">
                                &nbsp;&nbsp;&nbsp;
                            </div>
                        </div>
                        <br>
                        <table width="75%" style="border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #333;">
                                    <td>Date</td>
                                    <td>Check No.</td>
                                    <td>Transation Desc.</td>
                                    <td>Payment Amt.</td>
                                    <td>Deposit Amt.</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $bank_rec_array = null;
                                $totalPayments = 0;
                                $totalDeposits = 0;

                                if (isset($chFinancialReport['bank_reconciliation_array']) && $chFinancialReport['bank_reconciliation_array'] !== null) {
                                    $blobData = base64_decode($chFinancialReport['bank_reconciliation_array']);
                                    $bank_rec_array = unserialize($blobData);

                                    if ($bank_rec_array === false) {
                                        echo "Error: Failed to unserialize data.";
                                    } else {
                                        if (is_array($bank_rec_array) && count($bank_rec_array) > 0) {
                                            foreach ($bank_rec_array as $row) {
                                                // Sanitize and validate inputs
                                                $paymentAmount = is_numeric(str_replace(',', '', $row['bank_rec_payment_amount'])) ? floatval(str_replace(',', '', $row['bank_rec_payment_amount'])) : 0;
                                                $depositAmount = is_numeric(str_replace(',', '', $row['bank_rec_desposit_amount'])) ? floatval(str_replace(',', '', $row['bank_rec_desposit_amount'])) : 0;
                                                $checkNo = htmlspecialchars($row['bank_rec_check_no']);
                                                $desc = htmlspecialchars($row['bank_rec_desc']);
                                                $date = $row['bank_rec_date'] ? date('m/d/Y', strtotime($row['bank_rec_date'])) : '';

                                                echo "<tr>";
                                                echo "<td>$date</td>";
                                                echo "<td>$checkNo</td>";
                                                echo "<td>$desc</td>";
                                                echo "<td>$" . number_format($paymentAmount, 2) . "</td>";
                                                echo "<td>$" . number_format($depositAmount, 2) . "</td>";
                                                echo "</tr>";

                                                // Accumulate totals
                                                $totalPayments += $paymentAmount;
                                                $totalDeposits += $depositAmount;
                                            }

                                            // Total row
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td><strong>Total</strong></td>";
                                            echo "<td><strong></strong></td>";
                                            echo "<td><strong></strong></td>";
                                            echo "<td><strong>$" . number_format($totalPayments, 2) . "</strong></td>";
                                            echo "<td><strong>$" . number_format($totalDeposits, 2) . "</strong></td>";
                                            echo "</tr>";
                                        } else {
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td colspan='5'>No Reconciliation Transactions Entered.</td>";
                                            echo "</tr>";
                                        }
                                    }
                                } else {
                                    echo "<tr style='border-top: 1px solid #333;'>";
                                    echo "<td colspan='5'>No Reconciliation Transactions Entered.</td>";
                                    echo "</tr>";
                                }

                                $totalReconciliation = $totalDeposits - $totalPayments;
                                ?>
                            </tbody>
                            </table>
                        <br>
                        Reconciled Bank Statement:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($chFinancialReport ['bank_balance_now'] + $totalReconciliation, 2) }}</strong><br>

						<hr style="border-bottom: 2px solid #007bff">
				<!-- start:report_review -->
				<div  class="form-row report_review">
                    <div class="card-header col-md-12">
                        <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                    </div>
                            <div class="card-body form-row">
                                <div class="col-12">

                                    @if (!is_null($chDocuments['statement_1_path']))
                                        <div class="col-12">
                                            <label>Bank Statement Uploaded:</label><a href="https://drive.google.com/uc?export=download&id=<?php echo $chDocuments['statement_1_path']; ?>" >&nbsp; View Bank Statement</a><br>
                                        </div>
                                    @endif
                                    @if (!is_null($chDocuments['statement_2_path']))
                                        <div class="col-12">
                                            <label>Additional Statement Uploaded:</label><a href="https://drive.google.com/uc?export=download&id=<?php echo $chDocuments['statement_2_path']; ?>" >&nbsp; View Additional Bank Statement</a><br>
                                        </div>
                                    @endif
                                    <div class="col-12" id="StatementBlock">
                                        <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Upload or Replace Bank Statement(s).<br>
                                        @if (!is_null($chDocuments['statement_1_path']))
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Bank Statement</button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Bank Statement</button>
                                        @endif
                                    </div>
                                        <input type="hidden" name="StatementFile" id="StatementPath" value="<?php echo $chDocuments['statement_1_path']; ?>">
                                    <div class="clearfix"></div>
                                    <div class="col-12"><br></div>
                                    <div class="col-12" id="Statement2Block">
                                        @if (!is_null($chDocuments['statement_2_path']))
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Additional Bank Statement</button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Additional Bank Statement</button>
                                        @endif
                                    </div>
                                    <input type="hidden" name="Statement2File" id="Statement2Path" value="<?php echo $chDocuments['statement_2_path']; ?>">
                                    <div class="clearfix"></div>
                                    <div class="col-12"><br></div>

                                <div class="col-12">

                                <div class="col-12">
                                    <div class="form-group row">
                                        <label>Ending Balance on Last Year's Report:</label>
                                        <div class="col-md-12 row">
                                            <strong>{{ '$'.number_format($chFinancialReport['pre_balance'], 2) }}</strong>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label>Does this year's Beginning Balance match last year's Ending Balance?<span class="field-required">*&nbsp;</span></label>
                                        <div class="col-12 row">
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" name="check_beginning_balance" value="1" {{ $chFinancialReport['check_beginning_balance'] === 1 ? 'checked' : '' }}>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="check_beginning_balance" value="0" {{ $chFinancialReport['check_beginning_balance'] === 0 ? 'checked' : '' }}>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label>Current bank statement included and balance matches chapter entry:<span class="field-required">*&nbsp;</span></label>
                                        <div class="col-12 row">
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" name="checkBankStatementIncluded" value="1" {{ $chFinancialReport['check_bank_statement_included'] === 1 ? 'checked' : '' }}>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="checkBankStatementIncluded" value="0" {{ $chFinancialReport['check_bank_statement_included'] === 0 ? 'checked' : '' }}>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label>Treasury Balance Now matches Reconciled Bank Balance:<span class="field-required">*&nbsp;</span></label>
                                        <div class="col-md-12 row">
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" name="checkBankStatementMatches" value="1" {{ $chFinancialReport['check_bank_statement_matches'] === 1 ? 'checked' : '' }}>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="checkBankStatementMatches" value="0" {{ $chFinancialReport['check_bank_statement_matches'] === 0 ? 'checked' : '' }}>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-12">
                                        <div class="input-group row">
                                            <label for="post_balance">Enter Ending Balance (to be used as beginning balance on next year's report):</label>
                                           <div class="col-md-12 row">
                                             <div class="input-group-prepend">
                                              <span class="input-group-text">$</span>
                                            </div>
                                            <input type="text" class="form-control" min="0" step="0.01" name="post_balance" id="post_balance" style="width: 120px;" value="<?php if(!empty($chFinancialReport)) echo $chFinancialReport['post_balance'] ?>">
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    <div class="col-12">
                                <div class="form-group row">
                                        <label for="Step10_Note">Add New Note:</label>
                                        <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(10)" name="Step10_Note" id="Step10_Note" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                                        <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
                                        <button type="button" id="AddNote10" class="btn btn-sm bg-gradient-success" onclick="AddNote(10)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
                                    </div>
                                </div>
                            </div>
                            </div>

							<div class="col-12">
								<label for="Step10_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
							</div>
							<div class="col-12">
								<textarea class="form-control" style="width:100%" rows="8" name="Step10_Log" id="Step10_Log" readonly><?php echo $chFinancialReport['step_10_notes_log']; ?></textarea>
							</div>
                            <div class="col-12"><br></div>

                    <!-- end:report_review -->
                    <div class="col-12 text-center">
                        <button type="submit" id="btn-step-10" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
                    </div>
                </div>
            </div>
                </section>
			</div>
			</div>
        </div>
			<!------End Step 10 ------>

            <!------Start Step 11 ------>
            <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='11') echo "active";?>">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseEleven" style="width: 100%;">990N IRS FILING</a>
                    </h4>
                </div>
                <div id="collapseEleven" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='11') echo 'show'; ?>" data-parent="#accordion">
                    <div class="card-body">
                <section>
                    <div class="flex-container">
                        <div class="col-12">
                            <p>The 990N filing is an IRS requirement that all chapters must complete, but it cannot be filed before July 1st.  After filing, upload a copy of your chapter's filing confirmation here.  You can upload a copy of your confirmation email or screenshot after filing.  All chapters should file their 990N directly with the IRS and not through a third party. <span style="color:red"><i>The IRS does not charge a fee for 990N filings.</i></span></p>
                            Did your chapter file their IRS 990N?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($chFinancialReport['file_irs']) ? 'Not Answered' : ($chFinancialReport['file_irs'] == 0 ? 'NO'
                                : ( $chFinancialReport ['file_irs'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport ['file_irs_explanation']}}</strong><br>
                        </div>
                </div>
            <hr style="border-bottom: 2px solid #007bff">
            <!-- start:report_review -->
            <div  class="form-row report_review">
                <div class="card-header col-md-12">
                    <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                </div>
                        <div class="card-body form-row">
                              <div class="col-12">
                            @if (!is_null($chDocuments['irs_path']))
                                    <div class="col-12">
                                        <label>990N Filing Uploaded:</label><a href="https://drive.google.com/uc?export=download&id={{ $chDocuments['irs_path'] }}">&nbsp; View 990N Confirmation</a><br>
                                    </div>
                                    <div class="col-12" id="990NBlock">
                                        <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Replace 990N File.<br>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace 990N Confirmation</button>
                                </div>
                            @else
                                <div class="col-12" id="990NBlock">
                                        <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Upload 990N File.<br>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload 990N Confirmation</button>
                                </div>
                            @endif
                            <input type="hidden" name="990NFiling" id="990NFiling" value="<?php echo $chDocuments['irs_path']; ?>">
                            <div class="clearfix"></div>
                            <div class="col-12"><br></div>
                            <div class="col-12">

                                <div class="form-group row">
                                    <label>Did the chapter file their 990N with the date range of <strong>7/1/<?php echo date('Y')-1 .' - 6/30/'.date('Y');?></strong>?<span class="field-required">*&nbsp;</span></label>
                                    <div class="col-12 row">
                                        <div class="form-check" style="margin-right: 20px;">
                                            <input class="form-check-input" type="radio" name="checkCurrent990NAttached" value="1" {{ $chFinancialReport['check_current_990N_included'] === 1 ? 'checked' : '' }}>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="checkCurrent990NAttached" value="0" {{ $chFinancialReport['check_current_990N_included'] === 0 ? 'checked' : '' }}>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>

                        <div class="col-12">
                            <div class="form-group row">
                                    <label for="Step11_Note">Add New Note:</label>
                                    <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(11)" name="Step11_Note" id="Step11_Note" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                                    <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
                                    <button type="button" id="AddNote11" class="btn btn-sm bg-gradient-success" onclick="AddNote(11)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
                                </div>
                            </div>
                        </div>
                        </div>

                        <div class="col-12">
                            <label for="Step11_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
                        </div>
                        <div class="col-12">
                            <textarea class="form-control" style="width:100%" rows="8" name="Step11_Log" id="Step11_Log" readonly><?php echo $chFinancialReport['step_11_notes_log']; ?></textarea>
                        </div>
                        <div class="col-12"><br></div>

                <!-- end:report_review -->
                <div class="col-12 text-center">
                    <button type="submit" id="btn-step-11" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
                </div>
            </div>
        </div>
            </section>
        </div>
        </div>
    </div>
        <!------End Step 11 ------>

			<!------Start Step 12 ------>
            <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited_coord'] =='12') echo "active";?>">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseTwelve" style="width: 100%;">CHAPTER QUESTIONS</a>
                    </h4>
                </div>
                <div id="collapseTwelve" class="collapse <?php if($chFinancialReport['farthest_step_visited_coord'] =='12') echo 'show'; ?>" data-parent="#accordion">
                    <div class="card-body">
				<section>
                    <table>
                        <tbody>
                            <tr><td>1.</td>
                                <td>Did you make the Bylaws and/or manual available for any chapter members that requested them?</td></tr>
                            <tr><td></td>
                             <td><strong>{{ is_null($chFinancialReport['bylaws_available']) ? 'Not Answered' : ($chFinancialReport['bylaws_available'] == 0 ? 'NO'
                                 : ( $chFinancialReport ['bylaws_available'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport ['bylaws_available_explanation']}}</strong></td></tr>
                            <tr><td>2.</td>
                                <td>Did your chapter vote on all activities and expenditures during the fiscal year?</td></tr>
                            <tr><td></td>
                            <td><strong>{{ is_null($chFinancialReport['vote_all_activities']) ? 'Not Answered' : ($chFinancialReport['vote_all_activities'] == 0 ? 'NO'
                                : ( $chFinancialReport ['vote_all_activities'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport ['vote_all_activities_explanation']}}</strong></td></tr>
                            <tr><td>3.</td>
                                <td>Did you have any child focused outings or activities?</td></tr>
                            <tr><td></td>
                            <td><strong>{{ is_null($chFinancialReport['child_outings']) ? 'Not Answered' : ($chFinancialReport['child_outings'] == 0 ? 'NO'
                                : ( $chFinancialReport ['child_outings'] == 1 ? 'YES' : 'Not Answered')) }}&nbsp;&nbsp;  {{ $chFinancialReport ['child_outings_explanation']}}</strong></td></tr>
                            <tr><td>4.</td>
                                <td>Did you have playgroups? If so, how were they arranged.</td></tr>
                            <tr><td></td>
                            <td><strong>{{ is_null($chFinancialReport['playgroups']) ? 'Not Answered' : ($chFinancialReport['playgroups'] == 0 ? 'NO'
                                : ( $chFinancialReport ['playgroups'] == 1 ? 'YES   Arranged by Age' : (['playgroups'] == 2 ? 'YES   Multi-aged Groups' : 'Not Answered'))) }}</strong></td></tr>
                            <tr><td>5.</td>
                                <td>Did your chapter have scheduled park days? If yes, how often?</td></tr>
                            <tr><td></td>
                            <td><strong>{{ is_null($chFinancialReport['park_day_frequency']) ? 'Not Answered' : ($chFinancialReport['park_day_frequency'] == 0 ? 'NO'
                                : ( $chFinancialReport['park_day_frequency'] == 1 ? '1-3 Times' : ($chFinancialReport['park_day_frequency'] == 2 ? '4-6 Times' :
                                    ($chFinancialReport['park_day_frequency'] == 3 ? '7-9 Times' : ($chFinancialReport['park_day_frequency'] == 4 ? '10+ Times' : 'Not Answered'))))) }}</strong></td></tr>
                            <tr><td>6.</td>
                                <td>Did you have any mother focused outings or activities?</td></tr>
                            <tr><td></td>
                            <td><strong>{{ is_null($chFinancialReport['mother_outings']) ? 'Not Answered' : ($chFinancialReport['mother_outings'] == 0 ? 'NO'
                                : ( $chFinancialReport ['mother_outings'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport ['mother_outings_explanation']}}</strong></td></tr>
                            <tr><td>7.</td>
                                <td>Did your chapter have any of the following activity groups?</td></tr>
                                <tr><td></td>
                                <td><strong>
                                    @php
                                        $activityArray = json_decode($chFinancialReport['activity_array']);
                                        $activityMapping = [
                                            '0' => 'N/A',
                                            '1' => 'Cooking',
                                            '2' => 'Cost Cutting Tips',
                                            '3' => 'Mommy Playgroup',
                                            '4' => 'Babysitting Co-op',
                                            '5' => 'MOMS Night Out',
                                            '6' => 'Other',
                                        ];
                                    @endphp

                                    @if (!empty($activityArray))
                                        {{ implode(', ', array_map(function($value) use ($activityMapping) {
                                            // Check if the key exists in the mapping array before accessing it
                                            return isset($activityMapping[$value]) ? $activityMapping[$value] : 'Not Answered';
                                        }, $activityArray)) }}
                                    @else
                                        N/A
                                    @endif
                                </strong></td></tr>
                                <tr><td>8.</td>
                                    <td>Did you offer or inform your members about MOMS Club merchandise?</td></tr>
                                <tr><td></td>
                                 <td><strong>{{ is_null($chFinancialReport['offered_merch']) ? 'Not Answered' : ($chFinancialReport['offered_merch'] == 0 ? 'NO'
                                     : ( $chFinancialReport ['offered_merch'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport ['offered_merch_explanation']}}</strong></td></tr>
                                <tr><td>9.</td>
                                    <td>Did you purchase any merchandise from International other than pins?</td></tr>
                                <tr><td></td>
                                <td><strong>{{ is_null($chFinancialReport['bought_merch']) ? 'Not Answered' : ($chFinancialReport['bought_merch'] == 0 ? 'NO'
                                    : ( $chFinancialReport ['bought_merch'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{$chFinancialReport ['bought_merch_explanation']}}</strong></td></tr>
                                <tr><td>10.</td>
                                    <td>Did you purchase pins from International?</td></tr>
                                <tr><td></td>
                                <td><strong>{{ is_null($chFinancialReport['purchase_pins']) ? 'Not Answered' : ($chFinancialReport['purchase_pins'] == 0 ? 'NO'
                                    : ( $chFinancialReport ['purchase_pins'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport ['purchase_pins_explanation']}}</strong></td></tr>
                                <tr><td>11.</td>
                                    <td>Did anyone in your chapter receive any compensation or pay for their work with your chapter?</td></tr>
                                <tr><td></td>
                                    <td><strong>{{ is_null($chFinancialReport['receive_compensation']) ? 'Not Answered' : ($chFinancialReport['receive_compensation'] == 0 ? 'NO'
                                    : ( $chFinancialReport ['receive_compensation'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport ['receive_compensation_explanation']}}</strong></td></tr>
                               <tr><td>12.</td>
                               <td>Did any officer, member or family of a member benefit financially in any way from the member's position with your chapter?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($chFinancialReport['financial_benefit']) ? 'Not Answered' : ($chFinancialReport['financial_benefit'] == 0 ? 'NO'
                                : ( $chFinancialReport ['financial_benefit'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport ['financial_benefit_explanation']}}</strong></td></tr>
                          <tr><td>13.</td>
                               <td>Did your chapter attempt to influence any national, state/provincial, or local legislation, or support any other organization that did?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($chFinancialReport['influence_political']) ? 'Not Answered' : ($chFinancialReport['influence_political'] == 0 ? 'NO'
                                : ( $chFinancialReport ['influence_political'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $chFinancialReport ['influence_political_explanation']}}</strong></td></tr>
                            <tr><td>14.</td>
                            <td>Did your chapter sister another chapter?</td></tr>
                        <tr><td></td>
                            <td><strong>{{ is_null($chFinancialReport['sister_chapter']) ? 'Not Answered' : ($chFinancialReport['sister_chapter'] == 0 ? 'NO'
                                : ( $chFinancialReport ['sister_chapter'] == 1 ? 'YES' : 'Not Answered' )) }}</strong></td></tr>
                          </tbody>
                   </table>
                   <hr style="border-bottom: 2px solid #007bff">
                   <!-- start:report_review -->
				<div  class="form-row report_review">
					<div class="card-header col-md-12">
                        <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                    </div>
                    <div class="card-body form-row">
                        <div class="col-12">

                            <div class="col-12">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <label>Did they purchase or have leftover pins?:<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkPurchasedPins" value="1" {{ $chFinancialReport['check_purchased_pins'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkPurchasedPins" value="0" {{ $chFinancialReport['check_purchased_pins'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label>Did they purchase MOMS Club merchandise?:<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkPurchasedMCMerch" value="1" {{ $chFinancialReport['check_purchased_mc_merch'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkPurchasedMCMerch" value="0" {{ $chFinancialReport['check_purchased_mc_merch'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label>Did they offer MOMS Club merchandise or info on how to buy to members?:<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkOfferedMerch" value="1" {{ $chFinancialReport['check_offered_merch'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkOfferedMerch" value="0" {{ $chFinancialReport['check_offered_merch'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label>Did they make the Manual/By-Laws available to members?:<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkBylawsMadeAvailable" value="1" {{ $chFinancialReport['check_bylaws_available'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkBylawsMadeAvailable" value="0" {{ $chFinancialReport['check_bylaws_available'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label>Did they Sistered another chapter?:<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkSisteredAnotherChapter" value="1" {{ $chFinancialReport['check_sistered_another_chapter'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkSisteredAnotherChapter" value="0" {{ $chFinancialReport['check_sistered_another_chapter'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="Step12_Note">Add New Note:</label>
                                            <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(12)" name="Step12_Note" id="Step12_Note" <?php if ($chFinancialReport['review_complete']!="") echo "readonly"?>></textarea>
                                            <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
                                            <button type="button" id="AddNote12" class="btn btn-sm bg-gradient-success" onclick="AddNote(12)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

							<div class="col-12">
								<label for="Step12_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
							</div>
							<div class="col-12">
								<textarea class="form-control" style="width:100%" rows="8" name="Step12_Log" id="Step12_Log" readonly><?php echo $chFinancialReport['step_12_notes_log']; ?></textarea>
							</div>
                            <div class="col-12"><br></div>

				<!-- end:report_review -->
                <div class="col-12 text-center">
						<button type="submit" id="btn-step-12" class="btn bg-gradient-primary" ><i class="fas fa-save mr-2"></i>Save Report Review</button>
					</div>
                </div>
            </div>
			  </section>
		  </div>
		  </div>
        </div>
			<!------End Step 12 ------>
    </div>
                 <!-- end of accordion -->
    </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
            </div>

            <div class="card-body text-center">
                @if ($chDocuments->financial_report_received)
                    <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $chDocuments['financial_pdf_path']; ?>" class="btn bg-gradient-primary mb-2" ><i class="fas fa-download mr-2"></i>Download PDF Report</a>
                @else
                    <a id="downloadPdfLink" href="#" class="btn bg-gradient-primary mb-2 disabled">Download PDF Report</a>
                @endif
                <br>
                <button type="button" id="back-list" class="btn bg-gradient-primary mb-2" onclick="window.location.href='{{ route('eoyreports.eoyfinancialreport') }}'"><i class="fas fa-reply mr-2"></i>Back to Financial Report List</button>
                <button type="button" id="back-details" class="btn bg-gradient-primary mb-2" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to EOY Details</button>
            </div>
        </form>
    </div>
</div>

</section>

@endsection
@section('customscript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('unsubmit').addEventListener('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "Unsubmitting this report will make it editable by the chapter again and will disable coordinator editing until the chapter has resubmitted - any unsaved changes will be lost.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Unsubmit',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('/eoy/unsubmit/' . $chDetails->id) }}";
            }
        });
    });

    document.getElementById('review-clear').addEventListener('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will clear the 'review complete' flag and coordinators will be able to edit the report again. Do you wish to continue?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Clear Review',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('/eoy/clearreview/' . $chDetails->id) }}";
            }
        });
    });
});

function showRosterUploadModal() {
    var chapter_id = "{{ $chDetails->id }}";

    Swal.fire({
        title: 'Upload Chapter Roster',
        html: `
            <form id="uploadRosterForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadRosterForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeRoster', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Roster uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

function showStatement1UploadModal() {
    var chapter_id = "{{ $chDetails->id }}";

    Swal.fire({
        title: 'Upload Statement',
        html: `
            <form id="uploadStatement1Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadStatement1Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeStatement1', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Statement uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

function showStatement2UploadModal() {
    var chapter_id = "{{ $chDetails->id }}";

    Swal.fire({
        title: 'Upload Additional Statement',
        html: `
            <form id="uploadStatement2Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadStatement2Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeStatement2', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Additional Statement uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

function show990NUploadModal() {
    var chapter_id = "{{ $chDetails->id }}";

    Swal.fire({
        title: 'Upload 990N',
        html: `
            <form id="upload990NForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('upload990NForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/store990n', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: '990N uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

    $("#review-complete").click(function() {
        if (!CheckMembers()) {
        return false;
        }
        if (!CheckService()) {
            return false;
        }
        if (!CheckParties()) {
            return false;
        }
        if (!CheckFinancial()) {
            return false;
        }
        if (!CheckReconciliation()) {
            return false;
        }
        if (!CheckQuestions()) {
            return false;
        }
        var post_balance = $('#post_balance').val();
		if(post_balance == null || post_balance == ''){
			customWarningAlert('Please enter Ending Balance in the Bank Reconciliation Section');
            accordion.openAccordionItem('accordion-header-reconciliation');
			$('#post_balance').focus();
			return false;
		}
		var result=confirm("This will finalize this report and flag it as 'review complete'.  Do you wish to continue?");
		if(result){
            $("#submit_type").val('review_complete');
            $("#FurthestStep").val('14');
            $("#financial_report").submit();
		 } else {
            $(this).prop('disabled', false);
        }
	});

    document.getElementById('AssignedReviewer').addEventListener('change', function() {
        var emailMessageGroup = document.getElementById('emailMessageGroup');
        if (this.value != '') {
            emailMessageGroup.style.display = 'block';
        } else {
            emailMessageGroup.style.display = 'none';
        }
    });

    $(document).ready(function() {
    function submitFormWithStep(step) {
        $("#FurthestStep").val(step);
        $("#financial_report").submit();
    }

    $("#btn-step-1").click(function() {
        if (!CheckMembers()) return false;
        submitFormWithStep(1);
    });
    $("#btn-step-2").click(function() {
        submitFormWithStep(2);
    });
    $("#btn-step-3").click(function() {
        if (!CheckService()) return false;
        submitFormWithStep(3);
    });
    $("#btn-step-4").click(function() {
        if (!CheckParties()) return false;
        submitFormWithStep(4);
    });
    $("#btn-step-5").click(function() {
        submitFormWithStep(5);
    });
    $("#btn-step-6").click(function() {
        submitFormWithStep(6);
    });
    $("#btn-step-7").click(function() {
        submitFormWithStep(7);
    });
    $("#btn-step-8").click(function() {
        submitFormWithStep(8);
    });
    $("#btn-step-9").click(function() {
        if (!CheckFinancial()) return false;
        submitFormWithStep(9);
    });
    $("#btn-step-10").click(function() {
        var post_balance = $('#post_balance').val();
        if (post_balance == null || post_balance == '') {
            customWarningAlert('Please enter Ending Balance');
            $('#post_balance').focus();
            return false;
        } if (!CheckReconciliation()) {
            return false;
        }
        submitFormWithStep(10);
    });
    $("#btn-step-11").click(function() {
        submitFormWithStep(11);
    });
    $("#btn-step-12").click(function() {
        if (!CheckQuestions()) return false;
        submitFormWithStep(12);
    });
    $("#btn-step-13").click(function() {
        submitFormWithStep(13);
    });
    $("#btn-step-14").click(function() {
        var assignedReviewer = $('#AssignedReviewer').val();
        if (assignedReviewer == null || assignedReviewer == '') {
            customWarningAlert('Please select a Reviewer');
            $('#AssignedReviewer').focus();
            return false;
        }
        submitFormWithStep(14);
    });
});

</script>
<script>

    /* Disable fields and buttons  */
    $(document).ready(function () {
        var submitted = @json($chDocuments->financial_review_complete);
        var received =  @json($chDocuments->financial_report_received);

        if (received != '1') {
            $('button').not('#btn-back').prop('disabled', true);
            $('input, select, textarea').not('#logout-form input, #logout-form select, #logout-form textarea').prop('disabled', true);
        } else if (submitted == '1') {
            $('button').not('#btn-back, #btn-download-pdf, #review-clear').prop('disabled', true);
            $('input, select, textarea').not('#logout-form input, #logout-form select, #logout-form textarea').prop('disabled', true);
        } else {
            $('button, input, select, textarea').prop('disabled', false);
        }
        var allDisabled = true;
        $('input, select, textarea').not('#logout-form input, #logout-form select, #logout-form textarea').each(function() {
            if (!$(this).prop('disabled')) {
                allDisabled = false;
                return false;
            }
        });

        if (allDisabled) {
            $('.description').show();
        } else {
            $('.description').hide();
        }
    });

    function EnableNoteLogButton(NoteNumber){
        if(document.getElementById("Step" + NoteNumber + "_Note").value!="")
            document.getElementById("AddNote" + NoteNumber).disabled = false;
        else
            document.getElementById("AddNote" + NoteNumber).disabled = true;
    }

    function AddNote(NoteNumber){
        var Note = "";
        var Log = "";
        var d = new Date();
        var now = "";
        var SummaryNote="";

        now = d.toString();

        <?php $date = date('m/d/Y'); ?>

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
        console.log(Note);
        Log += "\n" + noteTextValue + " Section, <?php echo $date; ?>, <?php echo $loggedInName; ?>, " + Note;

        document.getElementById("Step" + NoteNumber + "_Log").value += Log;
        document.getElementById("Step" + NoteNumber + "_Note").value = "";
        document.getElementById("AddNote" + NoteNumber).disabled = true;

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
