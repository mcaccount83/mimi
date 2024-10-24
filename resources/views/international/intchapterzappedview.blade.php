@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        International Zapped Chapter List
        <small>View</small>
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">International Zapped Chapter List</li>
      </ol>
    </section>


    <!-- Main content -->
    <form method="POST" action='{{ route("chapters.store") }}'">
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
                <label>MOMS Club of</label>
                <input type="text" name="ch_name" class="form-control my-colorpicker1" disabled value="{{ $chapterList[0]->name }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>State</label>
                <select id="ch_state" name="ch_state" class="form-control select2" style="width: 100%;" disabled>
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Country</label>
                <select id="ch_country" name="ch_country" class="form-control select2" style="width: 100%;" disabled>
                <option value="">{{ $chapterList[0]->country }}</option>
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Region</label>
                <select id="ch_region" name="ch_region" class="form-control select2" style="width: 100%;" disabled>
                  <option value="">Select Region</option>
                    @foreach($regionList as $rl)
                      <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}}>{{$rl->long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>EIN</label>
                <input type="text" id="ch_ein" name="ch_ein" class="form-control my-colorpicker1" value="{{ $chapterList[0]->ein }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Status</label>
                <select id="ch_status" name="ch_status" class="form-control select2" style="width: 100%;" disabled>
                  <option value="">Select Status</option>
                  <option value="1">Operating OK</option>
                  <option value="4">On Hold Do not Refer</option>
                  <option value="5">Probation</option>
                  <option value="6">Probation Do Not Link</option>
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Boundaries</label>
                <input type="text" name="ch_boundariesterry" class="form-control my-colorpicker1" rows="4" value="{{ $chapterList[0]->territory }} "disabled>
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
                <label>First Name</label>
                <input type="text" name="ch_pre_fname" class="form-control my-colorpicker1" value="{{ $chapterList[0]->first_name }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_pre_lname" class="form-control my-colorpicker1" value="{{ $chapterList[0]->last_name }}" disabled>
              </div>
              </div>
			  <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control my-colorpicker1" value="{{ $chapterList[0]->bd_email }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_pre_phone" class="form-control my-colorpicker1" value="{{ $chapterList[0]->phone }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <textarea name="ch_pre_street" class="form-control my-colorpicker1" rows="4"  disabled>{{ $chapterList[0]->street_address }}</textarea>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_pre_city" class="form-control my-colorpicker1" value="{{ $chapterList[0]->city }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>State</label>
                <select name="ch_pre_state" class="form-control select2" style="width: 100%;" disabled>
                <option value="">{{ $chapterList[0]->bd_state }}</option>

                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_pre_zip" class="form-control my-colorpicker1" value="{{ $chapterList[0]->zip }}" disabled>
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
                <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_fname}}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_lname}}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_avp_email" id="ch_avp_email" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_email}}" disabled>
              </div>
              </div>
			  <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control my-colorpicker1" value="{{$AVPDetails[0]->avp_phone}}"disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <textarea name="ch_avp_street" id="ch_avp_street" class="form-control my-colorpicker1" rows="4"  disabled>{{ $AVPDetails[0]->avp_addr }}</textarea>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control my-colorpicker1" value="{{ $AVPDetails[0]->avp_city }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>State</label>
                <select name="ch_avp_state" id="ch_avp_state" class="form-control select2" style="width: 100%;" disabled>
                <option value="">{{ $AVPDetails[0]->avp_state }}</option>

                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control my-colorpicker1" value="{{ $AVPDetails[0]->avp_zip }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
			  <div class="radio-chk">
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="AVPVacant" id="AVPVacant" class="ios-switch green bigswitch" disabled {{$AVPDetails[0]->avp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" /><div><div></div></div>
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
                <input type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control my-colorpicker1" value="{{ $MVPDetails[0]->mvp_fname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control my-colorpicker1" value="{{ $MVPDetails[0]->mvp_lname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control my-colorpicker1" value="{{ $MVPDetails[0]->mvp_email }}" disabled>
              </div>
              </div>
			   <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_mvp_phone" id="ch_mvp_phone"  class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_phone }}" disabled>
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <textarea name="ch_mvp_street" id="ch_mvp_street" class="form-control my-colorpicker1" rows="4"  disabled>{{$MVPDetails[0]->mvp_addr}}</textarea>
              </div>
              </div>
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control my-colorpicker1" value="{{ $MVPDetails[0]->mvp_city }}"  disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>State</label>
                <select name="ch_mvp_state" id="ch_mvp_state" class="form-control select2" style="width: 100%;" disabled>
                   <option value="">{{ $MVPDetails[0]->mvp_state }}</option>
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_mvp_zip" id="ch_mvp_zip"  class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_zip }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="MVPVacant" id="MVPVacant" class="ios-switch green bigswitch" disabled {{$MVPDetails[0]->mvp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div>
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
                <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control my-colorpicker1" value="{{ $TRSDetails[0]->trs_fname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control my-colorpicker1" value="{{ $TRSDetails[0]->trs_lname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_trs_email" id="ch_trs_email" class="form-control my-colorpicker1" value="{{ $TRSDetails[0]->trs_email }}" disabled>
              </div>
              </div>
			  <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_trs_phone" id="ch_trs_phone"  class="form-control my-colorpicker1"  value="{{ $TRSDetails[0]->trs_phone }}" disabled>
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <textarea name="ch_trs_street" id="ch_trs_street" class="form-control my-colorpicker1" rows="4"  disabled>{{$TRSDetails[0]->trs_addr}}</textarea>
              </div>
              </div>
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control my-colorpicker1" value="{{ $TRSDetails[0]->trs_city }}"  disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>State</label>
                <select name="ch_trs_state" id="ch_trs_state" class="form-control select2" style="width: 100%;" disabled>
                   <option value="">{{ $TRSDetails[0]->trs_state }}</option>
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_trs_zip" id="ch_trs_zip"  class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_zip }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="TreasVacant" id="TreasVacant" class="ios-switch green bigswitch" disabled {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div>
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
                <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control my-colorpicker1" value="{{ $SECDetails[0]->sec_fname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control my-colorpicker1" value="{{ $SECDetails[0]->sec_lname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_sec_email" id="ch_sec_email" class="form-control my-colorpicker1" value="{{ $SECDetails[0]->sec_email }}" disabled>
              </div>
              </div>
			   <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_sec_phone" id="ch_sec_phone"  class="form-control my-colorpicker1"  value="{{ $SECDetails[0]->sec_phone }}" disabled>
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <textarea name="ch_sec_street" id="ch_sec_street" class="form-control my-colorpicker1" rows="4"  disabled>{{$SECDetails[0]->sec_addr}}</textarea>
              </div>
              </div>
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control my-colorpicker1" value="{{ $SECDetails[0]->sec_city }}"  disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>State</label>
                <select name="ch_sec_state" id="ch_sec_state" class="form-control select2" style="width: 100%;" disabled>
                   <option value="">{{ $SECDetails[0]->sec_state }}</option>
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_sec_zip" id="ch_sec_zip"  class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_zip }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" disabled {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)"/><div><div></div></div>
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
                      <input type="text" name="ch_website" class="form-control my-colorpicker1" placeholder="http://www.momsclubofchaptername.com" value="{{$chapterList[0]->website_url}}"  id="validate_url" onchange="is_url(); updateWebsiteStatus();">
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
                <input type="text" name="ch_onlinediss" class="form-control my-colorpicker1" value="{{ $chapterList[0]->egroup}}"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Facebook</label>
                <input type="text" name="ch_social1" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social1}}"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Twitter</label>
                <input type="text" name="ch_social2" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social2}}"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Instagram</label>
                <input type="text" name="ch_social3" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social3}}"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Chapter Email Address</label>
                <input type="email" name="ch_email" class="form-control my-colorpicker1" value="{{ $chapterList[0]->email}}"  >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>PO Box</label>
                <input type="text" name="ch_pobox" class="form-control my-colorpicker1"  value="{{ $chapterList[0]->po_box}}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Inquiries Email Address</label> <span class="field-required">*</span>
                <input type="email" name="ch_inqemailcontact" class="form-control my-colorpicker1" value="{{ $chapterList[0]->inquiries_contact}}"  required >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Inquiries Notes (not visible to board members)</label>
                <input type="text" name="ch_inqnote" class="form-control my-colorpicker1" value="{{ $chapterList[0]->inquiries_note}}"  >
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
                <h3 class="box-title"></h3>
              </div>
              <div class="box-body">
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
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Founded Year</label>
                <input type="text" name="ch_foundyear" class="form-control my-colorpicker1" value="{{ $chapterList[0]->start_year}}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Re-Registration dues last paid</label>
                <input type="text" name="ch_dues" class="form-control my-colorpicker1" value="{{$chapterList[0]->dues_last_paid }}" readonly>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last number of members registered</label>
                <input type="text" name="ch_memberpaid" class="form-control my-colorpicker1" value="{{ $chapterList[0]->members_paid_for }}" readonly>
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
                <select name="ch_primarycor" id="ch_primarycor" class="form-control select2" style="width: 100%;" required disabled>
                <option value="">Select Primary Coordinator</option>
                @foreach($primaryCoordinatorList as $pcl)
                      <option value="{{$pcl->cid}}" {{$chapterList[0]->primary_coordinator_id == $pcl->cid  ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
                    @endforeach
                </select>
              </div>
              <div id="display_corlist"> </div>
              </div>
              </div>
			   <div class="box-header with-border mrg-t-10">
                <h3 class="box-title"></h3>
              </div>
              <div class="box-body">
				<div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Zap Date</label>
                <input type="text" name="ch_memberpaid" class="form-control my-colorpicker1" value="{{$chapterList[0]->zap_date}}" disabled>
              </div>
              </div>
			   <!-- /.form group -->
				  <div class="col-sm-6 col-xs-12">
				  <div class="form-group">
					<label>Last Updated By</label>
					<input type="text" class="form-control my-colorpicker1" value="{{$chapterList[0]->last_updated_by}}" readonly>
				  </div>
				  </div>
				  <!-- /.form group -->
				  <div class="col-sm-6 col-xs-12">
				  <div class="form-group">
					<label>Last Updated Date</label>
					<input type="text" class="form-control my-colorpicker1" value="{{$chapterList[0]->last_updated_date}}" readonly>
				  </div>
				  </div>
			  </div>

              </div>
      </div>

            <!-- /.box-body -->
            <div class="box-body text-center">
              <a href="{{ route('international.intchapterzapped') }}" class="btn btn-themeBlue margin"><i class="fa fa-reply fa-fw" aria-hidden="true" ></i>&nbsp; Back</a>
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

  $( document ).ready(function() {
	var avp = $("#ch_avp_fname").val();
    var mvp = $("#ch_mvp_fname").val();
    var trs = $("#ch_trs_fname").val();
    var sec = $("#ch_sec_fname").val();
    });


</script>

@endsection
