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
                    <h4 class="text-center"> EIN: {{ $chapterList[0]->ein }} </h4>
                    <h4 class="text-center">Boundaries: {{ $chapterList[0]->territory }} </h4>
                <div class="col-md-12"><br><br></div>
                    <h4 class="text-center"> {{$chapterList[0]->first_name}} {{$chapterList[0]->last_name}}, {{$boardPositionAbbreviation}}</h4>
                    <p class="description text-center">
                           Welcome to the MOMS information Management Interface, affectionately called MIMI!
                           </br>Here you can view your chapter's information, update your profile, complete End of Year Reports, etc.
                        </p>
                <div id="readOnlyText" class="description text-center">
                        @if($thisDate->month >= 5 && $thisDate->month <= 7)
                            <p><span style="color: red;">All Board Member Information is <strong>READ ONLY</strong> at this time.<br>
                                @if($chapterList[0]->new_board_active != '1')
                                In order to add new board members to MIMI, please complete the Board Election Report.<br>
                            @endif
                            @if($chapterList[0]->new_board_active == '1')
                                If you need to make updates to your listed officers, please contact your Primary Coordinator.</span></p>
                            @endif
                            @if($chapterList[0]->new_board_active == '1')
                                <p>Incoming Board Members have been activated and have full MIMI access.<br>
                                    Outgoing Board Members can still log in and access Financial Reports Only.</p>
                            @endif
                        @endif
                </div>
                </div>

            {{-- </div>
        </div> --}}

        @php
            $admin = DB::table('admin')
                ->select('admin.*',
                    DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'),)
                ->leftJoin('coordinator_details as cd', 'admin.updated_id', '=', 'cd.coordinator_id')
                ->orderBy('admin.id', 'desc') // Assuming 'id' represents the order of insertion
                ->first();

            $eoy_boardreport = $admin->eoy_boardreport;
            $eoy_financialreport = $admin->eoy_financialreport;
            $boardreport_yes = ($eoy_boardreport == 1);
            $financialreport_yes = ($eoy_financialreport == 1);
        @endphp

        {{-- <div class="col-md-12">
            <div class="card card-primary card-outline"> --}}
                    <div class="card-body">
	                    <div class="row">
                    @foreach($chapterList as $list)
                        @if ($thisDate->gte($due_date))
                        <div class=" col-md-12 text-center">
                            @if ($due_date->month === $thisDate->month)
                            <p><span style="color: green;">
                                Your chapter's anniversary month is <strong>{{ $startMonth }}</strong>.&nbsp; Your Re-registration payment is due now.
                            </p>
                            @else
                            <p><span style="color: red;">
                                Your chapter's anniversary month was <strong>{{ $startMonth }}</strong>.&nbsp; Your Re-registration payment is now considered overdue.
                            </p>
                            @endif
                                <a href="{{ route('board.showreregpayment') }}" class="btn btn-primary">
                                    <i class="fas fa-dollar-sign"></i>&nbsp; PAY HERE
                                </a>
                            </div>
                            <div class="col-md-12"><br><br></div>
                        @endif
                        <div class="col-md-12 text-center">
                            <p><span >
                                Reports and Letters available for your chapter can be viewed/downloaded here.
                            </p>
                                @if($list->ein_letter=='1')
                                    <a class="btn btn-primary" href="{{ $chapterList[0]->ein_letter_path }}" target="blank">
                                        <i class="fas fa-university"></i>&nbsp; Chapter EIN Letter
                                    </a>
                                @else
                                    <a class="btn btn-primary disabled" href="#">
                                        <i class="fas fa-university"></i>&nbsp; No EIN Letter on File
                                    </a>
                                @endif
                                <button id="GoodStanding" type="button" class="btn btn-primary" onclick="window.open('{{ route('pdf.chapteringoodstanding', ['id' => $list->id]) }}', '_blank')">
                                    <i class="fas fa-home"></i>&nbsp; Good Standing Chapter Letter
                                </button>

                                @if($financial_report_array->financial_pdf_path!=null)
                                    <a id="btn-download-pdf" href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['financial_pdf_path']; ?>" class="btn btn-primary" ><i class="fas fa-download" ></i>&nbsp; Financial Report PDF</a>
                                @else
                                    <button id="ReportPDF" type="button" class="btn btn-primary" onclick="">
                                        <i class="fas fa-file-pdf"></i>&nbsp; No Financial Report on File
                                    </button>
                                @endif

                        </div>
                        <div class="col-md-12"><br></div>

                        <div class="col-md-12 text-center">
                            <p><span >
                                End of Year Filing for your chapter should be done here.
                            </p>
                                 @if($thisDate->month >= 6 && $thisDate->month <= 12 && $boardreport_yes)
                                        @if($list->new_board_active!='1')
                                            <button id="BoardReport" type="button" class="btn btn-primary" onclick="window.location.href='{{ route('boardinfo.showboardinfo', ['id' => $list->id]) }}'">
                                                <i class="fas fa-users"></i>&nbsp; {{ date('Y') . '-' . (date('Y') + 1) }} Board Report
                                            </button>
                                        @else
                                            <a class="btn btn-primary disabled" href="#">
                                                <i class="fas fa-users"></i>&nbsp; Board Report Activated
                                            </a>
                                        @endif
                                @else
                                    <a class="btn btn-primary disabled" href="#">
                                        <i class="fas fa-users"></i>&nbsp; Board Report Not Available
                                    </a>
                                @endif
                                @if($thisDate->month >= 6 && $thisDate->month <= 12 && $financialreport_yes)
                                        <button id="FinancialReport" type="button" class="btn btn-primary" onclick="window.location.href='{{ route('board.showfinancial', ['id' => $list->id]) }}'">
                                            <i class="fas fa-file-invoice-dollar"></i>&nbsp; {{ date('Y')-1 .'-'.date('Y') }} Financial Report
                                        </button>
                                @else
                                    <a class="btn btn-primary disabled" href="#">
                                        <i class="fas fa-file-invoice-dollar"></i>&nbsp; Financial Report Not Available
                                    </a>
                                @endif
                                @if($thisDate->month >= 7 && $thisDate->month <= 12)
                                    <a href="https://sa.www4.irs.gov/sso/ial1?resumePath=%2Fas%2F5Ad0mGlkzW%2Fresume%2Fas%2Fauthorization.ping&allowInteraction=true&reauth=false&connectionId=SADIPACLIENT&REF=3C53421849B7D5B806E50960DF0AC7530889D9ADE9238D5D3B8B00000069&vnd_pi_requested_resource=https%3A%2F%2Fsa.www4.irs.gov%2Fepostcard%2F&vnd_pi_application_name=EPOSTCARD"
                                        class="btn btn-primary" target="_blank" ><i class="fas fa-globe" ></i>&nbsp; {{ date('Y')-1 }} 990N IRS Online Filing</a>
                                @else
                                    <a class="btn btn-primary disabled" href="#" >
                                        <i class="fas fa-globe"></i>&nbsp; 990N Not Available Until July 1st
                                    </a>
                                @endif
                        </div>

                    @endforeach
                </div>
            </div>

        </div>
    </div>
    <div class="col-md-12">
        <div class="card card-primary card-outline">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label> <span class="field-required">*</span>
                                    <input  type="email" name="ch_pre_email" id="ch_pre_email" class="form-control" placeholder="Email ID" value="{{ $chapterList[0]->bd_email }}" maxlength="50" required >
									<input  type="hidden" id="ch_pre_email_chk" value="{{ $chapterList[0]->bd_email }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label> <span class="field-required">*</span>
                                    <input  type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" placeholder="Phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $chapterList[0]->phone }}" >
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


