@extends('layouts.coordinator_theme')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Details&nbsp;<small>(Edit)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Details</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

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

    <!-- Main content -->
    <form method="POST" action='{{ route("chapters.update",$chapterList[0]->id) }}'>
    @csrf
     <section class="content">
        <div class="container-fluid">

            @php
            $assistConferenceCoordinatorCondition = ($positionid >= 6 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25);  //CC-Founder & ACC
            $regionalCoordinatorCondition = ($positionid >= 5 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25);  //*RC-Founder & ACC
            $assistRegionalCoordinatorCondition = ($positionid >= 4 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25);  //*ARC-Founder & ACC
            $bigSisterCondition = ($positionid >= 1 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25);  //*BS-Founder & ACC
            $eoyTestCondition = ($positionid >= 6 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25) ||
                    ($positionid == 29 || $secpositionid == 29);  //CC-Founder & ACC, AR Tester
            $eoyReportCondition = ($positionid >= 1 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25) ||
                    ($positionid == 19 || $secpositionid == 19) || ($positionid == 29 || $secpositionid == 29);  //*BS-Founder & ACC, AR Reviewer, AR Tester
            $eoyReportConditionDISABLED = ($positionid == 13 || $secpositionid == 13);  //*IT Coordinator
        @endphp
        @php
            $admin = DB::table('admin')
                ->select('admin.*',
                    DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'),)
                ->leftJoin('coordinator_details as cd', 'admin.updated_id', '=', 'cd.coordinator_id')
                ->orderBy('admin.id', 'desc') // Assuming 'id' represents the order of insertion
                ->first();

            $eoy_testers = $admin->eoy_testers;
            $eoy_coordinators = $admin->eoy_coordinators;

            $testers_yes = ($eoy_testers == 1);
            $coordinators_yes = ($eoy_coordinators == 1);
        @endphp


        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Chapter</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>MOMS Club of</label> <span class="field-required">*</span>
                        <input type="text" name="ch_name" class="form-control" maxlength="200" required value="{{ $chapterList[0]->name }}" onchange="PreviousNameReminder()">
                    </div>
                    </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>State</label> <span class="field-required">*</span>
                        <select id="ch_state" name="ch_state" class="form-control select2-bs4" style="width: 100%;" required >
                        <option value="">Select State</option>
                            @foreach($stateArr as $state)
                            <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="ch_hid_state" value="{{ $chapterList[0]->state }}">
                    </div>
                    </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>Country</label> <span class="field-required">*</span>
                        <select id="ch_country" name="ch_country" class="form-control select2-bs4" style="width: 100%;" required >
                        <option value="">Select Country</option>
                            @foreach($countryArr as $con)
                            <option value="{{$con->short_name}}" {{$chapterList[0]->country == $con->short_name  ? 'selected' : ''}}>{{$con->name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="ch_hid_country" value="{{ $chapterList[0]->country }}">
                    </div>
                    </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>Conference</label> <span class="field-required">*</span>
                        <select id="ch_conference" name="ch_conference" class="form-control select2-bs4" style="width: 100%;" required disabled>
                        <option value="">Select Conference</option>
                                    @foreach($confList as $con)
                            <option value="{{$con->id}}" {{$chapterList[0]->conference == $con->id  ? 'selected' : ''}} >{{$con->conference_name}} </option>
                            @endforeach
                                </select>
                                </div>
                            </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>Region</label> <span class="field-required">*</span>
                        <select id="ch_region" name="ch_region" class="form-control select2-bs4-bs4" style="width: 100%;" required >
                        <option value="">Select Region</option>
                            @foreach($regionList as $rl)
                            <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}} >{{$rl->long_name}} </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="ch_hid_region" value="{{ $chapterList[0]->region }}">
                    </div>
                    </div>
                            <!-- /.form group -->
                        <div class="col-sm-2 ">
                    <div class="form-group">
                        <label>EIN</label>
                        <input type="text" id="ch_ein" name="ch_ein" class="form-control" value="{{ $chapterList[0]->ein }}" maxlength="10" readonly>
                    </div>
                    </div>
                        <!-- /.form group -->
                            <div class="col-sm-2 ">
                    <div class="form-group">
                        <label>&nbsp;&nbsp;&nbsp;</label><br>
                        @if(empty($chapterList[0]->ein_letter_path))
                            <a class="btn bg-gradient-primary disabled" href="#" ><i class="fas fa-university"></i>&nbsp;&nbsp;&nbsp;No EIN Letter on File</i></a>
                                    @else
                                    <a class="btn bg-gradient-primary" href="{{ $chapterList[0]->ein_letter_path }}" target="blank"><i class="fas fa-university"></i>&nbsp;&nbsp;&nbsp;View/Download EIN Letter</a>
                                    @endif
                        <input type="hidden" id="ch_ein_letter_path" name="ch_ein_letter_path" class="form-control" value="{{ $chapterList[0]->ein_letter_path }}" maxlength="250" readonly>
                    </div>
                    </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>Status</label> <span class="field-required">*</span>
                        <select id="ch_status" name="ch_status" class="form-control select2-bs4" style="width: 100%;" required >
                        <option value="">Select Status</option>
                        <option value="1" {{$chapterList[0]->status == 1  ? 'selected' : ''}}>Operating OK</option>
                        <option value="4" {{$chapterList[0]->status == 4  ? 'selected' : ''}}>On Hold Do not Refer</option>
                        <option value="5" {{$chapterList[0]->status == 5  ? 'selected' : ''}}>Probation</option>
                        <option value="6" {{$chapterList[0]->status == 6  ? 'selected' : ''}}>Probation Do Not Refer</option>
                        </select>
                        <input type="hidden" name="ch_hid_status" value="{{ $chapterList[0]->status }}">
                    </div>
                    </div>
                                    <!-- /.form group -->
                        <div class="col-sm-8 ">
                    <div class="form-group">
                        <label>Status Notes (not visible to board members)</label>
                        <input type="text" name="ch_notes" class="form-control" maxlength="50" value="{{ $chapterList[0]->notes}}" >
                    </div>
                    </div>
                    <!-- /.form group -->
                    <div class="col-sm-12">
                    <div class="form-group">
                        <label>Boundaries</label> <span class="field-required">*</span>
                        <input type="text" name="ch_boundariesterry" class="form-control" rows="2" value="{{ $chapterList[0]->territory }}" required >
                    </div>
                    </div>
              </div>
            </div>

            <div class="card-header">
                <h3 class="card-title">President</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>First Name</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_fname" class="form-control " value="{{ $chapterList[0]->first_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 ">
              <div class="form-group">
                <label>Last Name</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_lname" class="form-control " value="{{ $chapterList[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
                            <!-- /.form group -->
              <div class="col-sm-6 ">
              <div class="form-group">
                <label>Email</label> <span class="field-required">*</span>
                <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control " onblur="checkDuplicateEmail(this.value,this.id)" value="{{ $chapterList[0]->bd_email }}" maxlength="50" required >
                <input type="hidden" id="ch_pre_email_chk" value="{{ $chapterList[0]->bd_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
                <div class="form-group">
                    <label>Phone</label><span class="field-required">*</span>
                        <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $chapterList[0]->phone }}" required>
                </div>
            </div>



                {{-- <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control " value="{{ $chapterList[0]->phone }}" maxlength="12" required onkeypress="return isPhone(event)"> --}}

              <!-- /.form group -->
              <div class="col-sm-12 ">
              <div class="form-group">
                <label>Street Address</label> <span class="field-required">*</span>
                <input name="ch_pre_street" class="form-control " rows="4"  maxlength="250" value="{{ $chapterList[0]->street_address }}" required>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 ">
              <div class="form-group">
                <label>City</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_city" class="form-control " maxlength="50" value="{{ $chapterList[0]->city }}" required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 ">
              <div class="form-group">
                <label>State</label> <span class="field-required">*</span>
                <select name="ch_pre_state" class="form-control select2-bs4" style="width: 100%;" required >
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}" {{$chapterList[0]->bd_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 ">
              <div class="form-group">
                <label>Zip</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_zip" class="form-control " value="{{ $chapterList[0]->zip }}" maxlength="10" required onkeypress="return isNumber(event)">
              </div>
              </div>

              <div class="card-body text-center">
                <button type="button" class="btn bg-gradient-primary" id="{{ $chapterList[0]->user_id }}" onclick="return resetPassword(this.id)"><i class="fas fa-redo-alt"></i>&nbsp;&nbsp;&nbsp;Reset Password</button>
              </div>
              </div>
            </div>

            <div class="card-header">
                <h3 class="card-title">AVP</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                     <!-- /.form group -->
              <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="AVPVacant" id="AVPVacant" class="custom-control-input" {{$AVPDetails[0]->avp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="AVPVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 avp-field">&nbsp;</div>
                  <!-- /.form group -->
              <div class="col-sm-6  avp-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control " value="{{$AVPDetails[0]->avp_fname != ''  ? $AVPDetails[0]->avp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6  avp-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control " value="{{$AVPDetails[0]->avp_lname != ''  ? $AVPDetails[0]->avp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6  avp-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_avp_email" id="ch_avp_email" class="form-control " onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" maxlength="50">
                <input type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails[0]->avp_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6  avp-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control " data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$AVPDetails[0]->avp_phone != ''  ? $AVPDetails[0]->avp_phone : ''}}">
              </div>
              </div>
              <!-- /.form group -->
               <div class="col-sm-12  avp-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_avp_street" id="ch_avp_street" class="form-control " rows="4" maxlength="250" value="{{$AVPDetails[0]->avp_addr != ''  ? $AVPDetails[0]->avp_addr : ''}}">
              </div>
              </div>
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control " value="{{$AVPDetails[0]->avp_city != ''  ? $AVPDetails[0]->avp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6  avp-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_avp_state" id="ch_avp_state" class="form-control select2-bs4" style="width: 100%;" >
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}" {{$AVPDetails[0]->avp_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6  avp-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_avp_zip" id="ch_avp_zip" maxlength="10" class="form-control " value="{{$AVPDetails[0]->avp_zip != ''  ? $AVPDetails[0]->avp_zip : ''}}" onkeypress="return isNumber(event)">
              </div>
              </div>
              <!-- /.form group -->
              {{-- <div class="radio-chk">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="AVPVacant" id="AVPVacant" class="ios-switch green bigswitch" {{$AVPDetails[0]->avp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div>
                        </label>
                    </div>
                </div>
              </div> --}}
              <div class="col-sm-6  avp-field">
                <label>&nbsp;</label><br>
                <button type="button" class="btn bg-gradient-primary" id="{{ $AVPDetails[0]->user_id }}" onclick="return resetPassword(this.id)" {{$AVPDetails[0]->user_id == ''  ? 'disabled' : ''}} ><i class="fas fa-redo-alt"></i>&nbsp;&nbsp;&nbsp;Reset Password</button>
              </div>
              </div>
            </div>

            <div class="card-header">
                <h3 class="card-title">MVP</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                     <!-- /.form group -->
              <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="MVPVacant" id="MVPVacant" class="custom-control-input" {{$MVPDetails[0]->mvp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="MVPVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 mvp-field">&nbsp;</div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_mvp_fname" id="ch_mvp_fname"  class="form-control" value="{{$MVPDetails[0]->mvp_fname != ''  ? $MVPDetails[0]->mvp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" value="{{$MVPDetails[0]->mvp_lname != ''  ? $MVPDetails[0]->mvp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$MVPDetails[0]->mvp_email != ''  ? $MVPDetails[0]->mvp_email : ''}}" maxlength="50">
                <input type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails[0]->mvp_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$MVPDetails[0]->mvp_phone != ''  ? $MVPDetails[0]->mvp_phone : ''}}">
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 mvp-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_mvp_street" id="ch_mvp_street" class="form-control" rows="4" value="{{$MVPDetails[0]->mvp_addr != ''  ? $MVPDetails[0]->mvp_addr : ''}}" maxlength="250">
              </div>
              </div>
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" value="{{$MVPDetails[0]->mvp_city != ''  ? $MVPDetails[0]->mvp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_mvp_state" id="ch_mvp_state" class="form-control select2-bs4" style="width: 100%;" >
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}" {{$MVPDetails[0]->mvp_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" maxlength="10" class="form-control" value="{{$MVPDetails[0]->mvp_zip != ''  ? $MVPDetails[0]->mvp_zip : ''}}" onkeypress="return isNumber(event)" >
              </div>
              </div>
              <!-- /.form group -->
               {{-- <div class="radio-chk">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="MVPVacant" id="MVPVacant" class="ios-switch green bigswitch" {{$MVPDetails[0]->mvp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" /><div><div></div></div>
                        </label>
                    </div>
                </div>
              </div> --}}
              <div class="col-sm-6 mvp-field">
                <label>&nbsp;</label><br>
                <button type="button" class="btn bg-gradient-primary" id="{{ $MVPDetails[0]->user_id }}" onclick="return resetPassword(this.id)" {{$MVPDetails[0]->user_id == ''  ? 'disabled' : ''}} ><i class="fas fa-redo-alt" aria-hidden="true" ></i>&nbsp; Reset Password</button>
              </div>
              </div>
            </div>

              <div class="card-header">
                <h3 class="card-title">Treasurer</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <!-- /.form group -->
              <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="TreasVacant" id="TreasVacant" class="custom-control-input" {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="TreasVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 treas-field">&nbsp;</div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control" value="{{$TRSDetails[0]->trs_fname != ''  ? $TRSDetails[0]->trs_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" value="{{$TRSDetails[0]->trs_lname != ''  ? $TRSDetails[0]->trs_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
             <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_trs_email" id="ch_trs_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$TRSDetails[0]->trs_email != ''  ? $TRSDetails[0]->trs_email : ''}}" maxlength="50">
                <input type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails[0]->trs_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$TRSDetails[0]->trs_phone != ''  ? $TRSDetails[0]->trs_phone : ''}}">
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 treas-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_trs_street" id="ch_trs_street" class="form-control" rows="4" value="{{$TRSDetails[0]->trs_addr != ''  ? $TRSDetails[0]->trs_addr : ''}}" maxlength="250" >
              </div>
              </div>
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" value="{{$TRSDetails[0]->trs_city != ''  ? $TRSDetails[0]->trs_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_trs_state" id="ch_trs_state" class="form-control select2-bs4" style="width: 100%;">
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}" {{$TRSDetails[0]->trs_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_trs_zip" id="ch_trs_zip" maxlength="10" class="form-control" value="{{$TRSDetails[0]->trs_zip != ''  ? $TRSDetails[0]->trs_zip : ''}}" onkeypress="return isNumber(event)">
              </div>
              </div>
              <!-- /.form group -->
               {{-- <div class="radio-chk">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="TreasVacant" id="TreasVacant" class="ios-switch green bigswitch" {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div>
                        </label>
                    </div>
                </div>
              </div> --}}
              <div class="col-sm-6 treas-field">
                <label>&nbsp;</label><br>
                <button type="button" class="btn bg-gradient-primary" id="{{ $TRSDetails[0]->user_id }}" onclick="return resetPassword(this.id)" {{$TRSDetails[0]->user_id == ''  ? 'disabled' : ''}} ><i class="fas fa-redo-alt" aria-hidden="true" ></i>&nbsp; Reset Password</button>
              </div>
              </div>
            </div>

              <div class="card-header">
                <h3 class="card-title">Secretary</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <!-- /.form group -->
              <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="SecVacant" id="SecVacant" class="custom-control-input" {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="SecVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 sec-field">&nbsp;</div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" value="{{$SECDetails[0]->sec_fname != ''  ? $SECDetails[0]->sec_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" value="{{$SECDetails[0]->sec_lname != ''  ? $SECDetails[0]->sec_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
             <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_sec_email" id="ch_sec_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}" maxlength="50" >
                <input type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails[0]->sec_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$SECDetails[0]->sec_phone != ''  ? $SECDetails[0]->sec_phone : ''}}" >
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 sec-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_sec_street" id="ch_sec_street" class="form-control" rows="4" value="{{$SECDetails[0]->sec_addr != ''  ? $SECDetails[0]->sec_addr : ''}}" maxlength="250" >
              </div>
              </div>
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" value="{{$SECDetails[0]->sec_city != ''  ? $SECDetails[0]->sec_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_sec_state" id="ch_sec_state" class="form-control select2-bs4" style="width: 100%;" >
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}" {{$SECDetails[0]->sec_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_sec_zip" id="ch_sec_zip" maxlength="10" class="form-control" value="{{$SECDetails[0]->sec_zip != ''  ? $SECDetails[0]->sec_zip : ''}}" onkeypress="return isNumber(event)" >
              </div>
              </div>
               {{-- <div class="radio-chk">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" /><div><div></div></div>
                        </label>
                    </div>
                </div>
              </div> --}}
              <div class="col-sm-6 sec-field">
                <label>&nbsp;</label><br>
                <button type="button" class="btn bg-gradient-primary" id="{{ $SECDetails[0]->user_id }}" onclick="return resetPassword(this.id)" {{$SECDetails[0]->user_id == ''  ? 'disabled' : ''}} ><i class="fas fa-redo-alt" aria-hidden="true" ></i>&nbsp; Reset Password</button>
            </div>
              </div>
            </div>

              <div class="card-header">
                <h3 class="card-title">Information</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                <!-- /.form group -->
                <div class="col-sm-6">
                    <div class="form-group">
                      <label>Chapter Website</label>
                      <input type="text" name="ch_website" class="form-control" placeholder="http://www.momsclubofchaptername.com" value="{{$chapterList[0]->website_url}}" maxlength="50" id="validate_url" onchange="is_url(); updateWebsiteStatus();">
                    </div>
                    </div>
                      <!-- /.form group -->
                      <div class="col-sm-6">
                        <div class="form-group">
                            <label>Website Link Status</label> <span class="field-required">*</span>
                            <select id="ch_webstatus" name="ch_webstatus" class="form-control select2-bs4" style="width: 100%;" required>
                                <option value="0" id="option0" {{$chapterList[0]->website_status == 0 ? 'selected' : ''}} disabled>Website Not Linked</option>
                                <option value="1" id="option1" {{$chapterList[0]->website_status == 1 ? 'selected' : ''}}>Website Linked</option>
                                <option value="2" id="option2" {{$chapterList[0]->website_status == 2 ? 'selected' : ''}}>Add Link Requested</option>
                                <option value="3" id="option3" {{$chapterList[0]->website_status == 3 ? 'selected' : ''}}>Do Not Link</option>
                            </select>

                            <input type="hidden" name="ch_hid_webstatus" value="{{ $chapterList[0]->website_status }}">
                        </div>
                    </div>
                <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">
                <label>Online Discussion Group (Meetup, Google Groups, etc)</label>
                <input type="text" name="ch_onlinediss" class="form-control" value="{{ $chapterList[0]->egroup}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Facebook</label>
                <input type="text" name="ch_social1" class="form-control" value="{{ $chapterList[0]->social1}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Twitter</label>
                <input type="text" name="ch_social2" class="form-control" value="{{ $chapterList[0]->social2}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Instagram</label>
                <input type="text" name="ch_social3" class="form-control" value="{{ $chapterList[0]->social3}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Chapter Email Address</label>
                <input type="email" name="ch_email" class="form-control" value="{{ $chapterList[0]->email}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>PO Box</label>
                <input type="text" name="ch_pobox" class="form-control" maxlength="30" value="{{ $chapterList[0]->po_box}}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Inquiries Email Address</label> <span class="field-required">*</span>
                <input type="email" name="ch_inqemailcontact" class="form-control" value="{{ $chapterList[0]->inquiries_contact}}" maxlength="50" required >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Inquiries Notes (not visible to board members)</label>
                <input type="text" name="ch_inqnote" class="form-control" value="{{ $chapterList[0]->inquiries_note}}" maxlength="50" >
              </div>
              </div>



              <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">
                <label>Additional Information (not visible to board members)</label>
                <textarea name="ch_addinfo" class="form-control" rows="4" >{{ $chapterList[0]->additional_info }}</textarea>
              </div>
              </div>
            </div>
        </div>

            <div class="card-header">
                <h3 class="card-title"></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
               <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Previously Known As</label>
                <input type="text" name="ch_preknown" id="ch_preknown" class="form-control" value="{{ $chapterList[0]->former_name}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Sistered By</label>
                <input type="text" name="ch_sistered" id="ch_sistered" class="form-control" value="{{ $chapterList[0]->sistered_by}}" maxlength="50" >
              </div>
              </div>
                <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Founded Month</label>
                <select name="ch_founddate" class="form-control select2-bs4" style="width: 100%;" disabled>
                 <option value="">Select Month</option>
                  @foreach($foundedMonth as $key=>$val)
                  <option value="{{$key}}" {{$currentMonth == $key  ? 'selected' : ''}}>{{$val}}</option>
                 @endforeach
                </select>
                <input type="hidden" name="ch_hid_founddate" value="{{$currentMonth}}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Founded Year</label>
                <input type="text" name="ch_foundyear" class="form-control" maxlength="4" value="{{ $chapterList[0]->start_year}}" disabled>
                <input type="hidden" name="ch_hid_foundyear" value="{{$chapterList[0]->start_year}}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Re-Registration Dues Paid</label>
                <input type="text" name="ch_dues" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{$chapterList[0]->dues_last_paid }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Number of Members</label>
                <input type="text" name="ch_memberpaid" class="form-control" value="{{ $chapterList[0]->members_paid_for }}" disabled>
              </div>
              </div>
                               <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">
                <label>Re-Registration Notes (not visible to board members)</label>
                <input type="text" name="ch_regnotes" class="form-control" maxlength="50" value="{{ $chapterList[0]->reg_notes}}" >
              </div>
              </div>

                                    <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>M2M Fund Donation Date</label>
                <input type="date" name="ch_m2mdate" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{$chapterList[0]->m2m_date }}" disabled>
                {{-- <input type="date" name="ch_m2mdate" class="form-control" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" value="{{$chapterList[0]->m2m_date }}" disabled> --}}
              </div>
              </div>
                            <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>M2M Fund Donation</label>
                <input type="text" name="ch_m2mpayment" class="form-control" value="${{ $chapterList[0]->m2m_payment }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Sustaining Chapter Donation Date</label>
                <input type="date" name="ch_sustaining_date" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{$chapterList[0]->sustaining_date }}" disabled>
                {{-- <input type="date" name="ch_sustaining_date" class="form-control" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" value="{{$chapterList[0]->sustaining_date }}" disabled> --}}
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Sustaining Chapter Donation</label>
                <input type="text" name="ch_sustaining_donation" class="form-control" value="${{ $chapterList[0]->sustaining_donation }}" disabled>
              </div>
              </div>
            </div>
        </div>

            @if ($eoyReportConditionDISABLED || ($eoyReportCondition && $eoyTestCondition && $testers_yes) || ($eoyReportCondition && $coordinators_yes))
            <div class="card-header">
                <h3 class="card-title">EOY Reporting</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
            <!-- /.form group -->
            <div class="col-sm-3">
              <div class="custom-control custom-switch">
                    <input disabled type="checkbox" name="BoardInfo" id="BoardInfo" class="custom-control-input"  {{$chapterList[0]->new_board_submitted ? 'checked' : '' }} />
                    <label class="custom-control-label" for="BoardInfo" ><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Info Received</label>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-3">
                <div class="custom-control custom-switch">
                    <input disabled type="checkbox" name="BoardActive" id="BoardActive" class="custom-control-input" {{ $chapterList[0]->new_board_active ? 'checked' : '' }} />
                    <label class="custom-control-label" for="BoardActive">{{ date('Y') . '-' . (date('Y') + 1) }} Board Activated</label>
                </div>
                </div>
              <!-- /.form group -->
              <div class="col-sm-3">
                <div class="custom-control custom-switch">
                    <input disabled type="checkbox" name="FinancialReceived" id="FinancialReceived" class="custom-control-input" {{$chapterList[0]->financial_report_received ? 'checked' : '' }} />
                    <label class="custom-control-label" for="FinancialReceived"><?php echo date('Y')-1 .'-'.date('Y');?> Financial Report Received</label>
                    </div>
                </div>
              <!-- /.form group -->
              <div class="col-sm-3">
                <div class="custom-control custom-switch">
                    <input disabled type="checkbox" name="ch_financial_complete" id="FinancialComplete" class="custom-control-input" {{$chapterList[0]->financial_report_complete ? 'checked' : '' }} />
                    <label class="custom-control-label" for="FinancialComplete"><?php echo date('Y')-1 .'-'.date('Y');?> Financial Review Completed</label>
                    </div>
                </div>
                <div class="card-body text-center">
                @foreach($chapterList as $list)
                @if (($regionalCoordinatorCondition) || ($eoyTestCondition))
                        <button type="button" id="ReportStatus" class="btn bg-gradient-primary" onclick="window.location.href='{{ route('chapter.statusview', ['id' => $list->id]) }}'">
                            Update Report Status
                        </button>
                    @if($list->new_board_active != '1')
                        <button type="button" id="BoardReportAlwaysDisabled" class="btn bg-gradient-primary" onclick="window.location.href='{{ route('chapter.showboardinfo', ['id' => $list->id]) }}'">
                            {{ date('Y') . '-' . (date('Y') + 1) }} Board Election Report
                        </button>
                    @endif
                    @endif
                        <button type="button" id="FinancialReport" class="btn bg-gradient-primary" onclick="window.location.href='{{ route('chapter.showfinancial', ['id' => $list->id]) }}'">
                            {{ date('Y')-1 .'-'.date('Y') }} Financial Report
                        </button>
                @endforeach
                 </div>
            </div>
        </div>
        @endif

        @if (!($eoyReportConditionDISABLED || ($eoyReportCondition && $eoyTestCondition && $testers_yes) || ($eoyReportCondition && $coordinators_yes)))
        <div class="card-header">
            <h3 class="card-title">EOY Reporting</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
            <br>
            <div id="reportStatusText" class="description text-center" style="color: red;">
                <h4><strong><?php echo date('Y')-1 .'-'.date('Y');?> Report Status/Links are not available at this time.</strong></h4>
            </div>

            </div>
        </div>
        @endif

        <div class="card-header">
            <h3 class="card-title">International MOMS Club Coordinators</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
              <!-- /.form group -->
              <div class="col-sm-4">
                <div class="form-group">
                @if($regionalCoordinatorCondition)
                <label>Primary Coordinator</label> <span class="field-required">*</span>
                <select name="ch_primarycor" id="ch_primarycor" class="form-control select2-bs4" style="width: 100%;" onchange="checkReportId(this.value)" required >
                <option value="">Select Primary Coordinator</option>
                @foreach($primaryCoordinatorList as $pcl)
                      <option value="{{$pcl->cid}}" {{$chapterList[0]->primary_coordinator_id == $pcl->cid  ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_primarycor" value="{{$chapterList[0]->primary_coordinator_id}}">
              <div id="display_corlist"> </div>
              @endif
              @if(!($regionalCoordinatorCondition))
                <label>Primary Coordinator</label> <span class="field-required">*</span>
                <select name="ch_primarycor" id="ch_primarycor" class="form-control select2-bs4" style="width: 100%;" onchange="checkReportId(this.value)" required disabled>
                <option value="">Select Primary Coordinator</option>
                @foreach($primaryCoordinatorList as $pcl)
                      <option value="{{$pcl->cid}}" {{$chapterList[0]->primary_coordinator_id == $pcl->cid  ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_primarycor" value="{{$chapterList[0]->primary_coordinator_id}}">
              <div id="display_corlist"> </div>
              @endif
                </div>
            </div>
            </div>
        </div>

              <div class="card-header">
            <h3 class="card-title"></h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
               <!-- /.form group -->
                  <div class="col-sm-6">
                  <div class="form-group">
                    <label>Last Updated By</label>
                    <input type="text" class="form-control" value="{{$chapterList[0]->last_updated_by}}" disabled>
                  </div>
                  </div>
                  <!-- /.form group -->
                  <div class="col-sm-6">
                  <div class="form-group">
                    <label>Last Updated Date</label>
                    <input type="text" class="form-control" value="{{$chapterList[0]->last_updated_date}}" disabled>
                  </div>
                  </div>
              </div>
              </div>
        </div>
    </div>

            <!-- /.box-body -->
            <div class="card-body text-center">
            @if($bigSisterCondition)
              <button type="submit" class="btn bg-gradient-primary" onclick="return PreSaveValidate()" ><i class="fas fa-save"></i>&nbsp; Save</button>
              <a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=MOMS Club of {{ $chapterList[0]->name }}" class="btn bg-gradient-primary"><i class="fas fa-envelope"></i>&nbsp; E-mail Board</a>
              <button type="button" class="btn bg-gradient-primary" onclick="ConfirmCancel(this);" ><i class="fas fa-undo"></i>&nbsp; Reset Data</button>
            @endif
              <a href="{{ route('chapter.list') }}" class="btn bg-gradient-primary"><i class="fa fa-reply fa-fw" aria-hidden="true" ></i>&nbsp; Back</a>

                <br><br>
                    @if($assistConferenceCoordinatorCondition)
                <button type="button" class="btn bg-gradient-primary" onclick="return UpdateEIN()"><i class="fas fa-university"  ></i>&nbsp; Update EIN</button>
                @if(empty($chapterList[0]->ein_letter_path))
                    <button type="button" class="btn bg-gradient-primary" data-toggle="modal" data-target="#modal-ein"><i class="fas fa-upload" ></i>&nbsp; Upload EIN Letter</button>
                @else
                    <button type="button" class="btn bg-gradient-primary" data-toggle="modal" data-target="#modal-ein"><i class="fas fa-upload" ></i>&nbsp; Replace EIN Letter</button>               @endif
                @endif
            @if($regionalCoordinatorCondition)
              <button type="button" class="btn bg-gradient-primary" data-toggle="modal" data-target="#modal-disband"><i class="fas fa-ban"  ></i>&nbsp; Disband Chapter</button>
            @endif
              </div>

            </form>
            <!-- /.box-body -->

          </div>
          <!-- /.box -->
        </div>
      </div>

      <div class="modal fade" id="modal-ein">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Upload EIN Letter</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/files/storeEIN/'. $id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name='file' required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" >Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-disband">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Chapter Disband Reason</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <p>Marking a chapter as disbanded will remove the logins for all board members and remove the chapter. Please enter the reason for disbanding and press OK</p>
                        <input type="text" id="disband_reason" name="disband_reason" class="form-control">
                        <input type="hidden" id="chapter_id" name="chapter_id" class="form-control" value="{{ $chapterList[0]->id }}">

                        <div class="radio-chk">
                            <label>Send Standard Disband Letter</label>
                            <div style="color: red;">Please be patient if sending letter, screen does take a while to zap/refresh after clicking OK.</div>
                            <label style="display: block;">
                                <input type="checkbox" name="disband_letter" id="disband_letter" class="ios-switch green bigswitch" {{$chapterList[0]->disband_letter == '1'  ? 'checked' : ''}}>
                                <div><div></div></div>
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="return disbandChapter()">OK</button>
                    <div id="loading-spinner" style="display: none;">
                        <!-- Add your loading spinner or message here -->
                        <i class="fa fa-spinner fa-spin"></i> Processing...
                    </div>
                </div>
            </div>
        </div>
    </div>

    </section>

    @endsection

  @section('customscript')
  <script>
    // Disble fields for non Primary Coordinators
    $(document).ready(function () {
        // Check if the assistant conference coordinator condition is not met
        if (!($bigSisterCondition)) {
            // Disable all input fields, select elements, textareas, and Save button
            $('input, select, textarea').prop('disabled', true);
        }
    });

// Disable Web Link Status option 0
    document.getElementById('option0').disabled = true;

    function disbandChapter() {
        var txt = $("#disband_reason").val();
        var cid = $("#chapter_id").val();
        var ltr = $("#disband_letter").is(":checked") ? '1' : '0';

        if (txt == '') {
            alert("Please enter reason for Disband");
            $("#disband_reason").focus();
            return false;
        } else {
            // Show loading spinner
            $('#loading-spinner').show();

            $.ajax({
                url: '{{ route('chapter.disband') }}',
                type: 'POST',
                data: { reason: txt, letter: ltr, chapterid: cid, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        window.location.href = response.redirect;
                    } else {
                        alert(response.message);
                    }
                },
                error: function(jqXHR, exception) {
                    alert("Something went wrong, Please try again.");
                },
                complete: function() {
                    // Hide loading spinner
                    $('#loading-spinner').hide();
                }
            });
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

//   $(document ).ready(function() {
    // var phoneListArr = ["ch_pre_phone","ch_avp_phone","ch_mvp_phone","ch_trs_phone","ch_sec_phone"];
    // for (var i = phoneListArr.length - 1; i >= 0; i--) {
    //     var inputValue = $("#"+phoneListArr[i]).val();
    //     if(inputValue.length > 10) inputValue = inputValue.substring(0,12);
    //     var reInputValue = inputValue.replace(/(\d{3})(\d{3})/, "$1-$2-");
    //     $("#"+phoneListArr[i]).val(reInputValue);
    // }

    // $("#ch_pre_phone").keyup(function() {
    //     this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    // });
    // $("#ch_avp_phone").keyup(function() {
    //     this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    // });
    // $("#ch_mvp_phone").keyup(function() {
    //     this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    // });
    // $("#ch_trs_phone").keyup(function() {
    //     this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    // });
    // $("#ch_sec_phone").keyup(function() {
    //     this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    // });

//     $(document ).ready(function() {

//     var selectedCorId = $("select#ch_primarycor option").filter(":selected").val();
//     if(selectedCorId !=""){
//       $.ajax({
//         url: '{{ url("/checkreportid/") }}' + '/' + selectedCorId,
//             type: "GET",
//             success: function(result) {
//                $("#display_corlist").html(result);
//             },
//             error: function (jqXHR, exception) {

//             }
//         });
//     }
//      function checkReportId(val){
//           $.ajax({
//             url: '{{ url("/checkreportid/") }}' + '/' + val,
//             type: "GET",
//             success: function(result) {
//                $("#display_corlist").html(result);
//             },
//             error: function (jqXHR, exception) {

//             }
//         });

//       }

//   });


$(document).ready(function() {

// Function to load the coordinator list based on the selected value
function loadCoordinatorList(corId) {
    if(corId != "") {
        $.ajax({
            url: '{{ url("/checkreportid/") }}' + '/' + corId,
            type: "GET",
            success: function(result) {
               $("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {
               console.log("Error: ", jqXHR, exception);
            }
        });
    }
}

// Get the selected coordinator ID on page load
var selectedCorId = $("#ch_primarycor").val();
loadCoordinatorList(selectedCorId);

// Update the coordinator list when the dropdown changes
$("#ch_primarycor").change(function() {
    var selectedValue = $(this).val();
    loadCoordinatorList(selectedValue);
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


      function ConfirmCancel(element){
        var result=confirm("Any unsaved changes will be lost. Do you want to continue?");
        if(result)
            location.reload()
        else
            return false;
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

                //Okay, all validation passed, save the records to the database
                return true;
            }


    function resetPassword(userid){
        // var new_password="";
        //         new_password = prompt("Please enter new password for this board member.", "TempPass4You");
        //         if (new_password != null && userid !='') {
        //             //Verify the password entered is of an allowable size
        //             if(new_password.length < 7){
        //                 alert("Password must be at least 7 characters.  The password has not been reset.");
        //                 return false;
        //             }
        //             else{
        //        $.ajax({
        //           url: '/chapter/resetpswd',
        //           type: "POST",
        //           data: { pswd:new_password,user_id:userid, _token: '{{csrf_token()}}' },
        //           success: function(result) {
        //               alert('Password has been reset successfully');

        //           },
        //           error: function (jqXHR, exception) {

        //           }
        //       });

        //         return true;
        //             }
        //         }
        //         else{
        //   //alert('Not Allowed');
        //   return false;
        // }

        //     }


        var new_password = prompt("Please enter new password for this board member.", "TempPass4You");
        if (new_password != null && userid != '') {
            // Verify the password entered is of an allowable size
            if (new_password.length < 7) {
                alert("Password must be at least 7 characters. The password has not been reset.");
                return false;
            } else {
                $.ajax({
                    url: '{{ route('chapter.resetpswd') }}',
                    type: "POST",
                    data: {
                        pswd: new_password,
                        user_id: userid,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        alert(result.message);
                    },
                    error: function(jqXHR, exception) {
                        if (jqXHR.responseJSON && jqXHR.responseJSON.error) {
                            alert(jqXHR.responseJSON.error);
                        } else {
                            alert('An error occurred while resetting the password.');
                        }
                    }
                });
                return true;
            }
        } else {
            return false;
        }
    }



function UpdateEIN(){
                var ein=document.getElementById("ch_ein").value;
                var new_ein="";

                if (ein==""){
                    new_ein = prompt("Please enter the EIN for the chapter");

                    if (new_ein != null) {
                        document.getElementById("ch_ein").value = new_ein;
                        return true;
                    }
                }
                else{
                    var result=confirm("This chapter already has an assigned EIN.  Once a chapter has been assigned an EIN it should not be changed.  Are you REALLY sure you want to do this?");
                    if(result){
                        new_ein = prompt("Please enter the EIN for the chapter");
                        if (new_ein != null) {
                            document.getElementById("ch_ein").value = new_ein;
                            return true;
                        }
                    }
                    else{
                        return false;
                    }
                }
            }

    // function EINLetter(){
    //             var ein=document.getElementById("ch_ein_letter_path").value;
    //             var new_ein="";

    //             if (ein==""){
    //                 new_ein = prompt("Please enter url path for the chapter's EIN Letter");

    //                 if (new_ein != null) {
    //                     document.getElementById("ch_ein_letter_path").value = new_ein;
    //                     return true;
    //                 }
    //             }
    //             else{
    //                 var result=confirm("This chapter already has an EIN Letter.  Are you REALLY sure you want to do this?");
    //                 if(result){
    //                     new_ein = prompt("Please enter url path for the chapter's EIN Letter");
    //                     if (new_ein != null) {
    //                         document.getElementById("ch_ein_letter_path").value = new_ein;
    //                         return true;
    //                     }
    //                 }
    //                 else{
    //                     return false;
    //                 }
    //             }
    //         }

    function PreviousNameReminder(){

        alert("If you are changing the chapter name, please be sure to note the old name.");
        $('#ch_preknown').focus();

    }
</script>
@endsection
