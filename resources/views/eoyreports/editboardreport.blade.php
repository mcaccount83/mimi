@extends('layouts.coordinator_theme')

@section('page_title', 'Board Election Report')
@section('breadcrumb', 'EOY Board Report')

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
    <form id="board-info" method="POST" action='{{ route("eoyreports.updateboardreport", $chDetails->id) }}'>
        @csrf

        <input type="hidden" name="submit_type" id="submit_type" value="" />
        <input type="hidden" name="presID" id="presID" value="<?php echo $PresDetails->id; ?>" />
        <input type="hidden" name="avpID" id="avpID" value="<?php echo $AVPDetails->id; ?>" />
        <input type="hidden" name="mvpID" id="mvpID" value="<?php echo $MVPDetails->id; ?>" />
        <input type="hidden" name="trsID" id="trsID" value="<?php echo $TRSDetails->id; ?>" />
        <input type="hidden" name="secID" id="secID" value="<?php echo $SECDetails->id; ?>" />
        <input type="hidden" id="ch_state" value="{{$stateShortName}}">
        <input type="hidden" name="ch_hid_webstatus" id="ch_hid_webstatus" value="{{ $chDetails->website_status }}">
        <input type="hidden" id="ch_pre_email_chk" value="{{ $PresDetails->email }}">
        <input type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails->email }}">
        <input type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails->email }}">
        <input type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails->email }}">
        <input type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails->email }}">

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                <br>
                @if ($chDetails->documents->new_board_submitted != '1' )
                    <p><span style="color:#dc3545;">Board Election Report has NOT been submitted.</span><br>
                        <br>Chapter needs to complete and Submit the Board Election Report before new board members can be activated in MIMI.<br>
                        <br>Submission can be made by a Coordinator <strong>HERE</strong>.<br>
                    </p>
                @endif
                @if ($chDetails->documents->new_board_submitted == '1' && $chDetails->documents->new_board_active !='1')
                    <p><span style="color:#28a745;">Board Election Report HAS been submitted Submitted.</span><br>
                        <br>Changes can be made by a Coordinator <strong>HERE</strong> Prior to Activation.<br>
                        <br><span style="color:#dc3545;">Board Election Report has NOT been activated.</span><br>
                        <br>New Board Members will need to be activated by a Coordinator after July 1st! Once activated, they will have full MIMI Access.<br>
                        <br>Outgoing board members will have access to Financial Reports Only.<br>
                    </p>
                @endif
                @if ($chDetails->documents->new_board_active =='1')
                    <p><span style="color:#28a745;">Board Election Report HAS been Submitted and Activated!</span><br>
                        <br>New board members now have full MIMI Access.<br>
                        <br>Outgoing board members have access to Financial Reports Only.<br>
                        <br>Future board member changes can be made on the Chapter Details pages.<br>
                    </p>
                @endif

            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->

                @if ($chDetails->documents->new_board_active != '1')

                <div class="col-md-6">
                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">

                            <div class="card-body box-profile">
                                 <!-- /.card-header -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Board Members</h5>
                            <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 mb-1 col-form-label">President:</label>
                                    <div class="col-sm-5 mb-1">
                                    <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control" value="{{ $PresDetails->first_name }}" required placeholder="First Name" >
                                    </div>
                                    <div class="col-sm-5 mb-1">
                                    <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" value="{{ $PresDetails->last_name }}" required placeholder="Last Name">
                                    </div>
                                    <label class="col-sm-2 mb-1 col-form-label"></label>
                                    <div class="col-sm-5 mb-1">
                                    <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control"  value="{{ $PresDetails->email }}" required placeholder="Email Address" >
                                    </div>
                                    <div class="col-sm-5 mb-1">
                                    <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $PresDetails->phone }}" required placeholder="Phone Number" >
                                    </div>
                                    <label class="col-sm-2 mb-1 col-form-label"></label>
                                    <div class="col-sm-10 mb-1">
                                    <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" value="{{ $PresDetails->street_address }}" placeholder="Address" required >
                                    </div>
                                    <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                    <div class="col-sm-5 mb-1">
                                    <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control" value="{{ $PresDetails->city }}" placeholder="City" required >
                                    </div>
                                    <div class="col-sm-3 mb-1">
                                        <select name="ch_pre_state" id="ch_pre_state" class="form-control" style="width: 100%;" required >
                                            <option value="">Select State</option>
                                            @foreach($allStates as $state)
                                                <option value="{{$state->state_short_name}}"
                                                    @if($PresDetails->state == $state->state_short_name) selected @endif>
                                                    {{$state->state_long_name}}
                                                </option>
                                            @endforeach
                                            </select>
                                    </div>
                                    <div class="col-sm-2 mb-1">
                                        <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control" value="{{ $PresDetails->zip }}" placeholder="Zip" required >
                                    </div>
                                </div>

                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 mb-1 col-form-label">AVP:</label>
                                    <div class="col-sm-10 mt-1 custom-control custom-switch">
                                        <input type="checkbox" name="AVPVacant" id="AVPVacant" class="custom-control-input" {{$AVPDetails->id == '' ? 'checked' : ''}} >
                                        <label class="custom-control-label" for="AVPVacant">Vacant</label>
                                    </div>
                                    <label class="avp-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="avp-field col-sm-5 mb-1">
                                        <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" value="{{$AVPDetails->first_name != ''  ? $AVPDetails->first_name : ''}}" required placeholder="First Name" >
                                        </div>
                                        <div class="avp-field col-sm-5 mb-1">
                                        <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" value="{{$AVPDetails->last_name != ''  ? $AVPDetails->last_name : ''}}" required placeholder="Last Name" >
                                        </div>
                                        <label class="avp-field col-sm-2 mb-1 col-form-label"></label>
                                        <div class="avp-field col-sm-5 mb-1">
                                        <input type="text" name="ch_avp_email" id="ch_avp_email" class="form-control" value="{{$AVPDetails->email != ''  ? $AVPDetails->email : ''}}" required placeholder="Email Address" >
                                        </div>
                                        <div class="avp-field col-sm-5 mb-1">
                                        <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$AVPDetails->phone != ''  ? $AVPDetails->phone : ''}}" required placeholder="Phone Number" >
                                        </div>
                                        <label class="avp-field col-sm-2 mb-1 col-form-label"></label>
                                        <div class="avp-field col-sm-10 mb-1">
                                        <input type="text" name="ch_avp_street" id="ch_avp_street" class="form-control" value="{{$AVPDetails->street_address != ''  ? $AVPDetails->street_address : ''}}" Placeholder="Address" required >
                                        </div>
                                        <label class="avp-field col-sm-2 mb-1 col-form-label"><br></label>
                                        <div class="avp-field col-sm-5 mb-1">
                                        <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" value="{{$AVPDetails->city != ''  ? $AVPDetails->city : ''}}" Placeholder="City" required >
                                        </div>
                                        <div class="avp-field col-sm-3 mb-1">
                                            <select name="ch_avp_state" class="form-control" style="width: 100%;" required>
                                                <option value="">Select State</option>
                                                @foreach($allStates as $state)
                                                    <option value="{{$state->state_short_name}}"
                                                        @if($AVPDetails->state == $state->state_short_name) selected @endif>
                                                        {{$state->state_long_name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="avp-field col-sm-2 mb-1">
                                            <input type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" value="{{$AVPDetails->zip != ''  ? $AVPDetails->zip : ''}}" Placeholder="Zip" required >
                                        </div>
                                </div>

                                 <!-- /.form group -->
                                 <div class="form-group row">
                                    <label class="col-sm-2 mb-1 col-form-label">MVP:</label>
                                    <div class="col-sm-10 mt-1 custom-control custom-switch">
                                            <input type="checkbox" name="MVPVacant" id="MVPVacant" class="custom-control-input" {{$MVPDetails->id == '' ? 'checked' : ''}} >
                                            <label class="custom-control-label" for="MVPVacant">Vacant</label>
                                    </div>
                                    <label class="mvp-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="mvp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control" value="{{$MVPDetails->first_name != ''  ? $MVPDetails->first_name : ''}}" required placeholder="First Name" >
                                    </div>
                                    <div class="mvp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" value="{{$MVPDetails->last_name != ''  ? $MVPDetails->last_name : ''}}" required placeholder="Last Name" >
                                    </div>
                                    <label class="mvp-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="mvp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_mvp_email" id="ch_mvp_email" class="form-control" value="{{$MVPDetails->email != ''  ? $MVPDetails->email : ''}}" required placeholder="Email Address" >
                                    </div>
                                    <div class="mvp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$MVPDetails->phone != ''  ? $MVPDetails->phone : ''}}" required placeholder="Phone Number" >
                                    </div>
                                    <label class="mvp-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="mvp-field col-sm-10 mb-1">
                                    <input type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control" value="{{$MVPDetails->street_address != ''  ? $MVPDetails->street_address : ''}}" placeholder="Address" required >
                                    </div>
                                    <label class="mvp-field col-sm-2 mb-1 col-form-label"><br></label>
                                    <div class="mvp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" value="{{$MVPDetails->city != ''  ? $MVPDetails->city : ''}}" placeholder="City" required >
                                    </div>
                                    <div class="mvp-field col-sm-3 mb-1">
                                        <select name="ch_mvp_state" class="form-control" style="width: 100%;" required>
                                            <option value="">Select State</option>
                                            @foreach($allStates as $state)
                                                <option value="{{$state->state_short_name}}"
                                                    @if($MVPDetails->state == $state->state_short_name) selected @endif>
                                                    {{$state->state_long_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mvp-field col-sm-2 mb-1">
                                        <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" class="form-control" value="{{$MVPDetails->zip != ''  ? $MVPDetails->zip : ''}}" placeholder="Zip" required >
                                    </div>
                                </div>

                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 mb-1 col-form-label">Treasurer:</label>
                                    <div class="col-sm-10 mt-1 custom-control custom-switch">
                                            <input type="checkbox" name="TreasVacant" id="TreasVacant" class="custom-control-input" {{$TRSDetails->id == '' ? 'checked' : ''}} >
                                            <label class="custom-control-label" for="TreasVacant">Vacant</label>
                                    </div>
                                    <label class="treas-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="treas-field col-sm-5 mb-1">
                                    <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control"  value="{{$TRSDetails->first_name != ''  ? $TRSDetails->first_name : ''}}" required placeholder="First Name" >
                                    </div>
                                    <div class="treas-field col-sm-5 mb-1">
                                    <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" value="{{$TRSDetails->last_name != ''  ? $TRSDetails->last_name : ''}}" required placeholder="Last Name" >
                                    </div>
                                    <label class="treas-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="treas-field col-sm-5 mb-1">
                                    <input type="text" name="ch_trs_email" id="ch_trs_email" class="form-control" value="{{$TRSDetails->email != ''  ? $TRSDetails->email : ''}}" required placeholder="Email Address" >
                                    </div>
                                    <div class="treas-field col-sm-5 mb-1">
                                    <input type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$TRSDetails->phone != ''  ? $TRSDetails->phone : ''}}" required placeholder="Phone Number" >
                                    </div>
                                    <label class="treas-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="treas-field col-sm-10 mb-1">
                                    <input type="text" name="ch_trs_street" id="ch_trs_street" class="form-control" value="{{$TRSDetails->street_address != ''  ? $TRSDetails->street_address : ''}}" placeholder="Address" required >
                                    </div>
                                    <label class="treas-field col-sm-2 mb-1 col-form-label"><br></label>
                                    <div class="treas-field col-sm-5 mb-1">
                                    <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" value="{{$TRSDetails->city != ''  ? $TRSDetails->city : ''}}" placeholder="City" required >
                                    </div>
                                    <div class="treas-field col-sm-3 mb-1">
                                        <select name="ch_trs_state" class="form-control" style="width: 100%;" required>
                                            <option value="">Select State</option>
                                            @foreach($allStates as $state)
                                                <option value="{{$state->state_short_name}}"
                                                    @if($TRSDetails->state == $state->state_short_name) selected @endif>
                                                    {{$state->state_long_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="treas-field col-sm-2 mb-1">
                                        <input type="text" name="ch_trs_zip" id="ch_trs_zip" class="form-control" value="{{$TRSDetails->zip != ''  ? $TRSDetails->zip : ''}}" placeholder="Zip" required >
                                    </div>
                                </div>

                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 mb-1 col-form-label">Secretary:</label>
                                    <div class="col-sm-10 mt-1 custom-control custom-switch">
                                            <input type="checkbox" name="SecVacant" id="SecVacant" class="custom-control-input" {{$SECDetails->id == '' ? 'checked' : ''}}>
                                            <label class="custom-control-label" for="SecVacant">Vacant</label>
                                    </div>
                                    <label class="sec-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="sec-field col-sm-5 mb-1">
                                    <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" value="{{$SECDetails->first_name != ''  ? $SECDetails->first_name : ''}}" required placeholder="First Name" >
                                    </div>
                                    <div class="sec-field col-sm-5 mb-1">
                                    <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" value="{{$SECDetails->last_name != ''  ? $SECDetails->last_name : ''}}" required placeholder="Last Name" >
                                    </div>
                                    <label class="sec-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="sec-field col-sm-5 mb-1">
                                    <input type="text" name="ch_sec_email" id="ch_sec_email" class="form-control" value="{{$SECDetails->email != ''  ? $SECDetails->email : ''}}" required placeholder="Email Address" >
                                    </div>
                                    <div class="sec-field col-sm-5 mb-1">
                                    <input type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$SECDetails->phone != ''  ? $SECDetails->phone : ''}}" required placeholder="Phone Number" >
                                    </div>
                                    <label class="sec-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="sec-field col-sm-10 mb-1">
                                    <input type="text" name="ch_sec_street" id="ch_sec_street" class="form-control" value="{{$SECDetails->street_address != ''  ? $SECDetails->street_address : ''}}" placeholder="Address" required >
                                    </div>
                                    <label class="sec-field col-sm-2 mb-1 col-form-label"><br></label>
                                    <div class="sec-field col-sm-5 mb-1">
                                    <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control"  value="{{$SECDetails->city != ''  ? $SECDetails->city : ''}}" placeholder="City" required >
                                    </div>
                                    <div class="sec-field col-sm-3 mb-1">
                                        <select name="ch_sec_state" class="form-control" style="width: 100%;" required>
                                            <option value="">Select State</option>
                                            @foreach($allStates as $state)
                                                <option value="{{$state->state_short_name}}"
                                                    @if($SECDetails->state == $state->state_short_name) selected @endif>
                                                    {{$state->state_long_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="sec-field col-sm-2 mb-1">
                                        <input type="text" name="ch_sec_zip" id="ch_sec_zip" class="form-control" value="{{$SECDetails->zip != ''  ? $SECDetails->zip : ''}}" placeholder="Zip" required >
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->

                    <div class="col-md-3">
                        <!-- Profile Image -->
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Boundaries listed in MIMI (used for Inquiries)</label>
                                            <div>{{ $chDetails->territory }}</div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">Are your listed boundaries correct?<span class="field-required">*</span></label>
                                        <div class="col-sm-12 row ml-2 mb-2">
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" id="BoundaryStatusYes" name="BoundaryStatus" value="0" {{ $chDetails->boundary_issues === 0 ? 'checked' : '' }} onChange="ShowBoundaryError()">
                                                <label class="form-check-label" for="BoundaryStatusYes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" id="BoundaryStatusNo" name="BoundaryStatus" value="1" {{ $chDetails->boundary_issues === 1 ? 'checked' : '' }} onChange="ShowBoundaryError()">
                                                <label class="form-check-label" for="BoundaryStatusNo">No</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12" id="divBoundaryIssue">
                                            <label for="BoundaryIssue">Please indicate which part of the Boundaries not NOT match our records<span class="field-required">*</span></label>
                                            <input type="text" rows="2"class="form-control" name="BoundaryIssue" id="BoundaryIssue" value="{{ $chDetails->boundary_issue_notes }}" ></input>
                                        </div>
                                    </div>

                                <div class="form-group row">
                                    <label class="col-sm-12 col-form-label">Website:</label>
                                    <div class="col-sm-12 mb-2">
                                        <input type="text" name="ch_website" id="ch_website" class="form-control"
                                               value="{{$chDetails->website_url}}"
                                               placeholder="Chapter Website">
                                    </div>
                            <div class="col-sm-8">
                                <select name="ch_webstatus" id="ch_webstatus" class="form-control" style="width: 100%;" required>
                                    <option value="">Select Status</option>
                                    @foreach($allWebLinks as $status)
                                        <option value="{{$status->id}}"
                                            @if($chDetails->website_status == $status->id) selected @endif>
                                            {{$status->link_status}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-12 col-form-label">Social Media:</label>
                            <div class="col-sm-12 mb-2">
                            <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chDetails->egroup }}"  placeholder="Forum/Group/App" >
                            </div>
                            <div class="col-sm-12 mb-2">
                            <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chDetails->social1 }}" placeholder="Facebook"  >
                            </div>

                            <div class="col-sm-12 mb-2">
                                <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chDetails->social2 }}"  placeholder="Twitter" >
                            </div>
                            <div class="col-sm-12 mb-2">
                                <input type="text" name="ch_social3" id="ch_social3" class="form-control" value="{{ $chDetails->social3 }}"  placeholder="Instagram" >
                            </div>
                        </div>

                            </div>
                        <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->

            @else
                <div class="col-md-10"><br></div>
            @endif

        </div>

            <div class="card-body text-center">
                    <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.eoyboardreport') }}'"><i class="fas fa-reply mr-2"></i>Back to Board Eletion Report</button>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to EOY Details</button>

                    @if ($chDetails->documents->new_board_active != '1')
                        @if ($chDetails->documents->new_board_submitted != 1)
                            <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return validateBeforeSubmit(true)"><i class="fas fa-mail-forward mr-2" ></i>Submit</button>
                        @else
                            <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return validateBeforeSubmit(true)"><i class="fas fa-save mr-2" ></i>Save</button>
                        @endif
                        @if ($chDetails->documents->new_board_submitted == '1' )
                        <button type="button" id="board-active" class="btn bg-gradient-primary mb-3"><i class="fas fa-play mr-2" ></i>Ativate Board</button>
                        @endif
                    @endif
				</form>
            </div>

    </div>
    <!-- /.container- -->
