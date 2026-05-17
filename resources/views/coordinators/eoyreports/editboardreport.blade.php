@extends('layouts.mimi_theme')

@if ($ITCondition && !$displayEOYTESTING && !$displayEOYLIVE)
    @section('page_title', $reportYearRange.' EOY Details *ADMIN*')
    @section('breadcrumb', 'Board Election Report')
@elseif ($eoyTestCondition && $displayEOYTESTING)
    @section('page_title', $reportYearRange.' EOY Details *TESTING*')
    @section('breadcrumb', 'Board Election Report')
@else
    @section('page_title', $reportYearRange.' EOY Details')
    @section('breadcrumb', 'Board Election Report')
@endif

@section('content')
    <!-- Main content -->
    <form id="board-info" method="POST" action='{{ route("eoyreports.updateboardreport", $chDetails->id) }}'>
        @csrf

        <input type="hidden" name="submit_type" id="submit_type" value="" />
        <input type="hidden" name="presID" id="presID" value="{{ $PresDetails->id }}" />
        <input type="hidden" name="avpID" id="avpID" value="{{ $AVPDetails->id }}" />
        <input type="hidden" name="mvpID" id="mvpID" value="{{ $MVPDetails->id }}" />
        <input type="hidden" name="trsID" id="trsID" value="{{ $TRSDetails->id }}" />
        <input type="hidden" name="secID" id="secID" value="{{ $SECDetails->id }}" />
        <input type="hidden" id="ch_state" value="{{ $stateShortName }}">
        <input type="hidden" name="ch_hid_webstatus" id="ch_hid_webstatus" value="{{ $chDetails->website_status }}">
        <input type="hidden" id="ch_pre_email_chk" value="{{ $PresDetails->email }}">
        <input type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails->email }}">
        <input type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails->email }}">
        <input type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails->email }}">
        <input type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails->email }}">

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body">
                    <div class="card-header bg-transparent border-0">
                <h3>MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                 </div>
                    <!-- /.card-header -->
                    <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">
                        @if ($chEOYDocuments->new_board_submitted != '1' )
                            <label class="me-2">Report Status:</label><span class="badge bg-danger fs-7">Not Submitted</span><br><br>
                                Chapter needs to Complete and Submit the Board Election Report before new board members can be activated in MIMI.<br>
                                Submission can also be made by a Coordinator <strong>HERE</strong>.<br>
                        @endif
                        @if ($chEOYDocuments->new_board_submitted == '1' && $chEOYDocuments->new_board_active !='1')
                            <label class="me-2">Report Status:</label><span class="badge bg-warning text-dark fs-7">Submitted</span><br><br>
                                Changes are can no longer be made by the Chapter, but can be updated by a Coordinator <strong>HERE</strong> prior to Activation.<br>
                                <br>
                                New Board Members will need to be activated by a Coordinator after July 1st! Once activated, they will have full MIMI Access.<br>
                                Outgoing board members will have access to Financial Reports Only.<br>
                        @endif
                        @if ($chEOYDocuments->new_board_active =='1')
                            <label class="me-2">Report Status:</label><span class="badge bg-success fs-7">Activated</span><br><br>
                                New board members now have full MIMI Access.<br>
                                <br>
                                Outgoing board members have access to Financial Reports Only.<br>
                                <br>
                                Changes can be made on the Chapter Details page.<br>
                                <br>
                        @endif
                    </li>
                </ul>

                    </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
            </div>
            <!-- /.col -->

                @if ($chEOYDocuments->new_board_active != '1')
                 <div class="col-md-8">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <div class="card-header bg-transparent border-0">
                                    <h3>{{ $boardReportName}} Details</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">

                    <div class="row">
                                    <div class="col-md-12">
                                            <label>Boundaries listed in MIMI (used for Inquiries):</label>
                                            <div>{{ $chDetails->territory }}</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label>Are your listed boundaries correct?<span class="field-required">*</span></label>
                                    <div class="col-12 d-flex gap-4">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="BoundaryStatusYes" name="BoundaryStatus" value="0" {{ !is_null($chDetails->boundary_issues) && $chDetails->boundary_issues == 0 ? 'checked' : '' }} onChange="ShowBoundaryError()">
                                            <label class="form-check-label" for="BoundaryStatusYes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="BoundaryStatusNo" name="BoundaryStatus" value="1" {{ $chDetails->boundary_issues == 1 ? 'checked' : '' }} onChange="ShowBoundaryError()">
                                            <label class="form-check-label" for="BoundaryStatusNo">No</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12" id="divBoundaryIssue">
                                        <div class="col-sm-12">
                                        <label for="BoundaryIssue">Please indicate which part of the Boundaries not NOT match our records<span class="field-required">*</span></label>
                                        <input type="text" rows="2"class="form-control" name="BoundaryIssue" id="BoundaryIssue" value="{{ $chDetails->boundary_issue_notes }}" ></input>
                                     </div>
                                    </div>
                                    </div>

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
                                    <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" value="{{ $PresDetails->email }}" required placeholder="Email Address" >
                                    </div>
                                    <div class="col-sm-5 mb-1">
                                        @phoneInput('ch_pre_phone', $PresDetails->phone)
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

                                <div class="row mb-3">
                                    <label class="col-sm-2 mb-1 col-form-label">AVP:</label>
                                    <div class="col-sm-10 mt-1 form-check form-switch">
                                        <input type="checkbox" name="AVPVacant" id="AVPVacant" class="form-check-input" {{$AVPDetails->id == '' ? 'checked' : ''}}>
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
                                        <input type="text" name="ch_avp_email" id="ch_avp_email" class="form-control" value="{{$AVPDetails->email != ''  ? $AVPDetails->email : ''}}" required placeholder="Email Address" >
                                        </div>
                                        <div class="col-sm-5 mb-1">
                                            @phoneInput('ch_avp_phone', $AVPDetails->phone)
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
                                <div class="col-sm-2 mb-1" >
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

                                 <div class="row mb-3">
                                    <label class="col-sm-2 mb-1 col-form-label">MVP:</label>
                                    <div class="col-sm-10 mt-1 form-check form-switch">
                                            <input type="checkbox" name="MVPVacant" id="MVPVacant" class="form-check-input" {{$MVPDetails->id == '' ? 'checked' : ''}}>
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
                                    <input type="text" name="ch_mvp_email" id="ch_mvp_email" class="form-control" value="{{$MVPDetails->email != ''  ? $MVPDetails->email : ''}}" required placeholder="Email Address" >
                                    </div>
                                    <div class="col-sm-5 mb-1">
                                        @phoneInput('ch_mvp_phone', $MVPDetails->phone)
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

                                <div class="row mb-3">
                                    <label class="col-sm-2 mb-1 col-form-label">Treasurer:</label>
                                    <div class="col-sm-10 mt-1 form-check form-switch">
                                            <input type="checkbox" name="TreasVacant" id="TreasVacant" class="form-check-input" {{$TRSDetails->id == '' ? 'checked' : ''}}>
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
                                    <input type="text" name="ch_trs_email" id="ch_trs_email" class="form-control" value="{{$TRSDetails->email != ''  ? $TRSDetails->email : ''}}" required placeholder="Email Address" >
                                    </div>
                                    <div class="col-sm-5 mb-1">
                                        @phoneInput('ch_trs_phone', $TRSDetails->phone)
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

                                <div class="row mb-3">
                                    <label class="col-sm-2 mb-1 col-form-label">Secretary:</label>
                                    <div class="col-sm-10 mt-1 form-check form-switch">
                                            <input type="checkbox" name="SecVacant" id="SecVacant" class="form-check-input" {{$SECDetails->id == '' ? 'checked' : ''}}>
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
                                    <input type="text" name="ch_sec_email" id="ch_sec_email" class="form-control" value="{{$SECDetails->email != ''  ? $SECDetails->email : ''}}" required placeholder="Email Address" >
                                    </div>
                                    <div class="col-sm-5 mb-1">
                                        @phoneInput('ch_sec_phone', $SECDetails->phone)
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

                            <!-- /.form group -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Inquiries Email:</label>
                                <div class="col-sm-6">
                                <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" value="{{ $chDetails->inquiries_contact }}"  required >
                                </div>
                            </div>
                                <!-- /.form group -->
                             <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Chapter Email:</label>
                                <div class="col-sm-6">
                                <input type="text" name="ch_email" id="ch_email" class="form-control" value="{{ $chDetails->email }}"  placeholder="Chapter Email Address" >
                                </div>
                            </div>
                                <!-- /.form group -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Website:</label>
                                <div class="col-sm-6">
                                    <input type="text" name="ch_website" id="ch_website" class="form-control"
                                        value="{{$chDetails->website_url}}"
                                        placeholder="Chapter Website">
                                </div>
                            </div>

                            <!-- Website Status Container - Hidden by default -->
                            <div class="row mb-3" id="ch_webstatus-container" style="display: none;">
                                <label class="col-sm-2 col-form-label">Link Status:</label>
                                <div class="col-sm-3">
                                    <select name="ch_webstatus" id="ch_webstatus" class="form-control" style="width: 100%;">
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
                                <div class="col-sm-2">
                                <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chDetails->egroup }}"  placeholder="Forum/Group/App" >
                                </div>
                                <div class="col-sm-2">
                                <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chDetails->social1 }}" placeholder="Facebook"  >
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chDetails->social2 }}"  placeholder="Twitter" >
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" name="ch_social3" id="ch_social3" class="form-control" value="{{ $chDetails->social3 }}"  placeholder="Instagram" >
                                </div>
                        </div>

                </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->

           @else
                <div class="col-md-8">
                    <div class="card card-primary card-outline">
        <div class="card-body">
            <div class="card-header bg-transparent border-0">
                <h3>Current Board Details</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
  <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label class="me-2">President:</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    @include('boards.partials.presinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label class="me-2">AVP:</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    @include('boards.partials.avpinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label class="me-2">MVP:</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    @include('boards.partials.mvpinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label class="me-2">Treasurer:</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    @include('boards.partials.trsinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label class="me-2">Secretary:</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    @include('boards.partials.secinfo')
                                </div>
                            </div>

             </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->

            @endif


          <div class="col-md-12">
            <div class="card-body text-center mt-3">
                  @if ($chEOYDocuments->new_board_active != '1')
                    <div class="d-flex justify-content-center align-items-start flex-wrap">
                            <form method="POST" action="#" onsubmit="return validateBeforeSubmit(true)">
                                @csrf
                                @if ($chEOYDocuments->new_board_submitted != 1)
                                    <button type="submit" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-chevron-double-right me-2"></i>Submit</button>
                                @else
                                    <button type="submit" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-floppy-fill me-2"></i>Save</button>
                                @endif
                            </form>

                            @if ($chEOYDocuments->new_board_submitted == '1' && $PresDetails->first_name != null)
                           <form id="activateSingleBoardForm" action="{{ route('eoyreports.activateboardreport', ['id' => $chDetails->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="board" value="active">
                                <button type="button" class="btn btn-primary bg-gradient mb-2 ms-1" onclick="confirmActivateSingleBoard()">
                                    <i class="bi bi-play-fill me-2"></i>Activate Board
                                </button>
                            </form>
                            @endif
                        </div>
                    @endif

                    @if ($confId == $chConfId)
                        <button type="button" id="back-eoy" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('eoyreports.eoyboardreport') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-bounding-box me-2"></i>Back to Board Election Report</button>
                    @elseif ($confId != $chConfId && $ITCondition)
                        <button type="button" id="back-eoy" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('eoyreports.eoyboardreport', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-bounding-box me-2"></i>Back to International Board Election Report</button>
                    @endif
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Back to EOY Details</button>
                    <button type="button" id="back-details" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Chapter Details</button>
            </div>
     </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
@include('layouts.scripts.disablefieldseoy')
<script>

$(document).ready(function() {
    ShowBoundaryError();
});

function ShowBoundaryError() {
        var selectedRadio = document.querySelector('input[name="BoundaryStatus"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 4 */

        if (selectedValue == "1") {
            $('#divBoundaryIssue').addClass('tx-cls');
            document.getElementById("divBoundaryIssue").style.display = 'block'; // If "Yes" is selected
        } else {
            $('#divBoundaryIssue').removeClass('tx-cls');
            document.getElementById("divBoundaryIssue").style.display = 'none'; // If "No" is selected
        }
    }
</script>
@endsection

