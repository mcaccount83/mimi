@extends('layouts.coordinator_theme')

@section('page_title', 'EOY Details')
@section('breadcrumb', 'EOY Chapter Awards')

<style>

.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

.align-bottom {
        display: flex;
        align-items: flex-end;
    }

    .align-middle {
        display: flex;
        align-items: center;
    }

</style>

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("eoyreports.updateawards", $chapterList[0]->id) }}'>
        @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chapterList[0]->name }}, {{$chapterList[0]->statename}}</h3>
                <p class="text-center">{{ $chapterList[0]->confname }} Conference, {{ $chapterList[0]->regname }} Region

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>New Board Submitted:</label>
                                <span class="float-right">{{ $chapterList[0]->new_board_submitted == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>New Board Activated:</label>
                                <span class="float-right">{{ $chapterList[0]->new_board_active == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Financial Report Received</label>
                                <span class="float-right">{{ $chapterList[0]->financial_report_received == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Financial Review Complete:</label>
                                <span class="float-right">{{ $chapterList[0]->financial_report_complete == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Report Extension Given:</label>
                                <span class="float-right">{{ $chapterList[0]->report_extension == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>990N Verifed on irs.gov:</label>
                                <span class="float-right">{{ $financial_report_array->check_current_990N_verified_IRS == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Assigned Reviewer:</label>
                                    @if($financial_report_array->reviewer_id != null)
                                        <span class="float-right">{{ $chapterList[0]->rfname }} {{ $chapterList[0]->rlname }}</span>
                                    @else
                                        No Reviewer Assigned
                                    @endif
                            </div>
                        </div>
                </li>

                    <input type="hidden" id="ch_primarycor" value="{{ $chapterList[0]->primary_coordinator_id }}">
                    <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
                </ul>
                <div class="text-center">
                    @if ($chapterList[0]->is_active == 1 )
                        <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                    @else
                        <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                        Disband Date: <span class="date-mask">{{ $chapterList[0]->zap_date }}</span><br>
                        {{ $chapterList[0]->disband_reason }}
                    @endif
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                <h3 class="profile-username">{{ (date('Y') - 1) . '-' . date('Y') }} Chapter Awards</h3>
                    <!-- /.card-header -->

                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">Award #1:</label>
                            <div class="col-sm-4">
                                <select class="form-control" id="checkNominationType1" name="checkNominationType1">
                                    <option value="" style="display:none" disabled selected>Select an award type</option>
                                    <option value="1" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                    <option value="2" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                    <option value="3" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                    <option value="4" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                    <option value="5" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                    <option value="6" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                    <option value="7" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                </select>
                            </div>

                            <label class="col-sm-2 col-form-label ml-5">Approved:</label>
                            <div class="col-sm-4 d-flex align-items-center">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="checkAward1Approved" id="checkAward1Approved" class="custom-control-input"
                                    {{$financial_report_array->check_award_1_approved == 1 ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="checkAward1Approved"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">Description:</label>
                            <div class="col-sm-10">
                            <textarea class="form-control" rows="3" id="AwardDesc1" name="AwardDesc1"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_1_outstanding_project_desc'];}?></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <hr>

                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">Award #2:</label>
                            <div class="col-sm-4">
                                <select class="form-control" id="checkNominationType2" name="checkNominationType2" >
                                    <option value="" style="display:none" disabled selected>Select an award type</option>
                                    <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                    <option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                    <option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                    <option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                    <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                    <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                    <option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                </select>
                            </div>

                            <label class="col-sm-2 col-form-label ml-5">Approved:</label>
                            <div class="col-sm-4 d-flex align-items-center">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="checkAward2Approved" id="checkAward2Approved" class="custom-control-input"
                                    {{$financial_report_array->check_award_2_approved == 1 ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="checkAward2Approved"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">Description:</label>
                            <div class="col-sm-10">
                            <textarea class="form-control" rows="3" id="AwardDesc2" name="AwardDesc2">{{$financial_report_array['award_2_outstanding_project_desc']}}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <hr>

                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">Award #3:</label>
                            <div class="col-sm-4">
                                <select class="form-control" id="checkNominationType3" name="checkNominationType3"  >
                                    <option value="" style="display:none" disabled selected>Select an award type</option>
                                    <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                    <option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                    <option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                    <option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                    <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                    <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                    <option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                </select>
                            </div>

                            <label class="col-sm-2 col-form-label ml-5">Approved:</label>
                            <div class="col-sm-4 d-flex align-items-center">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="checkAward3Approved" id="checkAward3Approved" class="custom-control-input"
                                    {{$financial_report_array->check_award_3_approved == 1 ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="checkAward3Approved"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">Description:</label>
                            <div class="col-sm-10">
                            <textarea class="form-control" rows="3" id="AwardDesc3" name="AwardDesc3">{{$financial_report_array['award_3_outstanding_project_desc']}}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <hr>

                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">Award #4:</label>
                            <div class="col-sm-4">
                                <select class="form-control" id="checkNominationType4" name="checkNominationType4"  >
                                    <option value="" style="display:none" disabled selected>Select an award type</option>
                                    <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                    <option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                    <option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                    <option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                    <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                    <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                    <option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                </select>
                            </div>

                            <label class="col-sm-2 col-form-label ml-5">Approved:</label>
                            <div class="col-sm-4 d-flex align-items-center">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="checkAward4Approved" id="checkAward4Approved" class="custom-control-input"
                                    {{$financial_report_array->check_award_4_approved == 1 ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="checkAward4Approved"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">Description:</label>
                            <div class="col-sm-10">
                            <textarea class="form-control" rows="3" id="AwardDesc4" name="AwardDesc4">{{$financial_report_array['award_4_outstanding_project_desc']}}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <hr>

                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">Award #5:</label>
                            <div class="col-sm-4">
                                <select class="form-control" id="checkNominationType5" name="checkNominationType5"  >
                                    <option value="" style="display:none" disabled selected>Select an award type</option>
                                    <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                    <option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                    <option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                    <option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                    <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                    <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                    <option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                </select>
                            </div>

                            <label class="col-sm-2 col-form-label ml-5">Approved:</label>
                            <div class="col-sm-4 d-flex align-items-center">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="checkAward5Approved" id="checkAward5Approved" class="custom-control-input"
                                    {{$financial_report_array->check_award_5_approved == 1 ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="checkAward5Approved"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">Description:</label>
                            <div class="col-sm-10">
                            <textarea class="form-control" rows="3" id="AwardDesc5" name="AwardDesc5">{{$financial_report_array['award_5_outstanding_project_desc']}}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->

                  </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center">
                @if ($coordinatorCondition)
                    <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save Chapter Awards</button>
                    <br>
                    @endif
                    <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.eoyawards') }}'"><i class="fas fa-reply mr-2"></i>Back to Awards Report</button>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chapterList[0]->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to EOY Details</button>

            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>
    var $chIsActive = @json($chIsActive);
    var $einCondition = @json($einCondition);
    var $inquiriesCondition = @json($inquiriesCondition);
    var $chPCid = @json($chPCid);
    var $coordId = @json($coordId);
    var $corConfId = @json($corConfId);

$(document).ready(function () {
    // Disable fields for chapters that are not active
    if (($chIsActive != 1)) {
        $('input, select, textarea, button').prop('disabled', true);

        $('a[href^="mailto:"]').each(function() {
            $(this).addClass('disabled-link'); // Add disabled class for styling
            $(this).attr('href', 'javascript:void(0);'); // Prevent navigation
            $(this).on('click', function(e) {
                e.preventDefault(); // Prevent link click
            });
        });

        // Re-enable the specific "Back" buttons
        $('#back-eoy').prop('disabled', false);
    }
});

$(document).ready(function() {
    function loadCoordinatorList(corId) {
        if (corId != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list") }}' + '/' + corId,
                type: "GET",
                success: function(result) {
                    $("#display_corlist").html(result);
                },
                error: function (jqXHR, exception) {
                    console.log("Error: ", jqXHR, exception);
                }
            });
        }
    }

    var selectedCorId = $("#ch_primarycor").val();
    loadCoordinatorList(selectedCorId);

    $("#ch_primarycor").change(function() {
        var selectedValue = $(this).val();
        loadCoordinatorList(selectedValue);
    });
});


</script>
@endsection
