@extends('layouts.board_theme')
<style>
    .ml-2 {
        margin-left: 0.5rem !important; /* Adjust the margin to control spacing for Vacant Buttons */
    }

    .custom-control-input:checked ~ .custom-control-label {
        color: black; /* Label color when toggle is ON for Vacant Buttons */
    }

    .custom-control-input:not(:checked) ~ .custom-control-label {
        color: #b0b0b0; /* Subdued label color when toggle is OFF for Vacant Buttons */
        opacity: 0.6;   /* Optional: Adds a subdued effectfor Vacant Buttons */
    }

</style>

@section('content')

<div class="container">
<div class="row">
    <div class="col-md-12">
 <!-- Widget: user widget style 1 -->
 <div class="card card-widget widget-user">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header bg-primary">
        <div class="widget-user-image">
            <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}theme/dist/img/logo.png" alt="MC" style="width: 115px; height: 115px;">
          </div>
                </div>
                <div class="card-body">
                    @php
                        $thisDate = \Illuminate\Support\Carbon::now();
                    @endphp
                <div class="col-md-12"><br><br></div>
                    <h2 class="text-center"> MOMS Club of {{ $chapterList[0]->name }}, {{$chapterState}} </h2>
                    <h4 class="text-center"> <?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Election Report  </h4>
                    <div class="description text-center">
                        @if ($chapterList[0]->new_board_submitted != '1' )
                        <p>
                            Please complete the report below with information about your newly elected board.<br>
                            This will ensure they have access to all the tools they need to be successful in the upcoming year.</p>
                        <p>Your submitted report will be activated after July 1st.<br>
                            Once activated, new board members will have full MIMI Access. Outgoing board members will have access to Financial Reports Only.</p>
                        @endif
                        @if ($chapterList[0]->new_board_submitted == '1' && $chapterList[0]->new_board_active !='1')
                        <p><span style="color:#28a745;">Your chapter's Board Eleciton Report has been Submitted and will be activated after July 1st!</span><br>
                            Once activated, new board members will have full MIMI Access. Outgoing board members will have access to Financial Reports Only.<br>
                            Submitted entries are <span style="color:#dc3545;">Read Only</span>. If you need to make changes, please contact your Primary Coordinator.</p>
                        @endif
                        @if ($chapterList[0]->new_board_active =='1')
                        <p><span style="color:#28a745;">Your chapter's Board Eleciton Report has been Activated!</span><br>
                            New board members now have full MIMI Access. Outgoing board members have access to Financial Reports Only.</p>
                            Futrue board member changes can be made on your chapter's main profile page.</p>
                        @endif
                </div>
            </div>
        </div>
    </div>

        @if ($chapterList[0]->new_board_active != '1')
        <div class="col-md-12">
            <div class="card card-primary card-outline">

                <form id="boardinfo" method="POST" action="{{ route('boardinfo.createboardinfo',$chapterList[0]->id) }}">
				@csrf
				<div class="card-header">
                    <h4 class="card-title">Chapter Information</h4>
                </div>

				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Boundaries listed in MIMI (used for Inquiries)</label>
								<input type="text" name="ch_boundry" class="form-control" value="{{ $chapterList[0]->territory }}"  maxlength="250" readonly>
							</div>
						</div>
				    </div>
					<div class="col-md-12">
                        <div class="form-group row">
                            <label>Are your listed boundaries correct?<span class="field-required">*</span></label>
                            <div class="col-12 row">
                                <div class="form-check" style="margin-right: 20px;">
                                    <input class="form-check-input" type="radio" id="BoundaryStatusYes" name="BoundaryStatus" value="0" {{ $chapterList[0]->boundary_issues === 0 ? 'checked' : '' }} onChange="ShowBoundaryError()">
                                    <label class="form-check-label" for="BoundaryStatusYes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="BoundaryStatusNo" name="BoundaryStatus" value="1" {{ $chapterList[0]->boundary_issues === 1 ? 'checked' : '' }} onChange="ShowBoundaryError()">
                                    <label class="form-check-label" for="BoundaryStatusNo">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" id="divBoundaryIssue">
                                <label for="BoundaryIssue">Please indicate which part of the Boundaries not NOT match our records<span class="field-required">*</span></label>
                                <input type="text" rows="2"class="form-control" name="BoundaryIssue" id="BoundaryIssue" value="{{ $chapterList[0]->boundary_issue_notes }}" ></input>
                            </div>
						</div>
				{{-- </div> --}}

				{{-- <div class="card-header">
                    <h4 class="card-title">Chapter Information</h4>
                </div> --}}
				{{-- <div class="card-body"> --}}
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Email Address to Give to MOMS Interested in joining your Chapter</label>
								<input type="text" name="InquiriesContact" class="form-control" value="{{ $chapterList[0]->inquiries_contact }}" required maxlength="50" required>
							</div>
						</div>
                    </div>

               <!-- /.form group -->
               <div class="row">
                <div class="col-6">
                    <div class="form-group">
                      <label>Chapter Website</label>
                      <input type="text" name="ch_website" class="form-control my-colorpicker1" placeholder="http://www.momsclubofchaptername.com" value="{{$chapterList[0]->website_url}}" maxlength="50" id="validate_url" onchange="is_url(); updateWebsiteStatus();">
                    </div>
                    </div>
                      <!-- /.form group -->
                      <div class="col-6 ">
                        <div class="form-group">
                            <label>Website Link Status</label>
                            <select id="ch_webstatus" name="ch_webstatus" class="form-control select2" style="width: 100%;" required>
                                <option value="0" id="option0" {{$chapterList[0]->website_status == 0 ? 'selected' : ''}} {{ $chapterList[0]->website_status == 0 ? '' : 'disabled' }}>Website Not Linked</option>
                                <option value="1" id="option1" {{$chapterList[0]->website_status == 1 ? 'selected' : ''}} {{ $chapterList[0]->website_status == 1 ? '' : 'disabled' }}>Website Linked</option>
                                <option value="2" id="option2" {{$chapterList[0]->website_status == 2 ? 'selected' : ''}}>Add Link Requested</option>
                                <option value="3" id="option3" {{$chapterList[0]->website_status == 3 ? 'selected' : ''}}>Do Not Link</option>
                            </select>
                            <input type="hidden" name="ch_hid_webstatus" id="ch_hid_webstatus" value="{{ $chapterList[0]->website_status }}">
                        </div>
                    </div>
              <!-- /.form group -->
              <div class="col-12 ">
              <div class="form-group">
                <label>Online Discussion Group (Meetup, Google Groups, etc)</label>
                <input type="text" name="ch_onlinediss" class="form-control my-colorpicker1" value="{{ $chapterList[0]->egroup}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
                            <div class="col-md-4 ">
                                <div class="form-group">
                <label>Facebook</label>
                <input type="text" name="ch_social1" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social1}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-md-4 ">
              <div class="form-group">
                <label>Twitter</label>
                <input type="text" name="ch_social2" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social2}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-md-4">
              <div class="form-group">
                <label>Instagram</label>
                <input type="text" name="ch_social3" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social3}}" maxlength="50" >
              		</div>
						</div>

				</div>
            </div>
                <div class="card-header">
                    <h4 class="card-title">President</h4>
                </div>
                <div class="card-body">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="ch_pre_fname" class="form-control" placeholder="First Name" value="{{ $PREDetails[0]->pre_fname }}" required maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="ch_pre_lname" class="form-control" placeholder="Last Name" value="{{ $PREDetails[0]->pre_lname }}" required maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email ID</label>
                                    <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control" placeholder="Email ID" value="{{ $PREDetails[0]->pre_email }}" maxlength="50" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" placeholder="Phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $PREDetails[0]->pre_phone }}" required>
                                </div>
                            </div>
                            </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="ch_pre_street" class="form-control" placeholder="Street Address" value="{{ $PREDetails[0]->pre_addr }}" required maxlength="250">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="ch_pre_city" class="form-control" placeholder="City" value="{{ $PREDetails[0]->pre_city }}" required maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="ch_pre_state" class="form-control select2" style="width: 100%;" required>
										<option value="">Select State</option>
											@foreach($stateArr as $state)
											  <option value="{{$state->state_short_name}}" {{$PREDetails[0]->pre_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
											@endforeach
										</select>
                                </div>
                            </div>
                            <div class="col-md-4 pl-1">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="text" name="ch_pre_zip" class="form-control" placeholder="ZIP Code" value="{{ $PREDetails[0]->pre_zip }}" maxlength="10" required onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>
						<input type="hidden" name="presID" id="presID" value="<?php echo $PREDetails[0]->ibd_id; ?>" />
                        <div class="clearfix"></div>
                </div>

                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title mb-0">AVP</h4>
                    <div class="custom-control custom-switch ml-2">
                        <input type="checkbox" name="AVPVacant" id="AVPVacant" class="custom-control-input" {{$AVPDetails[0]->avp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                        <label class="custom-control-label" for="AVPVacant">Vacant</label>
                    </div>
                </div>
                <div class="card-body">

                        <div class="row" id="checkRadios">
                            <div class="col-md-6 avp-field">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" placeholder="First Name" value="{{$AVPDetails[0]->avp_fname != ''  ? $AVPDetails[0]->avp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6 avp-field">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" placeholder="Last Name" value="{{$AVPDetails[0]->avp_lname != ''  ? $AVPDetails[0]->avp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5 avp-field">
                                <div class="form-group">
                                    <label>Email ID</label>
                                    <input type="email" name="ch_avp_email" id="ch_avp_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-5 avp-field">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control" placeholder="Phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$AVPDetails[0]->avp_phone != ''  ? $AVPDetails[0]->avp_phone : ''}}" >
                                </div>
                            </div>
                            </div>
                        <div class="row">
                            <div class="col-md-12 avp-field">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="ch_avp_street" id="ch_avp_street" class="form-control" placeholder="Street Address" value="{{$AVPDetails[0]->avp_addr != ''  ? $AVPDetails[0]->avp_addr : ''}}" maxlength="250">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1 avp-field">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" placeholder="City" value="{{$AVPDetails[0]->avp_city != ''  ? $AVPDetails[0]->avp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1 avp-field">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="ch_avp_state" id="ch_avp_state" class="form-control select2" style="width: 100%;" >
										<option value="">Select State</option>
											@foreach($stateArr as $state)
											  <option value="{{$state->state_short_name}}" {{$AVPDetails[0]->avp_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
											@endforeach
										</select>
                                </div>
                            </div>
                            <div class="col-md-4 pl-1 avp-field">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" placeholder="ZIP Code" value="{{$AVPDetails[0]->avp_zip != ''  ? $AVPDetails[0]->avp_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>
						<input type="hidden" name="avpID" id="avpID" value="<?php echo $AVPDetails[0]->ibd_id; ?>" />
                </div>
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title mb-0">MVP</h4>
                    <div class="custom-control custom-switch ml-2">
                        <input type="checkbox" name="MVPVacant" id="MVPVacant" class="custom-control-input" {{$MVPDetails[0]->mvp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                        <label class="custom-control-label" for="MVPVacant">Vacant</label>
                    </div>
                </div>
                <div class="card-body">

                        <div class="row" id="checkRadios">
                            <div class="col-md-6 mvp-field">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control" placeholder="First Name" value="{{$MVPDetails[0]->mvp_fname != ''  ? $MVPDetails[0]->mvp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6 mvp-field">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" placeholder="Last Name" value="{{$MVPDetails[0]->mvp_lname != ''  ? $MVPDetails[0]->mvp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5 mvp-field">
                                <div class="form-group">
                                    <label>Email ID</label>
                                    <input type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$MVPDetails[0]->mvp_email != ''  ? $MVPDetails[0]->mvp_email : ''}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-5 mvp-field">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control" placeholder="Phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$MVPDetails[0]->mvp_phone != ''  ? $MVPDetails[0]->mvp_phone : ''}}">
                                </div>
                            </div>
                            </div>
                        <div class="row">
                            <div class="col-md-12 mvp-field">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control" placeholder="Street Address" value="{{$MVPDetails[0]->mvp_addr != ''  ? $MVPDetails[0]->mvp_addr : ''}}" maxlength="250">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1 mvp-field">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" placeholder="City" value="{{$MVPDetails[0]->mvp_city != ''  ? $MVPDetails[0]->mvp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1 mvp-field">
                                <div class="form-group">
                                    <label>State</label>
                                     <select name="ch_mvp_state" id="ch_mvp_state" class="form-control select2" style="width: 100%;">
										<option value="">Select State</option>
											@foreach($stateArr as $state)
											  <option value="{{$state->state_short_name}}" {{$MVPDetails[0]->mvp_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
											@endforeach
										</select>
                                </div>
                            </div>
                            <div class="col-md-4 pl-1 mvp-field">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" class="form-control" placeholder="ZIP Code" value="{{$MVPDetails[0]->mvp_zip != ''  ? $MVPDetails[0]->mvp_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>
							<input type="hidden" name="mvpID" id="mvpID" value="<?php echo $MVPDetails[0]->ibd_id; ?>" />
                </div>
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title mb-0">Treasurer</h4>
                    <div class="custom-control custom-switch ml-2">
                        <input type="checkbox" name="TreasVacant" id="TreasVacant" class="custom-control-input" {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                        <label class="custom-control-label" for="TreasVacant">Vacant</label>
                    </div>
                </div>
                <div class="card-body">

                        <div class="row" id="checkRadios">
                            <div class="col-md-6 treas-field">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control" placeholder="First Name" value="{{$TRSDetails[0]->trs_fname != ''  ? $TRSDetails[0]->trs_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6 treas-field">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" placeholder="Last Name" value="{{$TRSDetails[0]->trs_lname != ''  ? $TRSDetails[0]->trs_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5 treas-field">
                                <div class="form-group">
                                    <label>Email ID</label>
                                    <input type="email" name="ch_trs_email" id="ch_trs_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$TRSDetails[0]->trs_email != ''  ? $TRSDetails[0]->trs_email : ''}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-5 treas-field">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control" placeholder="Phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$TRSDetails[0]->trs_phone != ''  ? $TRSDetails[0]->trs_phone : ''}}" >
                                </div>
                            </div>
                            </div>
                        <div class="row">
                            <div class="col-md-12 treas-field">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="ch_trs_street" id="ch_trs_street" class="form-control" placeholder="Street Address" value="{{$TRSDetails[0]->trs_addr != ''  ? $TRSDetails[0]->trs_addr : ''}}" maxlength="250">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1 treas-field">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" placeholder="City" value="{{$TRSDetails[0]->trs_city != ''  ? $TRSDetails[0]->trs_city : ''}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1 treas-field">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="ch_trs_state" id="ch_trs_state" class="form-control select2" style="width: 100%;">
										<option value="">Select State</option>
											@foreach($stateArr as $state)
											  <option value="{{$state->state_short_name}}" {{$TRSDetails[0]->trs_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
											@endforeach
										</select>
                                </div>
                            </div>
                            <div class="col-md-4 pl-1 treas-field">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="text" name="ch_trs_zip" id="ch_trs_zip" class="form-control" placeholder="ZIP Code" value="{{$TRSDetails[0]->trs_zip != ''  ? $TRSDetails[0]->trs_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>
						<input type="hidden" name="trsID" id="trsID" value="<?php echo $TRSDetails[0]->ibd_id; ?>" />
                </div>
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title mb-0">Secratary</h4>
                    <div class="custom-control custom-switch ml-2">
                        <input type="checkbox" name="SecVacant" id="SecVacant" class="custom-control-input" {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                        <label class="custom-control-label" for="SecVacant">Vacant</label>
                    </div>
                </div>
                <div class="card-body">

                        <div class="row" id="checkRadios">
                            <div class="col-md-6 sec-field">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" placeholder="First Name" value="{{$SECDetails[0]->sec_fname != ''  ? $SECDetails[0]->sec_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6 sec-field">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" placeholder="Last Name" value="{{$SECDetails[0]->sec_lname != ''  ? $SECDetails[0]->sec_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5 sec-field">
                                <div class="form-group">
                                    <label>Email ID</label>
                                    <input type="email" name="ch_sec_email" id="ch_sec_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group sec-field">
                                    <label>Phone</label>
                                    <input type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control" placeholder="Phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$SECDetails[0]->sec_phone != ''  ? $SECDetails[0]->sec_phone : ''}}" >
                                </div>
                            </div>
                            </div>
                        <div class="row">
                            <div class="col-md-12 sec-field">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="ch_sec_street" id="ch_sec_street" class="form-control" placeholder="Street Address" maxlength="250" value="{{$SECDetails[0]->sec_addr != ''  ? $SECDetails[0]->sec_addr : ''}}" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1 sec-field">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" placeholder="City" value="{{$SECDetails[0]->sec_city != ''  ? $SECDetails[0]->sec_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1 sec-field">
                                <div class="form-group">
                                    <label>State</label>
                                     <select name="ch_sec_state" id="ch_sec_state" class="form-control select2" style="width: 100%;">
										<option value="">Select State</option>
											@foreach($stateArr as $state)
											  <option value="{{$state->state_short_name}}" {{$SECDetails[0]->sec_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
											@endforeach
										</select>
                                </div>
                            </div>
                            <div class="col-md-4 pl-1 sec-field">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="text" name="ch_sec_zip" id="ch_sec_zip" class="form-control" placeholder="ZIP Code" value="{{$SECDetails[0]->sec_zip != ''  ? $SECDetails[0]->sec_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>
						<input type="hidden" name="secID" id="secID" value="<?php echo $SECDetails[0]->ibd_id; ?>" />
                    </div>
                    @endif

                    <div class="box-body text-center">
                        @if($user_type === 'coordinator')
                            <a href="{{ route('chapter.viewpresident', $chapterList[0]->id) }}" class="btn btn-primary" id="btn-back"><i class="fas fa-reply"></i>&nbsp; Back</a>
                        @else
                            <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
                        @endif
                        @if ($chapterList[0]->new_board_submitted != '1')
                        <button type="submit" class="btn btn-primary" onclick="return PreSaveValidate()" <?php if($chapterList[0]->new_board_submitted) echo "disabled"; ?>><i class="fas fa-mail-forward " ></i>&nbsp; Submit</button>
                        @endif
                </div>
				</form>

            </div>
		</div>

    </div>
</div>
@endsection
@section('customscript')
<script>
$(document).ready(function() {
    var userType = @json($user_type);

  $('#add_link_req').parent().hide();
  $('#not_link').parent().hide();

  // Disable all input fields, select elements, textareas, and submit button if the condition is met
  if (userType === 'coordinator') {
        // Disable all input fields, select elements, textareas, and buttons
        $('button').not('#btn-back').prop('disabled', true);
        $('input, select, textarea').prop('disabled', true);

    } else if ("{{$chapterList[0]->new_board_submitted}}" === '1') {
        $('input, select, textarea').prop('disabled', true);
        $('#submit').prop('disabled', true);
    } else {
        // If the condition is not met, keep the fields active
        $('input, select, textarea').prop('disabled', false);
        $('#submit').prop('disabled', false);
    }
});

// Disable Web Link Status option 0 and 1
// document.getElementById('ch_webstatus').addEventListener('change', function() {
//         // Update hidden input field with the new value only if the selected option is not disabled
//         var selectedOption = this.options[this.selectedIndex];
//         if (!selectedOption.disabled) {
//             document.getElementById('ch_hid_webstatus').value = this.value;
//         }
//     });

//     // Ensure the hidden field is updated with the selected value on form submission
//     document.forms[0].addEventListener('submit', function() {
//         var selectedOption = document.getElementById('ch_webstatus').options[document.getElementById('ch_webstatus').selectedIndex];
//         if (selectedOption.disabled) {
//             document.getElementById('ch_hid_webstatus').value = selectedOption.value;
//         }
//     });

    /* Disables Web Link Status options 0 and 1 */
    var originalWebsiteUrl = "{{$chapterList[0]->website_url}}"; // Original value from the database

    function checkWebsiteChanged() {
        var currentValue = document.getElementById('validate_url').value;

        if (currentValue !== originalWebsiteUrl) {
            document.getElementById('staticStatusField').style.display = 'none';
            document.getElementById('editableStatusField').style.display = 'block';
        } else {
            document.getElementById('staticStatusField').style.display = 'block';
            document.getElementById('editableStatusField').style.display = 'none';
        }
    }
    document.getElementById('ch_webstatus').addEventListener('change', function() {
            // Update hidden input field with the new value only if the selected option is not disabled
            var selectedOption = this.options[this.selectedIndex];
            if (!selectedOption.disabled) {
                document.getElementById('ch_hid_webstatus').value = this.value;
            }
        });

    // Ensure the hidden field is updated with the selected value on form submission
    document.forms[0].addEventListener('submit', function() {
        var selectedOption = document.getElementById('ch_webstatus').options[document.getElementById('ch_webstatus').selectedIndex];
        if (selectedOption.disabled) {
            document.getElementById('ch_hid_webstatus').value = selectedOption.value;
        }
    });

function is_url() {
        var str = $("#validate_url").val().trim(); // Trim leading and trailing whitespace
        var chWebStatusSelect = document.querySelector('select[name="ch_webstatus"]');

        if (str === "") {
            chWebStatusSelect.value = '0'; // Set to 0 if the input is blank
            chWebStatusSelect.disabled = true; // Disable the select field
            return true; // Field is empty, so no validation needed
        }

        var regexp = /^(https?:\/\/)([a-z0-9-]+\.(com|org))$/;

        if (regexp.test(str)) {
            chWebStatusSelect.disabled = false; // Enable the select field if a valid URL is entered
            return true;
        } else {
            alert("Please Enter URL, Should be http://xxxxxxxx.xxx format");
            chWebStatusSelect.value = '0'; // Set to 0 if an invalid URL is entered
            chWebStatusSelect.disabled = true; // Disable the select field
            return false;
        }
    }

        function updateWebsiteStatus() {
            const chWebsiteInput = document.querySelector('input[name="ch_website"]');
            const chWebStatusSelect = document.querySelector('select[name="ch_webstatus"]');

            if (chWebsiteInput.value === '') {
                chWebStatusSelect.value = '0'; // Set to 0 if the input is blank
            } else if (chWebsiteInput.value !== 'http://www.momsclubofchaptername.com') {
                // Set to 2 or 3 based on some condition, you can customize this part.
                // For now, I'm setting it to 2.
                chWebStatusSelect.value = '2';
            }
        }


function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
function isAlphanumeric(e){
	var k;
	document.all ? k = e.keyCode : k = e.which;
	return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
}
function isPhone() {
    if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 8) {
        event.keyCode = 0;
        alert("Please Enter Number Only");
        return false;
    }
}

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


$(document).ready(function() {

	var check = <?php echo "\"" . $chapterList[0]->boundary_issues . "\""; ?>;

	var pcid = $("#pcid").val();
	if(pcid !=""){
		$.ajax({
            url: '{{ url("/checkreportid/") }}' + '/' + pcid,
            type: "GET",
            success: function(result) {
				$("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {

            }
        });
    }

  });

//submit validation function
  function PreSaveValidate(){
    var errMessage="";
          //Ensure there are no e-mail addresses repeated
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
					errMessage = "Please review your chapters boundaries and verify they match your chapters records.";		document.getElementById("BoundaryStatus1").focus();
				}

          if(errMessage.length > 0){
            alert (errMessage);
            return false;
          }


		var $myForm = $('#boardinfo');
            if($myForm[0].checkValidity()){
                alert ("Thank you for submitting your chapter's incoming board information. New board members will not be able to login until July 1st.");
                return true;
            }

	}

</script>
@endsection
