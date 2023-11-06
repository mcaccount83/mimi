@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        Zapped Chapter List
        <small>View</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Zapped Chapter List</li>
      </ol>
    </section>

    <!-- Main content -->
    <form method="POST" action='{{ route("chapter.updatezapped",$chapterList[0]->id) }}'>
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
                <input type="text" name="ch_pre_street" class="form-control my-colorpicker1" value="{{ $chapterList[0]->street_address }}" disabled>
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
                <input type="text" name="ch_avp_street" id="ch_avp_street" class="form-control my-colorpicker1" value="{{ $AVPDetails[0]->avp_addr }}" disabled>
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
                        <label style="display: block;"><input type="checkbox" name="AVPVacant" id="AVPVacant" class="ios-switch green bigswitch" disabled {{$AVPDetails[0]->avp_fname == ''  ? 'checked' : ''}} ><div><div></div></div>
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
                <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" maxlength="20" class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_phone }}" disabled>
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_addr}}" disabled>
              </div>
              </div>
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control my-colorpicker1" value="{{ $MVPDetails[0]->mvp_city }}" maxlength="30" disabled>
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
                <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" maxlength="20" class="form-control my-colorpicker1" value="{{$MVPDetails[0]->mvp_zip }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="MVPVacant" id="MVPVacant" class="ios-switch green bigswitch" disabled {{$MVPDetails[0]->mvp_fname == ''  ? 'checked' : ''}} ><div><div></div></div>
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
                <input type="text" name="ch_trs_phone" id="ch_trs_phone" maxlength="20" class="form-control my-colorpicker1"  value="{{ $TRSDetails[0]->trs_phone }}" disabled>
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="ch_trs_street" id="ch_trs_street" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_addr}}" disabled>
              </div>
              </div>
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control my-colorpicker1" value="{{ $TRSDetails[0]->trs_city }}" maxlength="30" disabled>
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
                <input type="text" name="ch_trs_zip" id="ch_trs_zip" maxlength="20" class="form-control my-colorpicker1" value="{{$TRSDetails[0]->trs_zip }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="TreasVacant" id="TreasVacant" class="ios-switch green bigswitch" disabled {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} ><div><div></div></div>
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
                <input type="text" name="ch_sec_phone" id="ch_sec_phone" maxlength="20" class="form-control my-colorpicker1"  value="{{ $SECDetails[0]->sec_phone }}" disabled>
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="ch_sec_street" id="ch_sec_street" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_addr}}" disabled>
              </div>
              </div>
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control my-colorpicker1" value="{{ $SECDetails[0]->sec_city }}" maxlength="30" disabled>
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
                <input type="text" name="ch_sec_zip" id="ch_sec_zip" maxlength="20" class="form-control my-colorpicker1" value="{{$SECDetails[0]->sec_zip }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" disabled {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}} ><div><div></div></div>
						</label>
                    </div>
				</div>
              </div>
              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title"></h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Additional Information</label>
                <textarea name="ch_addinfo" class="form-control my-colorpicker1" rows="4" disabled>{{ $chapterList[0]->additional_info }}</textarea>
              </div>
              </div>
			   <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Chapter Website</label>
                <input type="text" name="ch_website" class="form-control my-colorpicker1" placeholder="http://www.momsclubofchaptername.com" value="{{$chapterList[0]->website_url}}" maxlength="30" disabled>
              </div>
              </div>
              <!-- /.form group -->

              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label class="span-t mrg-10">
                   <input type="radio" name="ch_linkstatus" class="minimal" value="1" disabled {{$chapterList[0]->website_link_status == '1'  ? 'checked' : ''}}>
                  <span>Linked</span>
                </label>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label class="span-t mrg-10">
                  <input type="radio" name="ch_linkstatus" class="minimal" value="2" disabled {{$chapterList[0]->website_link_status == '2'  ? 'checked' : ''}}>
                  <span>Add Link Requested</span>
                </label>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label class="span-t mrg-10">
                  <input type="radio" name="ch_linkstatus" class="minimal" value="3" disabled {{$chapterList[0]->website_link_status == '3'  ? 'checked' : ''}}>
                 <span> Do not Link</span>
                </label>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Chapter Email Information</label>
                <input type="email" name="ch_email" class="form-control my-colorpicker1" value="{{ $chapterList[0]->email}}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email to give to moms interested in joining your chapter</label>
                <input type="email" name="ch_inqemailcontact" class="form-control my-colorpicker1" value="{{ $chapterList[0]->inquiries_contact}}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Inquiries Note</label>
                <input type="text" name="ch_inqnote" class="form-control my-colorpicker1" value="{{ $chapterList[0]->inquiries_note}}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Online Chapter Discussion Group</label>
                <input type="text" name="ch_onlinediss" class="form-control my-colorpicker1" value="{{ $chapterList[0]->egroup}}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>PO Box</label>
                <input type="text" name="ch_pobox" class="form-control my-colorpicker1" value="{{ $chapterList[0]->po_box}}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Notes</label>
                <input type="text" name="ch_notes" class="form-control my-colorpicker1" value="{{ $chapterList[0]->website_notes}}" disabled>
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
                <select name="ch_primarycor" id="ch_primarycor" class="form-control select2" style="width: 100%;" onchange="checkReportId(this.value)" required disabled>
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
               <a href="<?php echo url("/chapter/unzap/{$chapterList[0]->id}") ?>" class="btn btn-themeBlue margin">UnZap</a>
              <a href="{{ route('chapter.zapped') }}" class="btn btn-themeBlue margin">Back</a>
              </div>
              <div class="box-body text-center">
                    <button type="button" class="btn btn-themeBlue margin" onclick="return UpdateEmail()">Update President Email</button>


                          <button type="submit" class="btn btn-themeBlue margin" >Save Email Update</button>

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

