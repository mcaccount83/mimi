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
    <form id="board-info" method="POST" action='{{ route("eoyreports.updateboardreport",$chapterList[0]->id) }}'>
        @csrf

        <input type="hidden" name="submit_type" id="submit_type" value="" />
        <input type="hidden" name="presID" id="presID" value="<?php echo $PREDetails[0]->ibd_id; ?>" />
        <input type="hidden" name="avpID" id="avpID" value="<?php echo $AVPDetails[0]->ibd_id; ?>" />
        <input type="hidden" name="mvpID" id="mvpID" value="<?php echo $MVPDetails[0]->ibd_id; ?>" />
        <input type="hidden" name="trsID" id="trsID" value="<?php echo $TRSDetails[0]->ibd_id; ?>" />
        <input type="hidden" name="secID" id="secID" value="<?php echo $SECDetails[0]->ibd_id; ?>" />
        <input type="hidden" id="ch_state" value="{{$chapterState}}">
        <input type="hidden" name="ch_hid_webstatus" id="ch_hid_webstatus" value="{{ $chapterList[0]->website_status }}">
        <input type="hidden" id="ch_pre_email_chk" value="{{ $PREDetails[0]->pre_email }}">
        <input type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails[0]->avp_email }}">
        <input type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails[0]->mvp_email }}">
        <input type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails[0]->trs_email }}">
        <input type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails[0]->sec_email }}">

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chapterList[0]->name }}, {{$chapterState}}</h3>
                <br>
                @if ($chapterList[0]->new_board_submitted != '1' )
                    <p><span style="color:#dc3545;">Board Election Report has NOT been submitted.</span><br>
                        <br>Chapter needs to complete and Submit the Board Election Report before new board members can be activated in MIMI.<br>
                        <br>Submission can be made by a Coordinator <strong>HERE</strong>.<br>
                    </p>
                @endif
                @if ($chapterList[0]->new_board_submitted == '1' && $chapterList[0]->new_board_active !='1')
                    <p><span style="color:#28a745;">Board Election Report HAS been submitted Submitted.</span><br>
                        <br>Changes can be made by a Coordinator <strong>HERE</strong> Prior to Activation.<br>
                        <br><span style="color:#dc3545;">Board Election Report has NOT been activated.</span><br>
                        <br>New Board Members will need to be activated by a Coordinator after July 1st! Once activated, they will have full MIMI Access.<br>
                        <br>Outgoing board members will have access to Financial Reports Only.<br>
                    </p>
                @endif
                @if ($chapterList[0]->new_board_active =='1')
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


                @if ($chapterList[0]->new_board_active != '1')

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
                                    <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control" value="{{ $PREDetails[0]->pre_fname }}" required placeholder="First Name" >
                                    </div>
                                    <div class="col-sm-5 mb-1">
                                    <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" value="{{ $PREDetails[0]->pre_lname }}" required placeholder="Last Name">
                                    </div>
                                    <label class="col-sm-2 mb-1 col-form-label"></label>
                                    <div class="col-sm-5 mb-1">
                                    <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control"  value="{{ $PREDetails[0]->pre_email }}" required placeholder="Email Address" >
                                    </div>
                                    <div class="col-sm-5 mb-1">
                                    <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $PREDetails[0]->pre_phone }}" required placeholder="Phone Number" >
                                    </div>
                                    <label class="col-sm-2 mb-1 col-form-label"></label>
                                    <div class="col-sm-10 mb-1">
                                    <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" placeholder="Address" value="{{ $PREDetails[0]->pre_addr }}" required >
                                    </div>
                                    <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                    <div class="col-sm-5 mb-1">
                                    <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control" placeholder="City" value="{{ $PREDetails[0]->pre_city }}" required >
                                    </div>
                                    <div class="col-sm-3 mb-1">
                                        <select name="ch_pre_state" id="ch_pre_state" class="form-control" style="width: 100%;" required >
                                            <option value="">Select State</option>
                                                @foreach($stateArr as $state)
                                                  <option value="{{$state->state_short_name}}" {{$PREDetails[0]->pre_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="col-sm-2 mb-1">
                                        <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control" value="{{ $PREDetails[0]->pre_zip }}" placeholder="Zip" required >
                                    </div>
                                </div>

                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 mb-1 col-form-label">AVP:</label>
                                    <div class="col-sm-10 mt-1 custom-control custom-switch">
                                        <input type="checkbox" name="AVPVacant" id="AVPVacant" class="custom-control-input" {{$AVPDetails[0]->avp_fname == '' ? 'checked' : ''}} >
                                        <label class="custom-control-label" for="AVPVacant">Vacant</label>
                                    </div>
                                    <label class="avp-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="avp-field col-sm-5 mb-1">
                                        <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" value="{{$AVPDetails[0]->avp_fname != ''  ? $AVPDetails[0]->avp_fname : ''}}" required placeholder="First Name" >
                                        </div>
                                        <div class="avp-field col-sm-5 mb-1">
                                        <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" value="{{$AVPDetails[0]->avp_lname != ''  ? $AVPDetails[0]->avp_lname : ''}}" required placeholder="Last Name" >
                                        </div>
                                        <label class="avp-field col-sm-2 mb-1 col-form-label"></label>
                                        <div class="avp-field col-sm-5 mb-1">
                                        <input type="text" name="ch_avp_email" id="ch_avp_email" class="form-control" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" required placeholder="Email Address" >
                                        </div>
                                        <div class="avp-field col-sm-5 mb-1">
                                        <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$AVPDetails[0]->avp_phone != ''  ? $AVPDetails[0]->avp_phone : ''}}" required placeholder="Phone Number" >
                                        </div>
                                        <label class="avp-field col-sm-2 mb-1 col-form-label"></label>
                                        <div class="avp-field col-sm-10 mb-1">
                                        <input type="text" name="ch_avp_street" id="ch_avp_street" class="form-control" value="{{$AVPDetails[0]->avp_addr != ''  ? $AVPDetails[0]->avp_addr : ''}}"  required >
                                        </div>
                                        <label class="avp-field col-sm-2 mb-1 col-form-label"><br></label>
                                        <div class="avp-field col-sm-5 mb-1">
                                        <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" value="{{$AVPDetails[0]->avp_city != ''  ? $AVPDetails[0]->avp_city : ''}}"  required >
                                        </div>
                                        <div class="avp-field col-sm-3 mb-1">
                                            <select name="ch_avp_state" class="form-control" style="width: 100%;" required>
                                                <option value="">Select State</option>
                                                    @foreach($stateArr as $state)
                                                        <option value="{{$state->state_short_name}}" {{$AVPDetails[0]->avp_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                        <div class="avp-field col-sm-2 mb-1">
                                            <input type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" value="{{$AVPDetails[0]->avp_zip != ''  ? $AVPDetails[0]->avp_zip : ''}}"  required >
                                        </div>
                                </div>

                                 <!-- /.form group -->
                                 <div class="form-group row">
                                    <label class="col-sm-2 mb-1 col-form-label">MVP:</label>
                                    <div class="col-sm-10 mt-1 custom-control custom-switch">
                                            <input type="checkbox" name="MVPVacant" id="MVPVacant" class="custom-control-input" {{$MVPDetails[0]->mvp_fname == '' ? 'checked' : ''}} >
                                            <label class="custom-control-label" for="MVPVacant">Vacant</label>
                                    </div>
                                    <label class="mvp-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="mvp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control" value="{{$MVPDetails[0]->mvp_fname != ''  ? $MVPDetails[0]->mvp_fname : ''}}" required placeholder="First Name" >
                                    </div>
                                    <div class="mvp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" value="{{$MVPDetails[0]->mvp_lname != ''  ? $MVPDetails[0]->mvp_lname : ''}}" required placeholder="Last Name" >
                                    </div>
                                    <label class="mvp-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="mvp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_mvp_email" id="ch_mvp_email" class="form-control" value="{{$MVPDetails[0]->mvp_email != ''  ? $MVPDetails[0]->mvp_email : ''}}" required placeholder="Email Address" >
                                    </div>
                                    <div class="mvp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$MVPDetails[0]->mvp_phone != ''  ? $MVPDetails[0]->mvp_phone : ''}}" required placeholder="Phone Number" >
                                    </div>
                                    <label class="mvp-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="mvp-field col-sm-10 mb-1">
                                    <input type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control" placeholder="Address" value="{{$MVPDetails[0]->mvp_addr != ''  ? $MVPDetails[0]->mvp_addr : ''}}" required >
                                    </div>
                                    <label class="mvp-field col-sm-2 mb-1 col-form-label"><br></label>
                                    <div class="mvp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" placeholder="City" value="{{$MVPDetails[0]->mvp_city != ''  ? $MVPDetails[0]->mvp_city : ''}}" required >
                                    </div>
                                    <div class="mvp-field col-sm-3 mb-1">
                                        <select name="ch_mvp_state" class="form-control" style="width: 100%;" required>
                                            <option value="">Select State</option>
                                                @foreach($stateArr as $state)
                                                <option value="{{$state->state_short_name}}" {{$MVPDetails[0]->mvp_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                    <div class="mvp-field col-sm-2 mb-1">
                                        <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" class="form-control" placeholder="Zip" value="{{$MVPDetails[0]->mvp_zip != ''  ? $MVPDetails[0]->mvp_zip : ''}}" required >
                                    </div>
                                </div>

                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 mb-1 col-form-label">Treasurer:</label>
                                    <div class="col-sm-10 mt-1 custom-control custom-switch">
                                            <input type="checkbox" name="TreasVacant" id="TreasVacant" class="custom-control-input" {{$TRSDetails[0]->trs_fname == '' ? 'checked' : ''}} >
                                            <label class="custom-control-label" for="TreasVacant">Vacant</label>
                                    </div>
                                    <label class="treas-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="treas-field col-sm-5 mb-1">
                                    <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control"  value="{{$TRSDetails[0]->trs_fname != ''  ? $TRSDetails[0]->trs_fname : ''}}" required placeholder="First Name" >
                                    </div>
                                    <div class="treas-field col-sm-5 mb-1">
                                    <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" value="{{$TRSDetails[0]->trs_lname != ''  ? $TRSDetails[0]->trs_lname : ''}}" required placeholder="Last Name" >
                                    </div>
                                    <label class="treas-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="treas-field col-sm-5 mb-1">
                                    <input type="text" name="ch_trs_email" id="ch_trs_email" class="form-control" value="{{$TRSDetails[0]->trs_email != ''  ? $TRSDetails[0]->trs_email : ''}}" required placeholder="Email Address" >
                                    </div>
                                    <div class="treas-field col-sm-5 mb-1">
                                    <input type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$TRSDetails[0]->trs_phone != ''  ? $TRSDetails[0]->trs_phone : ''}}" required placeholder="Phone Number" >
                                    </div>
                                    <label class="treas-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="treas-field col-sm-10 mb-1">
                                    <input type="text" name="ch_trs_street" id="ch_trs_street" class="form-control" placeholder="Address" value="{{$TRSDetails[0]->trs_addr != ''  ? $TRSDetails[0]->trs_addr : ''}}" required >
                                    </div>
                                    <label class="treas-field col-sm-2 mb-1 col-form-label"><br></label>
                                    <div class="treas-field col-sm-5 mb-1">
                                    <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" placeholder="City" value="{{$TRSDetails[0]->trs_city != ''  ? $TRSDetails[0]->trs_city : ''}}" required >
                                    </div>
                                    <div class="treas-field col-sm-3 mb-1">
                                        <select name="ch_trs_state" class="form-control" style="width: 100%;" required>
                                            <option value="">Select State</option>
                                                @foreach($stateArr as $state)
                                                <option value="{{$state->state_short_name}}" {{$TRSDetails[0]->trs_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                    <div class="treas-field col-sm-2 mb-1">
                                        <input type="text" name="ch_trs_zip" id="ch_trs_zip" class="form-control" placeholder="Zip" value="{{$TRSDetails[0]->trs_zip != ''  ? $TRSDetails[0]->trs_zip : ''}}" required >
                                    </div>
                                </div>

                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 mb-1 col-form-label">Secretary:</label>
                                    <div class="col-sm-10 mt-1 custom-control custom-switch">
                                            <input type="checkbox" name="SecVacant" id="SecVacant" class="custom-control-input" {{$SECDetails[0]->sec_fname == '' ? 'checked' : ''}}>
                                            <label class="custom-control-label" for="SecVacant">Vacant</label>
                                    </div>
                                    <label class="sec-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="sec-field col-sm-5 mb-1">
                                    <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" value="{{$SECDetails[0]->sec_fname != ''  ? $SECDetails[0]->sec_fname : ''}}" required placeholder="First Name" >
                                    </div>
                                    <div class="sec-field col-sm-5 mb-1">
                                    <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" value="{{$SECDetails[0]->sec_lname != ''  ? $SECDetails[0]->sec_lname : ''}}" required placeholder="Last Name" >
                                    </div>
                                    <label class="sec-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="sec-field col-sm-5 mb-1">
                                    <input type="text" name="ch_sec_email" id="ch_sec_email" class="form-control" value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}" required placeholder="Email Address" >
                                    </div>
                                    <div class="sec-field col-sm-5 mb-1">
                                    <input type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$SECDetails[0]->sec_phone != ''  ? $SECDetails[0]->sec_phone : ''}}" required placeholder="Phone Number" >
                                    </div>
                                    <label class="sec-field col-sm-2 mb-1 col-form-label"></label>
                                    <div class="sec-field col-sm-10 mb-1">
                                    <input type="text" name="ch_sec_street" id="ch_sec_street" class="form-control" placeholder="Address" value="{{$SECDetails[0]->sec_addr != ''  ? $SECDetails[0]->sec_addr : ''}}" required >
                                    </div>
                                    <label class="sec-field col-sm-2 mb-1 col-form-label"><br></label>
                                    <div class="sec-field col-sm-5 mb-1">
                                    <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" placeholder="City" value="{{$SECDetails[0]->sec_city != ''  ? $SECDetails[0]->sec_city : ''}}" required >
                                    </div>
                                    <div class="sec-field col-sm-3 mb-1">
                                        <select name="ch_sec_state" class="form-control" style="width: 100%;" required>
                                            <option value="">Select State</option>
                                                @foreach($stateArr as $state)
                                                    <option value="{{$state->state_short_name}}" {{$SECDetails[0]->sec_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                    <div class="sec-field col-sm-2 mb-1">
                                        <input type="text" name="ch_sec_zip" id="ch_sec_zip" class="form-control" value="{{$SECDetails[0]->sec_zip != ''  ? $SECDetails[0]->sec_zip : ''}}" placeholder="Zip" required >
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
                                            <div>{{ $chapterList[0]->territory }}</div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">Are your listed boundaries correct?<span class="field-required">*</span></label>
                                        <div class="col-sm-12 row ml-2 mb-2">
                                            <div class="form-check" style="margin-right: 20px;">
                                                <input class="form-check-input" type="radio" id="BoundaryStatusYes" name="BoundaryStatus" value="0" {{ $chapterList[0]->boundary_issues === 0 ? 'checked' : '' }} onChange="ShowBoundaryError()">
                                                <label class="form-check-label" for="BoundaryStatusYes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" id="BoundaryStatusNo" name="BoundaryStatus" value="1" {{ $chapterList[0]->boundary_issues === 1 ? 'checked' : '' }} onChange="ShowBoundaryError()">
                                                <label class="form-check-label" for="BoundaryStatusNo">No</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12" id="divBoundaryIssue">
                                            <label for="BoundaryIssue">Please indicate which part of the Boundaries not NOT match our records<span class="field-required">*</span></label>
                                            <input type="text" rows="2"class="form-control" name="BoundaryIssue" id="BoundaryIssue" value="{{ $chapterList[0]->boundary_issue_notes }}" ></input>
                                        </div>
                                    </div>

                                <div class="form-group row">
                                    <label class="col-sm-12 col-form-label">Website:</label>
                                    <div class="col-sm-12 mb-2">
                                        <input type="text" name="ch_website" id="ch_website" class="form-control"
                                               value="{{$chapterList[0]->website_url}}"
                                               placeholder="Chapter Website">
                                    </div>
                            <div class="col-sm-8">
                                <select name="ch_webstatus" id="ch_webstatus" class="form-control" style="width: 100%;" required>
                                    <option value="">Select Status</option>
                                    @foreach($webStatusArr as $webstatusKey => $webstatusText)
                                        <option value="{{ $webstatusKey }}" {{ $chapterList[0]->website_status == $webstatusKey ? 'selected' : '' }}
                                            {{ in_array($webstatusKey, [0, 1]) ? 'disabled' : '' }}>
                                            {{ $webstatusText }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-12 col-form-label">Social Media:</label>
                            <div class="col-sm-12 mb-2">
                            <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chapterList[0]->egroup }}"  placeholder="Forum/Group/App" >
                            </div>
                            <div class="col-sm-12 mb-2">
                            <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chapterList[0]->social1 }}" placeholder="Facebook"  >
                            </div>

                            <div class="col-sm-12 mb-2">
                                <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chapterList[0]->social2 }}"  placeholder="Twitter" >
                            </div>
                            <div class="col-sm-12 mb-2">
                                <input type="text" name="ch_social3" id="ch_social3" class="form-control" value="{{ $chapterList[0]->social3 }}"  placeholder="Instagram" >
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
                <a href="{{ route('eoyreports.eoyboardreport') }}" class="btn bg-gradient-primary mb-3"><i class="fas fa-reply mr-2" ></i>Back</a>
                    @if ($chapterList[0]->new_board_active != '1')
                        @if ($chapterList[0]->new_board_submitted != 1)
                            <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return PreSaveValidate(true)"><i class="fas fa-mail-forward mr-2" ></i>Submit</button>
                        @else
                            <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return PreSaveValidate(true)"><i class="fas fa-save mr-2" ></i>Save</button>
                        @endif
                        @if ($chapterList[0]->new_board_submitted == '1' )
                            <button type="button" class="btn bg-gradient-primary mb-3" onclick="return PreSaveValidate(false)" ><i class="fas fa-user-plus mr-2" ></i>Activate Board</button>
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

