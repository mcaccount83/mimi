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
            <form id="financial_report" name="financial_report" role="form" data-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("eoyreports.eoyupdatefinancialreport", $chapterid) }}' novalidate>
                @csrf
                <input type="hidden" name="submitted" id="submitted" value="{{ $submitted }}" />
                <input type="hidden" name="FurthestStep" id="FurthestStep" value="<?php if($financial_report_array['farthest_step_visited_coord'] != null) echo $financial_report_array['farthest_step_visited_coord']; else echo '14'; ?>" />
                <input type="hidden" name="submit_type" id="submit_type" value="" />

          <!-- Profile Image -->
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chapterDetails[0]->chapter_name }}, {{$chapterDetails[0]->state}}</h3>

          {{-- <p><?php if(!$submitted) echo "<br><font color=\"red\">REPORT NOT YET SUBMITTED FOR REVIEW</font>"; ?></p>
          <?php if ($submitted): ?>
          <p>
              <?php $selectedReviewer = null; ?>
              <?php foreach ($reviewerList as $pcl): ?>
                  <?php if ($financial_report_array['reviewer_id'] == $pcl['cid']): ?>
                      <?php $selectedReviewer = $pcl['cname']; ?>
                  <?php endif; ?>
              <?php endforeach; ?>

              <?php if ($selectedReviewer !== null): ?>
                  <label for="AssignedReviewer" style="display: inline; font-weight: normal;">Assigned Reviewer: <?= $selectedReviewer ?></label>
              <?php else: ?>
                  <span style="display: inline; color: red;">No Reviewer Assigned - Select Reviewer in Final Review section before continuing to prevent errors.</span>
              <?php endif; ?>
         </p>
      <?php endif; ?>
      <p>Have some questions about reviewing?<br>
          <a href="https://momsclub.org/reviewing-reports-faq/"   target="_blank">Check out our FAQ!</a></p> --}}


          <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
               <h5>Review Summary</h5>
            Answers from questios in previous sections will show up here after they have been saved.<br>
            <br>
            @if ($submitted)
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Financial Report PDF:</b> <span class="float-right"><a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['financial_pdf_path']; ?>">Download PDF</a></span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Chapter Roster File:</b> <span class="float-right">
                @if ($financial_report_array['roster_path'] != null)
                <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['financial_pdf_path']; ?>">Chapter Roster</a></span>
                @else
                No file attached</span>
                @endif
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Primary Bank Statement:</b> <span class="float-right">
                @if ($financial_report_array['bank_statement_included_path'] != null)
                <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['bank_statement_included_path']; ?>">Primary Statement</a></span>
                @else
                No file attached</span>
                @endif
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Additional Bank Statement:</b> <span class="float-right">
                    @if ($financial_report_array['bank_statement_2_included_path'] != null)
                    <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['bank_statement_2_included_path']; ?>">Additional Statement</a></span>
                    @else
                    No file attached</span>
                    @endif
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>990N Filing:</b> <span class="float-right">
                    @if ($financial_report_array['file_irs_path'] != null)
                    <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['file_irs_path']; ?>">990N Confirmation</a></span>
                    @else
                    No file attached</span>
                    @endif
            </div>
            @endif
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Excel roster attached and complete:</b> <span class="float-right">{{ is_null($financial_report_array['check_roster_attached']) ? 'Please Review'
                    : ($financial_report_array ['check_roster_attached'] == 0 ? 'NO' : ($financial_report_array ['check_roster_attached'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Number of members/dues/renewal "seem right":</b> <span class="float-right">{{ is_null($financial_report_array['check_renewal_seems_right']) ? 'Please Review'
                    : ($financial_report_array ['check_renewal_seems_right'] == 0 ? 'NO' : ($financial_report_array ['check_renewal_seems_right'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Minimum of one service project completed:</b> <span class="float-right">{{ is_null($financial_report_array['check_minimum_service_project']) ? 'Please Review'
                    : ( $financial_report_array ['check_minimum_service_project'] == 0 ? 'NO' : ($financial_report_array ['check_minimum_service_project'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Donation to M2M Fund:</b> <span class="float-right">{{ is_null($financial_report_array['check_m2m_donation']) ? 'Please Review'
                    : ($financial_report_array ['check_m2m_donation'] == 0 ? 'NO' : ($financial_report_array ['check_m2m_donation'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Party Percentage:</b> <span class="float-right">
                    {{-- {{ number_format($partyPercentage * 100, 2) }}% --}}
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b> Party Percentage less than 15%:</b> <span class="float-right" style="
                    @if(is_null($financial_report_array['check_party_percentage']))
                        background-color: #FFFFFF; color: #000000;
                    @elseif($financial_report_array['check_party_percentage'] == 2)
                        background-color: #28a745; color: #FFFFFF;
                    @elseif($financial_report_array['check_party_percentage'] == 1)
                        background-color: #ffc107; color: #000000;
                    @elseif($financial_report_array['check_party_percentage'] == 0)
                        background-color: #dc3545; color: #FFFFFF;
                    @else
                        background-color: #FFFFFF; color: #000000;
                    @endif
                        ">
                    @if(is_null($financial_report_array['check_party_percentage']))
                        Please Review
                    @elseif($financial_report_array['check_party_percentage'] == 0)
                        They are over 20%
                    @elseif($financial_report_array['check_party_percentage'] == 1)
                        They are between 15-20%
                    @elseif($financial_report_array['check_party_percentage'] == 2)
                        They are under 15%
                    @else
                        Please Review
                    @endif
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Total income/revenue less than $50,000:</b> <span class="float-right">{{ is_null($financial_report_array['check_total_income_less']) ? 'Please Review'
                    : ( $financial_report_array ['check_total_income_less'] == 0 ? 'NO' : ($financial_report_array ['check_total_income_less'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Current bank statement included:</b> <span class="float-right">{{ is_null($financial_report_array['check_bank_statement_included']) ? 'Please Review'
                    : ( $financial_report_array ['check_bank_statement_included'] == 0 ? 'NO' : ($financial_report_array ['check_bank_statement_included'] == 1 ? 'YES' : 'Please Review')) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Treasury & Reconciled Balances Match:</b> <span class="float-right" style="
                    @if(is_null($financial_report_array['check_bank_statement_matches']))
                        background-color: #FFFFFF; color: #000000;
                    @elseif($financial_report_array['check_bank_statement_matches'] == 1)
                        background-color: #28a745; color: #FFFFFF;
                    @elseif($financial_report_array['check_bank_statement_matches'] == 0)
                        background-color: #dc3545; color: #FFFFFF;
                    @else
                        background-color: #FFFFFF; color: #000000;
                    @endif
                        ">
                    @if(is_null($financial_report_array['check_bank_statement_matches']))
                        Please Review
                    @elseif($financial_report_array['check_bank_statement_matches'] == 1)
                        In Balance
                    @elseif($financial_report_array['check_bank_statement_matches'] == 0)
                        Out of balance
                    @else
                        Please Review
                    @endif
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Proof of 990N Filing for 7/1/<?php echo date('Y')-1 .' - 6/30/'.date('Y');?> :</b> <span class="float-right" style="
                    @if(is_null($financial_report_array['check_current_990N_included']))
                        background-color: #FFFFFF; color: #000000;
                    @elseif($financial_report_array['check_current_990N_included'] == 1)
                        background-color: #28a745; color: #FFFFFF;
                    @elseif($financial_report_array['check_current_990N_included'] == 0)
                        background-color: #dc3545; color: #FFFFFF;
                    @else
                        background-color: #FFFFFF; color: #000000;
                    @endif
                        ">
                    @if(is_null($financial_report_array['check_current_990N_included']))
                       Please Review
                    @elseif($financial_report_array['check_current_990N_included'] == 1)
                        990N is filed
                    @elseif($financial_report_array['check_current_990N_included'] == 0)
                        990N has not been filed
                    @else
                        Please Review
                    @endif
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Purchased membership pins or had leftovers:</b> <span class="float-right">{{ is_null($financial_report_array['check_purchased_pins']) ? 'Please Review'
                    : ( $financial_report_array ['check_purchased_pins'] == 0 ? 'NO' : ($financial_report_array ['check_purchased_pins'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Purchased MOMS Club merchandise:</b> <span class="float-right">{{ is_null($financial_report_array['check_purchased_mc_merch']) ? 'Please Review'
                    : ($financial_report_array ['check_purchased_mc_merch'] == 0 ? 'NO' : ($financial_report_array ['check_purchased_mc_merch'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Offered MOMS Club merchandise or info to members:</b> <span class="float-right">{{ is_null($financial_report_array['check_offered_merch']) ? 'Please Review'
                    : ( $financial_report_array ['check_offered_merch'] == 0 ? 'NO' : ( $financial_report_array ['check_offered_merch'] == 1 ? 'YES' : 'Pleae Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Manual/by-laws made available to members:</b> <span class="float-right">{{ is_null($financial_report_array['check_bylaws_available']) ? 'Please Review'
                    : ( $financial_report_array ['check_bylaws_available'] == 0 ? 'NO' : ($financial_report_array ['check_bylaws_available'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Attended International Event:</b> <span class="float-right">{{ is_null($financial_report_array['check_attended_training']) ? 'Please Review'
                    : ($financial_report_array ['check_attended_training'] == 0 ? 'NO' : ($financial_report_array ['check_attended_training'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Sistered another chapter:</b> <span class="float-right">{{ is_null($financial_report_array['check_sistered_another_chapter']) ? 'Please Review'
                    : ($financial_report_array ['check_sistered_another_chapter'] == 0 ? 'NO' : ($financial_report_array ['check_sistered_another_chapter'] == 1 ? 'YES' : 'Please Review' )) }}</span>
            </div>

            @php
                $yesBackground = '#28a745';  // Green background for "YES"
                $noBackground = '#dc3545';   // Red background for "NO"
            @endphp

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Award #1 Status:</b> <span class="float-right" style="background-color: {{ is_null($financial_report_array['check_award_1_approved']) ? '#FFFFFF' : ($financial_report_array['check_award_1_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($financial_report_array['check_award_1_approved']) ? '#000000' : '#FFFFFF' }};">
                    {{ is_null($financial_report_array['award_1_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_1_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                    : ($financial_report_array['award_1_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_1_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_1_nomination_type'] == 4 ? 'Outstanding Spirit'
                    : ($financial_report_array['award_1_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_1_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_1_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Award #2 Status:</b> <span class="float-right" style="background-color: {{ is_null($financial_report_array['check_award_2_approved']) ? '#FFFFFF' : ($financial_report_array['check_award_2_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($financial_report_array['check_award_2_approved']) ? '#000000' : '#FFFFFF' }};">
                    {{ is_null($financial_report_array['award_1_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_1_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                        : ($financial_report_array['award_1_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_1_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_1_nomination_type'] == 4 ? 'Outstanding Spirit'
                        : ($financial_report_array['award_1_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_1_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_1_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Award #3 Status:</b> <span class="float-right" style="background-color: {{ is_null($financial_report_array['check_award_3_approved']) ? '#FFFFFF' : ($financial_report_array['check_award_3_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($financial_report_array['check_award_3_approved']) ? '#000000' : '#FFFFFF' }};">
                    {{ is_null($financial_report_array['award_3_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_3_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                        : ($financial_report_array['award_3_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_3_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_3_nomination_type'] == 4 ? 'Outstanding Spirit'
                        : ($financial_report_array['award_3_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_3_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_3_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Award #4 Status:</b> <span class="float-right" style="background-color: {{ is_null($financial_report_array['check_award_4_approved']) ? '#FFFFFF' : ($financial_report_array['check_award_4_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($financial_report_array['check_award_4_approved']) ? '#000000' : '#FFFFFF' }};">
                    {{ is_null($financial_report_array['award_4_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_4_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                        : ($financial_report_array['award_4_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_4_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_4_nomination_type'] == 4 ? 'Outstanding Spirit'
                        : ($financial_report_array['award_4_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_4_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_4_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                <b>Award #5 Status:</b> <span class="float-right" style="background-color: {{ is_null($financial_report_array['check_award_5_approved']) ? '#FFFFFF' : ($financial_report_array['check_award_5_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($financial_report_array['check_award_5_approved']) ? '#000000' : '#FFFFFF' }};">
                    {{ is_null($financial_report_array['award_5_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_5_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                        : ($financial_report_array['award_5_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_5_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_5_nomination_type'] == 4 ? 'Outstanding Spirit'
                        : ($financial_report_array['award_5_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_5_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_5_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                </span>
            </div>

            </li>
            <li class="list-group-item">

               <strong>Reviewer Notes Logged for this Report (not visible to chapter):</strong><br>
                    <?php
                    $financial_report_notes = [];
                    for ($i = 1; $i <= 13; $i++) {
                        $key = 'step_' . $i . '_notes_log';
                        if (isset($financial_report_array[$key])) {
                            $notes = explode("\n", $financial_report_array[$key]);
                            $financial_report_notes = array_merge($financial_report_notes, $notes);
                        }
                    }

                    echo empty($financial_report_notes) ? 'No notes logged for this report.' : implode('<br>', $financial_report_notes);
                    ?>

            </li>
            <li class="list-group-item">

    <div class="d-flex justify-content-between w-100">
        <b>Report Completed By:</b> <span class="float-right"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_name'];}?>
    </div>
    <div class="d-flex justify-content-between w-100">
        <b>Contact Email:</b> <span class="float-right"><a href="mailto:<?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_email'];}?>"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_email'];}?></a></p>
    </div>

    <div class="d-flex align-items-center justify-content-between w-100">
        <?php if ($chapterDetails[0]->financial_report_received == 1 && $financial_report_array['reviewer_id'] == null): ?>
                <span style="display: inline; color: red;">No Reviewer Assigned - Select Reviewer before continuing to prevent errors.<br></span>
            <?php endif; ?>
            <label for="AssignedReviewer"><strong>Assigned Reviewer:</strong></label>
            <select class="form-control" name="AssignedReviewer" id="AssignedReviewer" style="width: 250px;"  required>
                <option value="" style="display:none" disabled selected>Select a reviewer</option>
                @foreach($reviewerList as $pcl)
                    <option value="{{$pcl['cid']}}" {{$financial_report_array['reviewer_id'] == $pcl['cid']  ? 'selected' : ''}} >{{$pcl['cname']}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" id="emailMessageGroup" style="display: none;">
            <label for="AssignedReviewer"><strong>Additional Email Message for Reviewer:</strong></label>
            <textarea class="form-control" style="width:100%" rows="8" name="reviewer_email_message" id="reviewer_email_message"><?php echo $financial_report_array['reviewer_email_message']; ?></textarea>
        </div>


    <div class="card-body text-center">
        <br>
        <button type="submit" id="btn-step-14" class="btn bg-gradient-primary mb-2">Save Report Review</button>
        <br>
        @if ($financial_report_array['review_complete'] != "" && $submitted)
            @if ($regionalCoordinatorCondition)
                <button type="button" class="btn bg-gradient-success" id="review-clear">Clear Review Complete</button>
            @else
                <button type="button" class="btn bg-gradient-success disabled">Clear Review Complete</button>
            @endif
        @else
            <button type="button" class="btn bg-gradient-success" id="review-complete">Mark as Review Complete</button>
        @endif
            <button type="button" class="btn bg-gradient-danger" id="unsubmit">UnSubmit Report</button>
        <br>
        <span style="color:red;"><b>"Mark as Review Complete" is for FINAL REVIEWER USE ONLY!</b></span>
    </div>
    </li>
</ul>
        {{-- <div class="card-body text-center">
        @if ($submitted)
            <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['financial_pdf_path']; ?>" class="btn bg-gradient-primary mb-2" >Download PDF Report</a>
        @else
            <a id="downloadPdfLink" href="#" class="btn bg-gradient-primary mb-2 disabled">Download PDF Report</a>
        @endif
        <br>
        <button type="button" id="back-list" class="btn bg-gradient-primary mb-2" onclick="window.location.href='{{ route('eoyreports.eoyfinancialreport') }}'">Back to Financial Report List</button>
        <button type="button" id="back-details" class="btn bg-gradient-primary mb-2" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chapterid]) }}'">Back to EOY Details</button>
    </div> --}}
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
                    @php
                        $selectedReviewer = null;
                        foreach ($reviewerList as $pcl) {
                            if ($financial_report_array['reviewer_id'] == $pcl['cid']) {
                                $selectedReviewer = $pcl['cname'];
                                break;
                            }
                        }
                    @endphp

                    @if($submitted)
                        @if ($selectedReviewer != null)
                            <label>Assigned Reviewer:</label>&nbsp;&nbsp;{{$selectedReviewer}}
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
                    <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='1') echo "active";?>">
                        <div class="card-header" id="accordion-header-members">
                            <h4 class="card-title w-100">
                                <a class="d-block" data-toggle="collapse" href="#collapseOne" style="width: 100%;">CHAPTER DUES</a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='1') echo 'show'; ?>" data-parent="#accordion">
                            <div class="card-body">
						<section>
                            Did your chapter change dues this year?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($financial_report_array['changed_dues']) ? 'Not Answered' : ($financial_report_array['changed_dues'] == 0 ? 'NO'
                                : ($financial_report_array ['changed_dues'] == 1 ? 'YES' : 'Not Answered' )) }}</strong><br>
                            Did your chapter charge different amounts for new and returning members?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($financial_report_array['different_dues']) ? 'Not Answered' : ($financial_report_array['different_dues'] == 0 ? 'NO'
                                :( $financial_report_array ['different_dues'] == 1 ? 'YES' : 'Not Answered' )) }}</strong><br>
                            Did your chapter have any members who didn't pay full dues?&nbsp;&nbsp;&nbsp;
                            <strong>{{ is_null($financial_report_array['not_all_full_dues']) ? 'Not Answered' : ($financial_report_array['not_all_full_dues'] == 0 ? 'NO'
                                : ( $financial_report_array ['not_all_full_dues'] == 1 ? 'YES' : 'Not Answered' )) }}</strong><br>
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
                                @if ($financial_report_array['changed_dues'] != 1)
                                    <div class="flex-item">
                                        New Members:&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_new_members'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] != 1)
                                        <div class="flex-item">
                                            Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['dues_per_member'], 2) }}</strong>
                                        </div>
                                    @endif
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            New Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['dues_per_member'], 2) }}</strong>
                                        </div>
                                    @endif
                                    <div class="flex-item">
                                        Renewed Members:&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_renewed_members'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] != 1)
                                        <div class="flex-item">
                                           &nbsp;&nbsp;&nbsp;
                                        </div>
                                    @endif
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            Renewal Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['dues_per_member_renewal'], 2) }}</strong>
                                        </div>
                                    @endif
                                @endif

                                @if ($financial_report_array['changed_dues'] == 1)
                                    <div class="flex-item">
                                        New Members (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_new_members'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] != 1)
                                        <div class="flex-item">
                                            Dues (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['dues_per_member'], 2) }}</strong>
                                        </div>
                                    @endif
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            New Dues (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['dues_per_member'], 2) }}</strong>
                                        </div>
                                    @endif
                                    <div class="flex-item">
                                        Renewed Members (OLD dues amount): <strong>{{ $financial_report_array['total_renewed_members'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            Renewal Dues (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['dues_per_member_renewal'], 2) }}</strong>
                                        </div>
                                    @endif

                                    <div class="flex-item">
                                        New Members (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_new_members_changed_dues'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] != 1)
                                        <div class="flex-item">
                                            Dues (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['dues_per_member_new_changed'], 2) }}</strong>
                                        </div>
                                    @endif
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            New Dues (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['dues_per_member_new_changed'], 2) }}</strong>
                                        </div>
                                    @endif
                                    <div class="flex-item">
                                        Renewed Members (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_renewed_members_changed_dues'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            Renewal Dues (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['dues_per_member_renewal_changed'], 2) }}</strong>
                                        </div>
                                    @endif
                                @endif

                                @if ($financial_report_array['not_all_full_dues'] == 1)
                                    <div class="flex-item">
                                        Members Who Paid No Dues:&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['members_who_paid_no_dues'] }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        &nbsp;&nbsp;&nbsp;
                                    </div>
                                    <div class="flex-item">
                                        Members Who Paid Partial Dues:&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['members_who_paid_partial_dues'] }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        Partial Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['total_partial_fees_collected'], 2) }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        Assiciate Members:&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_associate_members'] }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        Associate Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['associate_member_fee'], 2) }}</strong>
                                    </div>
                                @endif
                            </div>

                            <?php
                                $newMembers = $financial_report_array['total_new_members'] * $financial_report_array['dues_per_member'];
                                $renewalMembers = $financial_report_array['total_renewed_members'] * $financial_report_array['dues_per_member'];
                                $renewalMembersDiff = $financial_report_array['total_renewed_members'] * $financial_report_array['dues_per_member_renewal'];
                                $newMembersNew = $financial_report_array['total_new_members_changed_dues'] * $financial_report_array['dues_per_member_new_changed'];
                                $renewMembersNew = $financial_report_array['total_renewed_members_changed_dues'] * $financial_report_array['dues_per_member_new_changed'];
                                $renewMembersNewDiff = $financial_report_array['total_renewed_members_changed_dues'] * $financial_report_array['dues_per_member_renewal_changed'];
                                $partialMembers = $financial_report_array['members_who_paid_partial_dues'] * $financial_report_array['total_partial_fees_collected'];
                                $associateMembers = $financial_report_array['total_associate_members'] * $financial_report_array['associate_member_fee'];

                                $totalMembers = $financial_report_array['total_new_members'] +$financial_report_array['total_renewed_members'] + $financial_report_array['total_new_members_changed_dues'] + $financial_report_array['total_renewed_members_changed_dues']
                                        + $financial_report_array['members_who_paid_partial_dues'] + $financial_report_array['total_associate_members']+ $financial_report_array['members_who_paid_no_dues'];

                                if ($financial_report_array['different_dues'] == 1 && $financial_report_array['changed_dues'] == 1) {
                                    $totalDues = $newMembers + $renewalMembersDiff + $newMembersNew + $renewMembersNewDiff + $partialMembers + $associateMembers;
                                } elseif ($financial_report_array['different_dues'] == 1) {
                                    $totalDues = $newMembers + $renewalMembersDiff + $partialMembers + $associateMembers;
                                } elseif ($financial_report_array['changed_dues'] == 1) {
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
                                @if (!is_null($financial_report_array['roster_path']))
                                        <div class="col-12">
                                            <label>Chapter Roster Uploaded:</label><a href="https://drive.google.com/uc?export=download&id={{ $financial_report_array['roster_path'] }}">&nbsp; View Chapter Roster</a><br>
                                        </div>
                                        <div class="col-12" id="RosterBlock">
                                            <strong style="color:red">Please Note</strong><br>
                                                This will refresh the screen - be sure to save all work before clicking button to Replace Roster File.<br>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Roster File</button>
                                            {{-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-roster" ><i class="fas fa-undo" ></i>&nbsp; Replace Roster File</button> --}}
                                    </div>
                                @else
                                    <div class="col-12" id="RosterBlock">
                                            <strong style="color:red">Please Note</strong><br>
                                                This will refresh the screen - be sure to save all work before clicking button to Upload Roster File.<br>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Roster File</button>
                                            {{-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-roster" ><i class="fas fa-upload" ></i>&nbsp; Upload Roster File</button> --}}
                                    </div>
                                @endif
                                <input type="hidden" name="RosterPath" id="RosterPath" value="<?php echo $financial_report_array['roster_path']; ?>">
                                <div class="clearfix"></div>
                                <div class="col-12"><br></div>
                                <div class="col-12">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <label>Excel roster attached and complete:<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkRosterAttached" value="1" {{ $financial_report_array['check_roster_attached'] === 1 ? 'checked' : '' }} required>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkRosterAttached" value="0" {{ $financial_report_array['check_roster_attached'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label>Number of members listed, dues received, and renewal paid "seem right":<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkRenewalSeemsRight" value="1" {{ $financial_report_array['check_renewal_seems_right'] === 1 ? 'checked' : '' }} required>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkRenewalSeemsRight" value="0" {{ $financial_report_array['check_renewal_seems_right'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
										<label for="Step1_Note">Add New Note:</label>
										<textarea class="form-control" style="width:100%" rows="3" name="Step1_Note" id="Step1_Note" oninput="EnableNoteLogButton(1)" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
										<textarea class="form-control" rows="8" name="Step1_Log" id="Step1_Log" readonly style="width:100%"><?php echo $financial_report_array['step_1_notes_log']; ?></textarea>
									</div>
                                    <div class="col-12"><br></div>

                            <!-- end:report_review -->
                            <div class="col-12 text-center">
								  <button type="submit" id="btn-step-1" class="btn bg-gradient-primary" >Save Report Review</button>
							</div>
                        </div>
                    </div>
						</section>
					</div>
				</div>
            </div>
				<!------End Step 1 ------>

				<!------Start Step 2 ------>
                <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='2') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseTwo" style="width: 100%;">MONTHLY MEETING EXPENSES</a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='2') echo 'show'; ?>" data-parent="#accordion" data-parent="#accordion">
                        <div class="card-body">
						<section>
                            Meeting Room Fees:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['manditory_meeting_fees_paid'], 2) }}</strong><br>
                            Voluntary Donations Paid:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['voluntary_donations_paid'], 2) }}</strong><br>
                            <strong>Total Meeting Room Expenses:&nbsp;&nbsp;&nbsp;
                                {{ '$'.number_format($financial_report_array['manditory_meeting_fees_paid'] + $financial_report_array['voluntary_donations_paid']) }}</strong><br>
                            <br>
                            Paid Babysitter Expense:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['paid_baby_sitters'], 2) }}</strong><br>
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

                                        if (isset($financial_report_array['childrens_room_expenses']) && $financial_report_array['childrens_room_expenses'] !== null) {
                                            $blobData = base64_decode($financial_report_array['childrens_room_expenses']);
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
                            <strong>Total Children's Room Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($financial_report_array['paid_baby_sitters'] + $totalChildrensRoomExpenses, 2) }}</strong><br>
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
                                                    <textarea class="form-control" style="width:100%" rows="3" name="Step2_Note" id="Step2_Note" oninput="EnableNoteLogButton(2)" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
                                                <textarea class="form-control" rows="8" name="Step2_Log" id="Step2_Log" readonly style="width:100%"><?php echo $financial_report_array['step_2_notes_log']; ?></textarea>
                                            </div>
                                            <div class="col-12"><br></div>

								<!-- end:report_review -->

                                <div class="col-12 text-center">
									<button type="submit" id="btn-step-2" class="btn bg-gradient-primary" >Save Report Review</button>
                                </div>
                            </div>
                        </div>
						</section>
					</div>
				</div>
            </div>
				<!------End Step 2 ------>

				<!------Start Step 3 ------>
                <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='3') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseThree" style="width: 100%;">SERVICE PROJECTS</a>
                        </h4>
                    </div>
                    <div id="collapseThree" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='3') echo 'show'; ?>" data-parent="#accordion">
                        <div class="card-body">
					<section>
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

if (isset($financial_report_array['service_project_array'])) {
    $blobData = base64_decode($financial_report_array['service_project_array']);
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
                                                    <input class="form-check-input" type="radio" name="checkServiceProject" value="1" {{ $financial_report_array['check_minimum_service_project'] === 1 ? 'checked' : '' }} required>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkServiceProject" value="0" {{ $financial_report_array['check_minimum_service_project'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label>Made a donation to the M2M Fund:<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkM2MDonation" value="1" {{ $financial_report_array['check_m2m_donation'] === 1 ? 'checked' : '' }} required>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkM2MDonation" value="0" {{ $financial_report_array['check_m2m_donation'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>

                                    <div class="col-12">
                                        <div class="form-group row">
										<label for="Step3_Note">Add New Note:</label>
									    <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(3)" name="Step3_Note" id="Step3_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
                                        <textarea class="form-control" style="width:100%" rows="8" name="Step3_Log" id="Step3_Log" readonly><?php echo $financial_report_array['step_3_notes_log']; ?></textarea>
									</div>
                               <div class="col-12"><br></div>

						<!-- end:report_review -->
                        <div class="col-12 text-center">
						  <button type="submit" id="btn-step-3" class="btn bg-gradient-primary" >Save Report Review</button>
					    </div>
                    </div>
                </div>
				    </section>
				</div>
				</div>
            </div>
				<!------End Step 3 ------>

				<!------Start Step 4 ------>
                <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='4') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseFour" style="width: 100%;">PARTIES & MEMBER BENEFITS</a>
                        </h4>
                    </div>
                    <div id="collapseFour" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='4') echo 'show'; ?>" data-parent="#accordion">
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

                            if (isset($financial_report_array['party_expense_array'])) {
                                $blobData = base64_decode($financial_report_array['party_expense_array']);
                                $party_expenses = unserialize($blobData);

                                if ($party_expenses === false) {
                                    echo "Error: Failed to unserialize data.";
                                } else {
                                    foreach ($party_expenses as $row) {
                                        // Sanitize inputs
                                        $income = is_numeric(str_replace(',', '', $row['party_expense_income'])) ? floatval(str_replace(',', '', $row['party_expense_income'])) : 0;
                                        $expense = is_numeric(str_replace(',', '', $row['party_expense_expenses'])) ? floatval(str_replace(',', '', $row['party_expense_expenses'])) : 0;


                                        // $income = is_numeric($row['party_expense_income']) ? floatval($row['party_expense_income']) : 0;
                                        // $expense = is_numeric($row['party_expense_expenses']) ? floatval($row['party_expense_expenses']) : 0;

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
                                                <input class="form-check-input" type="radio" name="check_party_percentage" value="2" {{ $financial_report_array['check_party_percentage'] === 2 ? 'checked' : '' }} required>
                                                <label class="form-check-label">They are under 15%</label>
                                            </div>
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" name="check_party_percentage" value="1" {{ $financial_report_array['check_party_percentage'] === 1 ? 'checked' : '' }}>
                                                <label class="form-check-label">They are between 15-20%</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="check_party_percentage" value="0" {{ $financial_report_array['check_party_percentage'] === 0 ? 'checked' : '' }}>
                                                <label class="form-check-label">They are over 20%</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group row">
                                        <label for="Step4_Note">Add New Note:</label>
									<textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(4)" name="Step4_Note" id="Step4_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
									<textarea class="form-control" style="width:100%" rows="8" name="Step4_Log" id="Step4_Log" readonly><?php echo $financial_report_array['step_4_notes_log']; ?></textarea>
								</div>
                                <div class="col-12"><br></div>

					<!-- end:report_review -->
                    <div class="col-12 text-center">
                        <button type="submit" id="btn-step-4" class="btn bg-gradient-primary" <?php if($financial_report_array['review_complete'] != "" || !$submitted) echo "disabled"; ?>>Save Report Review</button>
                    </div>
                </div>
            </div>
				</section>
				</div>
				</div>
            </div>
				<!------End Step 4 ------>

				<!------Start Step 5 ------>
                <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='5') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseFive" style="width: 100%;">OFFICE & OPERATING EXPENSES</a>
                        </h4>
                    </div>
                    <div id="collapseFive" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='5') echo 'show'; ?>" data-parent="#accordion">
                        <div class="card-body">
                <section>
                    Printing Costs:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['office_printing_costs'], 2) }}</strong><br>
                    Postage Costs:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['office_postage_costs'], 2) }}</strong><br>
                    Membership Pins:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array['office_membership_pins_cost'], 2) }}</strong><br>
                <br>
                Other Office/Operating Expenses:
                    <table width="50%" >
                        <tbody>
                            <?php
                            $other_office_expenses = null;
                            $totalOfficeExpense = 0;

                            if (isset($financial_report_array['office_other_expenses']) && $financial_report_array['office_other_expenses'] !== null) {
                                $blobData = base64_decode($financial_report_array['office_other_expenses']);
                                $other_office_expenses = unserialize($blobData);

                                if ($other_office_expenses === false) {
                                    echo "Error: Failed to unserialize data.";
                                } else {
                                    if (is_array($other_office_expenses) && count($other_office_expenses) > 0) {
                                        foreach ($other_office_expenses as $row) {
                                            // Sanitize inputs
                                            $expense = is_numeric(str_replace(',', '', $row['office_other_expense'])) ? floatval(str_replace(',', '', $row['office_other_expense'])) : 0;

                                            // $expense = is_numeric($row['office_other_expense']) ? floatval($row['office_other_expense']) : 0;

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
                    <strong>Total Office/Operating Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($financial_report_array['office_printing_costs'] + $financial_report_array['office_postage_costs'] +
                            $financial_report_array['office_membership_pins_cost'] + $totalOfficeExpense, 2) }}</strong><br>
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
								<textarea class="form-control" rows="3" style="width:100%" oninput="EnableNoteLogButton(5)" name="Step5_Note" id="Step5_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
								<textarea class="form-control" style="width:100%" rows="8" name="Step5_Log" id="Step5_Log" readonly><?php echo $financial_report_array['step_5_notes_log']; ?></textarea>
							</div>
                            <div class="col-12"><br></div>

                    <!-- end:report_review -->
                    <div class="col-12 text-center">
                        <button type="submit" id="btn-step-5" class="btn bg-gradient-primary" >Save Report Review</button>
                    </div>
                </div>
            </div>
                </section>
			</div>
			</div>
        </div>
			<!------End Step 5 ------>

            <!------Start Step 6 ------>
            <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='6') echo "active";?>">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseSix" style="width: 100%;">INTERNATIONAL EVENTS & RE-REGISTRATION</a>
                    </h4>
                </div>
                <div id="collapseSix" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='6') echo 'show'; ?>" data-parent="#accordion">
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
                                $international_event_array = null;
                                $totalEventIncome = 0;
                                $totalEventExpense = 0;

                                if (isset($financial_report_array['international_event_array']) && $financial_report_array['international_event_array'] !== null) {
                                    $blobData = base64_decode($financial_report_array['international_event_array']);
                                    $international_event_array = unserialize($blobData);

                                    if ($international_event_array === false) {
                                        echo "Error: Failed to unserialize data.";
                                    } else {
                                        if (is_array($international_event_array) && count($international_event_array) > 0) {
                                            foreach ($international_event_array as $row) {
                                                // Sanitize and validate inputs
                                                $income = is_numeric(str_replace(',', '', $row['intl_event_income'])) ? floatval(str_replace(',', '', $row['intl_event_income'])) : 0;
                                                $expense = is_numeric(str_replace(',', '', $row['intl_event_expenses'])) ? floatval(str_replace(',', '', $row['intl_event_expenses'])) : 0;

                                                // $income = is_numeric($row['intl_event_income']) ? floatval($row['intl_event_income']) : 0;
                                                // $expense = is_numeric($row['intl_event_expenses']) ? floatval($row['intl_event_expenses']) : 0;

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
                        <br>
                        <strong>Chapter Re-Registration:&nbsp;&nbsp;&nbsp;{{ '$'.number_format($financial_report_array['annual_registration_fee'], 2) }}</strong><br>
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
                                    <label for="Step6_Note">Add New Note:</label>
                                    <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(6)" name="Step6_Note" id="Step6_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
						<textarea class="form-control" style="width:100%" rows="8" name="Step6_Log" id="Step6_Log" readonly><?php echo $financial_report_array['step_6_notes_log']; ?></textarea>
					</div>
                    <div class="col-12"><br></div>

                    <!-- end:report_review -->
                    <div class="col-12 text-center">
                          <button type="submit" id="btn-step-6" class="btn bg-gradient-primary" >Save Report Review</button>
                    </div>
                </div>
            </div>
                    </section>
                </div>
                </div>
            </div>
                <!------End Step 6 ------>

			<!------Start Step 7 ------>
            <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='7') echo "active";?>">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseSeven" style="width: 100%;">DONATIONS TO YOUR CHAPTER</a>
                    </h4>
                </div>
                <div id="collapseSeven" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='7') echo 'show'; ?>" data-parent="#accordion">
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

                            if (isset($financial_report_array['monetary_donations_to_chapter']) && $financial_report_array['monetary_donations_to_chapter'] !== null) {
                                $blobData = base64_decode($financial_report_array['monetary_donations_to_chapter']);
                                $monetary_donations_to_chapter = unserialize($blobData);

                                if ($monetary_donations_to_chapter === false) {
                                    echo "Error: Failed to unserialize data.";
                                } else {
                                    if (is_array($monetary_donations_to_chapter) && count($monetary_donations_to_chapter) > 0) {
                                        foreach ($monetary_donations_to_chapter as $row) {
                                            // Sanitize and validate inputs
                                            $donationDate = $row['mon_donation_date'] ? date('m/d/Y', strtotime($row['mon_donation_date'])) : '';

                                            $donationAmount = is_numeric(str_replace(',', '', $row['mon_donation_amount'])) ? floatval(str_replace(',', '', $row['mon_donation_amount'])) : 0;

                                            // $donationAmount = is_numeric($row['mon_donation_amount']) ? floatval($row['mon_donation_amount']) : 0;

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

                                if (isset($financial_report_array['non_monetary_donations_to_chapter']) && $financial_report_array['non_monetary_donations_to_chapter'] !== null) {
                                    $blobData = base64_decode($financial_report_array['non_monetary_donations_to_chapter']);
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
										<textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(7)" name="Step7_Note" id="Step7_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
										<textarea class="form-control" style="width:100%" rows="8" name="Step7_Log" id="Step7_Log" readonly><?php echo $financial_report_array['step_7_notes_log']; ?></textarea>
									</div>
                                    <div class="col-12"><br></div>

						<!-- end:report_review -->
                        <div class="col-12 text-center">
							  <button type="submit" id="btn-step-7" class="btn bg-gradient-primary" >Save Report Review</button>
                        <div>
                        </div>
                    </div>
                    </section>
				</div>
				</div>
            </div>
				<!------End Step 7 ------>

				<!------Start Step 8 ------>
                <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='8') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseEight" style="width: 100%;">OTHER INCOME & EXPENSES</a>
                        </h4>
                    </div>
                    <div id="collapseEight" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='8') echo 'show'; ?>" data-parent="#accordion">
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

                                if (isset($financial_report_array['other_income_and_expenses_array'])) {
                                    $blobData = base64_decode($financial_report_array['other_income_and_expenses_array']);
                                    $other_income_and_expenses_array = unserialize($blobData);

                                    if ($other_income_and_expenses_array === false) {
                                        echo "Error: Failed to unserialize data.";
                                    } else {
                                        if (is_array($other_income_and_expenses_array) && count($other_income_and_expenses_array) > 0) {
                                            foreach ($other_income_and_expenses_array as $row) {
                                                // Sanitize and validate inputs
                                                $otherIncome = is_numeric(str_replace(',', '', $row['other_income'])) ? floatval(str_replace(',', '', $row['other_income'])) : 0;
                                                $otherExpenses = is_numeric(str_replace(',', '', $row['other_expenses'])) ? floatval(str_replace(',', '', $row['other_expenses'])) : 0;

                                                // $otherIncome = is_numeric($row['other_income']) ? floatval($row['other_income']) : 0;
                                                // $otherExpenses = is_numeric($row['other_expenses']) ? floatval($row['other_expenses']) : 0;

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
									<textarea class="form-control" style="width:100%" rows="3" oninput="EnableNoteLogButton(8)"  name="Step8_Note" id="Step8_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
									<textarea class="form-control" style="width:100%" rows="8" name="Step8_Log" id="Step8_Log" readonly><?php echo $financial_report_array['step_8_notes_log']; ?></textarea>
								</div>
                                <div class="col-12"><br></div>

					<!-- end:report_review -->
                    <div class="col-12 text-center">
							  <button type="submit" id="btn-step-8" class="btn bg-gradient-primary" >Save Report Review</button>
					</div>
                </div>
            </div>
				</section>
			  </div>
			  </div>
            </div>
				<!------End Step 8 ------>

                <!------Start Step 9 ------>
                <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='9') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseNine" style="width: 100%;">FINANCIAL SUMMARY</a>
                        </h4>
                    </div>
                    <div id="collapseNine" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='9') echo 'show'; ?>" data-parent="#accordion">
                        <div class="card-body">
				<section>
                    <?php
                        $totalIncome = $totalDues + $totalServiceIncome + $totalPartyIncome + $totalDonationAmount + $totalEventIncome + $totalOtherIncome;
                        $totalExpenses = $financial_report_array['manditory_meeting_fees_paid'] + $financial_report_array['voluntary_donations_paid'] + $financial_report_array['paid_baby_sitters'] + $totalChildrensRoomExpenses + $totalServiceProjectExpenses
                                + $totalPartyExpense + $financial_report_array['office_printing_costs'] + $financial_report_array['office_postage_costs'] +
                                $financial_report_array['office_membership_pins_cost'] + $totalOfficeExpense + $financial_report_array['annual_registration_fee'] + $totalEventExpense + $totalOtherExpenses;
                        $treasuryBalance = $financial_report_array ['amount_reserved_from_previous_year'] + $totalIncome - $totalExpenses
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
                            <td style="border-top: 1px solid #333;">{{ '$'.number_format($financial_report_array['manditory_meeting_fees_paid'] + $financial_report_array['voluntary_donations_paid'], 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Children's Room Expenses:</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Supplies</td>
                            <td>{{ '$'.number_format($totalChildrenSupplies, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paid Sitters</td>
                            <td>{{ '$'.number_format($financial_report_array['paid_baby_sitters'], 2)  }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other</td>
                            <td>{{ '$'.number_format($totalChildrenOther, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Children's Room Expense Total</td>
                            <td>{{ '$'.number_format($financial_report_array['paid_baby_sitters'] + $totalChildrensRoomExpenses, 2) }}</td></tr>
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
                            <td>{{ '$'.number_format($financial_report_array['office_printing_costs'], 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Postage</td>
                            <td>{{ '$'.number_format($financial_report_array['office_postage_costs'], 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Membership Pins</td>
                            <td>{{ '$'.number_format($financial_report_array['office_membership_pins_cost'], 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other</td>
                            <td>{{ '$'.number_format($totalOfficeExpense, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Office/Operating Expense Total</td>
                            <td>{{ '$'.number_format($financial_report_array['office_printing_costs'] + $financial_report_array['office_postage_costs'] +
                                $financial_report_array['office_membership_pins_cost'] + $totalOfficeExpense, 2) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Annual Chapter Re-registration Fee</td>
                            <td>{{ '$'.number_format($financial_report_array['annual_registration_fee'], 2)  }}</td></tr>
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
                                    <input class="form-check-input" type="radio" name="checkTotalIncome" value="1" {{ $financial_report_array['check_total_income_less'] === 1 ? 'checked' : '' }}>
                                    <label class="form-check-label">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="checkTotalIncome" value="0" {{ $financial_report_array['check_total_income_less'] === 0 ? 'checked' : '' }}>
                                    <label class="form-check-label">No</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group row">
                            <label for="Step9_Note">Add New Note:</label>
                            <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(9)" name="Step9_Note" id="Step9_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
						<textarea class="form-control" style="width:100%" rows="8" name="Step9_Log" id="Step9_Log" readonly><?php echo $financial_report_array['step_9_notes_log']; ?></textarea>
					</div>
                                            <div class="col-12"><br></div>
		    <!-- end:report_review -->
                    <div class="col-12 text-center">
                        <button type="submit" id="btn-step-9" class="btn bg-gradient-primary" >Save Report Review</button>
                        </div>
                    </div>
                </div>
				</section>
			</div>
			</div>
        </div>
			<!------End Step 9 ------>

                <!------Start Step 10 ------>
                <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='10') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseTen" style="width: 100%;">BANK RECONCILIATION</a>
                        </h4>
                    </div>
                    <div id="collapseTen" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='10') echo 'show'; ?>" data-parent="#accordion">
                        <div class="card-body">
					<section>
                        {{-- <?php
                            $totalIncome = $totalDues + $totalServiceIncome + $totalPartyIncome + $totalDonationAmount + $totalEventIncome + $totalOtherIncome;
                            $totalExpenses = $financial_report_array['manditory_meeting_fees_paid'] + $financial_report_array['voluntary_donations_paid'] + $financial_report_array['paid_baby_sitters'] + $totalChildrensRoomExpenses + $totalServiceProjectExpenses
                                    + $totalPartyExpense + $financial_report_array['office_printing_costs'] + $financial_report_array['office_postage_costs'] +
                                    $financial_report_array['office_membership_pins_cost'] + $totalOfficeExpense + $financial_report_array['annual_registration_fee'] + $totalEventExpense + $totalOtherExpenses;
                            $treasuryBalance = $financial_report_array ['amount_reserved_from_previous_year'] + $totalIncome - $totalExpenses
                        ?> --}}
                        <div class="flex-container">
                            <div class="flex-item">
                                Beginning Balance&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array ['amount_reserved_from_previous_year'], 2)}}</strong><br>
                            </div>
                            <div class="flex-item">
                                Ending Bank Statement Balance&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array ['bank_balance_now'], 2)}}</strong><br>
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

                                if (isset($financial_report_array['bank_reconciliation_array']) && $financial_report_array['bank_reconciliation_array'] !== null) {
                                    $blobData = base64_decode($financial_report_array['bank_reconciliation_array']);
                                    $bank_rec_array = unserialize($blobData);

                                    if ($bank_rec_array === false) {
                                        echo "Error: Failed to unserialize data.";
                                    } else {
                                        if (is_array($bank_rec_array) && count($bank_rec_array) > 0) {
                                            foreach ($bank_rec_array as $row) {
                                                // Sanitize and validate inputs
                                                $paymentAmount = is_numeric(str_replace(',', '', $row['bank_rec_payment_amount'])) ? floatval(str_replace(',', '', $row['bank_rec_payment_amount'])) : 0;
                                                $depositAmount = is_numeric(str_replace(',', '', $row['bank_rec_desposit_amount'])) ? floatval(str_replace(',', '', $row['bank_rec_desposit_amount'])) : 0;

                                                // $paymentAmount = is_numeric($row['bank_rec_payment_amount']) ? floatval($row['bank_rec_payment_amount']) : 0;
                                                // $depositAmount = is_numeric($row['bank_rec_desposit_amount']) ? floatval($row['bank_rec_desposit_amount']) : 0;
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
                        Reconciled Bank Statement:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($financial_report_array ['bank_balance_now'] + $totalReconciliation, 2) }}</strong><br>
                        {{-- Treasury Balance Now:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.number_format($treasuryBalance, 2)}}</strong><br> --}}

						<hr style="border-bottom: 2px solid #007bff">
				<!-- start:report_review -->
				<div  class="form-row report_review">
                    <div class="card-header col-md-12">
                        <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                    </div>
                            <div class="card-body form-row">
                                <div class="col-12">

                                    @if (!is_null($financial_report_array['bank_statement_included_path']))
                                        <div class="col-12">
                                            <label>Bank Statement Uploaded:</label><a href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['bank_statement_included_path']; ?>" >&nbsp; View Bank Statement</a><br>
                                        </div>
                                    @endif
                                    @if (!is_null($financial_report_array['bank_statement_2_included_path']))
                                        <div class="col-12">
                                            <label>Additional Statement Uploaded:</label><a href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['bank_statement_2_included_path']; ?>" >&nbsp; View Additional Bank Statement</a><br>
                                        </div>
                                    @endif
                                    <div class="col-12" id="StatementBlock">
                                        <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Upload or Replace Bank Statement(s).<br>
                                        @if (!is_null($financial_report_array['bank_statement_included_path']))
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Bank Statement</button>
                                            {{-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-statement1" ><i class="fas fa-undo" ></i>&nbsp; Replace Bank Statement</button> --}}
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Bank Statement</button>
                                            {{-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-statement1" ><i class="fas fa-upload" ></i>&nbsp; Upload Bank Statement</button> --}}
                                        @endif
                                    </div>
                                        <input type="hidden" name="StatementFile" id="StatementPath" value="<?php echo $financial_report_array['bank_statement_included_path']; ?>">
                                    <div class="clearfix"></div>
                                    <div class="col-12"><br></div>
                                    <div class="col-12" id="Statement2Block">
                                        @if (!is_null($financial_report_array['bank_statement_2_included_path']))
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Additional Bank Statement</button>
                                            {{-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-statement2" ><i class="fas fa-undo" ></i>&nbsp; Replace Additional Bank Statement</button> --}}
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Additional Bank Statement</button>
                                            {{-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-statement2" ><i class="fas fa-upload" ></i>&nbsp; Upload Additional Bank Statement</button> --}}
                                        @endif
                                    </div>
                                    <input type="hidden" name="Statement2File" id="Statement2Path" value="<?php echo $financial_report_array['bank_statement_2_included_path']; ?>">
                                    <div class="clearfix"></div>
                                    <div class="col-12"><br></div>

                                <div class="col-12">

                                <div class="col-12">
                                    <div class="form-group row">
                                        <label>Ending Balance on Last Year's Report:</label>
                                        <div class="col-md-12 row">
                                            <strong>{{ '$'.number_format($financial_report_array['pre_balance'], 2) }}</strong>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label>Does this year's Beginning Balance match last year's Ending Balance?<span class="field-required">*&nbsp;</span></label>
                                        <div class="col-12 row">
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" name="check_beginning_balance" value="1" {{ $financial_report_array['check_beginning_balance'] === 1 ? 'checked' : '' }}>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="check_beginning_balance" value="0" {{ $financial_report_array['check_beginning_balance'] === 0 ? 'checked' : '' }}>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label>Current bank statement included and balance matches chapter entry:<span class="field-required">*&nbsp;</span></label>
                                        <div class="col-12 row">
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" name="checkBankStatementIncluded" value="1" {{ $financial_report_array['check_bank_statement_included'] === 1 ? 'checked' : '' }}>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="checkBankStatementIncluded" value="0" {{ $financial_report_array['check_bank_statement_included'] === 0 ? 'checked' : '' }}>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label>Treasury Balance Now matches Reconciled Bank Balance:<span class="field-required">*&nbsp;</span></label>
                                        <div class="col-md-12 row">
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" name="checkBankStatementMatches" value="1" {{ $financial_report_array['check_bank_statement_matches'] === 1 ? 'checked' : '' }}>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="checkBankStatementMatches" value="0" {{ $financial_report_array['check_bank_statement_matches'] === 0 ? 'checked' : '' }}>
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
                                            <input type="text" class="form-control" min="0" step="0.01" name="post_balance" id="post_balance" style="width: 120px;" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['post_balance'] ?>">
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    <div class="col-12">
                                <div class="form-group row">
                                        <label for="Step10_Note">Add New Note:</label>
                                        <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(10)" name="Step10_Note" id="Step10_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
								<textarea class="form-control" style="width:100%" rows="8" name="Step10_Log" id="Step10_Log" readonly><?php echo $financial_report_array['step_10_notes_log']; ?></textarea>
							</div>
                            <div class="col-12"><br></div>

                    <!-- end:report_review -->
                    <div class="col-12 text-center">
                        <button type="submit" id="btn-step-10" class="btn bg-gradient-primary" >Save Report Review</button>
                    </div>
                </div>
            </div>
                </section>
			</div>
			</div>
        </div>
			<!------End Step 10 ------>

            <!------Start Step 11 ------>
            <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='11') echo "active";?>">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseEleven" style="width: 100%;">990N IRS FILING</a>
                    </h4>
                </div>
                <div id="collapseEleven" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='11') echo 'show'; ?>" data-parent="#accordion">
                    <div class="card-body">
                <section>
                    <div class="flex-container">
                        <div class="col-12">
                            <p>The 990N filing is an IRS requirement that all chapters must complete, but it cannot be filed before July 1st.  After filing, upload a copy of your chapter's filing confirmation here.  You can upload a copy of your confirmation email or screenshot after filing.  All chapters should file their 990N directly with the IRS and not through a third party. <span style="color:red"><i>The IRS does not charge a fee for 990N filings.</i></span></p>
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
                            @if (!is_null($financial_report_array['roster_path']))
                                    <div class="col-12">
                                        <label>990N Filing Uploaded:</label><a href="https://drive.google.com/uc?export=download&id={{ $financial_report_array['file_irs_path'] }}">&nbsp; View 990N Confirmation</a><br>
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
                            <input type="hidden" name="990NFiling" id="990NFiling" value="<?php echo $financial_report_array['file_irs_path']; ?>">
                            <div class="clearfix"></div>
                            <div class="col-12"><br></div>
                            <div class="col-12">

                                <div class="form-group row">
                                    <label>Did the chapter file their 990N with the date range of <strong>7/1/<?php echo date('Y')-1 .' - 6/30/'.date('Y');?></strong>?<span class="field-required">*&nbsp;</span></label>
                                    <div class="col-12 row">
                                        <div class="form-check" style="margin-right: 20px;">
                                            <input class="form-check-input" type="radio" name="checkCurrent990NAttached" value="1" {{ $financial_report_array['check_current_990N_included'] === 1 ? 'checked' : '' }}>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="checkCurrent990NAttached" value="0" {{ $financial_report_array['check_current_990N_included'] === 0 ? 'checked' : '' }}>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>

                <div class="col-12">
                            <div class="form-group row">
                                    <label for="Step11_Note">Add New Note:</label>
                                    <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(11)" name="Step11_Note" id="Step11_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
                            <textarea class="form-control" style="width:100%" rows="8" name="Step11_Log" id="Step11_Log" readonly><?php echo $financial_report_array['step_11_notes_log']; ?></textarea>
                        </div>
                        <div class="col-12"><br></div>

                <!-- end:report_review -->
                <div class="col-12 text-center">
                    <button type="submit" id="btn-step-11" class="btn bg-gradient-primary" >Save Report Review</button>
                </div>
            </div>
        </div>
            </section>
        </div>
        </div>
    </div>
        <!------End Step 11 ------>



			<!------Start Step 12 ------>
            <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='12') echo "active";?>">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseTwelve" style="width: 100%;">CHAPTER QUESTIONS</a>
                    </h4>
                </div>
                <div id="collapseTwelve" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='12') echo 'show'; ?>" data-parent="#accordion">
                    <div class="card-body">
				<section>
                    <table>
                        <tbody>
                           <tr><td>1.</td>
                               <td>Did anyone in your chapter receive any compensation or pay for their work with your chapter?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ is_null($financial_report_array['receive_compensation']) ? 'Not Answered' : ($financial_report_array['receive_compensation'] == 0 ? 'NO'
                                : ( $financial_report_array ['receive_compensation'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array ['receive_compensation_explanation']}}</strong></td></tr>
                           <tr><td>2.</td>
                               <td>Did any officer, member or family of a member benefit financially in any way from the member's position with your chapter?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['financial_benefit']) ? 'Not Answered' : ($financial_report_array['financial_benefit'] == 0 ? 'NO'
                                : ( $financial_report_array ['financial_benefit'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array ['financial_benefit_explanation']}}</strong></td></tr>
                           <tr><td>3.</td>
                               <td>Did your chapter attempt to influence any national, state/provincial, or local legislation, or support any other organization that did?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['influence_political']) ? 'Not Answered' : ($financial_report_array['influence_political'] == 0 ? 'NO'
                                : ( $financial_report_array ['influence_political'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array ['influence_political_explanation']}}</strong></td></tr>
                           <tr><td>4.</td>
                               <td>Did your chapter vote on all activities and expenditures during the fiscal year?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['vote_all_activities']) ? 'Not Answered' : ($financial_report_array['vote_all_activities'] == 0 ? 'NO'
                                : ( $financial_report_array ['vote_all_activities'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array ['vote_all_activities_explanation']}}</strong></td></tr>
                           <tr><td>5.</td>
                               <td>Did you purchase pins from International?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['purchase_pins']) ? 'Not Answered' : ($financial_report_array['purchase_pins'] == 0 ? 'NO'
                                : ( $financial_report_array ['purchase_pins'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array ['purchase_pins_explanation']}}</strong></td></tr>
                           <tr><td>6.</td>
                               <td>Did you purchase any merchandise from International other than pins?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['bought_merch']) ? 'Not Answered' : ($financial_report_array['bought_merch'] == 0 ? 'NO'
                                : ( $financial_report_array ['bought_merch'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{$financial_report_array ['bought_merch_explanation']}}</strong></td></tr>
                           <tr><td>7.</td>
                               <td>Did you offer or inform your members about MOMS Club merchandise?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['offered_merch']) ? 'Not Answered' : ($financial_report_array['offered_merch'] == 0 ? 'NO'
                                : ( $financial_report_array ['offered_merch'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array ['offered_merch_explanation']}}</strong></td></tr>
                           <tr><td>8.</td>
                               <td>Did you make the Bylaws and/or manual available for any chapter members that requested them?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['bylaws_available']) ? 'Not Answered' : ($financial_report_array['bylaws_available'] == 0 ? 'NO'
                                : ( $financial_report_array ['bylaws_available'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array ['bylaws_available_explanation']}}</strong></td></tr>
                           <tr><td>9.</td>
                               <td>Did you have a children's room with babysitters?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['childrens_room_sitters']) ? 'Not Answered' : ($financial_report_array['childrens_room_sitters'] == 0 ? 'NO'
                                : ( $financial_report_array ['childrens_room_sitters'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array['childrens_room_sitters_explanation']}}</strong></td></tr>
                           <tr><td>10.</td>
                               <td>Did you have playgroups? If so, how were they arranged.</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['playgroups']) ? 'Not Answered' : ($financial_report_array['playgroups'] == 0 ? 'NO'
                                : ( $financial_report_array ['playgroups'] == 1 ? 'YES   Arranged by Age' : (['playgroups'] == 2 ? 'YES   Multi-aged Groups' : 'Not Answered'))) }}</strong></td></tr>
                           <tr><td>11.</td>
                               <td>Did you have any child focused outings or activities?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['child_outings']) ? 'Not Answered' : ($financial_report_array['child_outings'] == 0 ? 'NO'
                                : ( $financial_report_array ['child_outings'] == 1 ? 'YES' : 'Not Answered')) }}&nbsp;&nbsp;  {{ $financial_report_array ['child_outings_explanation']}}</strong></td></tr>
                           <tr><td>12.</td>
                               <td>Did you have any mother focused outings or activities?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['mother_outings']) ? 'Not Answered' : ($financial_report_array['mother_outings'] == 0 ? 'NO'
                                : ( $financial_report_array ['mother_outings'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array ['mother_outings_explanation']}}</strong></td></tr>
                           <tr><td>13.</td>
                               <td>Did you have speakers at any meetings?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['meeting_speakers']) ? 'Not Answered' : ($financial_report_array['meeting_speakers'] == 0 ? 'NO'
                                : ( $financial_report_array ['meeting_speakers'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array ['meeting_speakers_explanation']}}</strong></td></tr>
                            <tr><td></td>
                                <td><strong>
                                    @php
                                        $meetingSpeakersArray = json_decode($financial_report_array['meeting_speakers_array']);
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
                                </strong></td></tr>

                           <tr><td>14.</td>
                               <td>Did you have any discussion topics at your meetings? If yes, how often?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['discussion_topic_frequency']) ? 'Not Answered' : ($financial_report_array['discussion_topic_frequency'] == 0 ? 'NO'
                                : ( $financial_report_array['discussion_topic_frequency'] == 1 ? '1-3 Times' : ($financial_report_array['discussion_topic_frequency'] == 2 ? '4-6 Times' :
                                   ($financial_report_array['discussion_topic_frequency'] == 3 ? '7-9 Times' : ($financial_report_array['discussion_topic_frequency'] == 4 ? '10+ Times' : 'Not Answered'))))) }}</strong></td></tr>
                           <tr><td>15.</td>
                               <td>Did your chapter have scheduled park days? If yes, how often?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['park_day_frequency']) ? 'Not Answered' : ($financial_report_array['park_day_frequency'] == 0 ? 'NO'
                                : ( $financial_report_array['park_day_frequency'] == 1 ? '1-3 Times' : ($financial_report_array['park_day_frequency'] == 2 ? '4-6 Times' :
                                   ($financial_report_array['park_day_frequency'] == 3 ? '7-9 Times' : ($financial_report_array['park_day_frequency'] == 4 ? '10+ Times' : 'Not Answered'))))) }}</strong></td></tr>

                           <tr><td>16.</td>
                            <td>Did your chapter have any of the following activity groups?</td></tr>
                            <tr><td></td>
                            <td><strong>
                                @php
                                    $activityArray = json_decode($financial_report_array['activity_array']);
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

                           <tr><td>17.</td>
                               <td>Did your chapter make any contributions to any organization or individual that is not registered with the government as a charity?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['contributions_not_registered_charity']) ? 'Not Answered' : ($financial_report_array['contributions_not_registered_charity'] == 0 ? 'NO'
                                : ( $financial_report_array ['contributions_not_registered_charity'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array ['contributions_not_registered_charity_explanation']}}</strong></td></tr>
                           <tr><td>18.</td>
                               <td>Did your chapter perform at least one service project to benefit mothers or children?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['at_least_one_service_project']) ? 'Not Answered' : ($financial_report_array['at_least_one_service_project'] == 0 ? 'NO'
                                : ( $financial_report_array ['at_least_one_service_project'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array['at_least_one_service_project_explanation']}}</strong></td></tr>
                           <tr><td>19.</td>
                               <td>Did your chapter sister another chapter?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['sister_chapter']) ? 'Not Answered' : ($financial_report_array['sister_chapter'] == 0 ? 'NO'
                                : ( $financial_report_array ['sister_chapter'] == 1 ? 'YES' : 'Not Answered' )) }}</strong></td></tr>
                           <tr><td>20.</td>
                               <td>Did your chapter attend an International Event?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['international_event']) ? 'Not Answered' : ($financial_report_array['international_event'] == 0 ? 'NO'
                                : ( $financial_report_array['international_event'] == 1 ? 'YES' : 'Not Answered' )) }}</strong></td></tr>
                           <tr><td>21.</td>
                               <td>Did your chapter file their IRS 990N?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['file_irs']) ? 'Not Answered' : ($financial_report_array['file_irs'] == 0 ? 'NO'
                                : ( $financial_report_array ['file_irs'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{$financial_report_array ['file_irs_explanation']}}</strong></td></tr>
                           <tr><td>22.</td>
                               <td>Is a copy of your chapter's most recent bank statement included with the copy of this report that you are submitting to International?</td></tr>
                           <tr><td></td>
                            <td><strong>{{ is_null($financial_report_array['bank_statement_included']) ? 'Not Answered' : ($financial_report_array['bank_statement_included'] == 0 ? 'NO'
                                : ( $financial_report_array ['bank_statement_included'] == 1 ? 'YES' : 'Not Answered' )) }}&nbsp;&nbsp;  {{ $financial_report_array ['bank_statement_included_explanation']}}{{ $financial_report_array ['wheres_the_money'] }}</strong></td></tr>
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
                            {{-- @if (!is_null($financial_report_array['roster_path']))
                                    <div class="col-12">
                                        <label>990N Filing Uploaded:</label><a href="https://drive.google.com/uc?export=download&id={{ $financial_report_array['file_irs_path'] }}">&nbsp; View 990N Confirmation</a><br>
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
                            <input type="hidden" name="990NFiling" id="990NFiling" value="<?php echo $financial_report_array['file_irs_path']; ?>">
                            <div class="clearfix"></div>
                            <div class="col-12"><br></div> --}}
                            <div class="col-12">

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <label>Did they purchase or have leftover pins? (Quesion 5):<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkPurchasedPins" value="1" {{ $financial_report_array['check_purchased_pins'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkPurchasedPins" value="0" {{ $financial_report_array['check_purchased_pins'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label>Did they purchase MOMS Club merchandise? (Quesion 6):<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkPurchasedMCMerch" value="1" {{ $financial_report_array['check_purchased_mc_merch'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkPurchasedMCMerch" value="0" {{ $financial_report_array['check_purchased_mc_merch'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label>Did they offer MOMS Club merchandise or info on how to buy to members? (Question 7):<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkOfferedMerch" value="1" {{ $financial_report_array['check_offered_merch'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkOfferedMerch" value="0" {{ $financial_report_array['check_offered_merch'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label>Did they make the Manual/By-Laws available to members? (Question 8):<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkBylawsMadeAvailable" value="1" {{ $financial_report_array['check_bylaws_available'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkBylawsMadeAvailable" value="0" {{ $financial_report_array['check_bylaws_available'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label>Did they Sistered another chapter? (Question 19):<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkSisteredAnotherChapter" value="1" {{ $financial_report_array['check_sistered_another_chapter'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkSisteredAnotherChapter" value="0" {{ $financial_report_array['check_sistered_another_chapter'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label>Did they attended an in person or virtual International Event? (Question 20):<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkAttendedTraining" value="1" {{ $financial_report_array['check_attended_training'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkAttendedTraining" value="0" {{ $financial_report_array['check_attended_training'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="form-group row">
                                            <label>Did they attach proof of 990N Filing with the date range of <strong>7/1/<?php echo date('Y')-1 .' - 6/30/'.date('Y');?></strong>?<span class="field-required">*&nbsp;</span></label>
                                            <div class="col-12 row">
                                                <div class="form-check" style="margin-right: 20px;">
                                                    <input class="form-check-input" type="radio" name="checkCurrent990NAttached" value="1" {{ $financial_report_array['check_current_990N_included'] === 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="checkCurrent990NAttached" value="0" {{ $financial_report_array['check_current_990N_included'] === 0 ? 'checked' : '' }}>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                            </div>
                                        </div> --}}

                                        <div class="form-group row">
                                            <label for="Step12_Note">Add New Note:</label>
                                            <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(12)" name="Step12_Note" id="Step12_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
								<textarea class="form-control" style="width:100%" rows="8" name="Step12_Log" id="Step12_Log" readonly><?php echo $financial_report_array['step_12_notes_log']; ?></textarea>
							</div>
                            <div class="col-12"><br></div>

				<!-- end:report_review -->
                <div class="col-12 text-center">
						<button type="submit" id="btn-step-12" class="btn bg-gradient-primary" >Save Report Review</button>
					</div>
                </div>
            </div>
			  </section>
		  </div>
		  </div>
        </div>
			<!------End Step 12 ------>

			<!------Start Step 13 ------>
            <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='13') echo "active";?>">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseThirteen" style="width: 100%;">AWARD NOMINATIONS</a>
                    </h4>
                </div>
                <div id="collapseThirteen" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='13') echo 'show'; ?>" data-parent="#accordion">
                    <div class="card-body">
					<section>
					<div class="form-row form-group">
                        <input type="hidden" id="TotalAwardNominations" name="TotalAwardNominations" value=<?php
							if (!empty($financial_report_array)) {
								if ($financial_report_array['award_nominations']>0){
									echo $financial_report_array['award_nominations'];
								}
								else {
									echo "0";
								}
							}
							else {
								echo "0";
							} ?>>

							<?php
                                if (empty($financial_report_array) || $financial_report_array['award_1_nomination_type'] === null || $financial_report_array['award_1_nomination_type'] == 0) {
                                    echo "No Award Nominations for this Chapter";
                                }
                            ?>

						<!-- Award 1 Start -->
						<div class="box_brd_contentpad" id="Award1Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
                                    <div class="col-md-12">
                                        Award #1:&nbsp;&nbsp;&nbsp;{{ is_null($financial_report_array['award_1_nomination_type']) ? 'N/A' : ($financial_report_array['award_1_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_1_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_1_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_1_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_1_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_1_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_1_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                    </div>
								</div>
							</div>
                            <div class="col-sm-12">
								 Description:
                                 <div class="col-sm-12">
                                    <?php
                                        if (!empty($financial_report_array)) {
                                            $award_1_files = $financial_report_array['award_1_outstanding_project_desc'];
                                            echo ($award_1_files !== null) ? $award_1_files : "No description entered";
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <?php if (!empty($financial_report_array['award_1_files'])): ?>
                                        <label class="control-label" for="Award1Files">File Attachment:</label>
                                        <div class="col-sm-12">
                                            <a href="<?php echo $financial_report_array['award_1_files']; ?>" target="_blank">Award 1 Files</a>
                                        </div>
                                <?php else: ?>
                                        <label class="control-label" for="Award1Files">File Attachment:</label>
                                        <div class="col-sm-12">
                                            No files attached
                                        </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($financial_report_array) && ($financial_report_array['award_1_nomination_type'] == 5 || $financial_report_array['award_1_nomination_type'] == 6)) : ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
                            <div class="col-sm-12">
                                <table>
                                    <tr><td colspan="2">Did you follow the Bylaws and all instructions from International?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_1_outstanding_follow_bylaws']) ? 'Not Answered' : ($financial_report_array['award_1_outstanding_follow_bylaws'] == 0 ? 'NO'
                                            : ($financial_report_array['award_1_outstanding_follow_bylaws'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you run a well-rounded program for your members?</td></tr>
                                    <tr><td>&nbsp;&nbsp;&nbsp;</td>
                                    <td>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits
                                        kept under 15% of the dues received -- these are all taken into consideration.<br>
                                        A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older
                                        children (or vice versa) would not be offering a well-rounded program.</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_1_outstanding_well_rounded']) ? 'Not Answered' : ($financial_report_array['award_1_outstanding_well_rounded'] == 0 ? 'NO'
                                            : ($financial_report_array['award_1_outstanding_well_rounded'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you communicate with your Coordinator?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_1_outstanding_communicated']) ? 'Not Answered' : ($financial_report_array['award_1_outstanding_communicated'] == 0 ? 'NO'
                                        : ($financial_report_array['award_1_outstanding_communicated'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2" colspan="2">Did you support the International MOMS Club?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_1_outstanding_support_international']) ? 'Not Answered' : ($financial_report_array['award_1_outstanding_support_international'] == 0 ? 'NO'
                                        : ($financial_report_array['award_1_outstanding_support_international'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <?php endif; ?>
						</div>
						<!-- Award 1 Stop -->
						<!-- Award 2 Start -->
						<div class="box_brd_contentpad" id="Award2Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
                                    <div class="col-md-12">
                                        Award #2:&nbsp;&nbsp;&nbsp;{{ is_null($financial_report_array['award_2_nomination_type']) ? 'N/A' : ($financial_report_array['award_2_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_2_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_2_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_2_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_2_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_2_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_2_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                    </div>
								</div>
							</div>
                            <div class="col-sm-12">
								 Description:
                                 <div class="col-sm-12">
                                    <?php
                                        if (!empty($financial_report_array)) {
                                            $description = $financial_report_array['award_2_outstanding_project_desc'];
                                            echo ($description !== null) ? $description : "No description entered";
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <?php if (!empty($financial_report_array['award_2_files'])): ?>
                                        <label class="control-label" for="Award2Files">File Attachment:</label>
                                        <div class="col-sm-12">
                                            <a href="<?php echo $financial_report_array['award_2_files']; ?>" target="_blank">Award 2 Files</a>
                                        </div>
                                <?php else: ?>
                                        <label class="control-label" for="Award2Files">File Attachment:</label>
                                        <div class="col-sm-12">
                                            No files attached
                                        </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($financial_report_array) && ($financial_report_array['award_2_nomination_type'] == 5 || $financial_report_array['award_2_nomination_type'] == 6)) : ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
                            <div class="col-sm-12">
                                <table>
                                    <tr><td colspan="2">Did you follow the Bylaws and all instructions from International?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_2_outstanding_follow_bylaws']) ? 'Not Answered' : ($financial_report_array['award_2_outstanding_follow_bylaws'] == 0 ? 'NO'
                                            : ($financial_report_array['award_2_outstanding_follow_bylaws'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you run a well-rounded program for your members?</td></tr>
                                    <tr><td>&nbsp;&nbsp;&nbsp;</td>
                                    <td>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits
                                        kept under 15% of the dues received -- these are all taken into consideration.<br>
                                        A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older
                                        children (or vice versa) would not be offering a well-rounded program.</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_2_outstanding_well_rounded']) ? 'Not Answered' : ($financial_report_array['award_2_outstanding_well_rounded'] == 0 ? 'NO'
                                            : ($financial_report_array['award_2_outstanding_well_rounded'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you communicate with your Coordinator?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_2_outstanding_communicated']) ? 'Not Answered' : ($financial_report_array['award_2_outstanding_communicated'] == 0 ? 'NO'
                                        : ($financial_report_array['award_2_outstanding_communicated'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2" colspan="2">Did you support the International MOMS Club?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_2_outstanding_support_international']) ? 'Not Answered' : ($financial_report_array['award_2_outstanding_support_international'] == 0 ? 'NO'
                                        : ($financial_report_array['award_2_outstanding_support_international'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <?php endif; ?>
						</div>
						<!-- Award 2 Stop -->
						<!-- Award 3 Start -->
						<div class="box_brd_contentpad" id="Award3Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
                                    <div class="col-md-12">
                                        Award #3:&nbsp;&nbsp;&nbsp;{{ is_null($financial_report_array['award_3_nomination_type']) ? 'N/A' : ($financial_report_array['award_3_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_3_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_3_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_3_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_3_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_3_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_3_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                    </div>
								</div>
							</div>
                            <div class="col-sm-12">
								 Description:
                                 <div class="col-sm-12">
                                    <?php
                                        if (!empty($financial_report_array)) {
                                            $description = $financial_report_array['award_3_outstanding_project_desc'];
                                            echo ($description !== null) ? $description : "No description entered";
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <?php if (!empty($financial_report_array['award_3_files'])): ?>
                                        <label class="control-label" for="Award3Files">File Attachment:</label>
                                        <div class="col-sm-12">
                                            <a href="<?php echo $financial_report_array['award_3_files']; ?>" target="_blank">Award 3 Files</a>
                                        </div>
                                <?php else: ?>
                                        <label class="control-label" for="Award3Files">File Attachment:</label>
                                        <div class="col-sm-12">
                                            No files attached
                                        </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($financial_report_array) && ($financial_report_array['award_3_nomination_type'] == 5 || $financial_report_array['award_3_nomination_type'] == 6)) : ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
                            <div class="col-sm-12">
                                <table>
                                    <tr><td colspan="2">Did you follow the Bylaws and all instructions from International?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_3_outstanding_follow_bylaws']) ? 'Not Answered' : ($financial_report_array['award_3_outstanding_follow_bylaws'] == 0 ? 'NO'
                                            : ($financial_report_array['award_3_outstanding_follow_bylaws'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you run a well-rounded program for your members?</td></tr>
                                    <tr><td>&nbsp;&nbsp;&nbsp;</td>
                                    <td>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits
                                        kept under 15% of the dues received -- these are all taken into consideration.<br>
                                        A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older
                                        children (or vice versa) would not be offering a well-rounded program.</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_3_outstanding_well_rounded']) ? 'Not Answered' : ($financial_report_array['award_3_outstanding_well_rounded'] == 0 ? 'NO'
                                            : ($financial_report_array['award_3_outstanding_well_rounded'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you communicate with your Coordinator?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_3_outstanding_communicated']) ? 'Not Answered' : ($financial_report_array['award_3_outstanding_communicated'] == 0 ? 'NO'
                                        : ($financial_report_array['award_3_outstanding_communicated'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2" colspan="2">Did you support the International MOMS Club?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_3_outstanding_support_international']) ? 'Not Answered' : ($financial_report_array['award_3_outstanding_support_international'] == 0 ? 'NO'
                                        : ($financial_report_array['award_3_outstanding_support_international'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <?php endif; ?>
						</div>
						<!-- Award 3 Stop -->
						<!-- Award 4 Start -->
						<div class="box_brd_contentpad" id="Award1Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
                                    <div class="col-md-12">
                                        Award #4:&nbsp;&nbsp;&nbsp;{{ is_null($financial_report_array['award_4_nomination_type']) ? 'N/A' : ($financial_report_array['award_4_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_4_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_4_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_4_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_4_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_4_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_4_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                    </div>
								</div>
							</div>
                            <div class="col-sm-12">
								 Description:
                                 <div class="col-sm-12">
                                    <?php
                                        if (!empty($financial_report_array)) {
                                            $description = $financial_report_array['award_4_outstanding_project_desc'];
                                            echo ($description !== null) ? $description : "No description entered";
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <?php if (!empty($financial_report_array['award_4_files'])): ?>
                                        <label class="control-label" for="Award4Files">File Attachment:</label>
                                        <div class="col-sm-12">
                                            <a href="<?php echo $financial_report_array['award_4_files']; ?>" target="_blank">Award 4 Files</a>
                                        </div>
                                <?php else: ?>
                                        <label class="control-label" for="Award4Files">File Attachment:</label>
                                        <div class="col-sm-12">
                                            No files attached
                                        </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($financial_report_array) && ($financial_report_array['award_4_nomination_type'] == 5 || $financial_report_array['award_4_nomination_type'] == 6)) : ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
                            <div class="col-sm-12">
                                <table>
                                    <tr><td colspan="2">Did you follow the Bylaws and all instructions from International?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_4_outstanding_follow_bylaws']) ? 'Not Answered' : ($financial_report_array['award_4_outstanding_follow_bylaws'] == 0 ? 'NO'
                                            : ($financial_report_array['award_4_outstanding_follow_bylaws'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you run a well-rounded program for your members?</td></tr>
                                    <tr><td>&nbsp;&nbsp;&nbsp;</td>
                                    <td>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits
                                        kept under 15% of the dues received -- these are all taken into consideration.<br>
                                        A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older
                                        children (or vice versa) would not be offering a well-rounded program.</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_4_outstanding_well_rounded']) ? 'Not Answered' : ($financial_report_array['award_4_outstanding_well_rounded'] == 0 ? 'NO'
                                            : ($financial_report_array['award_4_outstanding_well_rounded'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you communicate with your Coordinator?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_4_outstanding_communicated']) ? 'Not Answered' : ($financial_report_array['award_4_outstanding_communicated'] == 0 ? 'NO'
                                        : ($financial_report_array['award_4_outstanding_communicated'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2" colspan="2">Did you support the International MOMS Club?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_4_outstanding_support_international']) ? 'Not Answered' : ($financial_report_array['award_4_outstanding_support_international'] == 0 ? 'NO'
                                        : ($financial_report_array['award_4_outstanding_support_international'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <?php endif; ?>
						</div>
						<!-- Award 4 Stop -->
						<!-- Award 5 Start -->
						<div class="box_brd_contentpad" id="Award1Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
                                    <div class="col-md-12">
                                        Award #5:&nbsp;&nbsp;&nbsp;{{ is_null($financial_report_array['award_5_nomination_type']) ? 'N/A' : ($financial_report_array['award_5_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_5_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_5_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_5_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_5_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_5_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_5_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                    </div>
								</div>
							</div>
                            <div class="col-sm-12">
								 Description:
                                 <div class="col-sm-12">
                                    <?php
                                        if (!empty($financial_report_array)) {
                                            $description = $financial_report_array['award_5_outstanding_project_desc'];
                                            echo ($description !== null) ? $description : "No description entered";
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <?php if (!empty($financial_report_array['award_5_files'])): ?>
                                        <label class="control-label" for="Award5Files">File Attachment:</label>
                                        <div class="col-sm-12">
                                            <a href="<?php echo $financial_report_array['award_5_files']; ?>" target="_blank">Award 5 Files</a>
                                        </div>
                                <?php else: ?>
                                        <label class="control-label" for="Award5Files">File Attachment:</label>
                                        <div class="col-sm-12">
                                            No files attached
                                        </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($financial_report_array) && ($financial_report_array['award_5_nomination_type'] == 5 || $financial_report_array['award_5_nomination_type'] == 6)) : ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
                            <div class="col-sm-12">
                                <table>
                                    <tr><td colspan="2">Did you follow the Bylaws and all instructions from International?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_5_outstanding_follow_bylaws']) ? 'Not Answered' : ($financial_report_array['award_5_outstanding_follow_bylaws'] == 0 ? 'NO'
                                            : ($financial_report_array['award_5_outstanding_follow_bylaws'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you run a well-rounded program for your members?</td></tr>
                                    <tr><td>&nbsp;&nbsp;&nbsp;</td>
                                    <td>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits
                                        kept under 15% of the dues received -- these are all taken into consideration.<br>
                                        A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older
                                        children (or vice versa) would not be offering a well-rounded program.</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_5_outstanding_well_rounded']) ? 'Not Answered' : ($financial_report_array['award_5_outstanding_well_rounded'] == 0 ? 'NO'
                                            : ($financial_report_array['award_5_outstanding_well_rounded'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you communicate with your Coordinator?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_5_outstanding_communicated']) ? 'Not Answered' : ($financial_report_array['award_5_outstanding_communicated'] == 0 ? 'NO'
                                        : ($financial_report_array['award_5_outstanding_communicated'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2" colspan="2">Did you support the International MOMS Club?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_5_outstanding_support_international']) ? 'Not Answered' : ($financial_report_array['award_5_outstanding_support_international'] == 0 ? 'NO'
                                        : ($financial_report_array['award_5_outstanding_support_international'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <?php endif; ?>
						</div>
						<!-- Award 5 Stop -->

						<div class="box_brd_contentpad" id="AwardSignatureBlock" style="display: none;">
							  <div class="box_brd_title_box">
								 <h4>ALL ENTRIES MUST INCLUDE THIS SIGNED AGREEMENT</h4>
							  </div>
								<div class="award_acc_con">
									<p>I, THE UNDERSIGNED, AFFIRM THAT I HAVE THE RIGHT TO SUBMIT THE ENCLOSED ENTRY TO THE INTERNATIONAL MOMS CLUB FOR CONSIDERATION IN THEIR OUTSTANDING CHAPTER RECOGNITIONS, THAT THE ENCLOSED INFORMATION IS ACCURATE AND COMPLETE TO THE BEST OF MY ABILITY AND THAT I HAVE RECEIVED PERMISSION TO ENTER THIS INFORMATION FROM ANY OTHER MEMBERS WHO MAY HAVE CONTRIBUTED TO THIS ENTRY OR THE ORIGINAL ACTIVITY/PROJECT THAT IS BEING CONSIDERED. I UNDERSTAND THAT, WHETHER OR NOT MY CHAPTER RECEIVES A RECOGNITION, THE ENCLOSED ENTRY WILL BECOME THE PROPERTY OF THE INTERNATIONAL MOMS CLUB AND THAT THE INFORMATION, PICTURES, CLIPPINGS AND/OR OTHER MATERIALS ENCLOSED MAY BE SHARED WITH OTHER MOMS CLUB CHAPTERS OR USED IN ANY WAY THE INTERNATIONAL MOMS CLUB SEES FIT, WITH NO COMPENSATION TO ME, OTHERS INVOLVED IN THIS PROJECT AND/OR THE CHAPTER(S). NO ENTRIES OR SUBMITTED MATERIALS WILL BE RETURNED AND THE INTERNATIONAL MOMS CLUB MAY REASSIGN ANY ENTRY TO ANOTHER CATEGORY IF IT DEEMS NECESSARY. RECOGNITIONS WILL BE GIVEN IN THE VARIOUS CATEGORIES ACCORDING TO THE DECISION OF THE INTERNATIONAL MOMS CLUB. THE AWARDING OF RECOGNITIONS WILL BE ACCORDING TO MERIT, AND THE INTERNATIONAL MOMS CLUB MAY DECIDE NOT TO GIVE AN AWARD IN ANY OR ALL CATEGORIES IF IT SO CHOOSES. ALL DECISIONS OF THE INTERNATIONAL MOMS CLUB ARE FINAL. ANY RECOGNITIONS ARE OFFICIALLY PRESENTED TO THE LOCAL CHAPTERS, NOT THE INDIVIDUAL, AND RECOGNITIONS WILL NOT BE PERSONALIZED WITH ANY INDIVIDUAL’S NAME. REPLACEMENT RECOGNITIONS MAY OR MAY NOT BE MADE AVAILABLE AT INTERNATIONAL’S DISCRETION, AND IF A REPLACEMENT IS MADE BECAUSE OF AN ERROR IN THE ENTRY INFORMATION, THE COST WILL BE PAID IN ADVANCE BY THE LOCAL CHAPTER.</p>
									<div class="checkbox">
										<label><input type="checkbox" id="AwardsAgree" name="AwardsAgree" disabled>I understand and agree to the above</label>
									</div>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
										<input id="NominationSubmitor" name="NominationSubmitor" type="text" class="form-control" placeholder="Kathleen MacPhee">
									</div>
								</div>
                            </div>

                    </div>
                    <hr style="border-bottom: 2px solid #007bff">

					<div  class="award_acc_con">
                            <!-- start:report_review -->
							<div class="form-row report_review">
								<div class="card-header col-md-12">
                                    <h3 class="card-title" style="color:#007bff"><strong>ANNUAL REPORT REVIEW</strong></h3>
                                </div>
                                <div class="card-body form-row">
                                    <div class="col-12">

									<div class="col-12" <?php if ($financial_report_array['award_1_nomination_type']==NULL) echo "style=\"display: none;\""; ?> ?>
										<div class="form-group col-md-6">
											<label for="NominationType1">Award #1:</label>
											<select class="form-control" id="sumcheckNominationType1" name="sumcheckNominationType1" disabled >
												<option value="" style="display:none" disabled selected>Select an award type</option>
												<option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
												<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
												<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
												<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
												<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
												<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
												<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
											</select>
										</div>
										<div class="col-md-6">
                                            <div class="col-12">
                                            <div class="form-inline">
                                                <label style="display: block;">Award Status:<span class="field-required">*</span></label>
                                                <select id="checkAward1Approved" name="checkAward1Approved" class="form-control select2-sb4" style="width: 150px;" required >
                                                    <option value="" {{ is_null($financial_report_array->check_award_1_approved) ? 'selected' : '' }} disabled>Please Select</option>
                                                    <option value="0" {{$financial_report_array->check_award_1_approved === 0 ? 'selected' : ''}}>No</option>
                                                    <option value="1" {{$financial_report_array->check_award_1_approved == 1 ? 'selected' : ''}}>Yes</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
									</div>

									<div class="col-12" <?php if ($financial_report_array['award_2_nomination_type']==NULL) echo "style=\"display: none;\""; ?>>
										<div class="form-group col-md-6">
											<label for="NominationType2">Award #2:</label>
											<select class="form-control" id="sumcheckNominationType2" name="sumcheckNominationType2" disabled >
												<option value="" style="display:none" disabled selected>Select an award type</option>
												<option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
												<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
												<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
												<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
												<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
												<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
												<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
											</select>
										</div>
                                        <div class="col-md-6">
                                            <div class="col-12">
                                            <div class="form-inline">
                                                <label style="display: block;">Award Status:<span class="field-required">*</span></label>
                                                <select id="checkAward2Approved" name="checkAward2Approved" class="form-control select2-sb4" style="width: 150px;" required >
                                                    <option value="" {{ is_null($financial_report_array->check_award_2_approved) ? 'selected' : '' }} disabled>Please Select</option>
                                                    <option value="0" {{$financial_report_array->check_award_2_approved === 0 ? 'selected' : ''}}>No</option>
                                                    <option value="1" {{$financial_report_array->check_award_2_approved == 1 ? 'selected' : ''}}>Yes</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
									</div>

									<div class="col-12" <?php if ($financial_report_array['award_3_nomination_type']==NULL) echo "style=\"display: none;\""; ?>>
										<div class="form-group col-md-6">
											<label for="NominationType3">Award #3:</label>
											<select class="form-control" id="sumcheckNominationType3" name="sumcheckNominationType3" disabled >
												<option value="" style="display:none" disabled selected>Select an award type</option>
												<option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
												<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
												<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
												<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
												<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
												<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
												<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
											</select>
										</div>
										<div class="col-md-6">
                                            <div class="col-12">
                                            <div class="form-inline">
                                                <label style="display: block;">Award Status:<span class="field-required">*</span></label>
                                                <select id="checkAward3Approved" name="checkAward3Approved" class="form-control select2-sb4" style="width: 150px;" required >
                                                    <option value="" {{ is_null($financial_report_array->check_award_3_approved) ? 'selected' : '' }} disabled>Please Select</option>
                                                    <option value="0" {{$financial_report_array->check_award_3_approved === 0 ? 'selected' : ''}}>No</option>
                                                    <option value="1" {{$financial_report_array->check_award_3_approved == 1 ? 'selected' : ''}}>Yes</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
									</div>

									<div class="col-12" <?php if ($financial_report_array['award_4_nomination_type']==NULL) echo "style=\"display: none;\""; ?>>
										<div class="form-group col-md-6">
											<label for="NominationType4">Award #4:</label>
											<select class="form-control" id="sumcheckNominationType4" name="sumcheckNominationType4" disabled >
												<option value="" style="display:none" disabled selected>Select an award type</option>
												<option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
												<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
												<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
												<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
												<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
												<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
												<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
											</select>
										</div>
										<div class="col-md-6">
                                            <div class="col-12">
                                            <div class="form-inline">
                                                <label style="display: block;">Award Status:<span class="field-required">*</span></label>
                                                <select id="checkAward4Approved" name="checkAward4Approved" class="form-control select2-sb4" style="width: 150px;" required >
                                                    <option value="" {{ is_null($financial_report_array->check_award_4_approved) ? 'selected' : '' }} disabled>Please Select</option>
                                                    <option value="0" {{$financial_report_array->check_award_4_approved === 0 ? 'selected' : ''}}>No</option>
                                                    <option value="1" {{$financial_report_array->check_award_4_approved == 1 ? 'selected' : ''}}>Yes</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
									</div>

									<div class="col-12" <?php if ($financial_report_array['award_5_nomination_type']==NULL) echo "style=\"display: none;\""; ?> >
										<div class="form-group col-md-6">
											<label for="NominationType5">Award #5:</label>
											<select class="form-control" id="sumcheckNominationType5" name="sumcheckNominationType5" disabled >
												<option value="" style="display:none" disabled selected>Select an award type</option>
												<option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
												<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
												<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
												<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
												<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
												<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
												<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
											</select>
										</div>
										<div class="col-md-6">
                                            <div class="col-12">
                                            <div class="form-inline">
                                                <label style="display: block;">Award Status:<span class="field-required">*</span></label>
                                                <select id="checkAward5Approved" name="checkAward5Approved" class="form-control select2-sb4" style="width: 150px;" required >
                                                    <option value="" {{ is_null($financial_report_array->check_award_5_approved) ? 'selected' : '' }} disabled>Please Select</option>
                                                    <option value="0" {{$financial_report_array->check_award_5_approved === 0 ? 'selected' : ''}}>No</option>
                                                    <option value="1" {{$financial_report_array->check_award_5_approved == 1 ? 'selected' : ''}}>Yes</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
									</div>

									<div class="col-12" >
                                            <strong style="color:red">Please Note</strong><br>
                                            This will take you to a new screen - be sure to save all work before clicking button to Add Additional Awards.<br>
                                            <a id="addAwardsLink" href="{{ url("/chapter/awardsview/{$chapterid}") }}" class="btn btn-sm bg-gradient-primary" <?php if($financial_report_array['review_complete'] != "" || !$submitted) echo "disabled"; ?>><i class="fas fa-trophy" ></i>&nbsp; Add/Edit Awards</a>
									<div class="clearfix"></div>
                                    <div class="col-12"><br></div>


                                    <div class="col-12">
                                        <div class="form-group row">
											<label for="Step13_Note">Add New Note:</label>
											<textarea class="form-control" style="width:100%" oninput="EnableNoteLogButton(13)" rows="3" name="Step13_Note" id="Step13_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
                                            <div class="form-group row" style="margin-left: 5px; margin-top: 5px">
											<button type="button" id="AddNote13" class="btn btn-sm bg-gradient-success" onclick="AddNote(13)" disabled><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Note to Log</button>
										</div>
                                    </div>
                                </div>
                                </div>

										<div class="col-md-12">
											<label for="Step13_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
										</div>
										<div class="col-12">
											<textarea class="form-control" style="width:100%" rows="8" name="Step13_Log" id="Step13_Log" readonly><?php echo $financial_report_array['step_13_notes_log']; ?></textarea>
										</div>
                                        <div class="col-12"><br></div>

							<!-- end:report_review -->
                        <div class="col-12 text-center">
							 <button type="submit" id="btn-step-13" class="btn bg-gradient-primary" >Save Report Review</button>
						</div>
                    </div>
                </div>
					</section>
				</div>
            </div>
        </div>
            <!------End Step 13 ------>

			<!------Start Step 14 ------>
            {{-- <div class="card card-primary <?php if($financial_report_array['farthest_step_visited_coord'] =='14') echo "active";?>">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseFourteen" style="width: 100%;">REVIEW SUMMARY</a>
                    </h4>
                </div>
                <div id="collapseFourteen" class="collapse <?php if($financial_report_array['farthest_step_visited_coord'] =='14') echo 'show'; ?>" data-parent="#accordion">
                    <div class="card-body">
						<section>
                            <div class="col-md-12">
                                <strong style="color:red">Please Note</strong><br>
                                Answers from questios in previous sections will show up here after they have been saved.<br>
                            <br>
                            </div>

                            @if ($submitted)
                                <div class="col-md-12">
                                    <div class="col-xs-12">
                                        <label class="control-label" for="DownloadPDF">Financial Report PDF:</label>
                                <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['financial_pdf_path']; ?>">Download PDF</a>
                                </div>

                                    <?php if (!empty($financial_report_array['roster_path'])): ?>
                                        <div class="col-xs-12">
                                            <label class="control-label" for="RosterLink">Chapter Roster File:</label>
                                            <a href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['roster_path']; ?>">Chapter Roster</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-xs-12">
                                            <label class="control-label" for="RosterLink">Chapter Roster File:</label>
                                            No file attached
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-12">
                                    <?php if (!empty($financial_report_array['bank_statement_included_path'])): ?>
                                        <div class="col-xs-12">
                                            <label class="control-label" for="Statement1ink">Primary Bank Statement:</label>
                                            <a href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['bank_statement_included_path']; ?>">Statement 1</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-xs-12">
                                            <label class="control-label" for="Statement1Link">Primary Bank Statement:</label>
                                            No file attached
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-12">
                                    <?php if (!empty($financial_report_array['bank_statement_2_included_path'])): ?>
                                        <div class="col-xs-12">
                                            <label class="control-label" for="Statement2Link">Additional Bank Statement:</label>
                                            <a href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['bank_statement_2_included_path']; ?>">Statement 2</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-xs-12">
                                            <label class="control-label" for="Statement2Link">Additional Bank Statement:</label>
                                            No file attached
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-12">
                                    <?php if (!empty($financial_report_array['file_irs_path'])): ?>
                                        <div class="col-xs-12">
                                            <label class="control-label" for="990NLink">990N Filing:</label>
                                            <a href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['file_irs_path']; ?>">990N Confirmation</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-xs-12">
                                            <label class="control-label" for="990NLink">990N Filing:</label>
                                            No file attached
                                        </div>
                                    <?php endif; ?>
                                </div>
									<div class="clearfix"></div>
                                @endif
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

                                    <div class="form-group col-md-12">
                                        <div class="col-md-12">
                                        <div class="flex-container2">
                                        <div class="flex-item2">
                                            Excel roster attached and complete (should be above):&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_roster_attached']) ? 'Please Review'
                                                : ($financial_report_array ['check_roster_attached'] == 0 ? 'NO' : ($financial_report_array ['check_roster_attached'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Number of members listed, dues received, and renewal paid "seem right":&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_renewal_seems_right']) ? 'Please Review'
                                                : ($financial_report_array ['check_renewal_seems_right'] == 0 ? 'NO' : ($financial_report_array ['check_renewal_seems_right'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Minimum of one service project completed:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_minimum_service_project']) ? 'Please Review'
                                                : ( $financial_report_array ['check_minimum_service_project'] == 0 ? 'NO' : ($financial_report_array ['check_minimum_service_project'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Donation to M2M Fund:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_m2m_donation']) ? 'Please Review'
                                                : ($financial_report_array ['check_m2m_donation'] == 0 ? 'NO' : ($financial_report_array ['check_m2m_donation'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Party Percentage:&nbsp;&nbsp;&nbsp;<strong>{{ number_format($partyPercentage * 100, 2) }}%</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Party Percentage less than 15%:&nbsp;&nbsp;&nbsp;
                                            <strong>
                                                <span style="
                                                    @if(is_null($financial_report_array['check_party_percentage']))
                                                        background-color: #FFFFFF; color: #000000;
                                                    @elseif($financial_report_array['check_party_percentage'] == 2)
                                                        background-color: #28a745; color: #FFFFFF;
                                                    @elseif($financial_report_array['check_party_percentage'] == 1)
                                                        background-color: #ffc107; color: #000000;
                                                    @elseif($financial_report_array['check_party_percentage'] == 0)
                                                        background-color: #dc3545; color: #FFFFFF;
                                                    @else
                                                        background-color: #FFFFFF; color: #000000;
                                                    @endif
                                                    padding: 2px 5px; border-radius: 3px;">
                                                    @if(is_null($financial_report_array['check_party_percentage']))
                                                        Please Review
                                                    @elseif($financial_report_array['check_party_percentage'] == 0)
                                                        They are over 20%
                                                    @elseif($financial_report_array['check_party_percentage'] == 1)
                                                        They are between 15-20%
                                                    @elseif($financial_report_array['check_party_percentage'] == 2)
                                                        They are under 15%
                                                    @else
                                                        Please Review
                                                    @endif
                                                </span>
                                            </strong><br>
                                        </div>

                                        <div class="flex-item2">
                                            Total income/revenue less than $50,000:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_total_income_less']) ? 'Please Review'
                                                : ( $financial_report_array ['check_total_income_less'] == 0 ? 'NO' : ($financial_report_array ['check_total_income_less'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Current bank statement included (should be above):&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_bank_statement_included']) ? 'Please Review'
                                                : ( $financial_report_array ['check_bank_statement_included'] == 0 ? 'NO' : ($financial_report_array ['check_bank_statement_included'] == 1 ? 'YES' : 'Please Review')) }}</strong><br>
                                        </div>


                                        <div class="flex-item2">
                                            Treasury Balance Now matches Reconciled Bank Balance:&nbsp;&nbsp;&nbsp;
                                            <strong>
                                                <span style="
                                                    @if(is_null($financial_report_array['check_bank_statement_matches']))
                                                        background-color: #FFFFFF; color: #000000;
                                                    @elseif($financial_report_array['check_bank_statement_matches'] == 1)
                                                        background-color: #28a745; color: #FFFFFF;
                                                    @elseif($financial_report_array['check_bank_statement_matches'] == 0)
                                                        background-color: #dc3545; color: #FFFFFF;
                                                    @else
                                                        background-color: #FFFFFF; color: #000000;
                                                    @endif
                                                    padding: 2px 5px; border-radius: 3px;">
                                                    @if(is_null($financial_report_array['check_bank_statement_matches']))
                                                        Please Review
                                                    @elseif($financial_report_array['check_bank_statement_matches'] == 1)
                                                        Report balances
                                                    @elseif($financial_report_array['check_bank_statement_matches'] == 0)
                                                        Report is out of balance
                                                    @else
                                                        Please Review
                                                    @endif
                                                </span>
                                            </strong><br>
                                        </div>

                                        <div class="flex-item2">
                                            Proof of 990N Filing for 7/1/<?php echo date('Y')-1 .' - 6/30/'.date('Y');?> (should be above):&nbsp;&nbsp;&nbsp;
                                            <strong>
                                                <span style="
                                                    @if(is_null($financial_report_array['check_current_990N_included']))
                                                        background-color: #FFFFFF; color: #000000;
                                                    @elseif($financial_report_array['check_current_990N_included'] == 1)
                                                        background-color: #28a745; color: #FFFFFF;
                                                    @elseif($financial_report_array['check_current_990N_included'] == 0)
                                                        background-color: #dc3545; color: #FFFFFF;
                                                    @else
                                                        background-color: #FFFFFF; color: #000000;
                                                    @endif
                                                    padding: 2px 5px; border-radius: 3px;">
                                                    @if(is_null($financial_report_array['check_current_990N_included']))
                                                        Please Review
                                                    @elseif($financial_report_array['check_current_990N_included'] == 1)
                                                        990N is filed
                                                    @elseif($financial_report_array['check_current_990N_included'] == 0)
                                                        990N has not been filed
                                                    @else
                                                        Please Review
                                                    @endif
                                                </span>
                                            </strong><br>
                                        </div>

                                        <div class="flex-item2">
                                            Purchased membership pins or had leftovers:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_purchased_pins']) ? 'Please Review'
                                                : ( $financial_report_array ['check_purchased_pins'] == 0 ? 'NO' : ($financial_report_array ['check_purchased_pins'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Purchased MOMS Club merchandise:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_purchased_mc_merch']) ? 'Please Review'
                                                : ($financial_report_array ['check_purchased_mc_merch'] == 0 ? 'NO' : ($financial_report_array ['check_purchased_mc_merch'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Offered MOMS Club merchandise or info on how to buy to members:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_offered_merch']) ? 'Please Review'
                                                : ( $financial_report_array ['check_offered_merch'] == 0 ? 'NO' : ( $financial_report_array ['check_offered_merch'] == 1 ? 'YES' : 'Pleae Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Manual/by-laws made available to members:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_bylaws_available']) ? 'Please Review'
                                                : ( $financial_report_array ['check_bylaws_available'] == 0 ? 'NO' : ($financial_report_array ['check_bylaws_available'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Attended International Event (in person or virtual):&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_attended_training']) ? 'Please Review'
                                                : ($financial_report_array ['check_attended_training'] == 0 ? 'NO' : ($financial_report_array ['check_attended_training'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Sistered another chapter:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_sistered_another_chapter']) ? 'Please Review'
                                                : ($financial_report_array ['check_sistered_another_chapter'] == 0 ? 'NO' : ($financial_report_array ['check_sistered_another_chapter'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>



                                    </div>
                                    </div>
                                    <div class="col-md-12"><br></div>
                                    @php
                                        $yesBackground = '#28a745';  // Green background for "YES"
                                        $noBackground = '#dc3545';   // Red background for "NO"
                                    @endphp

                                    <div class="col-md-4" >
                                        Award #1 Status:&nbsp;&nbsp;&nbsp;
                                        <strong><span style="background-color: {{ is_null($financial_report_array['check_award_1_approved']) ? '#FFFFFF' : ($financial_report_array['check_award_1_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($financial_report_array['check_award_1_approved']) ? '#000000' : '#FFFFFF' }};
                                            padding: 2px 5px; border-radius: 3px;">
                                            {{ is_null($financial_report_array['check_award_1_approved']) ? 'N/A' : ($financial_report_array['check_award_1_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_1_approved'] == 1 ? 'YES' : 'N/A')) }}
                                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_1_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_1_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_1_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_1_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_1_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_1_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_1_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_1_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                        </span></strong><br>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4" >
                                        Award #2 Status:&nbsp;&nbsp;&nbsp;
                                        <strong><span style="background-color: {{ is_null($financial_report_array['check_award_2_approved']) ? '#FFFFFF' : ($financial_report_array['check_award_2_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($financial_report_array['check_award_2_approved']) ? '#000000' : '#FFFFFF' }};
                                            padding: 2px 5px; border-radius: 3px;">                                            {{ is_null($financial_report_array['check_award_2_approved']) ? 'N/A' : ($financial_report_array['check_award_2_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_2_approved'] == 1 ? 'YES' : 'N/A')) }}
                                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_2_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_2_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_2_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_2_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_2_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_2_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_2_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_2_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                        </span></strong><br>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4" >
                                        Award #3 Status:&nbsp;&nbsp;&nbsp;
                                        <strong><span style="background-color: {{ is_null($financial_report_array['check_award_3_approved']) ? '#FFFFFF' : ($financial_report_array['check_award_3_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($financial_report_array['check_award_3_approved']) ? '#000000' : '#FFFFFF' }};
                                            padding: 2px 5px; border-radius: 3px;">                                            {{ is_null($financial_report_array['check_award_3_approved']) ? 'N/A' : ($financial_report_array['check_award_3_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_3_approved'] == 1 ? 'YES' : 'N/A')) }}
                                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_3_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_3_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_3_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_3_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_3_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_3_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_3_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_3_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                        </span></strong><br>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4">
                                        Award #4 Status:&nbsp;&nbsp;&nbsp;
                                        <strong><span style="background-color: {{ is_null($financial_report_array['check_award_4_approved']) ? '#FFFFFF' : ($financial_report_array['check_award_4_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($financial_report_array['check_award_4_approved']) ? '#000000' : '#FFFFFF' }};
                                            padding: 2px 5px; border-radius: 3px;">                                            {{ is_null($financial_report_array['check_award_4_approved']) ? 'N/A' : ($financial_report_array['check_award_4_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_4_approved'] == 1 ? 'YES' : 'N/A')) }}
                                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_4_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_4_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_4_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_4_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_4_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_4_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_4_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_4_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                        </span></strong><br>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4">
                                        Award #5 Status:&nbsp;&nbsp;&nbsp;
                                        <strong><span style="background-color: {{ is_null($financial_report_array['check_award_5_approved']) ? '#FFFFFF' : ($financial_report_array['check_award_5_approved'] == 1 ? $yesBackground : $noBackground) }}; color: {{ is_null($financial_report_array['check_award_5_approved']) ? '#000000' : '#FFFFFF' }};
                                            padding: 2px 5px; border-radius: 3px;">                                            {{ is_null($financial_report_array['check_award_5_approved']) ? 'N/A' : ($financial_report_array['check_award_5_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_5_approved'] == 1 ? 'YES' : 'N/A')) }}
                                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_5_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_5_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_5_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_5_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_5_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_5_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_5_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_5_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                        </span></strong><br>
                                    </div>
                                </div>
                                <div class="col-md-12">
									<div class="col-md-12">
										<strong>Reviewer Notes Logged for this Report (not visible to chapter):</strong><br>
                                        <?php
                                        $financial_report_notes = [];
                                        for ($i = 1; $i <= 13; $i++) {
                                            $key = 'step_' . $i . '_notes_log';
                                            if (isset($financial_report_array[$key])) {
                                                $notes = explode("\n", $financial_report_array[$key]);
                                                $financial_report_notes = array_merge($financial_report_notes, $notes);
                                            }
                                        }

                                        echo empty($financial_report_notes) ? 'No notes logged for this report.' : implode('<br>', $financial_report_notes);
                                        ?>
									</div>
						<div class="col-sm-12">
                        <strong>Contact information for the person who completed the report:</strong><br>
                        Name -&nbsp;<?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_name'];}?><br>
                        Email -&nbsp;<a href="mailto:<?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_email'];}?>"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_email'];}?></a></p>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <?php if ($chapterDetails[0]->financial_report_received == 1 && $financial_report_array['reviewer_id'] == null): ?>
                                    <span style="display: inline; color: red;">No Reviewer Assigned - Select Reviewer before continuing to prevent errors.<br></span>
                                <?php endif; ?>
                                <label for="AssignedReviewer"><strong>Assigned Reviewer:</strong></label>
                                <select class="form-control" name="AssignedReviewer" id="AssignedReviewer" style="width: 250px;"  required>
                                    <option value="" style="display:none" disabled selected>Select a reviewer</option>
                                    @foreach($reviewerList as $pcl)
                                        <option value="{{$pcl['cid']}}" {{$financial_report_array['reviewer_id'] == $pcl['cid']  ? 'selected' : ''}} >{{$pcl['cname']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="emailMessageGroup" style="display: none;">
                                <label for="AssignedReviewer"><strong>Additional Email Message for Reviewer:</strong></label>
                                <textarea class="form-control" style="width:100%" rows="8" name="reviewer_email_message" id="reviewer_email_message"><?php echo $financial_report_array['reviewer_email_message']; ?></textarea>
                            </div>
                        </div>

                        <div class="card-body text-center">
                            <br>
                            <button type="submit" id="btn-step-14" class="btn bg-gradient-primary"><i class="fas fa-save"></i>&nbsp; Save</button>

                            @if ($financial_report_array['review_complete'] != "" && $submitted)
                                @if ($regionalCoordinatorCondition)
                                    <button type="button" class="btn bg-gradient-success" id="review-clear"><i class="fas fa-minus"></i>&nbsp; Clear Review Complete</button>
                                @else
                                    <button type="button" class="btn bg-gradient-success disabled"><i class="fas fa-minus"></i>&nbsp; Clear Review Complete</button>
                                @endif
                            @else
                                <button type="button" class="btn bg-gradient-success" id="review-complete"><i class="fas fa-check"></i>&nbsp; Mark as Review Complete</button>
                            @endif
                                <button type="button" class="btn bg-gradient-danger" id="unsubmit"><i class="fas fa-times"></i>&nbsp; UnSubmit Report</button>
                                <p style="color:red;"><b>"Mark as Review Complete" is for FINAL REVIEWER USE ONLY!</b></p>
					</div>
                </section>
				</div>
            </div>
        </div> --}}
		<!------End Step 14 ------>
			</div>
            <!-- end of accordion -->
        {{-- </div>
    </div> --}}

    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.col -->

<div class="card-body text-center">
    @if ($submitted)
        <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['financial_pdf_path']; ?>" class="btn bg-gradient-primary mb-2" >Download PDF Report</a>
    @else
        <a id="downloadPdfLink" href="#" class="btn bg-gradient-primary mb-2 disabled">Download PDF Report</a>
    @endif
    <br>
    <button type="button" id="back-list" class="btn bg-gradient-primary mb-2" onclick="window.location.href='{{ route('eoyreports.eoyfinancialreport') }}'">Back to Financial Report List</button>
    <button type="button" id="back-details" class="btn bg-gradient-primary mb-2" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chapterid]) }}'">Back to EOY Details</button>
</div>

            {{-- <div class="card-body text-center">
                <button type="submit" id="btn-step-14" class="btn bg-gradient-primary">Save Report Review</button>

                <a href="{{ route('eoyreports.eoyfinancialreport') }}" class="btn bg-gradient-primary">Back to Financial Report List</a>
            @if ($submitted)
                <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['financial_pdf_path']; ?>" class="btn bg-gradient-primary" >Download PDF Report</a>
            @else
                <a id="downloadPdfLink" href="#" class="btn bg-gradient-primary disabled">Download PDF Report</a>
            @endif --}}

        </form>
    {{-- </div> --}}
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
                window.location.href = "{{ url('/eoy/unsubmit/' . $chapterDetails[0]->id) }}";
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
                window.location.href = "{{ url('/eoy/clearreview/' . $chapterDetails[0]->id) }}";
            }
        });
    });
});

function showRosterUploadModal() {
    var chapter_id = "{{ $chapterid }}";

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
    var chapter_id = "{{ $chapterid }}";

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
    var chapter_id = "{{ $chapterid }}";

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
    var chapter_id = "{{ $chapterid }}";

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
        var submitted = @json($chapterDetails[0]->financial_report_complete);
        var received =  @json($chapterDetails[0]->financial_report_received);

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
            13: 'Awards',
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