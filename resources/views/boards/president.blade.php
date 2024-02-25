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
                @php
                    $thisDate = \Illuminate\Support\Carbon::now();
                @endphp
                    <div class="author">
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
                            <b>{{$chapterList[0]->first_name}} {{$chapterList[0]->last_name}}, {{$boardPositionAbbreviation}}</b>
                        </p>
                    <p class="description text-center">
                           Welcome to the MOMS Club's "MOMS information Management Interface" -- MIMI!
                           </br>Here you can view your chapter's information, update your profile, complete End of Year Reports, etc.
                        </p>
                        @if($thisDate->month >= 5 && $thisDate->month <= 8)
                         <div id="readOnlyText" class="description text-center">
                            <p><span style="color: red;"><strong>All Board Member Information is READ ONLY at this time.<br>
                            In order to add new board members to MIMI, please complete the Board Election Report.<br>
                            If you need to make updates to your current year officers, please contact your Primary Coordinator.</strong></span></p>
                        </div>
                        @endif
                </div>

            </div>
        </div>

        <div class="col-md-12">
		    <div class="card">
               <div class="card-body">
                @foreach($chapterList as $list)

                @if ($thisDate->gte($due_date))
                    @if ($due_date->month === $thisDate->month)
                        <div class="col-md-12" style="color: green;"><center>Your chapter's anniversary month is <strong>{{ $startMonth }}</strong>.&nbsp; Your Re-registration payment is due now.</center></div>
                    @else
                        <div class="col-md-12" style="color: red;"><center>Your chapter's anniversary month was <strong>{{ $startMonth }}</strong>.&nbsp; Your Re-registration payment is now considered overdue.</center></div>
                    @endif
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 text-center">
                        <a href="{{ route('board.showreregpayment') }}" class="btn btn-info btn-fill"><i class="fa fa-money fa-fw" aria-hidden="true" ></i>&nbsp; PAY HERE</a>
                    </div>
                    <hr>
                <div class="col-md-12"><br></div>
                @endif

                @if($thisDate->month >= 1 && $thisDate->month <= 5)
                <div class="col-md-12"><br></div>
                    <div class="col-md-12 text-center">
                    <div class="col-md-4 float-left">
                        @if($list->ein_letter=='1')
                      <a class="btn btn-info btn-fill" href="{{ $chapterList[0]->ein_letter_path }}" target="blank"><i class="fa fa-bank fa-fw" aria-hidden="true" ></i>&nbsp; View/Download EIN Letter</a>
                      	@else
                       <a class="btn btn-info btn-fill" href="#" <?php echo "disabled";?>><i class="fa fa-bank fa-fw" aria-hidden="true" ></i>&nbsp; No EIN Letter on File</a>
                       	@endif
                      </div>
                      <div id="reportStatusText" class="description text-center">
                        <p><strong><?php echo date('Y')-1 .'-'.date('Y');?> EOY Reports are not available at this time.</strong></p>
                    </div>
                @endif

                @if($thisDate->month >= 6 && $thisDate->month <= 12)
                    <div class="col-md-4 float-left">
                        @if($list->new_board_active=='1')
                        <button id="BoardReportAlwaysDisabled" type="button"  class="btn btn-info btn-fill" onclick="window.location.href='{{ route('boardinfo.showboardinfo', ['id' => $list->id]) }}'">
                            <i class="fa fa-user-plus fa-fw" aria-hidden="true" ></i>&nbsp; {{ date('Y') . '-' . (date('Y') + 1) }} Board Election Report
                        </button>
                    @else
                        <button id="BoardReport" type="button" class="btn btn-info btn-fill" onclick="window.location.href='{{ route('boardinfo.showboardinfo', ['id' => $list->id]) }}'">
                            <i class="fa fa-user-plus fa-fw" aria-hidden="true" ></i>&nbsp; {{ date('Y') . '-' . (date('Y') + 1) }} Board Election Report
                        </button>
                    @endif
                </div>
                <div class="col-md-4 float-left">
                        <button id="FinancialReport" type="button" class="btn btn-info btn-fill" onclick="window.location.href='{{ route('board.showfinancial', ['id' => $list->id]) }}'">
                            <i class="fa fa-usd fa-fw" aria-hidden="true" ></i>&nbsp; {{ date('Y')-1 .'-'.date('Y') }} Financial Report
                        </button>
					</div>
                @endif

                @endforeach

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
                                    <label>Update Password</label>
                                    <input  type="password" class="form-control cls-pswd" placeholder="***********" name="ch_pre_pswd" id="ch_pre_pswd" value="" maxlength="30" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirm Updated Password</label>
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
                            <div class="col-md-6 avp-field">
                                <div class="form-group">
                                    <label>First Name</label><span id="ch_avp_fname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" placeholder="First Name" value="{{$AVPDetails[0]->avp_fname != ''  ? $AVPDetails[0]->avp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                            <div class="col-md-6 avp-field">
                                <div class="form-group">
                                    <label>Last Name</label><span id="ch_avp_lname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" placeholder="Last Name" value="{{$AVPDetails[0]->avp_lname != ''  ? $AVPDetails[0]->avp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 avp-field">
                                <div class="form-group">
                                    <label>Street Address</label><span id="ch_avp_street_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_street" id="ch_avp_street" class="form-control" placeholder="Street Address" value="{{$AVPDetails[0]->avp_addr != ''  ? $AVPDetails[0]->avp_addr : ''}}" maxlength="250" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1 avp-field">
                                <div class="form-group">
                                    <label>City</label><span id="ch_avp_city_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" placeholder="City" value="{{$AVPDetails[0]->avp_city != ''  ? $AVPDetails[0]->avp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                            <div class="col-md-4 pr-1 avp-field">
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
                            <div class="col-md-4 pl-1 avp-field">
                                <div class="form-group">
                                    <label>Zip Code</label><span id="ch_avp_zip_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" placeholder="ZIP Code" value="{{$AVPDetails[0]->avp_zip != ''  ? $AVPDetails[0]->avp_zip : ''}}" maxlength="10" ="return isNumber(event)" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5 avp-field">
                                <div class="form-group">
                                    <label>Email ID</label><span id="ch_avp_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_avp_email" id="ch_avp_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" maxlength="50">
									<input  type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails[0]->avp_email }}" >
                                </div>
                            </div>
                            <div class="col-md-5 avp-field">
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
                            <div class="col-md-6 mvp-field">
                                <div class="form-group">
                                    <label>First Name</label><span id="ch_mvp_fname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control" placeholder="First Name" value="{{$MVPDetails[0]->mvp_fname != ''  ? $MVPDetails[0]->mvp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6 mvp-field">
                                <div class="form-group">
                                    <label>Last Name</label><span id="ch_mvp_lname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" placeholder="Last Name" value="{{$MVPDetails[0]->mvp_lname != ''  ? $MVPDetails[0]->mvp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mvp-field">
                                <div class="form-group ">
                                    <label>Street Address</label><span id="ch_mvp_street_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control" placeholder="Street Address" value="{{$MVPDetails[0]->mvp_addr != ''  ? $MVPDetails[0]->mvp_addr : ''}}" maxlength="250" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1 mvp-field">
                                <div class="form-group">
                                    <label>City</label><span id="ch_mvp_city_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" placeholder="City" value="{{$MVPDetails[0]->mvp_city != ''  ? $MVPDetails[0]->mvp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1 mvp-field">
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
                            <div class="col-md-4 pl-1 mvp-field">
                                <div class="form-group">
                                    <label>Zip Code</label><span id="ch_mvp_zip_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_zip" id="ch_mvp_zip" class="form-control" placeholder="ZIP Code" value="{{$MVPDetails[0]->mvp_zip != ''  ? $MVPDetails[0]->mvp_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)" >
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5 mvp-field">
                                <div class="form-group">
                                    <label>Email ID</label><span id="ch_mvp_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$MVPDetails[0]->mvp_email != ''  ? $MVPDetails[0]->mvp_email : ''}}" maxlength="50" >
									<input  type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails[0]->mvp_email }}">
                                </div>
                            </div>
                            <div class="col-md-5 mvp-field">
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
                            <div class="col-md-6 treas-field">
                                <div class="form-group">
                                    <label>First Name</label><span id="ch_trs_fname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control" placeholder="First Name" value="{{$TRSDetails[0]->trs_fname != ''  ? $TRSDetails[0]->trs_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                            <div class="col-md-6 treas-field">
                                <div class="form-group">
                                    <label>Last Name</label><span id="ch_trs_lname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" placeholder="Last Name" value="{{$TRSDetails[0]->trs_lname != ''  ? $TRSDetails[0]->trs_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 treas-field">
                                <div class="form-group">
                                    <label>Street Address</label><span id="ch_trs_street_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_street" id="ch_trs_street" class="form-control" placeholder="Street Address" value="{{$TRSDetails[0]->trs_addr != ''  ? $TRSDetails[0]->trs_addr : ''}}" maxlength="250" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1 treas-field">
                                <div class="form-group">
                                    <label>City</label><span id="ch_trs_city_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" placeholder="City" value="{{$TRSDetails[0]->trs_city != ''  ? $TRSDetails[0]->trs_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-4 pr-1 treas-field">
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
                            <div class="col-md-4 pl-1 treas-field">
                                <div class="form-group">
                                    <label>Zip Code</label><span id="ch_trs_zip_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_zip" id="ch_trs_zip" class="form-control" placeholder="ZIP Code" value="{{$TRSDetails[0]->trs_zip != ''  ? $TRSDetails[0]->trs_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)" >
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5 treas-field">
                                <div class="form-group">
                                    <label>Email ID</label><span id="ch_trs_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_trs_email" id="ch_trs_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$TRSDetails[0]->trs_email != ''  ? $TRSDetails[0]->trs_email : ''}}" maxlength="50" >
									<input  type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails[0]->trs_email }}">
                                </div>
                            </div>
                            <div class="col-md-5 treas-field">
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
                            <div class="col-md-6 sec-field">
                                <div class="form-group">
                                    <label>First Name</label><span id="ch_sec_fname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" placeholder="First Name" value="{{$SECDetails[0]->sec_fname != ''  ? $SECDetails[0]->sec_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                            <div class="col-md-6 sec-field">
                                <div class="form-group">
                                    <label>Last Name</label><span id="ch_sec_lname_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" placeholder="Last Name" value="{{$SECDetails[0]->sec_lname != ''  ? $SECDetails[0]->sec_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 sec-field">
                                <div class="form-group">
                                    <label>Street Address</label><span id="ch_sec_street_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_street" id="ch_sec_street" class="form-control" placeholder="Street Address" value="{{$SECDetails[0]->sec_addr != ''  ? $SECDetails[0]->sec_addr : ''}}" maxlength="250" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1 sec-field">
                                <div class="form-group">
                                    <label>City</label><span id="ch_sec_city_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" placeholder="City" value="{{$SECDetails[0]->sec_city != ''  ? $SECDetails[0]->sec_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
                                </div>
                            </div>
                            <div class="col-md-4 pr-1 sec-field">
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
                            <div class="col-md-4 pl-1 sec-field">
                                <div class="form-group">
                                    <label>Zip Code</label><span id="ch_sec_zip_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_zip" id="ch_sec_zip" class="form-control" placeholder="ZIP Code" value="{{$SECDetails[0]->sec_zip != ''  ? $SECDetails[0]->sec_zip : ''}}" maxlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-5 sec-field">
                                <div class="form-group">
                                    <label>Email ID</label><span id="ch_sec_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_sec_email" id="ch_sec_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}" maxlength="50" >
									<input  type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails[0]->sec_email }}">
                                </div>
                            </div>
                            <div class="col-md-5 sec-field">
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
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                  <label>Chapter Website</label>
                                  <input type="text" name="ch_website" class="form-control my-colorpicker1" placeholder="http://www.momsclubofchaptername.com" value="{{$chapterList[0]->website_url}}" maxlength="50" id="validate_url" onchange="is_url(); updateWebsiteStatus();">
                                </div>
                                </div>
                                  <!-- /.form group -->
                                  <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label>Website Link Status</label> <span class="field-required">*</span>
                                        <select id="ch_webstatus" name="ch_webstatus" class="form-control select2" style="width: 100%;" required>
                                            <option value="0" id="option0" {{$chapterList[0]->website_status == 0 ? 'selected' : ''}} disabled>Website Not Linked</option>
                                            <option value="1" id="option1" {{$chapterList[0]->website_status == 1 ? 'selected' : ''}} disabled>Website Linked</option>
                                            <option value="2" id="option2" {{$chapterList[0]->website_status == 2 ? 'selected' : ''}}>Add Link Requested</option>
                                            <option value="3" id="option3" {{$chapterList[0]->website_status == 3 ? 'selected' : ''}}>Do Not Link</option>
                                        </select>
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
                                <p>{{$currentMonthAbbreviation}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>FOUNDED YEAR</label>
                                <p>{{$chapterList[0]->start_year}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>RE-REGISTRATION DUES LAST PAID</label>
                                <p>{{\Illuminate\Support\Carbon::parse($chapterList[0]->dues_last_paid)->format('m-d-Y')}}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>LAST NUMBER OF MEMBERS REGISTERED</label>
                                <p>{{ $chapterList[0]->members_paid_for}}</p>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-md-6 BoardInfoStatus">
                                <div class="form-group">
                                    <label><?php echo $a = date('Y'); echo "-"; echo $a + 1;?> Board Info Received</label>
                                    <p>{{$chapterList[0]->new_board_submitted == '1' ? 'Received' : 'Not Received'}}</p>
                                </div>
                            </div>
                            <div class="col-md-6 FinancialReportStatus">
                                <div class="form-group">
                                    <label><?php echo date('Y') - 1 . '-' . date('Y');?> Financial Report Received</label>
                                    <p>{{$chapterList[0]->financial_report_received == '1' ? 'Received' : 'Not Received'}}</p>
                                </div>
                            </div>
                </div>
            </div>

                <div class="card-header">
                    <h4 class="card-title">INTERNATIONAL MOMS CLUB COORDINATORS</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                    <div class="col-md-6">
						<input  type="hidden" id="pcid" value="{{ $chapterList[0]->primary_coordinator_id}}">
						<div id="display_corlist">
						</div>
                    </div>
                </div>

                <div class="card-body card-b"><hr></div>
                   <div class="box-body text-center">
                    <button id="Save" type="submit" class="btn btn-info btn-fill" onclick="return PreSaveValidate()"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button></div><br>
                    <div class="box-body text-center">
                    {{-- <button type="button" class="btn btn-info btn-fill" onclick="window.open('https://groups.google.com/a/momsclub.org/g/2023-24boardlist)"><i class="fa fa-list fa-fw" aria-hidden="true" ></i>&nbsp; BoardList Forum</button> --}}
                    <button type="button"  onclick="window.open('https://momsclub.org/elearning/')" class="btn btn-info btn-fill"><i class="fa fa-graduation-cap fa-fw" aria-hidden="true" ></i>&nbsp; eLearning Library</button>
                    <a href="{{ route('board.resources') }}" class="btn btn-info btn-fill"><i class="fa fa-briefcase fa-fw" aria-hidden="true" ></i>&nbsp; Chapter Resources</a>
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
/* Disable fields and buttons  */
$(document).ready(function () {
    var currentDate = new Date();
    var currentMonth = currentDate.getMonth() + 1; // JavaScript months are 0-based

    if (currentMonth >= 5 && currentMonth <= 8) {
        // Disable all input fields, select elements, textareas and Save button
        $('input, select, textarea').prop('disabled', true);
        $('#Save').prop('disabled', true);
    } else {
        // If the condition is not met, keep the fields active
        $('input, select, textarea').prop('disabled', false);
        $('#Save').prop('disabled', false);
    }

        // //Update to show/hide for true/false
        // $('#readOnlyText').hide();  /*read only text (.show/.hide to change visibility)*/
        // $('input, select, textarea').prop('disabled', false);  /*fields on page (true disables fields for editing)*/
        // $('#Save').prop('disabled', false);  /*save button (true grays out button)*/

        // $('#reportStatusText').show();  /*report status text (.show/.hide to change visibility)*/
        // $('#BoardReport').hide();  /*board report button (.show/.hide to change visibility)*/
        // $('#BoardReportAlwaysDisabled').hide();  /*board report button (.show/.hide to change visibility)*/
        // $('#FinancialReport').hide();  /*inancial report button (.show/.hide to change visibility)*/

        // $('.BoardInfoStatus').hide();  /*board info status (.show/.hide to change visibility)*/
        // $('.FinancialReportStatus').hide();  /*financial report status (.show/.hide to change visibility)*/

        // //ALWAYS leave thise fiels set to "true" it works on conditional logic for submtited Election Report
        // $('#BoardReportAlwaysDisabled').prop('disabled', true);

    //Check the disabled status of EOY Buttons and show the "fields are locked" description if necessary
    if ($('input, select, textarea').prop('disabled')) {
            $('.description').show();
        }
});

/* Disables Web Link Status options 0 and 1 */
//ALWAYS leave thise fiels set to "true"
document.getElementById('option0').disabled = true;
document.getElementById('option1').disabled = true;

$( document ).ready(function() {
	var phoneListArr = ["ch_pre_phone", "ch_avp_phone", "ch_mvp_phone", "ch_trs_phone", "ch_sec_phone"];
    for (var i = phoneListArr.length - 1; i >= 0; i--) {
        var inputValue = $("#"+phoneListArr[i]).val();
        if(inputValue.length > 10) inputValue = inputValue.substring(0,12);
        var reInputValue = inputValue.replace(/(\d{3})(\d{3})/, "$1-$2-");
        $("#"+phoneListArr[i]).val(reInputValue);
    }
	$("ch_pre_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
	$("ch_avp_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
    $("ch_mvp_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
    $("ch_trs_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
    $("ch_sec_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });

    var pcid = $("#pcid").val();
    if (pcid != "") {
        $.ajax({
            url: '{{ url("/checkreportid/") }}' + '/' + pcid,
            type: "GET",
            success: function (result) {
                console.log("AJAX result:", result);
                $("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {
                console.log("AJAX error:", exception);
            }
        });
    }

    $('.cls-pswd').on('keypress', function(e) {
    if (e.which == 32)
        return false;
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

function isPhone() {
    if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 8) {
        event.keyCode = 0;
        alert("Please Enter Number Only");
        return false;
    }
}

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




function PreSaveValidate(){
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

          if(errMessage.length > 0){
            alert (errMessage);
            return false;
          }

    var phoneListArr = ["ch_pre_phone", "ch_avp_phone", "ch_mvp_phone", "ch_trs_phone", "ch_sec_phone"];

        for (var i = 0; i < phoneListArr.length; i++) {
            var inputField = document.getElementById(phoneListArr[i]);
            var inputValue = inputField.value;
            inputValue = inputValue.replace(/-/g, ''); // Remove hyphens
            inputValue = inputValue.replace(/\D/g, '').substring(0, 10); // Remove non-digits and limit to 10 digits
            inputField.value = inputValue; // Update the input field with the cleaned value
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

    //Okay, all validation passed, save the records to the database
    return true;
}

</script>
@endsection