@endsection
@section('customscript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const websiteField = document.getElementById("ch_website");
    const statusField = document.getElementById("ch_webstatus");

    websiteField.addEventListener("input", function() {
        // Enable options 2 and 3, disable options 1 and 2
        Array.from(statusField.options).forEach(option => {
            if (["0", "1"].includes(option.value)) {
                option.disabled = true;
            } else if (["2", "3"].includes(option.value)) {
                option.disabled = false;
            }
        });
    });
});

// Function to handle show/hide logic for vacant checkboxes
function handleVacantCheckbox(checkboxId, fieldClass) {
    var fields = $("." + fieldClass);

    $("#" + checkboxId).change(function () {
        if ($(this).prop("checked")) {
            fields.hide().find('input, select, textarea').prop('required', false).val(null);
        } else {
            fields.show().find('input, select, textarea').prop('required', true);
        }
    });

    // Initial show/hide logic on page load
    if ($("#" + checkboxId).prop("checked")) {
        fields.hide().find('input, select, textarea').prop('required', false).val(null);
    } else {
        fields.show().find('input, select, textarea').prop('required', true);
    }
}

// Apply the logic for each checkbox with a specific class
handleVacantCheckbox("MVPVacant", "mvp-field");
handleVacantCheckbox("AVPVacant", "avp-field");
handleVacantCheckbox("SecVacant", "sec-field");
handleVacantCheckbox("TreasVacant", "treas-field");

