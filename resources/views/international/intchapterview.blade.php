@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        International Chapter List
       <small>View</small>
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">International Chapter List</li>
      </ol>
    </section>

    <!-- Main content -->
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
                <input type="text" name="ch_name" class="form-control my-colorpicker1" maxlength="200" required value="{{ $chapterList[0]->name }}" >
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
                <input type="text" name="ch_pre_fname" class="form-control my-colorpicker1" value="{{ $chapterList[0]->first_name }}" maxlength="50" required >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_lname" class="form-control my-colorpicker1" value="{{ $chapterList[0]->last_name }}" maxlength="50" required >
              </div>
              </div>
                            <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label> <span class="field-required">*</span>
                <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control my-colorpicker1"  value="{{ $chapterList[0]->bd_email }}" maxlength="50" required >
                <input type="hidden" id="ch_pre_email_chk" value="{{ $chapterList[0]->bd_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control my-colorpicker1" value="{{ $chapterList[0]->phone }}" maxlength="12" required >
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
                <input type="text" name="ch_pre_city" class="form-control my-colorpicker1" maxlength="50" value="{{ $chapterList[0]->city }}" required >
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
                <input type="text" name="ch_pre_zip" class="form-control my-colorpicker1" value="{{ $chapterList[0]->zip }}" maxlength="10" required >
              </div>
              </div>

              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">AVP</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_fname != ''  ? $AVPDetails[0]->avp_fname : ''}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_lname != ''  ? $AVPDetails[0]->avp_lname : ''}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_avp_email" id="ch_avp_email" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" maxlength="50">
                <input type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails[0]->avp_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_phone != ''  ? $AVPDetails[0]->avp_phone : ''}}" maxlength="12" required >
              </div>
              </div>
              <!-- /.form group -->
               <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_avp_street" id="ch_avp_street" class="form-control my-colorpicker1" rows="4" maxlength="250" value="{{$AVPDetails[0]->avp_addr != ''  ? $AVPDetails[0]->avp_addr : ''}}">
              </div>
              </div>
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_city != ''  ? $AVPDetails[0]->avp_city : ''}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
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
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_avp_zip" id="ch_avp_zip" maxlength="10" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_zip != ''  ? $AVPDetails[0]->avp_zip : ''}}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="radio-chk">
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="AVPVacant" id="AVPVacant" class="ios-switch green bigswitch" {{$AVPDetails[0]->avp_fname == ''  ? 'checked' : ''}}/><div><div></div></div>
                        </label>
                    </div>
                </div>
              </div>

              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">MVP</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_mvp_fname" id="ch_mvp_fname"  class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_fname != ''  ? $MVPDetails[0]->mvp_fname : ''}}" maxlength="50">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_lname != ''  ? $MVPDetails[0]->mvp_lname : ''}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control my-colorpicker1"  value="{{$MVPDetails[0]->mvp_email != ''  ? $MVPDetails[0]->mvp_email : ''}}" maxlength="50">
                <input type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails[0]->mvp_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_phone != ''  ? $MVPDetails[0]->mvp_phone : ''}}" maxlength="12" required >
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_mvp_street" id="ch_mvp_street" class="form-control my-colorpicker1" rows="4" value="{{$MVPDetails[0]->mvp_addr != ''  ? $MVPDetails[0]->mvp_addr : ''}}" maxlength="250">
              </div>
              </div>
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_city != ''  ? $MVPDetails[0]->mvp_city : ''}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
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
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" maxlength="10" class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_zip != ''  ? $MVPDetails[0]->mvp_zip : ''}}"  >
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="MVPVacant" id="MVPVacant" class="ios-switch green bigswitch" {{$MVPDetails[0]->mvp_fname == ''  ? 'checked' : ''}} ><div><div></div></div>
                        </label>
                    </div>
                </div>
              </div>

              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Treasurer</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_fname != ''  ? $TRSDetails[0]->trs_fname : ''}}" maxlength="50"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_lname != ''  ? $TRSDetails[0]->trs_lname : ''}}" maxlength="50"  >
              </div>
              </div>
             <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_trs_email" id="ch_trs_email" class="form-control my-colorpicker1"  value="{{$TRSDetails[0]->trs_email != ''  ? $TRSDetails[0]->trs_email : ''}}" maxlength="50">
                <input type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails[0]->trs_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_phone != ''  ? $TRSDetails[0]->trs_phone : ''}}" maxlength="12" required >
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_trs_street" id="ch_trs_street" class="form-control my-colorpicker1" rows="4" value="{{$TRSDetails[0]->trs_addr != ''  ? $TRSDetails[0]->trs_addr : ''}}" maxlength="250" >
              </div>
              </div>
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_city != ''  ? $TRSDetails[0]->trs_city : ''}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
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
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_trs_zip" id="ch_trs_zip" maxlength="10" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_zip != ''  ? $TRSDetails[0]->trs_zip : ''}}" >
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="TreasVacant" id="TreasVacant" class="ios-switch green bigswitch" {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} /><div><div></div></div>
                        </label>
                    </div>
                </div>
              </div>

              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Secretary</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_fname != ''  ? $SECDetails[0]->sec_fname : ''}}" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_lname != ''  ? $SECDetails[0]->sec_lname : ''}}" maxlength="50"  >
              </div>
              </div>
             <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_sec_email" id="ch_sec_email" class="form-control my-colorpicker1"  value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}" maxlength="50" >
                <input type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails[0]->sec_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_phone != ''  ? $SECDetails[0]->sec_phone : ''}}" maxlength="12" required  >
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_sec_street" id="ch_sec_street" class="form-control my-colorpicker1" rows="4" value="{{$SECDetails[0]->sec_addr != ''  ? $SECDetails[0]->sec_addr : ''}}" maxlength="250" >
              </div>
              </div>
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_city != ''  ? $SECDetails[0]->sec_city : ''}}" maxlength="50"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
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
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_sec_zip" id="ch_sec_zip" maxlength="10" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_zip != ''  ? $SECDetails[0]->sec_zip : ''}}"  >
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}}  /><div><div></div></div>
                        </label>
                    </div>
                </div>
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
                          <input type="text" name="ch_website" class="form-control my-colorpicker1" placeholder="http://www.momsclubofchaptername.com" value="{{$chapterList[0]->website_url}}" maxlength="50" id="validate_url" >
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
                <h3 class="box-title">International MOMS Club Coordinators</h3>
              </div>
              <div class="box-body">

              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Primary Coordinator (Changing this value will cause the page to refresh)</label> <span class="field-required">*</span>
                <select name="ch_primarycor" id="ch_primarycor" class="form-control select2" style="width: 100%;" required >
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

              <a href="{{ route('international.intchapter') }}" class="btn btn-themeBlue margin"><i class="fa fa-reply fa-fw" aria-hidden="true" ></i>&nbsp; Back</a>
              </div>

            <!-- /.box-body -->

          </div>
          <!-- /.box -->
        </div>
      </div>

    </section>
    </form>
    @endsection

  @section('customscript')
  <script>
  // Disable fields and buttons
    $(document).ready(function () {
            $('input, select, textarea').prop('disabled', true);
    });


  $(document ).ready(function() {
       var avp = $("#ch_avp_fname").val();
    var mvp = $("#ch_mvp_fname").val();
    var trs = $("#ch_trs_fname").val();
    var sec = $("#ch_sec_fname").val();




  });

</script>
@endsection