{{--
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
                         <div class="clearfix"></div> --}}

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
                        <div class="row radio-chk">
                            <div class="col-md-6 avp-field">
                                <div class="form-group">
                                    <label>Email</label><span id="ch_avp_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_avp_email" id="ch_avp_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" maxlength="50">
									<input  type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails[0]->avp_email }}" >
                                </div>
                            </div>
                            <div class="col-md-6 avp-field">
                                <div class="form-group">
                                    <label>Phone</label><span id="ch_avp_phone_req" class="field-required">*</span>
                                    <input  type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control" placeholder="Phone" placeholder="Phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$AVPDetails[0]->avp_phone != ''  ? $AVPDetails[0]->avp_phone : ''}}">
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
                                    <input  type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" placeholder="ZIP Code" value="{{$AVPDetails[0]->avp_zip != ''  ? $AVPDetails[0]->avp_zip : ''}}" maxlength="10"  onkeypress="return isNumber(event)" >
                                </div>
                            </div>
                        </div>
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
                        <div class="row radio-chk">
                            <div class="col-md-6 mvp-field">
                                <div class="form-group">
                                    <label>Email</label><span id="ch_mvp_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$MVPDetails[0]->mvp_email != ''  ? $MVPDetails[0]->mvp_email : ''}}" maxlength="50" >
									<input  type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails[0]->mvp_email }}">
                                </div>
                            </div>
                            <div class="col-md-6 mvp-field">
                                <div class="form-group">
                                    <label>Phone</label><span id="ch_mvp_phone_req" class="field-required">*</span>
                                    <input  type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control" placeholder="Phone" placeholder="Phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$MVPDetails[0]->mvp_phone != ''  ? $MVPDetails[0]->mvp_phone : ''}}">
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

                </div>
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title mb-0">TREASURER</h4>
                    <div class="custom-control custom-switch ml-2">
                        <input type="checkbox" name="TreasVacant" id="TreasVacant" class="custom-control-input" {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                        <label class="custom-control-label" for="TreasVacant">Vacant</label>
                    </div>
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
                        <div class="row radio-chk">
                            <div class="col-md-6 treas-field">
                                <div class="form-group">
                                    <label>Email</label><span id="ch_trs_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_trs_email" id="ch_trs_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$TRSDetails[0]->trs_email != ''  ? $TRSDetails[0]->trs_email : ''}}" maxlength="50" >
									<input  type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails[0]->trs_email }}">
                                </div>
                            </div>
                            <div class="col-md-6 treas-field">
                                <div class="form-group">
                                    <label>Phone</label><span id="ch_trs_phone_req" class="field-required">*</span>
                                    <input  type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control" placeholder="Phone" placeholder="Phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$TRSDetails[0]->trs_phone != ''  ? $TRSDetails[0]->trs_phone : ''}}">
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

                </div>
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title mb-0">SECRETARY</h4>
                    <div class="custom-control custom-switch ml-2">
                        <input type="checkbox" name="SecVacant" id="SecVacant" class="custom-control-input" {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                        <label class="custom-control-label" for="SecVacant">Vacant</label>
                    </div>
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
                        <div class="row radio-chk">
                            <div class="col-md-6 sec-field">
                                <div class="form-group">
                                    <label>Email</label><span id="ch_sec_email_req" class="field-required">*</span>
                                    <input  type="email" name="ch_sec_email" id="ch_sec_email" class="form-control" placeholder="Email ID" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}" maxlength="50" >
									<input  type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails[0]->sec_email }}">
                                </div>
                            </div>
                            <div class="col-md-6 sec-field">
                                <div class="form-group">
                                    <label>Phone</label><span id="ch_sec_phone_req" class="field-required">*</span>
                                    <input  type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control" placeholder="Phone" placeholder="Phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$SECDetails[0]->sec_phone != ''  ? $SECDetails[0]->sec_phone : ''}}" >
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

                </div>
                <div class="card-header">
                    <h4 class="card-title">CHAPTER INFORMATION</h4>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label>Chapter Website</label>
                                <input type="text" name="ch_website" class="form-control"
                                    placeholder="http://www.momsclubofchaptername.com"
                                    value="{{$chapterList[0]->website_url}}" maxlength="50"
                                    id="validate_url" onchange="is_url(); checkWebsiteChanged();">
                            </div>
                        </div>

                        <!-- Static Status Display -->
                        <div class="col-sm-6 col-xs-12" id="staticStatusField" style="display: block;">
                            <div class="form-group">
                                <label>Website Link Status</label>
                                <p>{{ $chapterList[0]->website_status == 0 ? 'Website Not Linked' :
                                    ($chapterList[0]->website_status == 1 ? 'Website Linked' :
                                    ($chapterList[0]->website_status == 2 ? 'Add Link Requested' :
                                    'Do Not Link')) }}</p>
                            </div>
                        </div>

                        <!-- Editable Status Dropdown -->
                        <div class="col-sm-6 col-xs-12" id="editableStatusField" style="display: none;">
                            <div class="form-group">
                                <label>Website Link Status</label>
                                <select id="ch_webstatus" name="ch_webstatus" class="form-control select2" style="width: 100%;" required>
                                    <option value="0" {{$chapterList[0]->website_status == 0 ? 'selected' : ''}} {{ $chapterList[0]->website_status == 0 ? '' : 'disabled' }}>Website Not Linked</option>
                                    <option value="1" {{$chapterList[0]->website_status == 1 ? 'selected' : ''}} {{ $chapterList[0]->website_status == 1 ? '' : 'disabled' }}>Website Linked</option>
                                    <option value="2" {{$chapterList[0]->website_status == 2 ? 'selected' : ''}}>Add Link Requested</option>
                                    <option value="3" {{$chapterList[0]->website_status == 3 ? 'selected' : ''}}>Do Not Link</option>
                                </select>

                                <input type="hidden" name="ch_hid_webstatus" id="ch_hid_webstatus" value="{{ $chapterList[0]->website_status }}">
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
                                    <label><?php echo $a = date('Y'); echo "-"; echo $a + 1;?> BOARD ELECTION REPORT</label>
                                    <p>{{$chapterList[0]->new_board_submitted == '1' ? 'Received' : 'Not Received'}}
                                    {{$chapterList[0]->new_board_active == '1' ? ' & Activated' : ''}}</p>
                                </div>
                            </div>
                            <div class="col-md-6 FinancialReportStatus">
                                <div class="form-group">
                                    <label><?php echo date('Y') - 1 . '-' . date('Y');?> FINANCIAL REPORT</label>
                                    <p>{{$chapterList[0]->financial_report_received == '1' ? 'Received' : 'Not Received'}}
                                    {{$chapterList[0]->financial_report_complete == '1' ? ' & Review Complete' : ''}}</p>
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
                    <button id="Save" type="submit" class="btn btn-primary" onclick="return PreSaveValidate()"><i class="fas fa-save" ></i>&nbsp; Save</button>
                </form>
                    <button type="button" class="btn btn-primary" onclick="showChangePasswordAlert()"><i class="fas fa-lock" ></i>&nbsp; Change Password</button>
                <a href="{{ route('logout') }}" class="btn btn-primary"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                 <span><i class="fas fa-undo" ></i>&nbsp; {{ __('Logout') }}</span>
             </a>
             <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                 @csrf
             </form>
                </div><br>
                    <div class="box-body text-center">
                    {{-- <button type="button" class="btn btn-primary" onclick="window.open('https://groups.google.com/a/momsclub.org/g/2023-24boardlist)"><i class="fa fa-list fa-fw" aria-hidden="true" ></i>&nbsp; BoardList Forum</button> --}}
                    <button type="button"  onclick="window.open('https://momsclub.org/elearning/')" class="btn btn-primary"><i class="fas fa-graduation-cap" ></i>&nbsp; eLearning Library</button>
                    <a href="{{ route('board.resources') }}" class="btn btn-primary"><i class="fas fa-briefcase" ></i>&nbsp; Chapter Resources</a>
                </div>
                </div>

                {{-- <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <form action="{{ route('board.updatepassword') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <!-- Current Password -->
                                    <div class="form-group">
                                        <label for="current_password">Current Password</label>
                                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    </div>

                                    <!-- New Password -->
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                                     </div>

                                    <!-- Confirm New Password -->
                                    <div class="form-group">
                                        <label for="new_password_confirmation">Confirm New Password</label>
                                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" required>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" class="btn btn-primary">Update Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> --}}

            </div>
