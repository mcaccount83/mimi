@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        Chapter List
       <small>Edit</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter List</li>
      </ol>
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
    <form method="POST" action='{{ route("chapters.update",$chapterList[0]->id) }}'">
    @csrf
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box card">
            <div class="box-header with-border">
              <h3 class="box-title">Chapter</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>MOMS Club of</label> <span class="field-required">*</span>
                <input type="text" name="ch_name" class="form-control my-colorpicker1" maxlength="200" required value="{{ $chapterList[0]->name }}" onchange="PreviousNameReminder()">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>State</label> <span class="field-required">*</span>
                <select id="ch_state" name="ch_state" class="form-control select2" style="width: 100%;" required >
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_state" value="{{ $chapterList[0]->state }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Country</label> <span class="field-required">*</span>
                <select id="ch_country" name="ch_country" class="form-control select2" style="width: 100%;" required >
                <option value="">Select Country</option>
                    @foreach($countryArr as $con)
                      <option value="{{$con->short_name}}" {{$chapterList[0]->country == $con->short_name  ? 'selected' : ''}}>{{$con->name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_country" value="{{ $chapterList[0]->country }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Conference</label> <span class="field-required">*</span>
                <select id="ch_conference" name="ch_conference" class="form-control select2" style="width: 100%;" required disabled>
                  <option value="">Select Conference</option>
							@foreach($confList as $con)
                      <option value="{{$con->id}}" {{$chapterList[0]->conference == $con->id  ? 'selected' : ''}} >{{$con->conference_name}} </option>
                    @endforeach
						</select>
						</div>
					</div>

              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Region</label> <span class="field-required">*</span>
                <select id="ch_region" name="ch_region" class="form-control select2" style="width: 100%;" required >
                  <option value="">Select Region</option>
                    @foreach($regionList as $rl)
                      <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}} >{{$rl->long_name}} </option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_region" value="{{ $chapterList[0]->region }}">
              </div>
              </div>
                    <!-- /.form group -->
              <div class="col-sm-2 col-xs-12">
              <div class="form-group">
                <label>EIN</label>
                <input type="text" id="ch_ein" name="ch_ein" class="form-control my-colorpicker1" value="{{ $chapterList[0]->ein }}" maxlength="10" readonly>
              </div>
              </div>
                  <!-- /.form group -->
                <div class="col-sm-2 col-xs-12">
              <div class="form-group">
                 @if(empty($chapterList[0]->ein_letter_path))
					<a class="btn btn-themeBlue margin" href=href="#" <?php echo "disabled";?>>No EIN Letter on File</a>
						    @else
						    <a class="btn btn-themeBlue margin" href="{{ $chapterList[0]->ein_letter_path }}" target="blank">View/Download EIN Letter</a>
						    @endif
                <input type="hidden" id="ch_ein_letter_path" name="ch_ein_letter_path" class="form-control my-colorpicker1" value="{{ $chapterList[0]->ein_letter_path }}" maxlength="250" readonly>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Status</label> <span class="field-required">*</span>
                <select id="ch_status" name="ch_status" class="form-control select2" style="width: 100%;" required >
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
              <div class="col-sm-8 col-xs-12">
              <div class="form-group">
                <label>Status Notes (not visible to board members)</label>
                <input type="text" name="ch_notes" class="form-control my-colorpicker1" maxlength="50" value="{{ $chapterList[0]->notes}}" >
              </div>
              </div>

              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Boundaries</label> <span class="field-required">*</span>
                <input type="text" name="ch_boundariesterry" class="form-control my-colorpicker1" rows="2" value="{{ $chapterList[0]->territory }}" required >
              </div>
              </div>

              </div>
            <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">President</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>First Name</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_fname" class="form-control my-colorpicker1" value="{{ $chapterList[0]->first_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_lname" class="form-control my-colorpicker1" value="{{ $chapterList[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
                            <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label> <span class="field-required">*</span>
                <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control my-colorpicker1" onblur="checkDuplicateEmail(this.value,this.id)" value="{{ $chapterList[0]->bd_email }}" maxlength="50" required >
                <input type="hidden" id="ch_pre_email_chk" value="{{ $chapterList[0]->bd_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control my-colorpicker1" value="{{ $chapterList[0]->phone }}" maxlength="12" required onkeypress="return isPhone(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label> <span class="field-required">*</span>
                <input name="ch_pre_street" class="form-control my-colorpicker1" rows="4"  maxlength="250" value="{{ $chapterList[0]->street_address }}" required>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>City</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_city" class="form-control my-colorpicker1" maxlength="50" value="{{ $chapterList[0]->city }}" required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>State</label> <span class="field-required">*</span>
                <select name="ch_pre_state" class="form-control select2" style="width: 100%;" required >
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}" {{$chapterList[0]->bd_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Zip</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_zip" class="form-control my-colorpicker1" value="{{ $chapterList[0]->zip }}" maxlength="10" required onkeypress="return isNumber(event)">
              </div>
              </div>

              <div class="box-body text-center">
                <button type="button" class="btn btn-themeBlue margin" id="{{ $chapterList[0]->user_id }}" onclick="return resetPassword(this.id)">Reset Password</button>
              </div>
              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">AVP</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 avp-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_fname != ''  ? $AVPDetails[0]->avp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 avp-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_lname != ''  ? $AVPDetails[0]->avp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 avp-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_avp_email" id="ch_avp_email" class="form-control my-colorpicker1" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" maxlength="50">
                <input type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails[0]->avp_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 avp-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_phone != ''  ? $AVPDetails[0]->avp_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)">
              </div>
              </div>
              <!-- /.form group -->
               <div class="col-sm-12 col-xs-12 avp-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_avp_street" id="ch_avp_street" class="form-control my-colorpicker1" rows="4" maxlength="250" value="{{$AVPDetails[0]->avp_addr != ''  ? $AVPDetails[0]->avp_addr : ''}}">
              </div>
              </div>
              <div class="col-sm-6 col-xs-12 avp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_city != ''  ? $AVPDetails[0]->avp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 avp-field">
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
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 avp-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_avp_zip" id="ch_avp_zip" maxlength="10" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_zip != ''  ? $AVPDetails[0]->avp_zip : ''}}" onkeypress="return isNumber(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="radio-chk">
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="AVPVacant" id="AVPVacant" class="ios-switch green bigswitch" {{$AVPDetails[0]->avp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div>
                        </label>
                    </div>
                </div>
              </div>
              <div class="box-body text-center avp-field">
                <button type="button" class="btn btn-themeBlue margin" id="{{ $AVPDetails[0]->user_id }}" onclick="return resetPassword(this.id)" {{$AVPDetails[0]->user_id == ''  ? 'disabled' : ''}} >Reset Password</button>
              </div>
              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">MVP</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 mvp-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_mvp_fname" id="ch_mvp_fname"  class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_fname != ''  ? $MVPDetails[0]->mvp_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 mvp-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_lname != ''  ? $MVPDetails[0]->mvp_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 mvp-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control my-colorpicker1" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$MVPDetails[0]->mvp_email != ''  ? $MVPDetails[0]->mvp_email : ''}}" maxlength="50">
                <input type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails[0]->mvp_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 mvp-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_phone != ''  ? $MVPDetails[0]->mvp_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)">
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12 mvp-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_mvp_street" id="ch_mvp_street" class="form-control my-colorpicker1" rows="4" value="{{$MVPDetails[0]->mvp_addr != ''  ? $MVPDetails[0]->mvp_addr : ''}}" maxlength="250">
              </div>
              </div>
              <div class="col-sm-6 col-xs-12 mvp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_city != ''  ? $MVPDetails[0]->mvp_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 mvp-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_mvp_state" id="ch_mvp_state" class="form-control select2" style="width: 100%;" >
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}" {{$MVPDetails[0]->mvp_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 mvp-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" maxlength="10" class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_zip != ''  ? $MVPDetails[0]->mvp_zip : ''}}" onkeypress="return isNumber(event)" >
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="MVPVacant" id="MVPVacant" class="ios-switch green bigswitch" {{$MVPDetails[0]->mvp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" /><div><div></div></div>
                        </label>
                    </div>
                </div>
              </div>
              <div class="box-body text-center mvp-field">
                <button type="button" class="btn btn-themeBlue margin" id="{{ $MVPDetails[0]->user_id }}" onclick="return resetPassword(this.id)" {{$MVPDetails[0]->user_id == ''  ? 'disabled' : ''}} >Reset Password</button>
              </div>
              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Treasurer</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 treas-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_fname != ''  ? $TRSDetails[0]->trs_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 treas-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_lname != ''  ? $TRSDetails[0]->trs_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
             <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 treas-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_trs_email" id="ch_trs_email" class="form-control my-colorpicker1" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$TRSDetails[0]->trs_email != ''  ? $TRSDetails[0]->trs_email : ''}}" maxlength="50">
                <input type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails[0]->trs_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 treas-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_phone != ''  ? $TRSDetails[0]->trs_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)">
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12 treas-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_trs_street" id="ch_trs_street" class="form-control my-colorpicker1" rows="4" value="{{$TRSDetails[0]->trs_addr != ''  ? $TRSDetails[0]->trs_addr : ''}}" maxlength="250" >
              </div>
              </div>
              <div class="col-sm-6 col-xs-12 treas-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_city != ''  ? $TRSDetails[0]->trs_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 treas-field">
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
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 treas-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_trs_zip" id="ch_trs_zip" maxlength="10" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_zip != ''  ? $TRSDetails[0]->trs_zip : ''}}" onkeypress="return isNumber(event)">
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="TreasVacant" id="TreasVacant" class="ios-switch green bigswitch" {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div>
                        </label>
                    </div>
                </div>
              </div>
              <div class="box-body text-center treas-field">
                <button type="button" class="btn btn-themeBlue margin" id="{{ $TRSDetails[0]->user_id }}" onclick="return resetPassword(this.id)" {{$TRSDetails[0]->user_id == ''  ? 'disabled' : ''}} >Reset Password</button>
              </div>
              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Secretary</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 sec-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_fname != ''  ? $SECDetails[0]->sec_fname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 sec-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_lname != ''  ? $SECDetails[0]->sec_lname : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
             <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 sec-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_sec_email" id="ch_sec_email" class="form-control my-colorpicker1" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}" maxlength="50" >
                <input type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails[0]->sec_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 sec-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_phone != ''  ? $SECDetails[0]->sec_phone : ''}}" maxlength="12" onkeypress="return isPhone(event)" >
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12 sec-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_sec_street" id="ch_sec_street" class="form-control my-colorpicker1" rows="4" value="{{$SECDetails[0]->sec_addr != ''  ? $SECDetails[0]->sec_addr : ''}}" maxlength="250" >
              </div>
              </div>
              <div class="col-sm-6 col-xs-12 sec-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_city != ''  ? $SECDetails[0]->sec_city : ''}}" maxlength="50" onkeypress="return isAlphanumeric(event)" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 sec-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_sec_state" id="ch_sec_state" class="form-control select2" style="width: 100%;" >
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}" {{$SECDetails[0]->sec_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12 sec-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_sec_zip" id="ch_sec_zip" maxlength="10" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_zip != ''  ? $SECDetails[0]->sec_zip : ''}}" onkeypress="return isNumber(event)" >
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" /><div><div></div></div>
                        </label>
                    </div>
                </div>
              </div>
              <div class="box-body text-center sec-field">
                <button type="button" class="btn btn-themeBlue margin" id="{{ $SECDetails[0]->user_id }}" onclick="return resetPassword(this.id)" {{$SECDetails[0]->user_id == ''  ? 'disabled' : ''}} >Reset Password</button>
              </div>
              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Information</h3>
              </div>
              <div class="box-body">

                <!-- /.form group -->
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
                                <option value="1" id="option1" {{$chapterList[0]->website_status == 1 ? 'selected' : ''}}>Website Linked</option>
                                <option value="2" id="option2" {{$chapterList[0]->website_status == 2 ? 'selected' : ''}}>Add Link Requested</option>
                                <option value="3" id="option3" {{$chapterList[0]->website_status == 3 ? 'selected' : ''}}>Do Not Link</option>
                            </select>

                            <input type="hidden" name="ch_hid_webstatus" value="{{ $chapterList[0]->website_status }}">
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
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Facebook</label>
                <input type="text" name="ch_social1" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social1}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Twitter</label>
                <input type="text" name="ch_social2" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social2}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Instagram</label>
                <input type="text" name="ch_social3" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social3}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Chapter Email Address</label>
                <input type="email" name="ch_email" class="form-control my-colorpicker1" value="{{ $chapterList[0]->email}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>PO Box</label>
                <input type="text" name="ch_pobox" class="form-control my-colorpicker1" maxlength="30" value="{{ $chapterList[0]->po_box}}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Inquiries Email Address</label> <span class="field-required">*</span>
                <input type="email" name="ch_inqemailcontact" class="form-control my-colorpicker1" value="{{ $chapterList[0]->inquiries_contact}}" maxlength="50" required >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Inquiries Notes (not visible to board members)</label>
                <input type="text" name="ch_inqnote" class="form-control my-colorpicker1" value="{{ $chapterList[0]->inquiries_note}}" maxlength="50" >
              </div>
              </div>



              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Additional Information (not visible to board members)</label>
                <textarea name="ch_addinfo" class="form-control my-colorpicker1" rows="4" >{{ $chapterList[0]->additional_info }}</textarea>
              </div>
              </div>
            </div>
             <div class="box-header with-border mrg-t-10">

              </div>
              <div class="box-body">
               <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Previously Known As</label>
                <input type="text" name="ch_preknown" id="ch_preknown" class="form-control my-colorpicker1" value="{{ $chapterList[0]->former_name}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Sistered By</label>
                <input type="text" name="ch_sistered" id="ch_sistered" class="form-control my-colorpicker1" value="{{ $chapterList[0]->sistered_by}}" maxlength="50" >
              </div>
              </div>
                <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Founded Month</label>
                <select name="ch_founddate" class="form-control select2" style="width: 100%;" disabled>
                 <option value="">Select Month</option>
                  @foreach($foundedMonth as $key=>$val)
                  <option value="{{$key}}" {{$currentMonth == $key  ? 'selected' : ''}}>{{$val}}</option>
                 @endforeach
                </select>
                <input type="hidden" name="ch_hid_founddate" value="{{$currentMonth}}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Founded Year</label>
                <input type="text" name="ch_foundyear" class="form-control my-colorpicker1" maxlength="4" value="{{ $chapterList[0]->start_year}}" disabled>
                <input type="hidden" name="ch_hid_foundyear" value="{{$chapterList[0]->start_year}}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Re-Registration Dues Paid</label>
                <input type="text" name="ch_dues" class="form-control my-colorpicker1" value="{{$chapterList[0]->dues_last_paid }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Number of Members</label>
                <input type="text" name="ch_memberpaid" class="form-control my-colorpicker1" value="{{ $chapterList[0]->members_paid_for }}" disabled>
              </div>
              </div>
                               <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Re-Registration Notes (not visible to board members)</label>
                <input type="text" name="ch_regnotes" class="form-control my-colorpicker1" maxlength="50" value="{{ $chapterList[0]->reg_notes}}" >
              </div>
              </div>

                                    <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>M2M Fund Donation Date</label>
                <input type="date" name="ch_m2mdate" class="form-control my-colorpicker1" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" value="{{$chapterList[0]->m2m_date }}" disabled>

              </div>
              </div>
                            <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>M2M Fund Donation</label>
                <input type="text" name="ch_m2mpayment" class="form-control my-colorpicker1" value="${{ $chapterList[0]->m2m_payment }}" disabled>
              </div>
              </div>
                                  <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Sustaining Chapter Donation Date</label>
                <input type="date" name="ch_sustaining_date" class="form-control my-colorpicker1" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" value="{{$chapterList[0]->sustaining_date }}" disabled>

              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Sustaining Chapter Donation</label>
                <input type="text" name="ch_sustaining_donation" class="form-control my-colorpicker1" value="${{ $chapterList[0]->sustaining_donation }}" disabled>
              </div>
              </div>
            </div>
             <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">End of Year Reporting</h3>
              </div>
              <div class="box-body">
            <br>
            <div id="reportStatusText" class="description text-center" style="color: red;">
                <h4><strong><?php echo date('Y')-1 .'-'.date('Y');?> Report Status/Links are not available at this time.</strong></h4>
            </div>
              <!-- /.form group -->
              <div class="radio-chk">
              <div class="col-sm-3 col-xs-12">
                  <div class="form-group">
                    <label><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Info Received</label>
                    <label style="display: block;"><input disabled type="checkbox" name="BoardInfo" id="BoardInfo" class="ios-switch green bigswitch" {{$chapterList[0]->new_board_submitted == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
              </div>
              </div>
              <!-- /.form group -->
              <div class="radio-chk">
                <div class="col-sm-3 col-xs-12">
                  <div class="form-group">
                    <label><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Activated</label>
                    <label style="display: block;"><input disabled type="checkbox" name="BoardActive" id="BoardActive" class="ios-switch green bigswitch" {{$chapterList[0]->new_board_active == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
                  </div>
              </div>
              <!-- /.form group -->

                    <div class="radio-chk">
                        <div class="col-sm-3 col-xs-12">
                            <div class="form-group">
                                <label><?php echo date('Y')-1 .'-'.date('Y');?> Financial Report Received</label>
                                <label style="display: block;"><input disabled type="checkbox" name="FinancialReceived" id="FinancialReceived" class="ios-switch green bigswitch" {{$chapterList[0]->financial_report_received == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                            </div>
                        </div>
                    </div>


              <!-- /.form group -->
              <div class="radio-chk">
                        <div class="col-sm-3 col-xs-12">
                            <div class="form-group">
                                <label><?php echo date('Y')-1 .'-'.date('Y');?> Financial Review Completed</label>
                                <label style="display: block;"><input disabled type="checkbox" name="ch_financial_complete" id="FinancialComplete" class="ios-switch green bigswitch" {{$chapterList[0]->financial_report_complete == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                            </div>
                        </div>
                    </div>
            <?php if($positionid !=12){?>
                <div class="box-body text-center">
                 @foreach($chapterList as $list)
                    <?php if($positionid >=5 && $positionid <=7){?>
                        <button type="button" id="ReportStatus" class="btn btn-themeBlue margin" onclick="window.location.href='{{ route('chapter.statusview', ['id' => $list->id]) }}'">
                            Update Report Status
                        </button>
                        <?php }?>
                     @if($list->new_board_active=='1')
                        <button type="button" id="BoardReportAlwaysDisabled" class="btn btn-themeBlue margin" onclick="window.location.href='{{ route('chapter.showboardinfo', ['id' => $list->id]) }}'">
                            {{ date('Y') . '-' . (date('Y') + 1) }} Board Election Report
                        </button>
                    @else
                        <button type="button" id="BoardReport" class="btn btn-themeBlue margin" onclick="window.location.href='{{ route('chapter.showboardinfo', ['id' => $list->id]) }}'">
                            {{ date('Y') . '-' . (date('Y') + 1) }} Board Election Report
                        </button>
                    @endif
                        <button type="button" id="FinancialReport" class="btn btn-themeBlue margin" onclick="window.location.href='{{ route('chapter.showfinancial', ['id' => $list->id]) }}'">
                            {{ date('Y')-1 .'-'.date('Y') }} Financial Report
                        </button>
                    @endforeach
                 </div>
            <?php }?>
            </div>

          <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">International MOMS Club Coordinators</h3>
              </div>
              <div class="box-body">

              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Primary Coordinator (Changing this value will cause the page to refresh)</label> <span class="field-required">*</span>
                <select name="ch_primarycor" id="ch_primarycor" class="form-control select2" style="width: 100%;" onchange="checkReportId(this.value)" required >
                <option value="">Select Primary Coordinator</option>
                @foreach($primaryCoordinatorList as $pcl)
                      <option value="{{$pcl->cid}}" {{$chapterList[0]->primary_coordinator_id == $pcl->cid  ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_primarycor" value="{{$chapterList[0]->primary_coordinator_id}}">
              </div>
              <div id="display_corlist"> </div>
              </div>
              </div>

              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title"></h3>
              </div>
              <div class="box-body">
               <!-- /.form group -->
                  <div class="col-sm-6 col-xs-12">
                  <div class="form-group">
                    <label>Last Updated By</label>
                    <input type="text" class="form-control my-colorpicker1" value="{{$chapterList[0]->last_updated_by}}" disabled>
                  </div>
                  </div>
                  <!-- /.form group -->
                  <div class="col-sm-6 col-xs-12">
                  <div class="form-group">
                    <label>Last Updated Date</label>
                    <input type="text" class="form-control my-colorpicker1" value="{{$chapterList[0]->last_updated_date}}" disabled>
                  </div>
                  </div>
              </div>
              </div>
        </div>

            <!-- /.box-body -->
            <div class="box-body text-center">
            <?php if (Session::get('positionid') <=7 || Session::get('positionid') == 25) {?>
              <button type="submit" class="btn btn-themeBlue margin" onclick="return PreSaveValidate()" >Save</button>
              <?php }?>
              <?php if (Session::get('positionid') <=7 || Session::get('positionid') == 25) {?>
              <a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=MOMS Club of {{ $chapterList[0]->name }}" class="btn btn-themeBlue margin">E-mail Board</a>
             <?php }?>
              <?php if ((Session::get('positionid') >=4 && Session::get('positionid') <=7) || Session::get('positionid') == 25) {?>
              <button type="button" class="btn btn-themeBlue margin" onclick="ConfirmCancel(this);" >Reset</button>
                <?php }?>
              <a href="{{ route('chapter.list') }}" class="btn btn-themeBlue margin">Back</a>
              </div>

              <div class="box-body text-center">
               <?php if ((Session::get('positionid') >=6 && Session::get('positionid') <=7) || Session::get('positionid') == 25) {?>
                <button type="button" class="btn btn-themeBlue margin" onclick="return UpdateEIN()">Update EIN</button>

                <button type="button" class="btn btn-themeBlue margin" data-toggle="modal" data-target="#modal-ein">Update EIN Letter</button>
                {{-- <button type="button" class="btn btn-themeBlue margin" onclick="return EINLetter()">Update EIN Letter</button> --}}
              <?php } ?>
              <?php if ((Session::get('positionid') >=5 && Session::get('positionid') <=7) || Session::get('positionid') == 25) {?>
              <button type="button" class="btn btn-themeBlue margin" data-toggle="modal" data-target="#modal-disband">Disband Chapter</button>
              <?php } ?>
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
                    <button type="button" class="btn" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" >Upload</button>
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
                        <p>Marking a chapter as disbanded will remove the logins for all board members and remove the chapter.  Please enter their reason for disbanding and press OK
                            <input type="text" id="disband_reason" name="disband_reason" class="form-control my-colorpicker1">
                            <input type="hidden" id="chapter_id" name="chapter_id" class="form-control my-colorpicker1" value="{{ $chapterList[0]->id}}">
                        </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="return disbandChapter()">OK</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </section>

    @endsection

  @section('customscript')
  <script>
 // Change .show/.hide to update visibility
 $(document).ready(function () {
        $('#reportStatusText').show();  /* report status text */
        $('#BoardInfo').closest('.radio-chk').hide();  /* board info received toggle */
        $('#BoardActive').closest('.radio-chk').hide();  /* board info activated toggle */
        $('#FinancialReceived').closest('.radio-chk').hide();  /* financial report received toggle */
        $('#FinancialComplete').closest('.radio-chk').hide();  /* financial report complete toggle */
        $('#ReportStatus').hide();  /* update report status button */
        $('#BoardReport').hide();  /* board report button */
        $('#BoardReportAlwaysDisabled').hide();  /* board report button */
        $('#FinancialReport').hide();  /* financial report button */

 // ALWAYS leave thise fiels set to "true" it works on conditional logic for submtited Election Report//
        $('#BoardReportAlwaysDisabled').prop('disabled', true);
});

// Disable Web Link Status option 0
    document.getElementById('option0').disabled = true;

  function disbandChapter(){
        var txt =  $("#disband_reason").val();
        var cid = $("#chapter_id").val();

        if(txt ==''){
            alert("Please enter reason for Disband");
            $("#disband_reason").focus();
            return false;
        }else{
            $.ajax({
              url: '{{ route('chapter.disband') }}',
              type: 'POST',
              data: { reason:txt,chapterid:cid, _token: '{{csrf_token()}}' },
              success: function(response) {
                    window.location.href = "{{ route('chapter.zapped') }}";

              },
              error: function (jqXHR, exception) {

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

  $(document ).ready(function() {
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

    var selectedCorId = $("select#ch_primarycor option").filter(":selected").val();
    if(selectedCorId !=""){
      $.ajax({
        url: '{{ url("/checkreportid/") }}' + '/' + selectedCorId,
            type: "GET",
            success: function(result) {
               $("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {

            }
        });
    }
     function checkReportId(val){
          $.ajax({
            url: '{{ url("/checkreportid/") }}' + '/' + val,
            type: "GET",
            success: function(result) {
               $("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {

            }
        });

      }

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
        var new_password="";
                new_password = prompt("Please enter new password for this board member.", "TempPass4You");
                if (new_password != null && userid !='') {
                    //Verify the password entered is of an allowable size
                    if(new_password.length < 7){
                        alert("Password must be at least 7 characters.  The password has not been reset.");
                        return false;
                    }
                    else{
               $.ajax({
                  url: '/chapter/resetpswd',
                  type: "POST",
                  data: { pswd:new_password,user_id:userid, _token: '{{csrf_token()}}' },
                  success: function(result) {
                      alert('Password has been reset successfully');

                  },
                  error: function (jqXHR, exception) {

                  }
              });

                return true;
                    }
                }
                else{
          //alert('Not Allowed');
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
