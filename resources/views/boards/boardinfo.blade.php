@extends('layouts.chapter_theme')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-user">
                <div class="card-image color_header">
                   
                </div>
                <div class="card-body">
                    <div class="author">
                        <a href="#">
                            <div class="border-gray avatar">
								<img src="{{ asset('chapter_theme/img/logo.png') }}" alt="...">
							</div>
                           <h2 class="moms-c"> MOMS Club of {{ $chapterList[0]->name }}, {{$chapterState}} </h2>
                           <h2 class="moms-c"> <?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Election Report </h2>
                        </a>
                        <!--<h4 class="ein">
                            EIN: {{ $chapterList[0]->ein }}
                        </h4>-->
                        <br>
                        <p class="description" style="padding: 0 20px">
                            Please complete the report below with information about your newly elcted board to ensure they have access to all the tools they need to be successful in the upcoming year. 
                        </p>
                       <h4><center><?php if($chapterList[0]->new_board_submitted) echo "<br><font color=\"red\">Thank You! Your chapter's Board Eleciton Report has been Submitted!</font>"; ?></center></h4>

                    </div>

                </div>
                
            </div>
        </div>
        <div class="col-md-12">
		    <div class="card">
               
                <form id="boardinfo" method="POST" action="{{ route('boardinfo.createboardinfo',$chapterList[0]->id) }}">
				@csrf 
				<div class="card-header">
                    <h4 class="card-title">Chapter Boundaries</h4>
                </div>
				
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>BOUNDARY DESCRIPTION</label>
								<input type="text" name="ch_boundry" class="form-control" value="{{ $chapterList[0]->territory }}"  maxlength="250" readonly>
							</div>
						</div>
				    </div>
					<div class="col-md-12">
						<div class="col-md-6 float-left">
							<div class="form-check form-check-radio">
								<label class="form-check-label">
									<input type="radio" class="form-check-input" name="BoundaryStatus" onClick="BoundaryError(false)" id="BoundaryStatus1" value="0" <?php if (!is_null($chapterList[0]->boundary_issues) && (!$chapterList[0]->boundary_issues == 1)) echo "checked"; ?> required>
									<span class="form-check-sign"></span>
									Boundaries are correct
								</label>
							</div>
						</div>
						<div class="col-md-6 float-left">
							<div class="form-check form-check-radio">
								<label class="form-check-label">
									<input type="radio" class="form-check-input" name="BoundaryStatus" onClick="BoundaryError(true)" id="BoundaryStatus2" value="1" <?php if ($chapterList[0]->boundary_issues == 1) echo "checked"; ?> required>
									<span class="form-check-sign"></span>
									Boundaries are not correct
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label id="BoundaryIssueLabel">PLEASE INDICATE WHICH PART OF THE BOUNDARIES DO NOT MATCH YOUR RECORDS:</label>
						<input type="text" name="BoundaryIssue" id="BoundaryIssue" class="form-control" value="{{ $chapterList[0]->boundary_issue_notes }}" maxlength="250">
					</div>
				</div>

				
				<div class="card-header">
                    <h4 class="card-title">Chapter Information</h4>
                </div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Email Address to Give to MOMS Interested in joining your Chapter</label>
								<input type="text" name="InquiriesContact" class="form-control" value="{{ $chapterList[0]->inquiries_contact }}" required maxlength="50" required>
							</div>
						</div>
                    </div>
					
				
               <!-- /.form group -->
                <div class="col-md-12">
                                <div class="form-group">
                <label>Chapter Website</label>
                                    <input  type="text" name="ch_website" class="form-control rowheight" id="ch_website" placeholder="http://www.momsclubofchaptername.com" maxlength="150" value="{{$chapterList[0]->website_url}}" onchange="is_url()" />
              </div>
            <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <label>LINK STATUS:</label><span class="field-required" id="link_status" hidden>*</span>
                                </div>
                                <div class="col-md-4 float-left">
                                <div class="form-check form-check-radio">
                                    <label class="form-check-label">
                                        <input  type="radio" class="form-check-input" name="ch_linkstatus" value="1" id="link" disabled {{$chapterList[0]->website_link_status == '1'  ? 'checked' : ''}}>
                                        <span class="form-check-sign"></span>
                                        LINKED
                                    </label>
                                </div>
                                </div>
                                <div class="col-md-4 float-left">
                                <div class="form-check form-check-radio">
                                    <label class="form-check-label">
                                        <input  type="radio" class="form-check-input" name="ch_linkstatus" id="add_link_req" value="2" disabled {{$chapterList[0]->website_link_status == '2'  ? 'checked' : ''}}>
                                        <span class="form-check-sign"></span>
                                        ADD LINK REQUESTED
                                    </label>
                                </div>
                                </div>
                                <div class="col-md-4 float-left">
                                <div class="form-check form-check-radio">
                                    <label class="form-check-label">
                                        <input  type="radio" class="form-check-input" name="ch_linkstatus" id="not_link" value="3" disabled {{$chapterList[0]->website_link_status == '3'  ? 'checked' : ''}}>
                                        <span class="form-check-sign"></span>
                                        DO NOT LINK
                                    </label>
                                </div>
                                </div>
                                 </div>
                                </div>

              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Online Discussion Group (Meetup, Google Groups, etc)</label>
                <input type="text" name="ch_onlinediss" class="form-control my-colorpicker1" value="{{ $chapterList[0]->egroup}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                <label>Facebook</label>
                <input type="text" name="ch_social1" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social1}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-md-4 pl-1">
              <div class="form-group">
                <label>Twitter</label>
                <input type="text" name="ch_social2" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social2}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-md-4 pl-1">
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
                                    <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" placeholder="Phone" value="{{ $PREDetails[0]->pre_phone }}" maxlength="12" required onkeypress="return isPhone(event)">
                                </div>
                            </div>
                            </div>
						<input type="hidden" name="presID" id="presID" value="<?php echo $PREDetails[0]->ibd_id; ?>" />
                        <div class="clearfix"></div>
                   
                </div>
                <div class="card-header">
                    <h4 class="card-title">AVP</h4>
                </div>
                <div class="card-body">
                  
                        <div class="row" id="checkRadios">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" placeholder="First Name" value="{{$AVPDetails[0]->avp_fname != ''  ? $AVPDetails[0]->avp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" placeholder="Last Name" value="{{$AVPDetails[0]->avp_lname != ''  ? $AVPDetails[0]->avp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="ch_avp_street" id="ch_avp_street" class="form-control" placeholder="Street Address" value="{{$AVPDetails[0]->avp_addr != ''  ? $AVPDetails[0]->avp_addr : ''}}" maxlength="250">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" placeholder="City" value="{{$AVPDetails[0]->avp_city != ''  ? $AVPDetails[0]->avp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1">
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
                            <div class="col-md-4 pl-1">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" placeholder="ZIP Code" value="{{$AVPDetails[0]->avp_zip != ''  ? $AVPDetails[0]->avp_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Email ID</label>
                                    <input type="email" name="ch_avp_email" id="ch_avp_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control" placeholder="Phone" value="{{$AVPDetails[0]->avp_phone != ''  ? $AVPDetails[0]->avp_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Vacant</label>
                                    <label style="display: block;"><input type="checkbox" name="AVPVacant" id="AVPVacant" class="ios-switch green bigswitch" {{$AVPDetails[0]->avp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"  /><div><div></div></div></label>
                                
                                </div>
                            </div>
                            </div>
						<input type="hidden" name="avpID" id="avpID" value="<?php echo $AVPDetails[0]->ibd_id; ?>" />
                </div>
                <div class="card-header">
                    <h4 class="card-title">MVP</h4>
                </div>
                <div class="card-body">
                  
                        <div class="row" id="checkRadios">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control" placeholder="First Name" value="{{$MVPDetails[0]->mvp_fname != ''  ? $MVPDetails[0]->mvp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" placeholder="Last Name" value="{{$MVPDetails[0]->mvp_lname != ''  ? $MVPDetails[0]->mvp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control" placeholder="Street Address" value="{{$MVPDetails[0]->mvp_addr != ''  ? $MVPDetails[0]->mvp_addr : ''}}" maxlength="250">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" placeholder="City" value="{{$MVPDetails[0]->mvp_city != ''  ? $MVPDetails[0]->mvp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1">
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
                            <div class="col-md-4 pl-1">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" class="form-control" placeholder="ZIP Code" value="{{$MVPDetails[0]->mvp_zip != ''  ? $MVPDetails[0]->mvp_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Email ID</label>
                                    <input type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$MVPDetails[0]->mvp_email != ''  ? $MVPDetails[0]->mvp_email : ''}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control" placeholder="Phone" value="{{$MVPDetails[0]->mvp_phone != ''  ? $MVPDetails[0]->mvp_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Vacant</label>
                                    <label style="display: block;"><input type="checkbox" name="MVPVacant" id="MVPVacant" class="ios-switch green bigswitch" {{$MVPDetails[0]->mvp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div></label>
                                
                                </div>
                            </div>
                            </div>
							<input type="hidden" name="mvpID" id="mvpID" value="<?php echo $MVPDetails[0]->ibd_id; ?>" />
                </div>
                <div class="card-header">
                    <h4 class="card-title">TREASURER</h4>
                </div>
                <div class="card-body">
                   
                        <div class="row" id="checkRadios">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control" placeholder="First Name" value="{{$TRSDetails[0]->trs_fname != ''  ? $TRSDetails[0]->trs_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" placeholder="Last Name" value="{{$TRSDetails[0]->trs_lname != ''  ? $TRSDetails[0]->trs_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="ch_trs_street" id="ch_trs_street" class="form-control" placeholder="Street Address" value="{{$TRSDetails[0]->trs_addr != ''  ? $TRSDetails[0]->trs_addr : ''}}" maxlength="250">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" placeholder="City" value="{{$TRSDetails[0]->trs_city != ''  ? $TRSDetails[0]->trs_city : ''}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1">
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
                            <div class="col-md-4 pl-1">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="text" name="ch_trs_zip" id="ch_trs_zip" class="form-control" placeholder="ZIP Code" value="{{$TRSDetails[0]->trs_zip != ''  ? $TRSDetails[0]->trs_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Email ID</label>
                                    <input type="email" name="ch_trs_email" id="ch_trs_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$TRSDetails[0]->trs_email != ''  ? $TRSDetails[0]->trs_email : ''}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control" placeholder="Phone" value="{{$TRSDetails[0]->trs_phone != ''  ? $TRSDetails[0]->trs_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Vacant</label>
                                    <label style="display: block;"><input type="checkbox" name="TreasVacant" id="TreasVacant" class="ios-switch green bigswitch" {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div></label>
                                
                                </div>
                            </div>
                            </div>
						<input type="hidden" name="trsID" id="trsID" value="<?php echo $TRSDetails[0]->ibd_id; ?>" />	
                </div>
                <div class="card-header">
                    <h4 class="card-title">SECRETARY</h4>
                </div>
                <div class="card-body">
                  
                        <div class="row" id="checkRadios">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" placeholder="First Name" value="{{$SECDetails[0]->sec_fname != ''  ? $SECDetails[0]->sec_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" placeholder="Last Name" value="{{$SECDetails[0]->sec_lname != ''  ? $SECDetails[0]->sec_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" name="ch_sec_street" id="ch_sec_street" class="form-control" placeholder="Street Address" maxlength="250" value="{{$SECDetails[0]->sec_addr != ''  ? $SECDetails[0]->sec_addr : ''}}" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" placeholder="City" value="{{$SECDetails[0]->sec_city != ''  ? $SECDetails[0]->sec_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1">
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
                            <div class="col-md-4 pl-1">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="text" name="ch_sec_zip" id="ch_sec_zip" class="form-control" placeholder="ZIP Code" value="{{$SECDetails[0]->sec_zip != ''  ? $SECDetails[0]->sec_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Email ID</label>
                                    <input type="email" name="ch_sec_email" id="ch_sec_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control" placeholder="Phone" value="{{$SECDetails[0]->sec_phone != ''  ? $SECDetails[0]->sec_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Vacant</label>
                                    <label style="display: block;"><input type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div></label>
                                
                                </div>
                            </div>
                            </div>
						<input type="hidden" name="secID" id="secID" value="<?php echo $SECDetails[0]->ibd_id; ?>" />	
                </div>
                
                <div class="card-body">
                    <div class="col-md-12 text-center">
                        
                    <button type="submit" class="btn btn-info btn-fill" onclick="return PreSaveValidate()" <?php if($chapterList[0]->new_board_submitted) echo "disabled"; ?>>Submit</button>
                    <a href="{{ route('home') }}" class="btn btn-info btn-fill">Back</a>
                    </div>
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
  $('#add_link_req').parent().hide();
  $('#not_link').parent().hide();
});

function is_url() {
  var str = $("#ch_website").val();
  var regexp = /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;

  if (regexp.test(str)) {
    if (str) {
      $('#link_status').removeAttr('hidden');
      $('#link').attr('disabled', true);
      $('#add_link_req').parent().show();
      $('#not_link').parent().show();
      $('#add_link_req').removeAttr('disabled');
      $('#not_link').removeAttr('disabled');

      var isChecked1 = $('#add_link_req').is(':checked');
      var isChecked2 = $('#not_link').is(':checked');
      
      if (!isChecked1 && !isChecked2) {
        alert("Please select a link status.");
        return false;
      }
    } else {
      $('#link_status').attr('hidden', true);
      $('#link').removeAttr('disabled');
      $('#add_link_req').parent().hide();
      $('#not_link').parent().hide();
      $('#add_link_req').attr('disabled', true);
      $('#not_link').attr('disabled', true);
      $('#link').prop('checked', true);
    }
    
    return true;
  } else {
    alert("Please enter a URL in the format http://xxxxxxxx.xxx");
    return false;
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
  function BoundaryError(error){
		if (error){
			$("#BoundaryIssue").prop("readonly",false);
			$("#BoundaryIssue").prop("required",true);
			$("#BoundaryIssueLabel").prop("readonly",false);
		}
		else{
			$("#BoundaryIssue").prop("readonly",true);
			$("#BoundaryIssue").prop("required",false);
			$("#BoundaryIssueLabel").prop("readonly",true);
		}
	}

$(document).ready(function() {
	var phoneListArr = ["ch_pre_phone","ch_avp_phone","ch_mvp_phone","ch_trs_phone","ch_sec_phone"];
    for (var i = phoneListArr.length - 1; i >= 0; i--) {
        var inputValue = $("#"+phoneListArr[i]).val();
        if(inputValue.length > 10) inputValue = inputValue.substring(0,10);
        var reInputValue = inputValue.replace(/(\d{3})(\d{3})/, "$1-$2-");
        $("#"+phoneListArr[i]).val(reInputValue);
    } 
	 $("#ch_pre_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2-")
    });
	$("#ch_avp_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2-")
    });
	$("#ch_mvp_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2-")
    });
	$("#ch_trs_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2-")
    });
	$("#ch_sec_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2-")
    });  
	
	var check = <?php echo "\"" . $chapterList[0]->boundary_issues . "\""; ?>;
	
	if(check == 0){
		$("#BoundaryIssue").prop("readonly",true);
		$("#BoundaryIssue").prop("required",false);
	}else{
		$("#BoundaryIssue").prop("readonly",false);
		$("#BoundaryIssue").prop("required",true);
	}	
	
	var pcid = $("#pcid").val();
	if(pcid !=""){
		$.ajax({
            url: '/mimi/checkreportid/'+pcid,
            type: "GET",
            success: function(result) {
				$("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {

            }
        });
    }
   
    var avp = $("#ch_avp_fname").val();
    if(avp ==''){
        $("#ch_avp_fname").prop("readonly",true);
        $("#ch_avp_lname").prop("readonly",true);
        $("#ch_avp_email").prop("readonly",true);
        $("#ch_avp_street").prop("readonly",true);
        $("#ch_avp_city").prop("readonly",true);
        $("#ch_avp_zip").prop("readonly",true);
        $("#ch_avp_phone").prop("readonly",true);
        $("#ch_avp_state").prop("disabled",true);
    }
    var mvp = $("#ch_mvp_fname").val();
    if(mvp ==''){
        $("#ch_mvp_fname").prop("readonly",true);
        $("#ch_mvp_lname").prop("readonly",true);
        $("#ch_mvp_email").prop("readonly",true);
        $("#ch_mvp_street").prop("readonly",true);
        $("#ch_mvp_city").prop("readonly",true);
        $("#ch_mvp_zip").prop("readonly",true);
        $("#ch_mvp_phone").prop("readonly",true);
        $("#ch_mvp_state").prop("disabled",true);
    }
    var trs = $("#ch_trs_fname").val();
    if(trs ==''){
        $("#ch_trs_fname").prop("readonly",true);
        $("#ch_trs_lname").prop("readonly",true);
        $("#ch_trs_email").prop("readonly",true);
        $("#ch_trs_street").prop("readonly",true);
        $("#ch_trs_city").prop("readonly",true);
        $("#ch_trs_zip").prop("readonly",true);
        $("#ch_trs_phone").prop("readonly",true);
        $("#ch_trs_state").prop("disabled",true);
    } 
    var sec = $("#ch_sec_fname").val();
    if(sec ==''){
        $("#ch_sec_fname").prop("readonly",true);
        $("#ch_sec_lname").prop("readonly",true);
        $("#ch_sec_email").prop("readonly",true);
        $("#ch_sec_street").prop("readonly",true);
        $("#ch_sec_city").prop("readonly",true);
        $("#ch_sec_zip").prop("readonly",true);
        $("#ch_sec_phone").prop("readonly",true);
        $("#ch_sec_state").prop("disabled",true);
    }
   
  });
  
  function ConfirmVacant(checkboxid) {
    switch(checkboxid){
					case "AVPVacant":
              if($("#AVPVacant").prop("checked") == true){
                $("#ch_avp_fname").prop("readonly",true);
                $("#ch_avp_lname").prop("readonly",true);
                $("#ch_avp_email").prop("readonly",true);
                $("#ch_avp_street").prop("readonly",true);
                $("#ch_avp_city").prop("readonly",true);
                $("#ch_avp_zip").prop("readonly",true);
                $("#ch_avp_phone").prop("readonly",true);
                $("#ch_avp_state").prop("disabled",true);
                $("#ch_avp_fname").val("");
                $("#ch_avp_lname").val("");
                $("#ch_avp_email").val("");
                $("#ch_avp_street").val("");
                $("#ch_avp_city").val("");
                $("#ch_avp_zip").val("");
                $("#ch_avp_phone").val("");
                $("#ch_avp_state").val("");
              }
              else{
                $("#ch_avp_fname").prop("readonly",false);
                $("#ch_avp_lname").prop("readonly",false);
                $("#ch_avp_email").prop("readonly",false);
                $("#ch_avp_street").prop("readonly",false);
                $("#ch_avp_city").prop("readonly",false);
                $("#ch_avp_zip").prop("readonly",false);
                $("#ch_avp_phone").prop("readonly",false);
                $("#ch_avp_state").prop("disabled",false);

                $("#ch_avp_fname").prop("required",true);
                $("#ch_avp_lname").prop("required",true);
                $("#ch_avp_email").prop("required",true);
                $("#ch_avp_street").prop("required",true);
                $("#ch_avp_city").prop("required",true);
                $("#ch_avp_zip").prop("required",true);
                $("#ch_avp_phone").prop("required",true);
                $("#ch_avp_state").prop("required",true);
              }  
            break; 
          case "MVPVacant":
              if($("#MVPVacant").prop("checked") == true){
                $("#ch_mvp_fname").prop("readonly",true);
                $("#ch_mvp_lname").prop("readonly",true);
                $("#ch_mvp_email").prop("readonly",true);
                $("#ch_mvp_street").prop("readonly",true);
                $("#ch_mvp_city").prop("readonly",true);
                $("#ch_mvp_zip").prop("readonly",true);
                $("#ch_mvp_phone").prop("readonly",true);
                $("#ch_mvp_state").prop("disabled",true);
                $("#ch_mvp_fname").val("");
                $("#ch_mvp_lname").val("");
                $("#ch_mvp_email").val("");
                $("#ch_mvp_street").val("");
                $("#ch_mvp_city").val("");
                $("#ch_mvp_zip").val("");
                $("#ch_mvp_phone").val("");
                $("#ch_mvp_state").val("");
              }
              else{
                $("#ch_mvp_fname").prop("readonly",false);
                $("#ch_mvp_lname").prop("readonly",false);
                $("#ch_mvp_email").prop("readonly",false);
                $("#ch_mvp_street").prop("readonly",false);
                $("#ch_mvp_city").prop("readonly",false);
                $("#ch_mvp_zip").prop("readonly",false);
                $("#ch_mvp_phone").prop("readonly",false);
                $("#ch_mvp_state").prop("disabled",false);
                $("#ch_mvp_fname").prop("required",true);
                $("#ch_mvp_lname").prop("required",true);
                $("#ch_mvp_email").prop("required",true);
                $("#ch_mvp_street").prop("required",true);
                $("#ch_mvp_city").prop("required",true);
                $("#ch_mvp_zip").prop("required",true);
                $("#ch_mvp_phone").prop("required",true);
                $("#ch_mvp_state").prop("required",true);
              }  
            break;
          case "TreasVacant":
              if($("#TreasVacant").prop("checked") == true){
                $("#ch_trs_fname").prop("readonly",true);
                $("#ch_trs_lname").prop("readonly",true);
                $("#ch_trs_email").prop("readonly",true);
                $("#ch_trs_street").prop("readonly",true);
                $("#ch_trs_city").prop("readonly",true);
                $("#ch_trs_zip").prop("readonly",true);
                $("#ch_trs_phone").prop("readonly",true);
                $("#ch_trs_state").prop("disabled",true);
                $("#ch_trs_fname").val("");
                $("#ch_trs_lname").val("");
                $("#ch_trs_email").val("");
                $("#ch_trs_street").val("");
                $("#ch_trs_city").val("");
                $("#ch_trs_zip").val("");
                $("#ch_trs_phone").val("");
                $("#ch_trs_state").val("");
              }
              else{
                $("#ch_trs_fname").prop("readonly",false);
                $("#ch_trs_lname").prop("readonly",false);
                $("#ch_trs_email").prop("readonly",false);
                $("#ch_trs_street").prop("readonly",false);
                $("#ch_trs_city").prop("readonly",false);
                $("#ch_trs_zip").prop("readonly",false);
                $("#ch_trs_phone").prop("readonly",false);
                $("#ch_trs_state").prop("disabled",false);
                $("#ch_trs_fname").prop("required",true);
                $("#ch_trs_lname").prop("required",true);
                $("#ch_trs_email").prop("required",true);
                $("#ch_trs_street").prop("required",true);
                $("#ch_trs_city").prop("required",true);
                $("#ch_trs_zip").prop("required",true);
                $("#ch_trs_phone").prop("required",true);
                $("#ch_trs_state").prop("required",true);
                
              }  
            break; 
          case "SecVacant":
              if($("#SecVacant").prop("checked") == true){
                $("#ch_sec_fname").prop("readonly",true);
                $("#ch_sec_lname").prop("readonly",true);
                $("#ch_sec_email").prop("readonly",true);
                $("#ch_sec_street").prop("readonly",true);
                $("#ch_sec_city").prop("readonly",true);
                $("#ch_sec_zip").prop("readonly",true);
                $("#ch_sec_phone").prop("readonly",true);
                $("#ch_sec_state").prop("disabled",true);
                $("#ch_sec_fname").val("");
                $("#ch_sec_lname").val("");
                $("#ch_sec_email").val("");
                $("#ch_sec_street").val("");
                $("#ch_sec_city").val("");
                $("#ch_sec_zip").val("");
                $("#ch_sec_phone").val("");
                $("#ch_sec_state").val("");
              }
              else{
                $("#ch_sec_fname").prop("readonly",false);
                $("#ch_sec_lname").prop("readonly",false);
                $("#ch_sec_email").prop("readonly",false);
                $("#ch_sec_street").prop("readonly",false);
                $("#ch_sec_city").prop("readonly",false);
                $("#ch_sec_zip").prop("readonly",false);
                $("#ch_sec_phone").prop("readonly",false);
                $("#ch_sec_state").prop("disabled",false);
                $("#ch_sec_fname").prop("required",true);
                $("#ch_sec_lname").prop("required",true);
                $("#ch_sec_email").prop("required",true);
                $("#ch_sec_street").prop("required",true);
                $("#ch_sec_city").prop("required",true);
                $("#ch_sec_zip").prop("required",true);
                $("#ch_sec_phone").prop("required",true);
                $("#ch_sec_state").prop("required",true);
              }  
            break;      
    }      
    
  }

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