//Boundary Visibility
ShowBoundaryError();

function ShowBoundaryError() {
    var selectedValue = document.querySelector('input[name="BoundaryStatus"]:checked').value;

    if (selectedValue == "1") {
        $('#BoundaryIssue').addClass('tx-cls');
        document.getElementById("divBoundaryIssue").style.display = 'block';
    } else {
        $('#BoundaryIssue').removeClass('tx-cls');
        document.getElementById("divBoundaryIssue").style.display = 'none';
    }
}

function validateBeforeSubmit() {
    // Check if a boundary status is selected
    const selectedBoundary = document.querySelector('input[name="BoundaryStatus"]:checked');

    if (!selectedBoundary) {
        Swal.fire({
            icon: 'error',
            title: 'Boundary Issue Required',
            text: 'Please indicate whether your listed boundaries are correct.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-danger'
            }
        });
        return false;
    }

    // If "No" (value 1) is selected, check if the issue field is filled out
    if (selectedBoundary.value === "1") {
        const boundaryIssue = document.getElementById("BoundaryIssue");
        if (!boundaryIssue.value.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Boundary Issue Required',
                text: 'Please indicate which part of the Boundaries do NOT match your records.',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn-sm btn-danger'
                }
            });
            boundaryIssue.focus();
            return false;
        }
    }

    // Get the values from the input fields
    const emails = [
        $('#ch_pre_email').val().trim(),
        $('#ch_avp_email').val().trim(),
        $('#ch_mvp_email').val().trim(),
        $('#ch_trs_email').val().trim(),
        $('#ch_sec_email').val().trim()
    ];

    // Filter out empty emails and check for duplicates
    const emailSet = new Set();
    const duplicateEmails = [];

    emails.forEach(email => {
        if (email !== '') {
            if (emailSet.has(email)) {
                if (!duplicateEmails.includes(email)) {
                    duplicateEmails.push(email);
                }
            } else {
                emailSet.add(email);
            }
        }
    });

    // If duplicates are found, show an alert
    if (duplicateEmails.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Duplicate Emails Found',
            html: 'The following emails are duplicates: <br>' + duplicateEmails.join('<br>') + '<br>Please correct them before submitting.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-danger'
            }
        });
        return false;
    }

    return true;
}

</script>
@endsection
