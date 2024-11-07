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
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Details</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form id="myForm" method="POST" action="{{ route('chapters.updatechap', $chapterList[0]->id) }}">
        @csrf
     <section class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Chapter</h3>
                    <br>
                    &nbsp;&nbsp;<a href="{{ route('viewas.viewchapterpresident', ['id' => $id]) }}" class="btn btn-xs bg-gradient-primary">
                        <i class="fas fa-eye"></i>&nbsp;&nbsp;&nbsp;View Chapter Profile As President
                    </a>

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>MOMS Club of</label> <span class="field-required">*</span>
                        <input type="text" name="ch_name" class="form-control disable-RCCondition"  required value="{{ $chapterList[0]->name }}" onchange="PreviousNameReminder()">
                    </div>
                    </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>State</label> <span class="field-required">*</span>
                        <select id="ch_state" name="ch_state" class="form-control disable-RCCondition select2-bs4" style="width: 100%;" required >
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
                        <select id="ch_country" name="ch_country" class="form-control disable-field select2-bs4" style="width: 100%;" required >
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
                        <select id="ch_conference" name="ch_conference" class="form-control disable-field select2-bs4" style="width: 100%;" required >
                        <option value="">Select Conference</option>
                                    @foreach($confList as $con)
                            <option value="{{$con->id}}" {{$chapterList[0]->conference == $con->id  ? 'selected' : ''}} >{{$con->conference_name}} </option>
                            @endforeach
                                </select>
                                <input type="hidden" name="ch_hid_conference" value="{{ $chapterList[0]->conference }}">
                                </div>
                            </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>Region</label> <span class="field-required">*</span>
                        <select id="ch_region" name="ch_region" class="form-control disable-field select2-bs4-bs4" style="width: 100%;" required >
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
                        <input type="text" id="ch_ein" name="ch_ein" class="form-control" value="{{ $chapterList[0]->ein }}" readonly >
                        <input type="hidden" name="ch_hid_ein" value="{{ $chapterList[0]->ein }}">
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
                        <input type="hidden" id="ch_ein_letter_path" name="ch_ein_letter_path" class="form-control" value="{{ $chapterList[0]->ein_letter_path }}"  readonly>
                    </div>
                    </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>Status</label> <span class="field-required">*</span>
                        <select id="ch_status" name="ch_status" class="form-control disable-RCCondition select2-bs4" style="width: 100%;" required >
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
                        <input type="text" name="ch_notes" class="form-control disable-RCCondition"  value="{{ $chapterList[0]->notes}}" >
                        <input type="hidden" name="ch_hid_notes" value="{{ $chapterList[0]->notes}}" >
                    </div>
                    </div>
                    <!-- /.form group -->
                    <div class="col-sm-12">
                    <div class="form-group">
                        <label>Boundaries</label> <span class="field-required">*</span>
                        <input type="text" name="ch_boundariesterry" class="form-control disable-RCCondition" rows="2" value="{{ $chapterList[0]->territory }}" required >
                        <input type="hidden" name="ch_hid_boundariesterry" value="{{ $chapterList[0]->territory}}" >
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
                <input type="text" name="ch_pre_fname" class="form-control " value="{{ $chapterList[0]->first_name }}"  required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 ">
              <div class="form-group">
                <label>Last Name</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_lname" class="form-control " value="{{ $chapterList[0]->last_name }}"  required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
                            <!-- /.form group -->
              <div class="col-sm-6 ">
              <div class="form-group">
                <label>Email</label> <span class="field-required">*</span>
                <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control " onblur="checkDuplicateEmail(this.value,this.id)" value="{{ $chapterList[0]->bd_email }}"  required >
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

              <!-- /.form group -->
              <div class="col-sm-12 ">
              <div class="form-group">
                <label>Street Address</label> <span class="field-required">*</span>
                <input name="ch_pre_street" class="form-control " rows="4"   value="{{ $chapterList[0]->street_address }}" required>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 ">
              <div class="form-group">
                <label>City</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_city" class="form-control "  value="{{ $chapterList[0]->city }}" required onkeypress="return isAlphanumeric(event)">
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
                <input type="text" name="ch_pre_zip" class="form-control " value="{{ $chapterList[0]->zip }}"  required onkeypress="return isNumber(event)">
              </div>
              </div>

              <div class="card-body text-center">
                <p>This will reset password to default "TempPass4You" for this user only.</p>
                <button type="button" class="btn bg-gradient-primary" id="{{ $chapterList[0]->user_id }}" onclick="return updatePassword(this.id)"><i class="fas fa-redo-alt"></i>&nbsp;&nbsp;&nbsp;Reset Password</button>
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
                <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control " value="{{$AVPDetails[0]->avp_fname != ''  ? $AVPDetails[0]->avp_fname : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6  avp-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control " value="{{$AVPDetails[0]->avp_lname != ''  ? $AVPDetails[0]->avp_lname : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6  avp-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_avp_email" id="ch_avp_email" class="form-control " onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" >
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
                <input name="ch_avp_street" id="ch_avp_street" class="form-control " rows="4"  value="{{$AVPDetails[0]->avp_addr != ''  ? $AVPDetails[0]->avp_addr : ''}}">
              </div>
              </div>
              <div class="col-sm-4 avp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control " value="{{$AVPDetails[0]->avp_city != ''  ? $AVPDetails[0]->avp_city : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4  avp-field">
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
              <div class="col-sm-4 avp-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_avp_zip" id="ch_avp_zip"  class="form-control " value="{{$AVPDetails[0]->avp_zip != ''  ? $AVPDetails[0]->avp_zip : ''}}" onkeypress="return isNumber(event)">
              </div>
              </div>
              <div class="card-body text-center avp-field">
                <p>This will reset password to default "TempPass4You" for this user only.</p>
                <button type="button" class="btn bg-gradient-primary" id="{{ $AVPDetails[0]->user_id }}" onclick="return updatePassword(this.id)" {{$AVPDetails[0]->user_id == ''  ? 'disabled' : ''}} ><i class="fas fa-redo-alt"></i>&nbsp;&nbsp;&nbsp;Reset Password</button>
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
                <input type="text" name="ch_mvp_fname" id="ch_mvp_fname"  class="form-control" value="{{$MVPDetails[0]->mvp_fname != ''  ? $MVPDetails[0]->mvp_fname : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" value="{{$MVPDetails[0]->mvp_lname != ''  ? $MVPDetails[0]->mvp_lname : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$MVPDetails[0]->mvp_email != ''  ? $MVPDetails[0]->mvp_email : ''}}" >
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
                <input name="ch_mvp_street" id="ch_mvp_street" class="form-control" rows="4" value="{{$MVPDetails[0]->mvp_addr != ''  ? $MVPDetails[0]->mvp_addr : ''}}" >
              </div>
              </div>
              <div class="col-sm-4 mvp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" value="{{$MVPDetails[0]->mvp_city != ''  ? $MVPDetails[0]->mvp_city : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 mvp-field">
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
              <div class="col-sm-4 mvp-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_mvp_zip" id="ch_mvp_zip"  class="form-control" value="{{$MVPDetails[0]->mvp_zip != ''  ? $MVPDetails[0]->mvp_zip : ''}}" onkeypress="return isNumber(event)" >
              </div>
              </div>
              <div class="card-body text-center mvp-field">
                <p>This will reset password to default "TempPass4You" for this user only.</p>
                <button type="button" class="btn bg-gradient-primary" id="{{ $MVPDetails[0]->user_id }}" onclick="return updatePassword(this.id)" {{$MVPDetails[0]->user_id == ''  ? 'disabled' : ''}} ><i class="fas fa-redo-alt" ></i>&nbsp; Reset Password</button>
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
                <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control" value="{{$TRSDetails[0]->trs_fname != ''  ? $TRSDetails[0]->trs_fname : ''}}"  onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" value="{{$TRSDetails[0]->trs_lname != ''  ? $TRSDetails[0]->trs_lname : ''}}"  onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
             <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_trs_email" id="ch_trs_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$TRSDetails[0]->trs_email != ''  ? $TRSDetails[0]->trs_email : ''}}" >
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
                <input name="ch_trs_street" id="ch_trs_street" class="form-control" rows="4" value="{{$TRSDetails[0]->trs_addr != ''  ? $TRSDetails[0]->trs_addr : ''}}"  >
              </div>
              </div>
              <div class="col-sm-4 treas-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" value="{{$TRSDetails[0]->trs_city != ''  ? $TRSDetails[0]->trs_city : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 treas-field">
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
              <div class="col-sm-4 treas-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_trs_zip" id="ch_trs_zip"  class="form-control" value="{{$TRSDetails[0]->trs_zip != ''  ? $TRSDetails[0]->trs_zip : ''}}" onkeypress="return isNumber(event)">
              </div>
              </div>
              <div class="card-body text-center treas-field">
                <p>This will reset password to default "TempPass4You" for this user only.</p>
                <button type="button" class="btn bg-gradient-primary" id="{{ $TRSDetails[0]->user_id }}" onclick="return updatePassword(this.id)" {{$TRSDetails[0]->user_id == ''  ? 'disabled' : ''}} ><i class="fas fa-redo-alt" ></i>&nbsp; Reset Password</button>
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
                <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" value="{{$SECDetails[0]->sec_fname != ''  ? $SECDetails[0]->sec_fname : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" value="{{$SECDetails[0]->sec_lname != ''  ? $SECDetails[0]->sec_lname : ''}}"  onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
             <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_sec_email" id="ch_sec_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}"  >
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
                <input name="ch_sec_street" id="ch_sec_street" class="form-control" rows="4" value="{{$SECDetails[0]->sec_addr != ''  ? $SECDetails[0]->sec_addr : ''}}"  >
              </div>
              </div>
              <div class="col-sm-4 sec-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" value="{{$SECDetails[0]->sec_city != ''  ? $SECDetails[0]->sec_city : ''}}"  onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 sec-field">
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
              <div class="col-sm-4 sec-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_sec_zip" id="ch_sec_zip"  class="form-control" value="{{$SECDetails[0]->sec_zip != ''  ? $SECDetails[0]->sec_zip : ''}}" onkeypress="return isNumber(event)" >
              </div>
              </div>
              <div class="card-body text-center sec-field">
                <p>This will reset password to default "TempPass4You" for this user only.</p>
                <button type="button" class="btn bg-gradient-primary" id="{{ $SECDetails[0]->user_id }}" onclick="return updatePassword(this.id)" {{$SECDetails[0]->user_id == ''  ? 'disabled' : ''}} ><i class="fas fa-redo-alt"></i>&nbsp; Reset Password</button>
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
                      <input type="text" name="ch_website" class="form-control" placeholder="http://www.momsclubofchaptername.com" value="{{$chapterList[0]->website_url}}"  id="validate_url" onchange="is_url(); updateWebsiteStatus();">
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
              <div class="col-sm-3">
              <div class="form-group">
                <label>Forum/Group/App</label>
                <input type="text" name="ch_onlinediss" class="form-control" value="{{ $chapterList[0]->egroup}}"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-3">
              <div class="form-group">
                <label>Facebook</label>
                <input type="text" name="ch_social1" class="form-control" value="{{ $chapterList[0]->social1}}"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-3">
              <div class="form-group">
                <label>Twitter</label>
                <input type="text" name="ch_social2" class="form-control" value="{{ $chapterList[0]->social2}}"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-3">
              <div class="form-group">
                <label>Instagram</label>
                <input type="text" name="ch_social3" class="form-control" value="{{ $chapterList[0]->social3}}"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Chapter Email Address</label>
                <input type="email" name="ch_email" class="form-control" value="{{ $chapterList[0]->email}}"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>PO Box</label>
                <input type="text" name="ch_pobox" class="form-control"  value="{{ $chapterList[0]->po_box}}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Inquiries Email Address</label> <span class="field-required">*</span>
                <input type="email" name="ch_inqemailcontact" class="form-control" value="{{ $chapterList[0]->inquiries_contact}}"  required >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Inquiries Notes (not visible to board members)</label>
                <input type="text" name="ch_inqnote" class="form-control" value="{{ $chapterList[0]->inquiries_note}}"  >
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
              <div class="col-sm-3">
                <div class="form-group">
                  <label>Founded Month</label>
                  <select name="ch_founddate" class="form-control disable-field select2-bs4" style="width: 100%;" >
                   <option value="">Select Month</option>
                    @foreach($foundedMonth as $key=>$val)
                    <option value="{{$key}}" {{$currentMonth == $key  ? 'selected' : ''}}>{{$val}}</option>
                   @endforeach
                  </select>
                  <input type="hidden" name="ch_hid_founddate" value="{{$chapterList[0]->start_month_id}}">
                </div>
                </div>
                <!-- /.form group -->
                <div class="col-sm-3">
                <div class="form-group">
                  <label>Founded Year</label>
                  <input type="text" name="ch_foundyear" class="form-control disable-field"  value="{{ $chapterList[0]->start_year}}" >
                  <input type="hidden" name="ch_hid_foundyear" value="{{$chapterList[0]->start_year}}">
                </div>
                </div>
               <!-- /.form group -->
              <div class="col-sm-3">
              <div class="form-group">
                <label>Previously Known As</label>
                <input type="text" name="ch_preknown" id="ch_preknown" class="form-control disable-RCCondition" value="{{ $chapterList[0]->former_name}}"  >
                <input type="hidden" name="ch_hid_preknown" value="{{$chapterList[0]->former_name}}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-3">
              <div class="form-group">
                <label>Sistered By</label>
                <input type="text" name="ch_sistered" id="ch_sistered" class="form-control disable-RCCondition" value="{{ $chapterList[0]->sistered_by}}"  >
                <input type="hidden" name="ch_hid_sistered" value="{{$chapterList[0]->sistered_by}}">
              </div>
              </div>

              <!-- /.form group -->
              <div class="col-sm-3">
              <div class="form-group">
                <label>Re-Registration Dues Paid</label>
                <input type="date" name="ch_dues" class="form-control disable-field" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{$chapterList[0]->dues_last_paid }}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-3">
              <div class="form-group">
                <label>Number of Members</label>
                <input type="text" name="ch_memberpaid" class="form-control disable-field" value="{{ $chapterList[0]->members_paid_for }}" >
              </div>
              </div>
                               <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Re-Registration Notes (not visible to board members)</label>
                <input type="text" name="ch_regnotes" class="form-control disable-field"  value="{{ $chapterList[0]->reg_notes}}" >
              </div>
              </div>

              <!-- /.form group -->
              <div class="col-sm-6">
                <div class="form-group">
                    <label>M2M Fund Donation</label>
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="date" name="ch_m2m_date" class="form-control disable-field" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chapterList[0]->m2m_date }}" >
                        </div>
                        <div class="col-sm-6">
                            <input type="text" name="ch_m2m_payment" id="ch_m2m_payment" class="form-control disable-field" value="${{ $chapterList[0]->m2m_payment }}" >
                        </div>
                    </div>
                </div>
            </div>
          <!-- /.form group -->
          <div class="col-sm-6">
            <div class="form-group">
                <label>Sustaining Chapter Donation</label>
                <div class="row">
                    <div class="col-sm-6">
                        <input type="date" name="ch_sustaining_date" class="form-control disable-field" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chapterList[0]->sustaining_date }}" >
                    </div>
                    <div class="col-sm-6">
                        <input type="text" name="ch_sustaining_donation" id="ch_sustaining_donation" class="form-control disable-field" value="${{ $chapterList[0]->sustaining_donation }}" >
                    </div>
                </div>
            </div>
        </div>
        @if($conferenceCoordinatorCondition)
        <div class="card-body text-center">
                <button type="button" id="ReportStatus" class="btn bg-gradient-primary" onclick="window.location.href='{{ route('chapters.chapreregpayment', ['id' => $id]) }}'">
                    Enter Re-Registration Payment
                </button>
                <button type="button" id="BoardReportAlwaysDisabled" class="btn bg-gradient-primary" onclick="window.location.href='{{ route('chapreports.chaprptdonationsview', ['id' => $id]) }}'">
                    Enter Chapter Donation
                </button>
             </div>
             @endif

            </div>
        </div>

            @if ($eoyReportConditionDISABLED || ($eoyReportCondition && $eoyTestCondition && $testers_yes) || ($eoyReportCondition && $coordinators_yes))
            <div class="card-header">
                {{-- <h3 class="card-title">EOY Reporting</h3> --}}
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
            <!-- /.form group -->
            <div class="col-sm-3">
              <div class="custom-control custom-switch">
                    <input type="checkbox" name="BoardInfo" id="BoardInfo" class="custom-control-input disable-field"  {{$chapterList[0]->new_board_submitted ? 'checked' : '' }} />
                    <label class="custom-control-label" for="BoardInfo" ><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Info Received</label>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-3">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="BoardActive" id="BoardActive" class="custom-control-input disable-field" {{ $chapterList[0]->new_board_active ? 'checked' : '' }} />
                    <label class="custom-control-label" for="BoardActive">{{ date('Y') . '-' . (date('Y') + 1) }} Board Activated</label>
                </div>
                </div>
              <!-- /.form group -->
              <div class="col-sm-3">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="FinancialReceived" id="FinancialReceived" class="custom-control-input disable-field" {{$chapterList[0]->financial_report_received ? 'checked' : '' }} />
                    <label class="custom-control-label" for="FinancialReceived"><?php echo date('Y')-1 .'-'.date('Y');?> Financial Report Received</label>
                    </div>
                </div>
              <!-- /.form group -->
              <div class="col-sm-3">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="ch_financial_complete" id="FinancialComplete" class="custom-control-input disable-field" {{$chapterList[0]->financial_report_complete ? 'checked' : '' }} />
                    <label class="custom-control-label" for="FinancialComplete"><?php echo date('Y')-1 .'-'.date('Y');?> Financial Review Completed</label>
                    </div>
                </div>
                <div class="card-body text-center">
                @foreach($chapterList as $list)
                @if (($regionalCoordinatorCondition) || ($eoyTestCondition))
                        <button type="button" id="ReportStatus" class="btn bg-gradient-primary" onclick="window.location.href='{{ route('eoyreports.eoystatusview', ['id' => $list->id]) }}'">
                            Update Report Status
                        </button>
                    @if($list->new_board_active != '1')
                        <button type="button" id="BoardReportAlwaysDisabled" class="btn bg-gradient-primary" onclick="window.location.href='{{ route('eoyreports.eoyboardreportview', ['id' => $list->id]) }}'">
                            {{ date('Y') . '-' . (date('Y') + 1) }} Board Election Report
                        </button>
                    @endif
                    @endif
                        <button type="button" id="FinancialReport" class="btn bg-gradient-primary" onclick="window.location.href='{{ route('eoyreports.eoyfinancialreportview', ['id' => $list->id]) }}'">
                            {{ date('Y')-1 .'-'.date('Y') }} Financial Report
                        </button>
                @endforeach
                 </div>
            </div>
        </div>
        @endif

        @if (!($eoyReportConditionDISABLED || ($eoyReportCondition && $eoyTestCondition && $testers_yes) || ($eoyReportCondition && $coordinators_yes)))
        <div class="card-header">
            {{-- <h3 class="card-title">EOY Reporting</h3> --}}
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
                <select name="ch_primarycor" id="ch_primarycor" class="form-control select2-bs4" style="width: 100%;" onchange="load-coordinator-list(this.value)" required >
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
                <select name="ch_primarycor" id="ch_primarycor" class="form-control select2-bs4" style="width: 100%;" required disabled>
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
            {{-- <h3 class="card-title"></h3> --}}
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
               <!-- /.form group -->
                  <div class="col-sm-6">
                  <div class="form-group">
                    <label>Last Updated By</label>
                    <input type="text" class="form-control disable-field" value="{{$chapterList[0]->last_updated_by}}" >
                  </div>
                  </div>
                  <!-- /.form group -->
                  <div class="col-sm-6">
                  <div class="form-group">
                    <label>Last Updated Date</label>
                    <input type="text" class="form-control disable-field" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy hh:mm:ss"
                        data-mask value="{{ \Carbon\Carbon::parse($chapterList[0]->last_updated_date)->format('m/d/Y H:i:s') }}" >
                  </div>
                  </div>
              </div>
              </div>

        </div>
    </div>

            <!-- /.box-body -->
            <div class="card-body text-center">
              <button type="submit" class="btn bg-gradient-primary" onclick="return PreSaveValidate()" ><i class="fas fa-save"></i>&nbsp; Save</button>
              <a href="mailto:{{ $emailListChap }}?cc={{ $emailListCoord }}&subject=MOMS Club of {{ $chapterList[0]->name }}" class="btn bg-gradient-primary"><i class="fas fa-envelope"></i>&nbsp; E-mail Board</a>
            </form>
            <button type="button" class="btn bg-gradient-primary" onclick="ConfirmCancel(this);" ><i class="fas fa-undo"></i>&nbsp; Reset Data</button>
              <a href="{{ route('chapters.chaplist') }}" class="btn bg-gradient-primary"><i class="fa fa-reply fa-fw" ></i>&nbsp; Back</a>

                <br><br>
            @if($assistConferenceCoordinatorCondition)
            <button type="button" class="btn bg-gradient-primary" onclick="return UpdateEIN()"><i class="fas fa-university"  ></i>&nbsp; Update EIN</button>
            @if(empty($chapterList[0]->ein_letter_path))
                {{-- <button type="button" class="btn bg-gradient-primary" data-toggle="modal" data-target="#modal-ein"><i class="fas fa-upload" ></i>&nbsp; Upload EIN Letter</button> --}}
                <button type="button" class="btn bg-gradient-primary" onclick="showFileUploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload EIN Letter</button>
            @else
                {{-- <button type="button" class="btn bg-gradient-primary" data-toggle="modal" data-target="#modal-ein"><i class="fas fa-upload" ></i>&nbsp; Replace EIN Letter</button>                --}}
                <button type="button" class="btn bg-gradient-primary" onclick="showFileUploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace EIN Letter</button>
            @endif
            @endif
            <button type="button" class="btn bg-gradient-primary" onclick="showEmailChapterModal()"><i class="fas fa-envelope"  ></i>&nbsp; Pre-Set Emails for Chapter</button>

            @if($regionalCoordinatorCondition)
              {{-- <button type="button" class="btn bg-gradient-primary" data-toggle="modal" data-target="#modal-disband"><i class="fas fa-ban"  ></i>&nbsp; Disband Chapter</button> --}}
              <button type="button" class="btn bg-gradient-primary" onclick="showDisbandChapterModal()"><i class="fas fa-ban"  ></i>&nbsp; Disband Chapter</button>
            @endif
              </div>


            <!-- /.box-body -->

          </div>
          <!-- /.box -->
        </div>
      </div>

    </section>

    @endsection

  @section('customscript')
  <script>
    //Disable fields
    $(document).ready(function () {
        // Disable fiels for all users with class
        $('.disable-field').prop('disabled', true);
        // Disable fiels for all uers below RC with class
        if (!($RegionalCoordinatorCondition)) {
            $('.disable-RCCondition').prop('disabled', true);
        }
    });

    // Disable Web Link Status option 0
    document.getElementById('option0').disabled = true;

    //If Chapter Name Change Warning
    function PreviousNameReminder(){
        customWarningAlert("If you are changing the chapter name, please be sure to note the old name in the 'Previously Known As' field.");
        $('#ch_preknown').focus();
    }

    function showFileUploadModal() {
        Swal.fire({
            title: 'Upload EIN Letter',
            html: `
                <form id="uploadEINForm" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name='file' required>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Upload',
            cancelButtonText: 'Close',
            preConfirm: () => {
                var formData = new FormData(document.getElementById('uploadEINForm'));

                // Show the processing Swal before starting the upload
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we upload your file.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();

                        // Perform the AJAX request
                        $.ajax({
                            url: '{{ url('/files/storeEIN/'. $id) }}',
                            type: 'POST',
                            data: formData,
                            contentType: false,  // Required for file uploads
                            processData: false,  // Required for file uploads
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'File uploaded successfully!',
                                    icon: 'success',
                                    showConfirmButton: false,  // Automatically close without "OK" button
                                    timer: 1500
                                }).then(() => {
                                    location.reload(); // Reload the page to reflect changes
                                });
                            },
                            error: function(jqXHR, exception) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong, please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });

                // Prevent further action until the request finishes
                return false;
            },
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            }
        });
    }

    function showDisbandChapterModal() {
        Swal.fire({
            title: 'Chapter Disband Reason',
            html: `
                <p>Marking a chapter as disbanded will remove the logins for all board members and remove the chapter. Please enter the reason for disbanding and press OK.</p>
                <div style="display: flex; align-items: center; ">
                    <input type="text" id="disband_reason" name="disband_reason" class="swal2-input" placeholder ="Enter Reason" required style="width: 100%;">
                </div>
                <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chapterList[0]->id }}">
                <br>
                <div class="custom-control custom-switch">
                    <input type="checkbox" id="disband_letter" class="custom-control-input">
                    <label class="custom-control-label" for="disband_letter">Send Standard Disband Letter to Chapter</label>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            preConfirm: () => {
                const disbandReason = Swal.getPopup().querySelector('#disband_reason').value;
                const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
                const disbandLetter = Swal.getPopup().querySelector('#disband_letter').checked;

                if (!disbandReason) {
                    Swal.showValidationMessage('Please enter the reason for disbanding.');
                    return false;
                }

                return {
                    disband_reason: disbandReason,
                    chapter_id: chapterId,
                    disband_letter: disbandLetter
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    customClass: {
                        confirmButton: 'btn-sm btn-success',
                        cancelButton: 'btn-sm btn-danger'
                    },
                    didOpen: () => {
                        Swal.showLoading();

                        // Perform the AJAX request
                        $.ajax({
                            url: '{{ route('chapters.updatechapdisband') }}',
                            type: 'POST',
                            data: {
                                reason: data.disband_reason,
                                letter: data.disband_letter ? '1' : '0',
                                chapterid: data.chapter_id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    showConfirmButton: false,  // Automatically close without "OK" button
                                    timer: 1500,
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                }).then(() => {
                                    if (response.redirect) {
                                        window.location.href = response.redirect;
                                    }
                                });
                            },
                            error: function(jqXHR, exception) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong, Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    }

    function showEmailChapterModal() {
        Swal.fire({
            title: 'Pre-Set Letters for Chapters',
            html: `
                <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chapterList[0]->id }}">
                <br>
                <div class="custom-control custom-switch">
                    <input type="checkbox" id="welcome_letter" class="custom-control-input">
                    <label class="custom-control-label" for="welcome_letter">Send Welcome Letter to Chapter</label>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Send',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            preConfirm: () => {
                const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

                return {
                    chapter_id: chapterId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    customClass: {
                        confirmButton: 'btn-sm btn-success',
                        cancelButton: 'btn-sm btn-danger'
                    },
                    didOpen: () => {
                        Swal.showLoading();

                        // Perform the AJAX request
                        $.ajax({
                            url: '{{ url('/mail/chapterwelcome') }}/' + data.chapter_id, // Pass chapter_id in the URL
                            type: 'POST',
                            data: {
                               _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    showConfirmButton: false,  // Automatically close without "OK" button
                                    timer: 1500,
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                }).then(() => {
                                    if (response.redirect) {
                                        window.location.href = response.redirect;
                                    }
                                });
                            },
                            error: function(jqXHR, exception) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong, Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
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

$(document).ready(function() {
    // Function to load the coordinator list based on the selected value
    function loadCoordinatorList(corId) {
        if(corId != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list") }}' + '/' + corId,
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


    function ConfirmCancel(element) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Any unsaved changes will be lost. Do you want to continue?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, continue',
            cancelButtonText: 'No, stay here',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Reload the page
                location.reload();
            } else {
                // Do nothing if the user cancels
                return false;
            }
        });
    }

    function checkDuplicateEmail(email, id) {
        $.ajax({
            url: '{{ url("/checkemail/") }}' + '/' + email,
            type: "GET",
            success: function(result) {
                if (result.exists) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Email',
                        html: 'This email is already used in the system.<br>Please try a new one.',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success',
                            cancelButton: 'btn-sm btn-danger'
                        }
                    }).then(() => {
                        $("#" + id).val('');
                        $("#" + id).focus();
                    });
                }
            },
            error: function(jqXHR, exception) {
                console.error("Error checking email: ", exception);
            }
        });
    }

  // Submit validation function before submit
function PreSaveValidate() {
    var errMessage = "";

    // Get email values and trim whitespace
    var preEmail = $("#ch_pre_email").val().trim();
    var avpEmail = $("#ch_avp_email").val().trim();
    var mvpEmail = $("#ch_mvp_email").val().trim();
    var trsEmail = $("#ch_trs_email").val().trim();
    var secEmail = $("#ch_sec_email").val().trim();

    // Create an array of emails
    var emails = [preEmail, avpEmail, mvpEmail, trsEmail, secEmail];

    // Use a Set to identify duplicates
    var uniqueEmails = new Set(emails.filter(email => email !== "")); // filter out empty values

    // Check for duplicates
    if (uniqueEmails.size !== emails.filter(email => email !== "").length) {
        errMessage = "You entered the same email address for more than one board member. Please enter a unique e-mail address for each board member or mark the position as vacant.";
    }

    if (errMessage.length > 0) {
        customErrorAlert(errMessage);
        return false;
    }

    return true;
}


    function updatePassword(userid){
        var new_password = "TempPass4You";

        $.ajax({
            url: '{{ route('updatepassword') }}',
            type: "PUT", // Ensure method matches your route
            data: {
                user_id: userid,
                new_password: "TempPass4You", // Static password
                _token: '{{ csrf_token() }}' // CSRF token
            },
            success: function(result) {
                customSuccessAlert(result.message.replace('<br>', '\n'));
            },
            error: function(jqXHR, exception) {
                console.log(jqXHR.responseText); // Log error response
            }
        });
        return true;
    }

    function UpdateEIN() {
        var ein = document.getElementById("ch_ein").value;
        if (ein === "") {
            // Prompt for EIN if not already filled
            Swal.fire({
                title: 'Enter EIN',
                input: 'text',
                inputLabel: 'Please enter the EIN for the chapter',
                inputPlaceholder: 'Enter EIN',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                // buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("ch_ein").value = result.value;
                    // Automatically submit the form after EIN is entered
                    submitForm();
                }
            });
        } else {
            // Confirm EIN change if already filled
            Swal.fire({
                title: 'Confirm EIN Change',
                text: 'This chapter already has an assigned EIN. Are you REALLY sure you want to change it?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Change EIN',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                // buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Enter New EIN',
                        input: 'text',
                        inputLabel: 'Please enter the EIN for the chapter',
                        inputPlaceholder: 'Enter EIN',
                        showCancelButton: true,
                        confirmButtonText: 'Submit',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn-sm btn-success',
                            cancelButton: 'btn-sm btn-danger'
                        },
                        // buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById("ch_ein").value = result.value;
                            // Automatically submit the form after EIN is entered
                            submitForm();
                        }
                    });
                }
            });
        }
    }

    function submitForm() {
        document.getElementById("myForm").submit(); // Trigger form submission
    }

</script>
@endsection
