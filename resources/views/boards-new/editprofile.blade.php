@extends('layouts.coordinator_theme')

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" action='{{ route("board-new.updateprofile", $chDetails->id) }}' autocomplete="off">
                        @csrf

                        <input type="hidden" id="ch_name" value="{{$chDetails->name}}">
                        <input type="hidden" id="ch_state" value="{{$stateShortName}}">
                        <input type="hidden" name="ch_hid_webstatus" value="{{ $chDetails->website_status }}">
                        <input type="hidden" id="ch_pre_email_chk" value="{{ $PresDetails->email }}">
                        <input type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails->email }}">
                        <input type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails->email }}">
                        <input type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails->email }}">
                        <input type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails->email }}">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card bg-primary">
                                    <div class="card-body text-center">
                                        <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h2 class="text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>

                                    @if ($bdPositionId == \App\Enums\BoardPosition::PRES)
                                        <h2 class="text-center">{{$PresDetails->first_name}} {{$PresDetails->last_name}}, {{$PresDetails->position?->position}}</h2>
                                    @elseif ($bdPositionId == \App\Enums\BoardPosition::AVP)
                                        <h2 class="text-center">{{$AVPDetails->first_name}} {{$AVPDetails->last_name}}, {{$AVPDetails->position?->position}}</h2>
                                    @elseif ($bdPositionId == \App\Enums\BoardPosition::MVP)
                                        <h2 class="text-center">{{$MVPDetails->first_name}} {{$MVPDetails->last_name}}, {{$MVPDetails->position?->position}}</h2>
                                    @elseif ($bdPositionId == \App\Enums\BoardPosition::TRS)
                                        <h2 class="text-center">{{$TRSDetails->first_name}} {{$TRSDetails->last_name}}, {{$TRSDetails->position?->position}}</h2>
                                    @elseif ($bdPositionId == \App\Enums\BoardPosition::SEC)
                                        <h2 class="text-center">{{$SECDetails->first_name}} {{$SECDetails->last_name}}, {{$SECDetails->position?->position}}</h2>
                                    @endif

                                    <p class="description text-center">
                                        Welcome to the MOMS information Management Interface, affectionately called MIMI!
                                        <br>Here you can view your chapter's information, update your profile, complete End of Year Reports, etc.
                                    </p>
                                    <div id="readOnlyText" class="description text-center">
                                        @if($displayBoardRptLIVE  && $chDetails->documentsEOY->new_board_active != '1')
                                            <p><span style="color: red;">All Board Member Information is currently <strong>READ ONLY</strong>.<br>
                                                In order to add new board members to MIMI, please complete the Board Election Report.<br>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        <!-- /.card -->
                        </div>
                    </div>
                </div>

    <div class="row">
            <div class="col-md-8">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                            <h3>Board Members</h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                        <!-- President -->
                        <div class="row mb-3">
                            <label class="col-sm-2 mb-1 col-form-label">President:</label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control" value="{{ $PresDetails->first_name }}" required placeholder="First Name" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" value="{{ $PresDetails->last_name }}" required placeholder="Last Name">
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  value="{{ $PresDetails->email }}" required placeholder="Email Address" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $PresDetails->phone }}" required placeholder="Phone Number" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-10 mb-1">
                            <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" placeholder="Address" value="{{ $PresDetails->street_address }}" required >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"><br></label>
                            <div class="col-sm-3 mb-1">
                             <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control" value="{{ $PresDetails->city }}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_pre_state" id="ch_pre_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}"
                                            @if($PresDetails->state_id == $state->id) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control" value="{{ $PresDetails->zip }}"  required placeholder="Zip">
                                </div>
                                <div class="col-sm-2" id="ch_pre_country-container" style="display: none;">
                                    <select name="ch_pre_country" id="ch_pre_country" class="form-control" style="width: 100%;" required>
                                        <option value="">Select Country</option>
                                        @foreach($allCountries as $country)
                                        <option value="{{$country->id}}"
                                            @if($PresDetails->country_id == $country->id) selected @endif>
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- aVP -->
                        <div class="row mb-3">
                            <label class="col-sm-2 mb-1 col-form-label">AVP:</label>
                            <div class="col-sm-10 mt-1 form-check form-switch">
                                <input type="checkbox" name="AVPVacant" id="AVPVacant" class="form-check-input" {{$AVPDetails->id == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
                                <label class="form-check-label" for="AVPVacant">Vacant</label>
                            </div>
                        <div class="avp-field row mb-3">
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" value="{{$AVPDetails->first_name != ''  ? $AVPDetails->first_name : ''}}" required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" value="{{$AVPDetails->last_name != ''  ? $AVPDetails->last_name : ''}}" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_avp_email" id="ch_avp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails->email != ''  ? $AVPDetails->email : ''}}" required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$AVPDetails->phone != ''  ? $AVPDetails->phone : ''}}" required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="ch_avp_street" id="ch_avp_street" class="form-control" value="{{$AVPDetails->street_address != ''  ? $AVPDetails->street_address : ''}}"  required >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                               <div class="col-sm-3 mb-1">
                                <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" value="{{$AVPDetails->city != ''  ? $AVPDetails->city : ''}}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_avp_state" id="ch_avp_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}"
                                            @if(isset($AVPDetails->state_id) && $AVPDetails->state_id == $state->id) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="avp-field col-sm-2 mb-1" >
                                    <input type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" value="{{$AVPDetails->zip != ''  ? $AVPDetails->zip : ''}}"  required placeholder="Zip">
                                </div>
                                  <div class="col-sm-2" id="ch_avp_country-container" style="display: none;">
                                    <select name="ch_avp_country" id="ch_avp_country" class="form-control" style="width: 100%;" required>
                                        <option value="">Select Country</option>
                                        @foreach($allCountries as $country)
                                        <option value="{{$country->id}}"
                                            @if(isset($AVPDetails->country_id) && $AVPDetails->country_id == $country->id) selected @endif>
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                         <!-- MVP -->
                         <div class="row mb-3">
                            <label class="col-sm-2 mb-1 col-form-label">MVP:</label>
                            <div class="col-sm-10 mt-1 form-check form-switch">
                                    <input type="checkbox" name="MVPVacant" id="MVPVacant" class="form-check-input" {{$MVPDetails->id == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
                                    <label class="form-check-label" for="MVPVacant">Vacant</label>
                            </div>
                            <div class="mvp-field row mb-3">
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control" value="{{$MVPDetails->first_name != ''  ? $MVPDetails->first_name : ''}}" required placeholder="First Name" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" value="{{$MVPDetails->last_name != ''  ? $MVPDetails->last_name : ''}}" required placeholder="Last Name" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_mvp_email" id="ch_mvp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$MVPDetails->email != ''  ? $MVPDetails->email : ''}}" required placeholder="Email Address" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$MVPDetails->phone != ''  ? $MVPDetails->phone : ''}}" required placeholder="Phone Number" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-10 mb-1">
                            <input type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control" placeholder="Address" value="{{$MVPDetails->street_address != ''  ? $MVPDetails->street_address : ''}}" required >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"><br></label>
                            <div class="col-sm-3 mb-1">
                                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" value="{{$MVPDetails->city != ''  ? $MVPDetails->city : ''}}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_mvp_state" id="ch_mvp_state"class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}"
                                            @if(isset($MVPDetails->state_id) && $MVPDetails->state_id == $state->id) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" class="form-control" value="{{$MVPDetails->zip != ''  ? $MVPDetails->zip : ''}}"  required placeholder="Zip">
                                </div>
                                <div class="col-sm-2"  id="ch_mvp_country-container" style="display: none;">
                                    <select name="ch_mvp_country" id="ch_mvp_country" class="form-control" style="width: 100%;" required>
                                        <option value="">Select Country</option>
                                        @foreach($allCountries as $country)
                                        <option value="{{$country->id}}"
                                            @if(isset($MVPDetails->country_id) && $MVPDetails->country_id == $country->id) selected @endif>
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                        <!-- Treaasurer -->
                        <div class="row mb-3">
                            <label class="col-sm-2 mb-1 col-form-label">Treasurer:</label>
                            <div class="col-sm-10 mt-1 form-check form-switch">
                                    <input type="checkbox" name="TreasVacant" id="TreasVacant" class="form-check-input" {{$TRSDetails->id == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
                                    <label class="form-check-label" for="TreasVacant">Vacant</label>
                            </div>
                            <div class="trs-field row mb-3">
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control"  value="{{$TRSDetails->first_name != ''  ? $TRSDetails->first_name : ''}}" required placeholder="First Name" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" value="{{$TRSDetails->last_name != ''  ? $TRSDetails->last_name : ''}}" required placeholder="Last Name" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_trs_email" id="ch_trs_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$TRSDetails->email != ''  ? $TRSDetails->email : ''}}" required placeholder="Email Address" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$TRSDetails->phone != ''  ? $TRSDetails->phone : ''}}" required placeholder="Phone Number" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-10 mb-1">
                            <input type="text" name="ch_trs_street" id="ch_trs_street" class="form-control" placeholder="Address" value="{{$TRSDetails->street_address != ''  ? $TRSDetails->street_address : ''}}" required >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"><br></label>
                            <div class="col-sm-3 mb-1">
                                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" value="{{$TRSDetails->city != ''  ? $TRSDetails->city : ''}}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_trs_state" id="ch_trs_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}"
                                            @if(isset($TRSDetails->state_id) && $TRSDetails->state_id == $state->id) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_trs_zip" id="ch_trs_zip" class="form-control" value="{{$TRSDetails->zip != ''  ? $TRSDetails->zip : ''}}"  required placeholder="Zip">
                                </div>
                                  <div class="col-sm-2" id="ch_trs_country-container" style="display: none;">
                                    <select name="ch_trs_country" id="ch_trs_country" class="form-control" style="width: 100%;" required>
                                        <option value="">Select Country</option>
                                        @foreach($allCountries as $country)
                                        <option value="{{$country->id}}"
                                            @if(isset($TRSDetails->country_id) && $TRSDetails->country_id == $country->id) selected @endif>
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                        <!-- Secretary -->
                        <div class="row mb-3">
                            <label class="col-sm-2 mb-1 col-form-label">Secretary:</label>
                            <div class="col-sm-10 mt-1 form-check form-switch">
                                    <input type="checkbox" name="SecVacant" id="SecVacant" class="form-check-input" {{$SECDetails->id == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
                                    <label class="form-check-label" for="SecVacant">Vacant</label>
                            </div>
                        <div class="sec-field row mb-3">
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" value="{{$SECDetails->first_name != ''  ? $SECDetails->first_name : ''}}" required placeholder="First Name" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" value="{{$SECDetails->last_name != ''  ? $SECDetails->last_name : ''}}" required placeholder="Last Name" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_sec_email" id="ch_sec_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails->email != ''  ? $SECDetails->email : ''}}" required placeholder="Email Address" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$SECDetails->phone != ''  ? $SECDetails->phone : ''}}" required placeholder="Phone Number" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-10 mb-1">
                            <input type="text" name="ch_sec_street" id="ch_sec_street" class="form-control" placeholder="Address" value="{{$SECDetails->street_address != ''  ? $SECDetails->street_address : ''}}" required >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"><br></label>
                          <div class="col-sm-3 mb-1">
                                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" value="{{$SECDetails->city != ''  ? $SECDetails->city : ''}}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_sec_state" id="ch_sec_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}"
                                            @if(isset($SECDetails->state_id) && $SECDetails->state_id == $state->id) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_sec_zip" id="ch_sec_zip" class="form-control" value="{{$SECDetails->zip != ''  ? $SECDetails->zip : ''}}"  required placeholder="Zip">
                                </div>
                                 <div class="col-sm-2" id="ch_sec_country-container" style="display: none;">
                                    <select name="ch_sec_country" id="ch_sec_country" class="form-control" style="width: 100%;" required>
                                        <option value="">Select Country</option>
                                        @foreach($allCountries as $country)
                                        <option value="{{$country->id}}"
                                            @if(isset($SECDetails->country_id) && $SECDetails->country_id == $country->id) selected @endif>
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                        <hr>

                        </div><div class="card-header bg-transparent border-0">
                            <h3>Contact Info</h3>
                        </div>
                <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Email:</label>
                            <div class="col-sm-5">
                            <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" value="{{ $chDetails->inquiries_contact }}" placeholder="Inquiries Email Address" required>
                            </div>
                            <div class="col-sm-5">
                            <input type="text" name="ch_email" id="ch_email" class="form-control" value="{{ $chDetails->email }}" placeholder="Chapter Email Address">
                            </div>
                        </div>
                        <!-- /.form group -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Mailing:</label>
                            <div class="col-sm-10">
                            <input type="text" name="ch_pobox" id="ch_pobox" class="form-control" value="{{ $chDetails->po_box }}" placeholder="Chapter PO Box/Mailing Address" >
                            </div>

                        </div>
                    </div>
                        <hr>

                         </div><div class="card-header bg-transparent border-0">
                            <h3>Website & Social Media</h3>
                        </div>
                <!-- /.card-header -->
                    <div class="card-body">
                         <!-- URL Field -->
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Website:</label>
                            <div class="col-sm-10">
                                <input type="text" name="ch_website" id="ch_website" class="form-control"
                                    value="{{$chDetails->website_url}}"
                                    placeholder="Chapter Website">
                            </div>
                        </div>

                        <!-- Web Status Field -->
                        <div class="row mb-3" id="ch_webstatus-container" style="display: none;">
                            <label class="col-sm-2 col-form-label">Web Status</label>
                            <div class="col-sm-5">
                                <select name="ch_webstatus" id="ch_webstatus" class="form-control" style="width: 100%;"
                                    @if($chDetails->website_url) required @endif>
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
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Social Media:</label>
                            <div class="col-sm-5">
                            <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chDetails->egroup }}"  placeholder="Forum/Group/App" >
                            </div>
                            <div class="col-sm-5">
                            <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chDetails->social1 }}" placeholder="Facebook"  >
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-5">
                                <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chDetails->social2 }}"  placeholder="Twitter" >
                            </div>
                            <div class="col-sm-5">
                                <input type="text" name="ch_social3" id="ch_social3" class="form-control" value="{{ $chDetails->social3 }}"  placeholder="Instagram" >
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
                    <div class="card-body">
                          <div class="card-header bg-transparent border-0">
                            <h3>Chapter Information</h3>
                        </div>
                    <!-- /.card-header -->
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-auto fw-bold">EIN:</div>
                            <div class="col text-end">
                                {{ $chDetails->ein}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Founded:</div>
                            <div class="col text-end">
                                {{ $startMonthName }} {{ $chDetails->start_year }}
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-auto fw-bold">Boundaries:</div>
                            <div class="col text-end">
                                {{ $chDetails->territory}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Dues Paid:</div>
                            <div class="col text-end">
                                    @if ($chPayments->rereg_members)
                                        <b>{{ $chPayments->rereg_members }} Members</b> on <b>{{\Illuminate\Support\Carbon::parse($chPayments->rereg_date)->format('m/d/Y')}}</b>
                                    @else
                                        N/A
                                    @endif
                            </div>
                        </div>
                       <div class="row">
                            <div class="col-auto fw-bold">M2M Donation:</div>
                            <div class="col text-end">
                                    @if ($chPayments->m2m_donation)
                                        <b>${{ $chPayments->m2m_donation }}</b> on <b>{{\Illuminate\Support\Carbon::parse($chPayments->m2m_date)->format('m/d/Y')}}</b>
                                    @else
                                        N/A
                                    @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Sustaining Donation:</div>
                            <div class="col text-end">
                                    @if ($chPayments->sustaining_donation)
                                        <b>${{ $chPayments->sustaining_donation }}</b> on <b>{{\Illuminate\Support\Carbon::parse($chPayments->sustaining_date)->format('m/d/Y')}}</b>
                                    @else
                                        N/A
                                    @endif
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <span style="color: red;">If anything in this section needs to be updated, please contact your Primary Coordinator.</span><br>
                        </div>

                        <li class="list-group-item">
                        <h3>Payments/Donations</h3>
                       <div class="row">
                            <div class="col-auto fw-bold">Anniversary Month</div>
                            <div class="col text-end">
                                {{ $startMonthName }}
                            </div>
                        </div>
                        <div class="col-sm-12">
                        @if ($currentDate->gte($dueDate))
                                @if ($startMonthId == $currentMonth)
                                    <span style="color: green;">Your Re-registration payment is due now.</span><br>
                                @else
                                    <span style="color: red;">Your Re-registration payment is now considered overdue.</span><br>
                                @endif
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm mt-1 mb-1" onclick="window.location.href='{{ route('board.editreregpayment', ['id' => $chDetails->id]) }}'">PAY HERE</button>
                            @else
                                Your Re-registration payment is not due at this time.
                            @endif
                        </div>
                        <div class="col-sm-12">
                            You can make a Mother-To-Mother Fund or Sustaining Chapter donation at any time.<br>
                            <button type="button" class="btn btn-primary bg-gradient btn-sm mt-1 mb-1" onclick="window.location.href='{{ route('board.editdonate', ['id' => $chDetails->id]) }}'">DONATE HERE</button>
                        </div>
                    </li>

                      <li class="list-group-item">
                        <h3>Document Center</h3>
                        <div class="col-sm-12">
                            @if($chDocuments->ein_letter_path != null)
                                <button type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="openPdfViewer('{{ $chDocuments->ein_letter_path }}')">EIN Letter</button><br>
                            @else
                                <button type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1 disabled " disabled>No EIN Letter on File</button><br>
                            @endif
                            <button id="GoodStanding" type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="window.open('{{ route('pdf.chapteringoodstanding', ['id' => $chDetails->id]) }}', '_blank')">Chapter in Good Standing</button><br>
                            @if($chDocuments->probation_path != null)
                                <button type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="openPdfViewer('{{ $chDocuments->probation_path }}')">Probation Letter</button><br>
                            @endif
                            @if($chDocuments->probation_release_path != null)
                                <button type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="openPdfViewer('{{ $chDocuments->probation_release_path }}')">Probation Release Letter</button><br>
                            @endif
                            @if($chDetails->probation_id == '3')
                                <button type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="window.location.href='{{ route('board.editprobation', ['id' => $chDetails->id]) }}'">Quarterly Financial Submission</button>
                            @endif
                        </div>
                      </li>

                      <li class="list-group-item">
                        <h3>Resources</h3>
                        <div class="col-sm-12">
                            <button id="Resources" type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="window.location='{{ route('board.viewresources', ['id' => $chDetails->id]) }}'">Chapter Resources</button><br>
                            <button id="Resources" type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="window.location='{{ route('board.viewelearning', ['id' => $chDetails->id]) }}'">eLearning Library</button><br>
                        </div>
                        </li>

                      <li class="list-group-item">
                            <h3>End of Year Filing</h3>
                            <div class="col-sm-12">
                            @if($userTypeId == \App\Enums\UserTypeEnum::COORD && $chEOYDocuments->new_board_active!='1')
                                @if($displayTESTING)
                                    <button id="BoardReport" type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="window.location.href='{{ route('board.editboardreport', ['id' => $chDetails->id]) }}'">
                                        {{ $boardReportName }} *TESTING*
                                    </button><br>
                                @elseif ($displayBoardRptLIVE)
                                    <button id="BoardReport" type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="window.location.href='{{ route('board.editboardreport', ['id' => $chDetails->id]) }}'">
                                        {{ $boardReportName }}
                                    </button><br>
                                @else
                                    <button id="BoardReport" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1 disabled" disabled>Board Report Not Available</button><br>
                                @endif
                            @elseif($userTypeId == \App\Enums\UserTypeEnum::COORD && $chEOYDocuments->new_board_active =='1')
                                @if($displayTESTING)
                                    <button id="BoardReport" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1 disabled" disabled>Board Report Activated *TESTING*</button><br>
                                @else
                                    <button id="BoardReport" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1 disabled" disabled>Board Report Activated</button><br>
                                @endif
                                @elseif ($displayBoardRptLIVE)
                                @if($chEOYDocuments->new_board_active!='1')
                                    <button id="BoardReport" type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="window.location.href='{{ route('board.editboardreport', ['id' => $chDetails->id]) }}'">
                                        {{ $boardReportName }}
                                    </button><br>
                                @else
                                    <button id="BoardReport" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1 disabled" disabled>Board Report Activated</button><br>
                                @endif
                            @else
                                <button id="BoardReport" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1 disabled" disabled>Board Report Not Available</button><br>
                            @endif

                            @if($userTypeId == \App\Enums\UserTypeEnum::COORD)
                                @if($displayTESTING)
                                    <button id="FinancialReport" type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="window.location.href='{{ route('board.editfinancialreport', ['id' => $chDetails->id]) }}'">
                                        {{ $financialReportName }} *TESTING*
                                    </button><br>
                                @elseif (($displayFinancialRptLIVE))
                                    <button id="FinancialReport" type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="window.location.href='{{ route('board.editfinancialreport', ['id' => $chDetails->id]) }}'">
                                        {{ $financialReportName }}
                                    </button><br>
                                @else
                                    <button id="990NLink" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1 disabled" disabled>Financial Report Not Available</button>
                                @endif
                            @elseif($displayFinancialRptLIVE)
                                <button id="FinancialReport" type="button" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" onclick="window.location.href='{{ route('board.editfinancialreport', ['id' => $chDetails->id]) }}'">
                                    {{ $financialReportName }}
                                </button><br>
                            @else
                                <button id="FinancialReport" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1 disabled" disabled>Financial Report Not Available</button><br>
                            @endif

                             @if($displayEINInstructionsLIVE)
                                <a href="https://sa.www4.irs.gov/sso/ial1?resumePath=%2Fas%2F5Ad0mGlkzW%2Fresume%2Fas%2Fauthorization.ping&allowInteraction=true&reauth=false&connectionId=SADIPACLIENT&REF=3C53421849B7D5B806E50960DF0AC7530889D9ADE9238D5D3B8B00000069&vnd_pi_requested_resource=https%3A%2F%2Fsa.www4.irs.gov%2Fepostcard%2F&vnd_pi_application_name=EPOSTCARD"
                                    class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1" target="_blank" >{{ $irsFilingName }}</a>
                            @else
                                <button id="990NLink" class="btn btn-primary bg-gradient bg-gradient btn-sm mb-1 disabled" disabled>990N Not Available Until July 1st</button>
                            @endif
                        </div>
                    </li>
                    <li class="list-group-item">
                        <h3>Coordinators</h3>
                            <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                            <input  type="hidden" id="pcid" value="{{ $chDetails->primary_coordinator_id}}">
                            <div class="row mb-2">
                          <span id="display_corlist"></span>
                            </div>
                        </li>

                  </ul>
                  <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                  <input  type="hidden" id="pcid" value="{{ $chDetails->primary_coordinator_id}}">
                  <div id="display_corlist" ></div>

                    </div>
                <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>

    <div class="card-body text-center mt-3">
        <button id="Save" type="submit" class="btn btn-primary bg-gradient mb-2" onclick="return validateEmailsBeforeSubmit()"><i class="bi bi-floppy-fill me-2"></i>Save</button>

    </form>
        <button id="Password" type="button" class="btn btn-primary bg-gradient mb-2" onclick="showChangePasswordAlert('{{ $borDetails->user_id }}')"><i class="bi bi-lock-fill me-2" ></i>Change Password</button>
        <button id="logout-btn" class="btn btn-primary bg-gradient mb-2" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right me-2" ></i>Logout</button>
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
        var displayBoardRptLIVE = @json($displayBoardRptLIVE);
        var userTypeId = @json($userTypeId);
        var userAdmin = @json($userAdmin);
        var boardActive = @json($boardActive);

        if (userAdmin == 1) {
            // Admin - ALWAYS allow edits for testing purposes
            $('#logout-btn').prop('disabled', true);
        } else if (userTypeId == 1 && userAdmin != 1) {
            // Coordinators - ALWAYS disable (never enabled)
            $('input, select, textarea').prop('disabled', true);
            $('#Save, #Password, #logout-btn').prop('disabled', true);
            $('#display_corlist').addClass('disabled-link').attr('href', '#');
         } else if (displayBoardRptLIVE == true && boardActive != 1) {
            // Board members in month 5-9 (only editable IF board report is activated)
            $('input, select, textarea').prop('disabled', true);
            $('#Save, #Password').prop('disabled', true);
         }
        // Board members in months 1-4 & 10-12 will be editable for everyone
    });

</script>
@endsection