function UpdateEmail(){
                var email=document.getElementById("ch_pre_email").value;
                var new_email="";

                if (email==""){
                    new_email = prompt("Please enter the new email address for the President");

                    if (new_email != null) {
                        document.getElementById("ch_pre_email").value = new_email;
                        return true;
                    }
                }
                else{
                    var result=confirm("This President already has an emails address, are you sure you want to update it?");
                    if(result){
                        new_email = prompt("Please enter the new email address for the President");
                        if (new_email != null) {
                            document.getElementById("ch_pre_email").value = new_email;
                            return true;
                        }
                    }
                    else{
                        return false;
                    }
                }
            }

  $( document ).ready(function() {

	var selectedCorId = $("select#ch_primarycor option").filter(":selected").val();
    if(selectedCorId !=""){
      $.ajax({
            url: '/mimi/checkreportid/'+selectedCorId,
            type: "GET",
            success: function(result) {
               $("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {

            }
        });
    }
	var avp = $("#ch_avp_fname").val();
    if(avp ==''){
        $("#ch_avp_fname").prop("readonly",true);
        $("#ch_avp_lname").prop("readonly",true);
        $("#ch_avp_email").prop("readonly",true);
        $("#ch_avp_street").prop("readonly",true);
        $("#ch_avp_city").prop("readonly",true);
        $("#ch_avp_zip").prop("readonly",true);
        $("#ch_avp_phone").prop("readonly",true);
        $("#ch_avp_state").prop("disabled",true);
    }
    var mvp = $("#ch_mvp_fname").val();
    if(mvp ==''){
        $("#ch_mvp_fname").prop("readonly",true);
        $("#ch_mvp_lname").prop("readonly",true);
        $("#ch_mvp_email").prop("readonly",true);
        $("#ch_mvp_street").prop("readonly",true);
        $("#ch_mvp_city").prop("readonly",true);
        $("#ch_mvp_zip").prop("readonly",true);
        $("#ch_mvp_phone").prop("readonly",true);
        $("#ch_mvp_state").prop("disabled",true);
    }
    var trs = $("#ch_trs_fname").val();
    if(trs ==''){
        $("#ch_trs_fname").prop("readonly",true);
        $("#ch_trs_lname").prop("readonly",true);
        $("#ch_trs_email").prop("readonly",true);
        $("#ch_trs_street").prop("readonly",true);
        $("#ch_trs_city").prop("readonly",true);
        $("#ch_trs_zip").prop("readonly",true);
        $("#ch_trs_phone").prop("readonly",true);
        $("#ch_trs_state").prop("disabled",true);
    }
    var sec = $("#ch_sec_fname").val();
    if(sec ==''){
        $("#ch_sec_fname").prop("readonly",true);
        $("#ch_sec_lname").prop("readonly",true);
        $("#ch_sec_email").prop("readonly",true);
        $("#ch_sec_street").prop("readonly",true);
        $("#ch_sec_city").prop("readonly",true);
        $("#ch_sec_zip").prop("readonly",true);
        $("#ch_sec_phone").prop("readonly",true);
        $("#ch_sec_state").prop("disabled",true);
    }
});


  //submit validation function
  function PreSaveValidate(){
    		var errMessage="";
       	//Ensure there are no e-mail addresses repeated
				if($("#ch_pre_email").val() == $("#ch_avp_email").val() || $("#ch_pre_email").val() == $("#ch_mvp_email").val() || $("#ch_pre_email").val() == $("#ch_trs_email").val() || $("#ch_pre_email").val() == $("#ch_sec_email").val()) {
					errMessage = "The e-mail address provided for the Chapter President was also provided for a different position.  Please enter a unique e-mail address for each board member or mark the position as vacant.";
				}


				if(errMessage.length > 0){
					alert (errMessage);
					return false;
				}

				//Okay, all validation passed, save the records to the database
				return true;
			}


</script>

@endsection
