@extends('layouts.chapter_theme')

@section('content')

	 
<div class="container">
<div>
	@if ($message = Session::get('success'))
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">×</button>	
         <p>{{ $message }}</p>
		</div>
    @endif
	@if ($message = Session::get('fail'))
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">×</button>	
         <p>{{ $message }}</p>
		</div>
    @endif
</div>
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
                        </a>
                        <h4 class="ein">
                            EIN: {{ $chapterList[0]->ein }}
                        </h4>
                        <p class="description">
                            Boundaries: {{ $chapterList[0]->territory }}
                        </p>
                    </div>
                 <p class="description text-center">
                        Welcome, <b>{{$chapterList[0]->first_name}} {{$chapterList[0]->last_name}}</b>, to the MOMS Club MOMS information Management Interface (MIMI)! Here you can view and update your chapter's information, update, add, or remove board members, etc.</p>
                      
                  <!--   <p class="description text-center" style="color:red;">All Board Member Information is READ ONLY at this time. 
                      <br>In order to add new board members to MIMI, please complete the Board Election Report.
                     <br>If you need to make updates to your current year officers please contact your Primary Coordinator.
                   </p> -->
                </div>
                
            </div>
        </div>
	
        <div class="col-md-12">
		    <div class="card">
               <div class="card-body">
                    <div class="col-md-12 text-center">
                        @foreach($chapterList as $list)
                        
                   <div class="col-md-4 float-left">
                       @if($list->ein_letter=='1')
                      <a class="btn btn-info btn-fill" href="{{ $chapterList[0]->ein_letter_path }}" target="blank">View/Download EIN Letter</a> 
                      	@else
                       <a class="btn btn-info btn-fill" href="#" <?php echo "disabled";?>>No EIN Letter on File</a>  
                       	@endif
                      </div>
                    
                    <div class="col-md-4 float-left">
					 @if($list->new_board_active=='1')
								<a class="btn btn-info btn-fill" href="#" <?php echo "disabled";?>><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Election Report</a>
							@else
							<!--LIVE BUTTON-->
						            <a class="btn btn-info btn-fill" href="<?php echo url("/boardinfo") ?>"><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Election Report</a>
							<!--DISABLED BUTTON-->
									<!--<a class="btn btn-info btn-fill" href="#" <?php echo "disabled";?>><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Election Report</a>-->

							@endif
							

					</div>
					
                    <div class="col-md-4 float-left">
                        <!--LIVE BUTTON-->
					        <a class="btn btn-info btn-fill" href="<?php echo url("/board/financial/{$list->id}") ?>"><?php echo date('Y')-1 .'-'.date('Y');?> Financial Report</a>
					   <!--DISABLED BUTTON-->
					        <!--<a class="btn btn-info btn-fill" href="#" disabled><?php echo date('Y')-1 .'-'.date('Y');?> Financial Report</a>-->
					    
					        
					        @endforeach
					</div>
                    
                    </div>
                </div>
                <form method="POST" action='{{ route("board.update",$chapterList[0]->id) }}' autocomplete="off">
				@csrf 
                <div class="card-header">
                    <h4 class="card-title">PRESIDENT</h4>
                </div>
                <div class="card-body">
                   
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label> <span class="field-required">*</span>
                                    <input   type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control" placeholder="First Name" value="{{ $chapterList[0]->first_name }}" maxlength="50" required  onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label> <span class="field-required">*</span>
                                    <input   type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" placeholder="Last Name" value="{{ $chapterList[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label> <span class="field-required">*</span>
                                    <input  type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" placeholder="Street Address" value="{{ $chapterList[0]->street_address }}" maxlength="250" required >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>City</label> <span class="field-required">*</span>
                                    <input  type="text" name="ch_pre_city" id="ch_pre_city" class="form-control" placeholder="City" value="{{ $chapterList[0]->city }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>State</label> <span class="field-required">*</span>
                                    <select name="ch_pre_state" id="ch_pre_state" class="form-control select2" style="width: 100%;" required >
										<option value="">Select State</option>
											@foreach($stateArr as $state)
											  <option value="{{$state->state_short_name}}" {{$chapterList[0]->bd_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
											@endforeach
										</select>
                                </div>
                            </div>
                            <div class="col-md-4 pl-1">
                                <div class="form-group">
                                    <label>Zip Code</label> <span class="field-required">*</span>
                                    <input  type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control" placeholder="ZIP Code" value="{{ $chapterList[0]->zip }}" maxlength="10" required onkeypress="return isNumber(event)" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email ID</label> <span class="field-required">*</span>
                                    <input  type="email" name="ch_pre_email" id="ch_pre_email" class="form-control" placeholder="Email ID" value="{{ $chapterList[0]->bd_email }}" maxlength="50" required >
									<input  type="hidden" id="ch_pre_email_chk" value="{{ $chapterList[0]->bd_email }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label> <span class="field-required">*</span>
                                    <input  type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" placeholder="Phone" value="{{ $chapterList[0]->phone }}" maxlength="12" required onkeypress="return isPhone(event)" >
                                </div>
                            </div>
                            </div>
							
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password</label>
                                    <input  type="password" class="form-control cls-pswd" placeholder="***********" name="ch_pre_pswd" id="ch_pre_pswd" value="" maxlength="30" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input  type="password" class="form-control cls-pswd" placeholder="***********" name="ch_pre_pswd_cnf" id="ch_pre_pswd_cnf" value="" maxlength="30">
                                    <input  type="hidden" name="ch_pre_pswd_chg" id="ch_pre_pswd_chg" value="0" >
                                </div>
                            </div>
                        </div>
                         <div class="clearfix"></div>
                   
                </div>
                <div class="card-header">
                    <h4 class="card-title">AVP</h4>
                </div>
                <div class="card-body">
                  
                        <div class="row" id="checkRadios">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label><span id="ch_avp_fname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" placeholder="First Name" value="{{$AVPDetails[0]->avp_fname != ''  ? $AVPDetails[0]->avp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label><span id="ch_avp_lname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" placeholder="Last Name" value="{{$AVPDetails[0]->avp_lname != ''  ? $AVPDetails[0]->avp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label><span id="ch_avp_street_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_street" id="ch_avp_street" class="form-control" placeholder="Street Address" value="{{$AVPDetails[0]->avp_addr != ''  ? $AVPDetails[0]->avp_addr : ''}}" maxlength="250" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>City</label><span id="ch_avp_city_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" placeholder="City" value="{{$AVPDetails[0]->avp_city != ''  ? $AVPDetails[0]->avp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>State</label><span id="ch_avp_state_req" class="field-required">*</span>
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
                                    <label>Zip Code</label><span id="ch_avp_zip_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" placeholder="ZIP Code" value="{{$AVPDetails[0]->avp_zip != ''  ? $AVPDetails[0]->avp_zip : ''}}" maxlength="10" ="return isNumber(event)" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Email ID</label><span id="ch_avp_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_avp_email" id="ch_avp_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" maxlength="50">
									<input  type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails[0]->avp_email }}" >
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Phone</label><span id="ch_avp_phone_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control" placeholder="Phone" value="{{$AVPDetails[0]->avp_phone != ''  ? $AVPDetails[0]->avp_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)" >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Vacant</label>
                                    <label style="display: block;"><input  type="checkbox" name="AVPVacant" id="AVPVacant" class="ios-switch green bigswitch" {{$AVPDetails[0]->avp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"  /><div><div></div></div></label>
                                
                                </div>
                            </div>
                            </div>
                  
                </div>
                <div class="card-header">
                    <h4 class="card-title">MVP</h4>
                </div>
                <div class="card-body">
                  
                        <div class="row" id="checkRadios">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label><span id="ch_mvp_fname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control" placeholder="First Name" value="{{$MVPDetails[0]->mvp_fname != ''  ? $MVPDetails[0]->mvp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label><span id="ch_mvp_lname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" placeholder="Last Name" value="{{$MVPDetails[0]->mvp_lname != ''  ? $MVPDetails[0]->mvp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label><span id="ch_mvp_street_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control" placeholder="Street Address" value="{{$MVPDetails[0]->mvp_addr != ''  ? $MVPDetails[0]->mvp_addr : ''}}" maxlength="250" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>City</label><span id="ch_mvp_city_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" placeholder="City" value="{{$MVPDetails[0]->mvp_city != ''  ? $MVPDetails[0]->mvp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>State</label><span id="ch_mvp_state_req" class="field-required">*</span>
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
                                    <label>Zip Code</label><span id="ch_mvp_zip_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_zip" id="ch_mvp_zip" class="form-control" placeholder="ZIP Code" value="{{$MVPDetails[0]->mvp_zip != ''  ? $MVPDetails[0]->mvp_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)" >
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Email ID</label><span id="ch_mvp_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$MVPDetails[0]->mvp_email != ''  ? $MVPDetails[0]->mvp_email : ''}}" maxlength="50" >
									<input  type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails[0]->mvp_email }}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Phone</label><span id="ch_mvp_phone_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control" placeholder="Phone" value="{{$MVPDetails[0]->mvp_phone != ''  ? $MVPDetails[0]->mvp_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)" >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Vacant</label>
                                    <label style="display: block;"><input  type="checkbox" name="MVPVacant" id="MVPVacant" class="ios-switch green bigswitch" {{$MVPDetails[0]->mvp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div></label>
                                
                                </div>
                            </div>
                            </div>
                   
                </div>
                <div class="card-header">
                    <h4 class="card-title">TREASURER</h4>
                </div>
                <div class="card-body">
                   
                        <div class="row" id="checkRadios">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label><span id="ch_trs_fname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control" placeholder="First Name" value="{{$TRSDetails[0]->trs_fname != ''  ? $TRSDetails[0]->trs_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label><span id="ch_trs_lname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" placeholder="Last Name" value="{{$TRSDetails[0]->trs_lname != ''  ? $TRSDetails[0]->trs_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label><span id="ch_trs_street_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_street" id="ch_trs_street" class="form-control" placeholder="Street Address" value="{{$TRSDetails[0]->trs_addr != ''  ? $TRSDetails[0]->trs_addr : ''}}" maxlength="250" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>City</label><span id="ch_trs_city_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" placeholder="City" value="{{$TRSDetails[0]->trs_city != ''  ? $TRSDetails[0]->trs_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>State</label><span id="ch_trs_state_req" class="field-required">*</span>
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
                                    <label>Zip Code</label><span id="ch_trs_zip_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_zip" id="ch_trs_zip" class="form-control" placeholder="ZIP Code" value="{{$TRSDetails[0]->trs_zip != ''  ? $TRSDetails[0]->trs_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)" >
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Email ID</label><span id="ch_trs_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_trs_email" id="ch_trs_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$TRSDetails[0]->trs_email != ''  ? $TRSDetails[0]->trs_email : ''}}" maxlength="50" >
									<input  type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails[0]->trs_email }}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Phone</label><span id="ch_trs_phone_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control" placeholder="Phone" value="{{$TRSDetails[0]->trs_phone != ''  ? $TRSDetails[0]->trs_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Vacant</label>
                                    <label style="display: block;"><input  type="checkbox" name="TreasVacant" id="TreasVacant" class="ios-switch green bigswitch" {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div></label>
                                
                                </div>
                            </div>
                            </div>
                  
                </div>
                <div class="card-header">
                    <h4 class="card-title">SECRETARY</h4>
                </div>
                <div class="card-body">
                  
                        <div class="row" id="checkRadios">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label><span id="ch_sec_fname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" placeholder="First Name" value="{{$SECDetails[0]->sec_fname != ''  ? $SECDetails[0]->sec_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label><span id="ch_sec_lname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" placeholder="Last Name" value="{{$SECDetails[0]->sec_lname != ''  ? $SECDetails[0]->sec_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label><span id="ch_sec_street_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_street" id="ch_sec_street" class="form-control" placeholder="Street Address" value="{{$SECDetails[0]->sec_addr != ''  ? $SECDetails[0]->sec_addr : ''}}" maxlength="250" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>City</label><span id="ch_sec_city_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" placeholder="City" value="{{$SECDetails[0]->sec_city != ''  ? $SECDetails[0]->sec_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>State</label><span id="ch_sec_state_req" class="field-required">*</span>
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
                                    <label>Zip Code</label><span id="ch_sec_zip_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_zip" id="ch_sec_zip" class="form-control" placeholder="ZIP Code" value="{{$SECDetails[0]->sec_zip != ''  ? $SECDetails[0]->sec_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Email ID</label><span id="ch_sec_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_sec_email" id="ch_sec_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}" maxlength="50" >
									<input  type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails[0]->sec_email }}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Phone</label><span id="ch_sec_phone_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control" placeholder="Phone" value="{{$SECDetails[0]->sec_phone != ''  ? $SECDetails[0]->sec_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)" >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Vacant</label>
                                    <label style="display: block;"><input  type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div></label>
                                
                                </div>
                            </div>
                            </div>
                   
                </div>
                <div class="card-header">
                    <h4 class="card-title">CHAPTER INFORMATION</h4>
                </div>
                <div class="card-body">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Chapter Website</label>
                                    <input  type="text" name="ch_website" class="form-control rowheight" id="ch_website" placeholder="http://www.momsclubofchaptername.com" maxlength="150" value="{{$chapterList[0]->website_url}}" onchange="is_url()" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <label>LINK STATUS (LISTED IN CHAPTER DIRECTORY ON MOMSCLUB.ORG WEBSITE):</label><span class="field-required" id="link_status" hidden>*</span>
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>ONINE DISCUSSION GROUP (MEETUP, GOOGLE GROUPS, ETC)</label>
                                    <input  type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chapterList[0]->egroup}}" maxlength="50">
                                </div>
                            </div>
                            </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>FACEBOOK</label>
                                    <input  type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chapterList[0]->social1}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>TWITTER</label>
                                    <input  type="text" name="ch_social2" id="ch_social2"  class="form-control" value="{{ $chapterList[0]->social2}}" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>INSTAGRAM</label>
                                    <input  type="text" name="ch_social3" id="ch_social3" class="form-control" value="{{ $chapterList[0]->social3}}" maxlength="50">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>CHAPTER E-MAIL ADDRESS</label>
                                    <input  type="email" name="ch_email" id="ch_email" class="form-control" value="{{ $chapterList[0]->email}}" maxlength="50">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>E-MAIL ADDRESS TO GIVE TO MOMS INTERESTED IN JOINING YOUR CHAPTER</label><span class="field-required">*</span>
                                    <input  type="email" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" value="{{ $chapterList[0]->inquiries_contact}}" maxlength="30" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>PO BOX</label>
                                    <input  type="text" name="ch_pobox" id="ch_pobox" class="form-control" placeholder="PO Box 123, Happytown, CA 90210" value="{{ $chapterList[0]->po_box}}" maxlength="30">
                                </div>
                            </div>
                        </div>
                 
                </div>
                <div class="card-header">
                    <h4 class="card-title">READ ONLY - PLEASE CONTACT PC IF INCORRECT</h4>
                </div>
                <div class="card-body">
                   
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>FOUNDED MONTH</label>
                                     <select name="ch_founddate" class="form-control select2" style="width: 100%;" required disabled>
                                        <option value="">Select Date</option>
                                        @foreach($foundedMonth as $key=>$val)
                                        <option value="{{$key}}" {{$currentMonth == $key  ? 'selected' : ''}}>{{$val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>FOUNDED Year</label>
                                    <input  type="text" class="form-control" value="{{ $chapterList[0]->start_year}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>RE-REGISTRATION DUES LAST PAID</label>
                                    <input  type="date" class="form-control" value="{{ $chapterList[0]->dues_last_paid}}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>LAST NUMBER OF MEMBERS REGISTERED</label>
                                    <input  type="text" class="form-control" value="{{ $chapterList[0]->members_paid_for}}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Info Received</label>
                                    <label style="display: block;"><input  type="checkbox" class="ios-switch green bigswitch" disabled {{$chapterList[0]->new_board_submitted == '1'  ? 'checked' : ''}} /><div><div></div></div></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo date('Y')-1 .'-'.date('Y');?> Financial Report Received</label>
                                    <label style="display: block;"><input  type="checkbox" class="ios-switch green bigswitch" disabled {{$chapterList[0]->financial_report_received == '1'  ? 'checked' : ''}} /><div><div></div></div></label>
                                
                                </div>
                            </div>
                            </div>
                  
                </div>
                <div class="card-header">
                    <h4 class="card-title">INTERNATIONAL MOMS CLUB COORDINATORS</h4>
                </div>
                <div class="card-body">
                    <div class="col-md-6 float-left">
						<input  type="hidden" id="pcid" value="{{ $chapterList[0]->primary_coordinator_id}}">
						<div id="display_corlist">
						</div>
                        
                    </div>    
                </div>
           
                <div class="card-body card-b"><hr></div>
                   <div class="box-body text-center">
                    <!--LIVE BUTTON-->   
                        <button type="submit" class="btn btn-info btn-fill" onclick="return PreSaveValidate()">Save</button></div>
                    <!--DISABLED BUTTON-->    
                       <!-- <button type="submit" class="btn btn-info btn-fill" href="#" <?php echo "disabled";?>>Save</button></div>-->
                    
                    <div class="box-body text-center">
                    <button type="button" class="btn btn-info btn-fill" onclick="window.open('https://groups.google.com/u/1/a/momsclub.org/g/2022-23boardlist')">BoardList Forum</button>
                    <button type="button"  onclick="window.open('https://momsclub.org/elearning/')" class="btn btn-info btn-fill">eLearning Library</button></div>
                    </div>
                </div>
			</form>		
            </div>
		</div>
        
    </div>
</div>
@endsection
@section('customscript')
<script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/signup-forms/popup/embed.js" data-dojo-config="usePlainJson: true, isDebug: false"></script>
<script>

//Disable Fields for EOY Editing
//    $("#ch_pre_fname").prop("readonly",true);
//    $("#ch_pre_lname").prop("readonly",true);
//    $("#ch_pre_street").prop("readonly",true);
//    $("#ch_pre_city").prop("readonly",true);
//    $("#ch_pre_state").prop("disabled",true);
//    $("#ch_pre_zip").prop("readonly",true);
//    $("#ch_pre_email").prop("readonly",true);
//    $("#ch_pre_phone").prop("readonly",true);
    
//    $("#ch_avp_fname").prop("readonly",true);
//    $("#ch_avp_lname").prop("readonly",true);
//    $("#ch_avp_street").prop("readonly",true);
//    $("#ch_avp_city").prop("readonly",true);
//    $("#ch_avp_state").prop("disabled",true);
//    $("#ch_avp_zip").prop("readonly",true);
//    $("#ch_avp_email").prop("readonly",true);
//    $("#ch_avp_phone").prop("readonly",true);
    
//    $("#ch_mvp_fname").prop("readonly",true);
//    $("#ch_mvp_lname").prop("readonly",true);
//    $("#ch_mvp_street").prop("readonly",true);
//    $("#ch_mvp_city").prop("readonly",true);
//    $("#ch_mvp_state").prop("disabled",true);
//    $("#ch_mvp_zip").prop("readonly",true);
//    $("#ch_mvp_email").prop("readonly",true);
//    $("#ch_mvp_phone").prop("readonly",true);
    
//   $("#ch_trs_fname").prop("readonly",true);
//    $("#ch_trs_lname").prop("readonly",true);
//    $("#ch_trs_street").prop("readonly",true);
//    $("#ch_trs_city").prop("readonly",true);
//    $("#ch_trs_state").prop("disabled",true);
//    $("#ch_trs_zip").prop("readonly",true);
//    $("#ch_trs_email").prop("readonly",true);
//    $("#ch_trs_phone").prop("readonly",true);
    
//    $("#ch_sec_fname").prop("readonly",true);
//   $("#ch_sec_lname").prop("readonly",true);
//    $("#ch_sec_street").prop("readonly",true);
//    $("#ch_sec_city").prop("readonly",true);
 //   $("#ch_sec_state").prop("disabled",true);
//    $("#ch_sec_zip").prop("readonly",true);
//    $("#ch_sec_email").prop("readonly",true);
//    $("#ch_sec_phone").prop("readonly",true);
    
//    $("#ch_website").prop("readonly",true);
//    $("#ch_onlinediss").prop("readonly",true);
//    $("#ch_social1").prop("readonly",true);
//    $("#ch_social2").prop("readonly",true);
//    $("#ch_social3").prop("readonly",true);
//    $("#ch_email").prop("readonly",true);
//    $("#ch_inqemailcontact").prop("readonly",true);
//    $("#ch_pobox").prop("readonly",true);
//    $("add_link_req").prop("readonly",true);
//    $("not_link").prop("readonly",true);


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
  $( document ).ready(function() {
	var phoneListArr = ["ch_pre_phone","ch_avp_phone","ch_mvp_phone","ch_trs_phone","ch_sec_phone"];
    for (var i = phoneListArr.length - 1; i >= 0; i--) {
        var inputValue = $("#"+phoneListArr[i]).val();
        if(inputValue.length > 10) inputValue = inputValue.substring(0,12);
        var reInputValue = inputValue.replace(/(\d{3})(\d{3})/, "$1-$2-");
        $("#"+phoneListArr[i]).val(reInputValue);
    }   
	$("#ch_pre_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
	$("#ch_avp_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
	$("#ch_mvp_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
	$("#ch_trs_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
	$("#ch_sec_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });  
	$('.cls-pswd').on('keypress', function(e) {
		if (e.which == 32)
			return false;
	});
	
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
        $("#ch_avp_fname").prop("",true);
        $("#ch_avp_lname").prop("",true);
        $("#ch_avp_email").prop("",true);
        $("#ch_avp_street").prop("",true);
        $("#ch_avp_city").prop("",true);
        $("#ch_avp_zip").prop("",true);
        $("#ch_avp_phone").prop("",true);
        $("#ch_avp_state").prop("disabled",true);
		
		$("#ch_avp_fname_req").hide();
        $("#ch_avp_lname_req").hide();
        $("#ch_avp_email_req").hide();
        $("#ch_avp_street_req").hide();
        $("#ch_avp_city_req").hide();
        $("#ch_avp_zip_req").hide();
        $("#ch_avp_phone_req").hide();
        $("#ch_avp_state_req").hide();
    }
    var mvp = $("#ch_mvp_fname").val();
    if(mvp ==''){
        $("#ch_mvp_fname").prop("",true);
        $("#ch_mvp_lname").prop("",true);
        $("#ch_mvp_email").prop("",true);
        $("#ch_mvp_street").prop("",true);
        $("#ch_mvp_city").prop("",true);
        $("#ch_mvp_zip").prop("",true);
        $("#ch_mvp_phone").prop("",true);
        $("#ch_mvp_state").prop("disabled",true);
		
		$("#ch_mvp_fname_req").hide();
        $("#ch_mvp_lname_req").hide();
        $("#ch_mvp_email_req").hide();
        $("#ch_mvp_street_req").hide();
        $("#ch_mvp_city_req").hide();
        $("#ch_mvp_zip_req").hide();
        $("#ch_mvp_phone_req").hide();
        $("#ch_mvp_state_req").hide();
    }
    var trs = $("#ch_trs_fname").val();
    if(trs ==''){
        $("#ch_trs_fname").prop("",true);
        $("#ch_trs_lname").prop("",true);
        $("#ch_trs_email").prop("",true);
        $("#ch_trs_street").prop("",true);
        $("#ch_trs_city").prop("",true);
        $("#ch_trs_zip").prop("",true);
        $("#ch_trs_phone").prop("",true);
        $("#ch_trs_state").prop("disabled",true);
		
		$("#ch_trs_fname_req").hide();
        $("#ch_trs_lname_req").hide();
        $("#ch_trs_email_req").hide();
        $("#ch_trs_street_req").hide();
        $("#ch_trs_city_req").hide();
        $("#ch_trs_zip_req").hide();
        $("#ch_trs_phone_req").hide();
        $("#ch_trs_state_req").hide();
    } 
    var sec = $("#ch_sec_fname").val();
    if(sec ==''){
        $("#ch_sec_fname").prop("",true);
        $("#ch_sec_lname").prop("",true);
        $("#ch_sec_email").prop("",true);
        $("#ch_sec_street").prop("",true);
        $("#ch_sec_city").prop("",true);
        $("#ch_sec_zip").prop("",true);
        $("#ch_sec_phone").prop("",true);
        $("#ch_sec_state").prop("disabled",true);
		
		$("#ch_sec_fname_req").hide();
        $("#ch_sec_lname_req").hide();
        $("#ch_sec_email_req").hide();
        $("#ch_sec_street_req").hide();
        $("#ch_sec_city_req").hide();
        $("#ch_sec_zip_req").hide();
        $("#ch_sec_phone_req").hide();
        $("#ch_sec_state_req").hide();
    }
   
  });

function ConfirmVacant(checkboxid) {
    switch(checkboxid){
			case "AVPVacant":
              if($("#AVPVacant").prop("checked") == true){
				$("#ch_avp_fname").prop("",true);
                $("#ch_avp_lname").prop("",true);
                $("#ch_avp_email").prop("",true);
                $("#ch_avp_street").prop("",true);
                $("#ch_avp_city").prop("",true);
                $("#ch_avp_zip").prop("",true);
                $("#ch_avp_phone").prop("",true);
                $("#ch_avp_state").prop("disabled",true);
                $("#ch_avp_fname").val("");
                $("#ch_avp_lname").val("");
                $("#ch_avp_email").val("");
                $("#ch_avp_street").val("");
                $("#ch_avp_city").val("");
                $("#ch_avp_zip").val("");
                $("#ch_avp_phone").val("");
                $("#ch_avp_state").val("");
				
				$("#ch_avp_fname_req").hide();
				$("#ch_avp_lname_req").hide();
				$("#ch_avp_email_req").hide();
				$("#ch_avp_street_req").hide();
				$("#ch_avp_city_req").hide();
				$("#ch_avp_zip_req").hide();
				$("#ch_avp_phone_req").hide();
				$("#ch_avp_state_req").hide();
			 }
              else{
				
                $("#ch_avp_fname").prop("",false);
                $("#ch_avp_lname").prop("",false);
                $("#ch_avp_email").prop("",false);
                $("#ch_avp_street").prop("",false);
                $("#ch_avp_city").prop("",false);
                $("#ch_avp_zip").prop("",false);
                $("#ch_avp_phone").prop("",false);
                $("#ch_avp_state").prop("disabled",false);

                $("#ch_avp_fname").prop("required",true);
                $("#ch_avp_lname").prop("required",true);
                $("#ch_avp_email").prop("required",true);
                $("#ch_avp_street").prop("required",true);
                $("#ch_avp_city").prop("required",true);
                $("#ch_avp_zip").prop("required",true);
                $("#ch_avp_phone").prop("required",true);
                $("#ch_avp_state").prop("required",true);
				
				$("#ch_avp_fname_req").show();
				$("#ch_avp_lname_req").show();
				$("#ch_avp_email_req").show();
				$("#ch_avp_street_req").show();
				$("#ch_avp_city_req").show();
				$("#ch_avp_zip_req").show();
				$("#ch_avp_phone_req").show();
				$("#ch_avp_state_req").show();
			  }  
            break; 
          case "MVPVacant":
              if($("#MVPVacant").prop("checked") == true){
                $("#ch_mvp_fname").prop("",true);
                $("#ch_mvp_lname").prop("",true);
                $("#ch_mvp_email").prop("",true);
                $("#ch_mvp_street").prop("",true);
                $("#ch_mvp_city").prop("",true);
                $("#ch_mvp_zip").prop("",true);
                $("#ch_mvp_phone").prop("",true);
                $("#ch_mvp_state").prop("disabled",true);
                $("#ch_mvp_fname").val("");
                $("#ch_mvp_lname").val("");
                $("#ch_mvp_email").val("");
                $("#ch_mvp_street").val("");
                $("#ch_mvp_city").val("");
                $("#ch_mvp_zip").val("");
                $("#ch_mvp_phone").val("");
                $("#ch_mvp_state").val("");
				
				$("#ch_mvp_fname_req").hide();
				$("#ch_mvp_lname_req").hide();
				$("#ch_mvp_email_req").hide();
				$("#ch_mvp_street_req").hide();
				$("#ch_mvp_city_req").hide();
				$("#ch_mvp_zip_req").hide();
				$("#ch_mvp_phone_req").hide();
				$("#ch_mvp_state_req").hide();
              }
              else{
                $("#ch_mvp_fname").prop("",false);
                $("#ch_mvp_lname").prop("",false);
                $("#ch_mvp_email").prop("",false);
                $("#ch_mvp_street").prop("",false);
                $("#ch_mvp_city").prop("",false);
                $("#ch_mvp_zip").prop("",false);
                $("#ch_mvp_phone").prop("",false);
                $("#ch_mvp_state").prop("disabled",false);
                $("#ch_mvp_fname").prop("required",true);
                $("#ch_mvp_lname").prop("required",true);
                $("#ch_mvp_email").prop("required",true);
                $("#ch_mvp_street").prop("required",true);
                $("#ch_mvp_city").prop("required",true);
                $("#ch_mvp_zip").prop("required",true);
                $("#ch_mvp_phone").prop("required",true);
                $("#ch_mvp_state").prop("required",true);
				
				$("#ch_mvp_fname_req").show();
				$("#ch_mvp_lname_req").show();
				$("#ch_mvp_email_req").show();
				$("#ch_mvp_street_req").show();
				$("#ch_mvp_city_req").show();
				$("#ch_mvp_zip_req").show();
				$("#ch_mvp_phone_req").show();
				$("#ch_mvp_state_req").show();
              }  
            break;
          case "TreasVacant":
              if($("#TreasVacant").prop("checked") == true){
                $("#ch_trs_fname").prop("",true);
                $("#ch_trs_lname").prop("",true);
                $("#ch_trs_email").prop("",true);
                $("#ch_trs_street").prop("",true);
                $("#ch_trs_city").prop("",true);
                $("#ch_trs_zip").prop("",true);
                $("#ch_trs_phone").prop("",true);
                $("#ch_trs_state").prop("disabled",true);
                $("#ch_trs_fname").val("");
                $("#ch_trs_lname").val("");
                $("#ch_trs_email").val("");
                $("#ch_trs_street").val("");
                $("#ch_trs_city").val("");
                $("#ch_trs_zip").val("");
                $("#ch_trs_phone").val("");
                $("#ch_trs_state").val("");
				
				$("#ch_trs_fname_req").hide();
				$("#ch_trs_lname_req").hide();
				$("#ch_trs_email_req").hide();
				$("#ch_trs_street_req").hide();
				$("#ch_trs_city_req").hide();
				$("#ch_trs_zip_req").hide();
				$("#ch_trs_phone_req").hide();
				$("#ch_trs_state_req").hide();
              }
              else{
                $("#ch_trs_fname").prop("",false);
                $("#ch_trs_lname").prop("",false);
                $("#ch_trs_email").prop("",false);
                $("#ch_trs_street").prop("",false);
                $("#ch_trs_city").prop("",false);
                $("#ch_trs_zip").prop("",false);
                $("#ch_trs_phone").prop("",false);
                $("#ch_trs_state").prop("disabled",false);
                $("#ch_trs_fname").prop("required",true);
                $("#ch_trs_lname").prop("required",true);
                $("#ch_trs_email").prop("required",true);
                $("#ch_trs_street").prop("required",true);
                $("#ch_trs_city").prop("required",true);
                $("#ch_trs_zip").prop("required",true);
                $("#ch_trs_phone").prop("required",true);
                $("#ch_trs_state").prop("required",true);
				
				$("#ch_trs_fname_req").show();
				$("#ch_trs_lname_req").show();
				$("#ch_trs_email_req").show();
				$("#ch_trs_street_req").show();
				$("#ch_trs_city_req").show();
				$("#ch_trs_zip_req").show();
				$("#ch_trs_phone_req").show();
				$("#ch_trs_state_req").show();
                
              }  
            break; 
          case "SecVacant":
              if($("#SecVacant").prop("checked") == true){
                $("#ch_sec_fname").prop("",true);
                $("#ch_sec_lname").prop("",true);
                $("#ch_sec_email").prop("",true);
                $("#ch_sec_street").prop("",true);
                $("#ch_sec_city").prop("",true);
                $("#ch_sec_zip").prop("",true);
                $("#ch_sec_phone").prop("",true);
                $("#ch_sec_state").prop("disabled",true);
                $("#ch_sec_fname").val("");
                $("#ch_sec_lname").val("");
                $("#ch_sec_email").val("");
                $("#ch_sec_street").val("");
                $("#ch_sec_city").val("");
                $("#ch_sec_zip").val("");
                $("#ch_sec_phone").val("");
                $("#ch_sec_state").val("");
				
				$("#ch_sec_fname_req").hide();
				$("#ch_sec_lname_req").hide();
				$("#ch_sec_email_req").hide();
				$("#ch_sec_street_req").hide();
				$("#ch_sec_city_req").hide();
				$("#ch_sec_zip_req").hide();
				$("#ch_sec_phone_req").hide();
				$("#ch_sec_state_req").hide();
              }
              else{
                $("#ch_sec_fname").prop("",false);
                $("#ch_sec_lname").prop("",false);
                $("#ch_sec_email").prop("",false);
                $("#ch_sec_street").prop("",false);
                $("#ch_sec_city").prop("",false);
                $("#ch_sec_zip").prop("",false);
                $("#ch_sec_phone").prop("",false);
                $("#ch_sec_state").prop("disabled",false);
                $("#ch_sec_fname").prop("required",true);
                $("#ch_sec_lname").prop("required",true);
                $("#ch_sec_email").prop("required",true);
                $("#ch_sec_street").prop("required",true);
                $("#ch_sec_city").prop("required",true);
                $("#ch_sec_zip").prop("required",true);
                $("#ch_sec_phone").prop("required",true);
                $("#ch_sec_state").prop("required",true);
				
				$("#ch_sec_fname_req").show();
				$("#ch_sec_lname_req").show();
				$("#ch_sec_email_req").show();
				$("#ch_sec_street_req").show();
				$("#ch_sec_city_req").show();
				$("#ch_sec_zip_req").show();
				$("#ch_sec_phone_req").show();
				$("#ch_sec_state_req").show();
              }  
            break;      
    }      
    
  }  
  
  //submit validation function
  function PreSaveValidate(){
    var errMessage="";
        //if($("#ch_pre_email").val() != "" || $("#ch_avp_email").val() != "" || $("#ch_mvp_email").val() != "" || $("#ch_trs_email").val() != "" || $("#ch_sec_email").val() != ""){
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
          
          if(errMessage.length > 0){
            alert (errMessage);	
            return false;
          }

          var NewPassword=document.getElementById("ch_pre_pswd").value;
				
				//They changed their password
				if(document.getElementById("ch_pre_pswd").value != document.getElementById("ch_pre_pswd").getAttribute("value")){
					if(document.getElementById("ch_pre_pswd").value != document.getElementById("ch_pre_pswd_cnf").value){  //Make sure the password and confirmation match
						alert ("The provided passwords do not match, please re-enter your password.");
						document.getElementById("ch_pre_pswd_cnf").focus();
						return false;
					}
					// Make sure the password is the right length
					else if(NewPassword.length < 7){
						alert("Password must be at least 7 characters.");
						document.getElementById("ch_pre_pswd").focus(); 
						return false;
					}
					else{
						document.getElementById("ch_pre_pswd_chg").value="1";
					}
					
				}
        //}
       						
		//Okay, all validation passed, save the records to the database
		return true;
	}
  

    
</script>  
@endsection