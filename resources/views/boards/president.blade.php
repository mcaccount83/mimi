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

    .disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #6c757d; /* Muted color */
}

</style>

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" action='{{ route("board.update", $chapterList[0]->id) }}' autocomplete="off">
                        @csrf

                        <input type="hidden" id="ch_name" value="{{$chapterList[0]->name}}">
                        <input type="hidden" id="ch_state" value="{{$chapterState}}">
                        <input type="hidden" name="ch_hid_webstatus" value="{{ $chapterList[0]->website_status }}">
                        <input type="hidden" id="ch_pre_email_chk" value="{{ $chapterList[0]->bd_email }}">
                        <input type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails[0]->avp_email }}">
                        <input type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails[0]->mvp_email }}">
                        <input type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails[0]->trs_email }}">
                        <input type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails[0]->sec_email }}">

                        <div class="col-md-12">
                            <div class="card card-widget widget-user">
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
                                    <h2 class="text-center">MOMS Club of {{ $chapterList[0]->name }}, {{$chapterState}}</h2>
                                    <h2 class="text-center">{{$chapterList[0]->first_name}} {{$chapterList[0]->last_name}}, {{$boardPositionAbbreviation}}</h2>
                                    <p class="description text-center">
                                        Welcome to the MOMS information Management Interface, affectionately called MIMI!
                                        <br>Here you can view your chapter's information, update your profile, complete End of Year Reports, etc.
                                    </p>
                                    <div id="readOnlyText" class="description text-center">
                                        @if($thisDate->month >= 5 && $thisDate->month <= 7)
                                            <p><span style="color: red;">All Board Member Information is <strong>READ ONLY</strong> at this time.<br>
                                                @if($chapterList[0]->new_board_active != '1')
                                                    In order to add new board members to MIMI, please complete the Board Election Report.<br>
                                                @endif
                                                @if($chapterList[0]->new_board_active == '1')
                                                    If you need to make updates to your listed officers, please contact your Primary Coordinator.</span></p>
                                                <p>Incoming Board Members have been activated and have full MIMI access.<br>
                                                    Outgoing Board Members can still log in and access Financial Reports Only.</p>
                                                @endif
                                            @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>


            @php
                $admin = DB::table('admin')
                    ->select('admin.*',
                        DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'),)
                    ->leftJoin('coordinators as cd', 'admin.updated_id', '=', 'cd.id')
                    ->orderBy('admin.id', 'desc') // Assuming 'id' represents the order of insertion
                    ->first();

                $eoy_boardreport = $admin->eoy_boardreport;
                $eoy_financialreport = $admin->eoy_financialreport;
                $boardreport_yes = ($eoy_boardreport == 1);
                $financialreport_yes = ($eoy_financialreport == 1);
            @endphp


    <div class="row">
            <div class="col-md-8">
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
                            <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control" value="{{ $chapterList[0]->first_name }}" required placeholder="First Name" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" value="{{ $chapterList[0]->last_name }}" required placeholder="Last Name">
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  value="{{ $chapterList[0]->bd_email }}" required placeholder="Email Address" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $chapterList[0]->phone }}" required placeholder="Phone Number" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-10 mb-1">
                            <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" placeholder="Address" value="{{ $chapterList[0]->street_address }}" required >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"><br></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control" placeholder="City" value="{{ $chapterList[0]->city }}" required >
                            </div>
                            <div class="col-sm-3 mb-1">
                                <select name="ch_pre_state" id="ch_pre_state" class="form-control select2" style="width: 100%;" required >
                                    <option value="">Select State</option>
                                        @foreach($stateArr as $state)
                                          <option value="{{$state->state_short_name}}" {{$chapterList[0]->bd_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="col-sm-2 mb-1">
                                <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control" value="{{ $chapterList[0]->zip }}" placeholder="Zip" required >
                            </div>
                        </div>

                        <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-2 mb-1 col-form-label">AVP:</label>
                            <div class="col-sm-10 mt-1 custom-control custom-switch">
                                <input type="checkbox" name="AVPVacant" id="AVPVacant" class="custom-control-input" {{$AVPDetails[0]->avp_fname == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
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
                                <input type="text" name="ch_avp_email" id="ch_avp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" required placeholder="Email Address" >
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
                                    <input type="checkbox" name="MVPVacant" id="MVPVacant" class="custom-control-input" {{$MVPDetails[0]->mvp_fname == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
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
                            <input type="text" name="ch_mvp_email" id="ch_mvp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$MVPDetails[0]->mvp_email != ''  ? $MVPDetails[0]->mvp_email : ''}}" required placeholder="Email Address" >
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
                                    <input type="checkbox" name="TreasVacant" id="TreasVacant" class="custom-control-input" {{$TRSDetails[0]->trs_fname == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
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
                            <input type="text" name="ch_trs_email" id="ch_trs_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$TRSDetails[0]->trs_email != ''  ? $TRSDetails[0]->trs_email : ''}}" required placeholder="Email Address" >
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
                                    <input type="checkbox" name="SecVacant" id="SecVacant" class="custom-control-input" {{$SECDetails[0]->sec_fname == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
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
                            <input type="text" name="ch_sec_email" id="ch_sec_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}" required placeholder="Email Address" >
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
                        <hr>

                        <h5>Contact Info</h5>
                        <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Email:</label>
                            <div class="col-sm-5">
                            <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" value="{{ $chapterList[0]->inquiries_contact }}" placeholder="Inquiries Email Address" required>
                            </div>
                            <div class="col-sm-5">
                            <input type="text" name="ch_email" id="ch_email" class="form-control" value="{{ $chapterList[0]->email }}" placeholder="Chapter Email Address">
                            </div>
                        </div>
                        <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Mailing:</label>
                            <div class="col-sm-10">
                            <input type="text" name="ch_pobox" id="ch_pobox" class="form-control" value="{{ $chapterList[0]->po_box }}" placeholder="Chapter PO Box/Mailing Address" >
                            </div>

                        </div>

                        <hr>
                        <h5>Website & Social Media</h5>
                         <!-- URL Field -->
<div class="form-group row">
    <label class="col-sm-2 col-form-label">Website:</label>
    <div class="col-sm-10">
        <input type="text" name="ch_website" id="ch_website" class="form-control"
               data-inputmask='"mask": "http://*{1,250}.*{2,25}"' data-mask
               value="{{ strpos($chapterList[0]->website_url, 'http://') === 0 ? substr($chapterList[0]->website_url, 7) : $chapterList[0]->website_url }}"
               placeholder="Chapter Website">
    </div>
</div>

<!-- Web Status Field -->
<div class="form-group row">
    <label class="col-sm-2 col-form-label">Web Status</label>
    <div class="col-sm-5">
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
                            <label class="col-sm-2 col-form-label">Social Media:</label>
                            <div class="col-sm-5">
                            <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chapterList[0]->egroup }}"  placeholder="Forum/Group/App" >
                            </div>
                            <div class="col-sm-5">
                            <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chapterList[0]->social1 }}" placeholder="Facebook"  >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-5">
                                <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chapterList[0]->social2 }}"  placeholder="Twitter" >
                            </div>
                            <div class="col-sm-5">
                                <input type="text" name="ch_social3" id="ch_social3" class="form-control" value="{{ $chapterList[0]->social3 }}"  placeholder="Instagram" >
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



            <div class="col-md-4">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">


                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">EIN:</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $chapterList[0]->ein}}</span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Founded:</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $startMonth }} {{ $chapterList[0]->start_year }}</span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Boundaries:</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $chapterList[0]->territory}}</span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Dues Paid:</label>
                            <div class="col-sm-8">
                                <span class="float-right">
                                    @if ($chapterList[0]->members_paid_for)
                                        <b>{{ $chapterList[0]->members_paid_for }} Members</b> on <b><span class="date-mask">{{ $chapterList[0]->dues_last_paid }}</span></b>
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">M2M Donation:</label>
                            <div class="col-sm-8">
                                <span class="float-right">
                                    @if ($chapterList[0]->m2m_payment)
                                        <b>${{ $chapterList[0]->m2m_payment }}</b> on <b><span class="date-mask">{{ $chapterList[0]->m2m_date }}</span></b>
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Sustaining Donation:</label>
                            <div class="col-sm-8">
                                <span class="float-right">
                                    @if ($chapterList[0]->sustaining_donation)
                                        <b>${{ $chapterList[0]->sustaining_donation }}</b> on <b><span class="date-mask">{{ $chapterList[0]->sustaining_date }}</span></b>
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>


                        <span style="color: red;">If anything in this section needs to be updated, please contact your Primary Coordinator.</span><br>

                        <ul class="list-group list-group-unbordered mt-2 mb-3">
                            <li class="list-group-item">

                        <h5>Re-Registration Dues</h5>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Anniversary Month</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $startMonth }}</span>
                            </div>
                        </div>

                        @if ($thisDate->gte($due_date))
                            @if ($due_date->month === $thisDate->month)
                                <span style="color: green;">Your Re-registration payment is due now.<br>
                            @else
                                <span style="color: red;">Your Re-registration payment is now considered overdue.<br>
                            @endif
                            @if($user_type === 'coordinator')
                                <button type="button" class="btn btn-primary btn-sm mt-1 mb-1" onclick="window.location.href='{{ route('chapter.viewreregpayment', ['id' => $chapterList[0]->id]) }}'">PAY HERE</button>
                            @else
                                <button type="button" class="btn btn-primary btn-sm mt-1 mb-1" onclick="window.location.href='{{ route('board.showreregpayment') }}'">PAY HERE</button>
                            @endif
                        @else
                            Your Re-registration payment is not due at this time.
                        @endif
                        </li>

                      <li class="list-group-item">
                        <h5>Resources</h5>
                            @if($chapterList[0]->ein_letter_path != null)
                                <button type="button" class="btn bg-primary btn-sm mb-1" onclick="window.open('{{ $chapterList[0]->ein_letter_path }}', '_blank')">View/Download EIN Letter</button><br>
                            @else
                                <button type="button" class="btn bg-primary btn-sm mb-1 disabled">No EIN Letter on File</button><br>
                            @endif
                            <button id="GoodStanding" type="button" class="btn bg-primary mb-1 btn-sm" onclick="window.open('{{ route('pdf.chapteringoodstanding', ['id' => $chapterList[0]->id]) }}', '_blank')">Good Standing Chapter Letter</button><br>
                            <button id="eLearning" type="button"  onclick="window.open('https://momsclub.org/elearning/')" class="btn bg-primary mb-1 btn-sm">eLearning Library</button><br>
                            <button id="Resources" class="btn bg-primary mb-1 btn-sm" onclick="window.location='{{ route('board.resources') }}'">Chapter Resources</button>
                      </li>
                      <li class="list-group-item">
                            <h5>End of year Filing</h5>
                            @if($thisDate->month >= 6 && $thisDate->month <= 12 && $boardreport_yes)
                                @if($chapterList[0]->new_board_active!='1')
                                    @if($user_type === 'coordinator')
                                        <button id="BoardReport" type="button" class="btn btn-primary btn-sm mb-1" onclick="window.location.href='{{ route('viewas.viewchapterboardinfo', ['id' => $chapterList[0]->id]) }}'">
                                            {{ date('Y') . '-' . (date('Y') + 1) }} Board Report
                                        </button><br>
                                    @else
                                        <button id="BoardReport" type="button" class="btn btn-primary btn-sm mb-1" onclick="window.location.href='{{ route('boardinfo.showboardinfo', ['id' => $chapterList[0]->id]) }}'">
                                            {{ date('Y') . '-' . (date('Y') + 1) }} Board Report
                                        </button><br>
                                    @endif

                                @else
                                    <button id="BoardReport" class="btn btn-primary btn-sm mb-1 disabled">Board Report Activated</button><br>
                                @endif
                            @else
                                <button id="BoardReport" class="btn btn-primary btn-sm mb-1 disabled">Board Report Not Available</button><br>
                            @endif
                            @if($thisDate->month >= 6 && $thisDate->month <= 12 && $financialreport_yes)
                                @if($user_type === 'coordinator')
                                    <button id="FinancialReport" type="button" class="btn btn-primary btn-sm mb-1" onclick="window.location.href='{{ route('viewas.viewchapterfinancial', ['id' => $chapterList[0]->id]) }}'">
                                        {{ date('Y')-1 .'-'.date('Y') }} Financial Report
                                    </button><br>
                                @else
                                    <button id="FinancialReport" type="button" class="btn btn-primary btn-sm mb-1" onclick="window.location.href='{{ route('board.showfinancial', ['id' => $chapterList[0]->id]) }}'">
                                        {{ date('Y')-1 .'-'.date('Y') }} Financial Report
                                    </button><br>
                                @endif
                            @else
                                <button id="FinancialReport" class="btn btn-primary btn-sm mb-1 disabled">Financial Report Not Available</button><br>
                            @endif
                            @if($thisDate->month >= 7 && $thisDate->month <= 12)
                                <a href="https://sa.www4.irs.gov/sso/ial1?resumePath=%2Fas%2F5Ad0mGlkzW%2Fresume%2Fas%2Fauthorization.ping&allowInteraction=true&reauth=false&connectionId=SADIPACLIENT&REF=3C53421849B7D5B806E50960DF0AC7530889D9ADE9238D5D3B8B00000069&vnd_pi_requested_resource=https%3A%2F%2Fsa.www4.irs.gov%2Fepostcard%2F&vnd_pi_application_name=EPOSTCARD"
                                    class="btn btn-primary btn-sm mb-1" target="_blank" >{{ date('Y')-1 }} 990N IRS Online Filing</a>
                            @else
                                <button id="990NLink" class="btn btn-primary btn-sm mb-1 disabled">990N Not Available Until July 1st</button>
                            @endif
                    </li>
                  </ul>

                  <h5>Coordinators</h5>
                  <input type="hidden" id="ch_primarycor" value="{{ $chapterList[0]->primary_coordinator_id }}">
                  <input  type="hidden" id="pcid" value="{{ $chapterList[0]->primary_coordinator_id}}">
                  <div id="display_corlist" ></div>

                    </div>
                <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>


    <div class="card-body text-center">
        <button id="Save" type="submit" class="btn btn-primary" onclick="return PreSaveValidate()"><i class="fas fa-save" ></i>&nbsp; Save</button>

    </form>
        <button id="Password" type="button" class="btn btn-primary" onclick="showChangePasswordAlert()"><i class="fas fa-lock" ></i>&nbsp; Change Password</button>
        <button id="logout-btn" class="btn btn-primary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-undo" ></i>&nbsp; Logout</button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

    </div>
    <!-- /.container- -->
@endsection
@section('customscript')
<script>
/* Disable fields and buttons  */
$(document).ready(function () {
    var currentDate = new Date();
    var currentMonth = currentDate.getMonth() + 1; // JavaScript months are 0-based
    var userType = @json($user_type);

    if (userType === 'coordinator') {
        // Disable all input fields, select elements, textareas, and buttons
        $('input, select, textarea').prop('disabled', true);
        $('#Save, #Password, #logout-btn, #eLearning, #Resources').prop('disabled', true);
        // Disable links by adding a class and modifying their behavior
        $('#display_corlist').addClass('disabled-link').attr('href', '#');

    } else if (currentMonth >= 5 && currentMonth <= 7) {
        // Disable all input fields, select elements, textareas, and Save button
        $('input, select, textarea').prop('disabled', true);
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

    $( document ).ready(function() {
        var pcid = $("#pcid").val();
        if (pcid != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list/") }}' + '/' + pcid,
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

    return true;
}

function checkDuplicateEmail(email, id) {
        $.ajax({
            url: '{{ url("/checkemail/") }}' + '/' + email,
            type: "GET",
            success: function(result) {
                if (result.exists) {
                    alert('This Email already used in the system. Please try with new one.');
                    $("#" + id).val('');
                    $("#" + id).focus();
                }
            },
            error: function(jqXHR, exception) {
                console.error("Error checking email: ", exception);
            }
        });
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
                url: '{{ route("checkpassword") }}',  // Check current password route
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
                url: '{{ route("updatepassword") }}',
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
