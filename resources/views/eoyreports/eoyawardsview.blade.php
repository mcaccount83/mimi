@extends('layouts.coordinator_theme')

@section('content')
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Awards&nbsp;<small>(Add)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Awards</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" action='{{ route("eoyreports.eoyupdateawards",$chapterList[0]->id) }}'>
    @csrf
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Chapter</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>MOMS Club of</label>
                <input type="text" name="ch_name" class="form-control"  required value="{{ $chapterList[0]->name }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>State</label>
                <select id="ch_state" name="ch_state" class="form-control select2-bs4" style="width: 100%;" required disabled>
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_state" value="{{ $chapterList[0]->state }}">
              </div>
              </div>
              <div class="col-sm-4">
              <div class="form-group">
                <label>Region</label> <span class="field-required">*</span>
                <select id="ch_region" name="ch_region" class="form-control select2-bs4" style="width: 100%;" required disabled>
                  <option value="">Select Region</option>
                    @foreach($regionList as $rl)
                      <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}} >{{$rl->long_name}} </option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_region" value="{{ $chapterList[0]->region }}">
              </div>
              </div>
            </div>
        </div>

                <div class="card-header">
                    <h3 class="card-title">Awards</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
          <!-- /.form group -->
                    <div class="col-md-12">
                        <div class="form-group col-md-4">
                            <label for="NominationType1">Award #1:</label>
                            <select class="form-control" id="checkNominationType1" name="checkNominationType1"  >
                                <option value="" style="display:none" disabled selected>Select an award type</option>
                                <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                <option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                <option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                <option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                <option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Status:<span class="field-required">*</span></label>
                                <select id="checkAward1Approved" name="checkAward1Approved" class="form-control select2-bs4" style="width: 150px;">
                                    <option value="" {{ is_null($financial_report_array->check_award_1_approved) ? 'selected' : '' }} >Please Select</option>
                                    <option value="0" {{$financial_report_array->check_award_1_approved === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_award_1_approved == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-sm-12" style="margin-bottom: 30px;">
                            Description:
                                <textarea class="form-control" rows="3" id="AwardDesc1" name="AwardDesc1"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_1_outstanding_project_desc'];}?></textarea>
                       </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group col-md-4">
                            <label for="NominationType2">Award #2:</label>
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
                        <div class="col-md-6">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Status:<span class="field-required">*</span></label>
                                <select id="checkAward2Approved" name="checkAward2Approved" class="form-control select2-bs4" style="width: 150px;" >
                                    <option value="" {{ is_null($financial_report_array->check_award_2_approved) ? 'selected' : '' }} >Please Select</option>
                                    <option value="0" {{$financial_report_array->check_award_2_approved === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_award_2_approved == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-sm-12" style="margin-bottom: 30px;">
                            Description:
                                <textarea class="form-control" rows="3" id="AwardDesc2" name="AwardDesc2"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_2_outstanding_project_desc'];}?></textarea>
                       </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group col-md-4">
                            <label for="NominationType3">Award #3:</label>
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
                        <div class="col-md-6">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Status:<span class="field-required">*</span></label>
                                <select id="checkAward3Approved" name="checkAward3Approved" class="form-control select2-bs4" style="width: 150px;" >
                                    <option value="" {{ is_null($financial_report_array->check_award_3_approved) ? 'selected' : '' }} >Please Select</option>
                                    <option value="0" {{$financial_report_array->check_award_3_approved === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_award_3_approved == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                            <div class="col-sm-12" style="margin-bottom: 30px;">
                                Description:
                                <textarea class="form-control" rows="3" id="AwardDesc3" name="AwardDesc3"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_3_outstanding_project_desc'];}?></textarea>
                        </div>
                    </div>

                    <div class="col-md-12" >
                        <div class="form-group col-md-4">
                            <label for="NominationType4">Award #4:</label>
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
                        <div class="col-md-6">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Status:<span class="field-required">*</span></label>
                                <select id="checkAward4Approved" name="checkAward4Approved" class="form-control select2-bs4" style="width: 150px;" >
                                    <option value="" {{ is_null($financial_report_array->check_award_4_approved) ? 'selected' : '' }} >Please Select</option>
                                    <option value="0" {{$financial_report_array->check_award_4_approved === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_award_4_approved == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                            <div class="col-sm-12" style="margin-bottom: 30px;">
                                Description:
                                <textarea class="form-control" rows="3" id="AwardDesc4" name="AwardDesc4"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_4_outstanding_project_desc'];}?></textarea>
                        </div>
                    </div>

                    <div class="col-md-12"  >
                        <div class="form-group col-md-4">
                            <label for="NominationType5">Award #5:</label>
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
                        <div class="col-md-6">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Status:<span class="field-required">*</span></label>
                                <select id="checkAward5Approved" name="checkAward5Approved" class="form-control select2-bs4" style="width: 150px;" >
                                    <option value="" {{ is_null($financial_report_array->check_award_5_approved) ? 'selected' : '' }} >Please Select</option>
                                    <option value="0" {{$financial_report_array->check_award_5_approved === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_award_5_approved == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                            <div class="col-sm-12" style="margin-bottom: 30px;">
                                Description:
                                <textarea class="form-control" rows="3" id="AwardDes5" name="AwardDesc5"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_5_outstanding_project_desc'];}?></textarea>
                           </div>
                    </div>
                </div>
            </div>


              <div class="card-body text-center">
                    <button type="submit" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp;&nbsp;&nbsp;Save</button>
                    {{-- <button type="button" class="btn bg-gradient-primary" onclick="window.history.go(-1); return false;"><i class="fas fa-reply" ></i>&nbsp;&nbsp;&nbsp;Back</button> --}}
                    <a href="{{ route('eoyreports.eoyawards') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
              </div>
              </div>
            </div>
        </div>
        </section>
    </form>
@endsection

@section('customscript')
<script>

</script>
@endsection