// $(document).ready(function() {
// 	var check = <?php echo "\"" . $chapterList[0]->boundary_issues . "\""; ?>;
//   });

//submit validation function
function PreSaveValidate(show_submit_message){
var errMessage="";
    if($("#ch_pre_email").val() != ""){
    if($("#ch_pre_email").val() == $("#ch_avp_email").val() || $("#ch_pre_email").val() == $("#ch_mvp_email").val() || $("#ch_pre_email").val() == $("#ch_trs_email").val() || $("#ch_pre_email").val() == $("#ch_sec_email").val()) {
        errMessage = "The e-mail address provided for the Chapter President was also provided for a different position.  Please enter a unique e-mail address for each board member or mark the position as vacant.";
    }
    }
    if($("#ch_avp_email").val() != ""){
    if($("#ch_avp_email").val() == $("#ch_mvp_email").val() || $("#ch_avp_email").val() == $("#ch_trs_email").val() || $("#ch_avp_email").val() == $("#ch_sec_email").val()) {
        errMessage = "The e-mail address provided for the Chapter AVP was provided for a different position.  Please enter a unique e-mail address for each board member or mark the position as vacant.";
    }
    }
    if($("#ch_mvp_email").val() != ""){
    if($("#ch_mvp_email").val() == $("#ch_trs_email").val() || $("#ch_mvp_email").val() == $("#ch_sec_email").val()) {
        errMessage = "The e-mail address provided for the Chapter MVP was provided for a different position.  Please enter a unique e-mail address for each board member or mark the position as vacant.";
    }
    }
    if($("#ch_trs_email").val() != ""){
    if($("#ch_trs_email").val() == $("#ch_sec_email").val()) {
        errMessage = "The e-mail address provided for the Chapter Treasurer was provided for a different position.  Please enter a unique e-mail address for each board member or mark the position as vacant.";
    }
    }
    if(!document.getElementById("BoundaryStatus1").checked && !document.getElementById("BoundaryStatus2").checked){
					errMessage = "Please review the chapters boundaries and verify they are what the chapter has in their records."; document.getElementById("BoundaryStatus1").focus();
				}

    if (errMessage.length > 0) {
        alert(errMessage);
        return false;
    }

    if (show_submit_message) {
        alert("Thank you for submitting the board information for this chapter. The new board will not be able to log in until the new board has been activated.");
    } else {
        $("#submit_type").val("activate_board");
        var result = confirm("Are you sure want to Activate Boards?");
        if (result) {
            $("#board-info").submit();
        } else {
            return false;
        }
    }

    return true;
}

</script>
@endsection