@endsection
@section('customscript')
<script>
/* Disable fields and buttons  */
$(document).ready(function () {
    var currentDate = new Date();
    var currentMonth = currentDate.getMonth() + 1; // JavaScript months are 0-based

    if (currentMonth >= 5 && currentMonth <= 7) {
        // Disable all input fields, select elements, textareas, and Save button except the logout elements
        $('input, select, textarea').not('#logout-form input, #logout-form select, #logout-form textarea').prop('disabled', true);
        $('#Save').prop('disabled', true);
    } else {
        // If the condition is not met, keep the fields active
        $('input, select, textarea').prop('disabled', false);
        $('#Save').prop('disabled', false);
    }

    // Check the disabled status of EOY Buttons and show the "fields are locked" description if necessary
    if ($('input, select, textarea').prop('disabled')) {
        $('.description').show();
    }
});


/* Disables Web Link Status options 0 and 1 */
//ALWAYS leave thise fiels set to "true"
// document.getElementById('option0').disabled = true;
// document.getElementById('option1').disabled = true;

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

$( document ).ready(function() {
	// var phoneListArr = ["ch_pre_phone", "ch_avp_phone", "ch_mvp_phone", "ch_trs_phone", "ch_sec_phone"];
    // for (var i = phoneListArr.length - 1; i >= 0; i--) {
    //     var inputValue = $("#"+phoneListArr[i]).val();
    //     if(inputValue.length > 10) inputValue = inputValue.substring(0,12);
    //     var reInputValue = inputValue.replace(/(\d{3})(\d{3})/, "$1-$2-");
    //     $("#"+phoneListArr[i]).val(reInputValue);
    // }
	// $("ch_pre_phone").keyup(function() {
    //     this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    // });
	// $("ch_avp_phone").keyup(function() {
    //     this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    // });
    // $("ch_mvp_phone").keyup(function() {
    //     this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    // });
    // $("ch_trs_phone").keyup(function() {
    //     this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    // });
    // $("ch_sec_phone").keyup(function() {
    //     this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    // });

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

// function isPhone() {
//     if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 8) {
//         event.keyCode = 0;
//         alert("Please Enter Number Only");
//         return false;
//     }
// }

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

// function isNumber(evt) {
//     evt = (evt) ? evt : window.event;
//     var charCode = (evt.which) ? evt.which : evt.keyCode;
//         if (charCode > 31 && (charCode < 48 || charCode > 57)) {
//             return false;
//         }
//         return true;
//     }

// function isAlphanumeric(e){
// 	var k;
//         document.all ? k = e.keyCode : k = e.which;
//         return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
//     }

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

    // var phoneListArr = ["ch_pre_phone", "ch_avp_phone", "ch_mvp_phone", "ch_trs_phone", "ch_sec_phone"];

    //     for (var i = 0; i < phoneListArr.length; i++) {
    //         var inputField = document.getElementById(phoneListArr[i]);
    //         var inputValue = inputField.value;
    //         inputValue = inputValue.replace(/-/g, ''); // Remove hyphens
    //         inputValue = inputValue.replace(/\D/g, '').substring(0, 10); // Remove non-digits and limit to 10 digits
    //         inputField.value = inputValue; // Update the input field with the cleaned value
    //     }

//     var NewPassword=document.getElementById("ch_pre_pswd").value;
//         //They changed their password
//         if(document.getElementById("ch_pre_pswd").value != document.getElementById("ch_pre_pswd").getAttribute("value")){
//             if(document.getElementById("ch_pre_pswd").value != document.getElementById("ch_pre_pswd_cnf").value){  //Make sure the password and confirmation match
//                 alert ("The provided passwords do not match, please re-enter your password.");
//                 document.getElementById("ch_pre_pswd_cnf").focus();
//                 return false;
//             }
//             // Make sure the password is the right length
//             else if(NewPassword.length < 7){
//                 alert("Password must be at least 7 characters.");
//                 document.getElementById("ch_pre_pswd").focus();
//                 return false;
//             }
//             else{
//                 document.getElementById("ch_pre_pswd_chg").value="1";
//             }
//         }

    //Okay, all validation passed, save the records to the database
    return true;
}

function showChangePasswordAlert() {
    Swal.fire({
        title: 'Change Password',
        html: `
            <form id="changePasswordForm">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="swal2-input" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="swal2-input" required>
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="swal2-input" required>
                </div>
            </form>
        `,
        confirmButtonText: 'Update Password',
        cancelButtonText: 'Cancel',
        showCancelButton: true,
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const currentPassword = Swal.getPopup().querySelector('#current_password').value;
            const newPassword = Swal.getPopup().querySelector('#new_password').value;
            const confirmNewPassword = Swal.getPopup().querySelector('#new_password_confirmation').value;

            // Validate input fields
            if (!currentPassword || !newPassword || !confirmNewPassword) {
                Swal.showValidationMessage('Please fill out all fields');
                return false;
            }

            if (newPassword !== confirmNewPassword) {
                Swal.showValidationMessage('New passwords do not match');
                return false;
            }

            // Return the AJAX call as a promise to let Swal wait for it
            return $.ajax({
                url: '{{ route("board.checkpassword") }}',  // Check current password route
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    current_password: currentPassword
                }
            }).then(response => {
                if (!response.isValid) {
                    Swal.showValidationMessage('Current password is incorrect');
                    return false;
                }
                return {
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: confirmNewPassword
                };
            }).catch(() => {
                Swal.showValidationMessage('Error verifying current password');
                return false;
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn-sm btn-success'
                },
                didOpen: () => Swal.showLoading()
            });

            // Send the form data via AJAX to update the password
            $.ajax({
                url: '{{ route("board.updatepassword") }}',
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    current_password: result.value.current_password,
                    new_password: result.value.new_password,
                    new_password_confirmation: result.value.new_password_confirmation
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Your password has been updated.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    });
                },
                error: function(jqXHR) {
                    Swal.fire({
                        title: 'Error!',
                        text: `Something went wrong: ${jqXHR.responseText}`,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-danger'
                        }
                    });
                }
            });
        }
    });
}

</script>
@endsection
