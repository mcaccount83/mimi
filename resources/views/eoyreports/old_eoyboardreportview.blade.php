@extends('layouts.coordinator_theme')

@section('content')
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Board Election Report&nbsp;<small>(Edit)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Board Election Report</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form id="board-info" method="POST" action='{{ route("eoyreports.eoyupdateboardreport",$chapterList[0]->id) }}'>
    @csrf
    <input type="hidden" name="submit_type" id="submit_type" value="" />
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Chapter Information</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
          <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>MOMS Club of</label>
                <input type="text" name="ch_name" class="form-control" disabled value="{{ $chapterList[0]->name }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>State</label>
                <select id="ch_state" name="ch_state" class="form-control select2-sb4" style="width: 100%;" disabled>
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Boundaries listed in MIMI (used for Inquiries)</label>
                            <input type="text" name="ch_boundry" class="form-control" value="{{ $chapterList[0]->territory }}"   readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group row">
                        <label>Are your listed boundaries correct?<span class="field-required">*</span></label>
                        <div class="col-12 row">
                            <div class="form-check" style="margin-right: 20px;">
                                <input class="form-check-input" type="radio" id="BoundaryStatusYes" name="BoundaryStatus" value="0" {{ $chapterList[0]->boundary_issues === 0 ? 'checked' : '' }} onChange="ShowBoundaryError()">
                                <label class="form-check-label" for="BoundaryStatusYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="BoundaryStatusNo" name="BoundaryStatus" value="1" {{ $chapterList[0]->boundary_issues === 1 ? 'checked' : '' }} onChange="ShowBoundaryError()">
                                <label class="form-check-label" for="BoundaryStatusNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" id="divBoundaryIssue">
                            <label for="BoundaryIssue">Please indicate which part of the Boundaries not NOT match our records<span class="field-required">*</span></label>
                            <input type="text" rows="2"class="form-control" name="BoundaryIssue" id="BoundaryIssue" value="{{ $chapterList[0]->boundary_issue_notes }}" ></input>
                        </div>
                    </div>

                    <div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Email Address to Give to MOMS Interested in joining your Chapter</label>
								<input type="text" name="InquiriesContact" class="form-control" value="{{ $chapterList[0]->inquiries_contact }}"   required>
							</div>
						</div>
                    </div>
  <!-- /.form group -->
  <div class="row">
    <div class="col-6">
        <div class="form-group">
          <label>Chapter Website</label>
          <input type="text" name="ch_website" class="form-control my-colorpicker1" placeholder="http://www.momsclubofchaptername.com" value="{{$chapterList[0]->website_url}}"  id="validate_url" onchange="is_url(); updateWebsiteStatus();">
        </div>
        </div>
          <!-- /.form group -->
          <div class="col-6 ">
            <div class="form-group">
                <label>Website Link Status</label>
                <select id="ch_webstatus" name="ch_webstatus" class="form-control selectsb2" style="width: 100%;" required>
                    <option value="0" id="option0" {{$chapterList[0]->website_status == 0 ? 'selected' : ''}} {{ $chapterList[0]->website_status == 0 ? '' : 'disabled' }}>Website Not Linked</option>
                    <option value="1" id="option1" {{$chapterList[0]->website_status == 1 ? 'selected' : ''}} {{ $chapterList[0]->website_status == 1 ? '' : 'disabled' }}>Website Linked</option>
                    <option value="2" id="option2" {{$chapterList[0]->website_status == 2 ? 'selected' : ''}}>Add Link Requested</option>
                    <option value="3" id="option3" {{$chapterList[0]->website_status == 3 ? 'selected' : ''}}>Do Not Link</option>
                </select>
                <input type="hidden" name="ch_hid_webstatus" id="ch_hid_webstatus" value="{{ $chapterList[0]->website_status }}">
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
                <input type="text" name="ch_pre_fname" class="form-control" value="{{ $PREDetails[0]->pre_fname }}"  required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Last Name</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_lname" class="form-control" value="{{ $PREDetails[0]->pre_lname }}"  required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
                            <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Email</label> <span class="field-required">*</span>
                <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{ $PREDetails[0]->pre_email }}"  required>
                <input type="hidden" id="ch_pre_email_chk" value="{{ $PREDetails[0]->pre_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
                <div class="form-group">
                    <label>Phone</label><span class="field-required">*</span>
                        <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $PREDetails[0]->pre_phone }}" required>
                </div>
            </div>
              <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">
                <label>Street Address</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_street" class="form-control"  value="{{ $PREDetails[0]->pre_addr }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>City</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_city" class="form-control"  value="{{ $PREDetails[0]->pre_city }}" required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>State</label> <span class="field-required">*</span>
                <select name="ch_pre_state" class="form-control select2-sb4" style="width: 100%;" required>
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}" {{$PREDetails[0]->pre_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Zip</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_zip" class="form-control" value="{{ $PREDetails[0]->pre_zip }}"  required onkeypress="return isNumber(event)">
              </div>
              </div>
                <input type="hidden" name="presID" id="presID" value="<?php echo $PREDetails[0]->ibd_id; ?>" />
              </div>
            </div>

            <div class="card-header d-flex align-items-center">
                <h4 class="card-title mb-0">AVP</h4>
                <div class="custom-control custom-switch ml-2">
                    <input type="checkbox" name="AVPVacant" id="AVPVacant" class="custom-control-input" {{$AVPDetails[0]->avp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="AVPVacant">Vacant</label>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                <div class="col-sm-12 avp-field">&nbsp;</div>
      <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" value="{{$AVPDetails[0]->avp_fname != ''  ? $AVPDetails[0]->avp_fname : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" value="{{$AVPDetails[0]->avp_lname != ''  ? $AVPDetails[0]->avp_lname : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_avp_email" id="ch_avp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails[0]->avp_email != ''  ? $AVPDetails[0]->avp_email : ''}}" >
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
               <div class="col-sm-12 avp-field">
              <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="ch_avp_street" id="ch_avp_street" class="form-control" rows="4"  value="{{$AVPDetails[0]->avp_addr != ''  ? $AVPDetails[0]->avp_addr : ''}}">
              </div>
              </div>
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" value="{{$AVPDetails[0]->avp_city != ''  ? $AVPDetails[0]->avp_city : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_avp_state" id="ch_avp_state" class="form-control select2-sb4" style="width: 100%;" >
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}" {{$AVPDetails[0]->avp_state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_avp_zip" id="ch_avp_zip"  class="form-control" value="{{$AVPDetails[0]->avp_zip != ''  ? $AVPDetails[0]->avp_zip : ''}}" onkeypress="return isNumber(event)">
              </div>
              </div>
             <input type="hidden" name="avpID" id="avpID" value="<?php echo $AVPDetails[0]->ibd_id; ?>" />
            </div>
              </div>

              <div class="card-header d-flex align-items-center">
                <h4 class="card-title mb-0">MVP</h4>
                <div class="custom-control custom-switch ml-2">
                    <input type="checkbox" name="MVPVacant" id="MVPVacant" class="custom-control-input" {{$MVPDetails[0]->mvp_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="MVPVacant">Vacant</label>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
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
                <input type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control" rows="4"  value="{{$MVPDetails[0]->mvp_addr != ''  ? $MVPDetails[0]->mvp_addr : ''}}">
              </div>
              </div>
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" value="{{$MVPDetails[0]->mvp_city != ''  ? $MVPDetails[0]->mvp_city : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_mvp_state" id="ch_mvp_state" class="form-control select2-sb4" style="width: 100%;">
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
                <input type="text" name="ch_mvp_zip" id="ch_mvp_zip"  class="form-control" value="{{$MVPDetails[0]->mvp_zip != ''  ? $MVPDetails[0]->mvp_zip : ''}}" onkeypress="return isNumber(event)">
              </div>
              </div>
             <input type="hidden" name="mvpID" id="mvpID" value="<?php echo $MVPDetails[0]->ibd_id; ?>" />
              </div>
            </div>

            <div class="card-header d-flex align-items-center">
                <h4 class="card-title mb-0">Treasurer</h4>
                <div class="custom-control custom-switch ml-2">
                    <input type="checkbox" name="TreasVacant" id="TreasVacant" class="custom-control-input" {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="TreasVacant">Vacant</label>
                </div>
            </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                <div class="col-sm-12 treas-field">&nbsp;</div>
          <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control" value="{{$TRSDetails[0]->trs_fname != ''  ? $TRSDetails[0]->trs_fname : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" value="{{$TRSDetails[0]->trs_lname != ''  ? $TRSDetails[0]->trs_lname : ''}}"  onkeypress="return isAlphanumeric(event)">
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
                <input type="text" name="ch_trs_street" id="ch_trs_street" class="form-control" rows="4"  valie="{{$TRSDetails[0]->trs_addr != ''  ? $TRSDetails[0]->trs_addr : ''}}">
              </div>
              </div>
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" value="{{$TRSDetails[0]->trs_city != ''  ? $TRSDetails[0]->trs_city : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_trs_state" id="ch_trs_state" class="form-control select2-sb4" style="width: 100%;">
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
                <input type="text" name="ch_trs_zip" id="ch_trs_zip"  class="form-control" value="{{$TRSDetails[0]->trs_zip != ''  ? $TRSDetails[0]->trs_zip : ''}}" onkeypress="return isNumber(event)">
              </div>
              </div>
             <input type="hidden" name="trsID" id="trsID" value="<?php echo $TRSDetails[0]->ibd_id; ?>" />
            </div>
              </div>

              <div class="card-header d-flex align-items-center">
                <h4 class="card-title mb-0">Secretary</h4>
                <div class="custom-control custom-switch ml-2">
                    <input type="checkbox" name="SecVacant" id="SecVacant" class="custom-control-input" {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}} onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="SecVacant">Vacant</label>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
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
                <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" value="{{$SECDetails[0]->sec_lname != ''  ? $SECDetails[0]->sec_lname : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
             <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_sec_email" id="ch_sec_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails[0]->sec_email != ''  ? $SECDetails[0]->sec_email : ''}}" >
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
                <input type="text" name="ch_sec_street" id="ch_sec_street" class="form-control" rows="4"  value="{{$SECDetails[0]->sec_addr != ''  ? $SECDetails[0]->sec_addr : ''}}">
              </div>
              </div>
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" value="{{$SECDetails[0]->sec_city != ''  ? $SECDetails[0]->sec_city : ''}}"  onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_sec_state" id="ch_sec_state" class="form-control select2-sb4" style="width: 100%;">
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
                <input type="text" name="ch_sec_zip" id="ch_sec_zip"  class="form-control" value="{{$SECDetails[0]->sec_zip != ''  ? $SECDetails[0]->sec_zip : ''}}" onkeypress="return isNumber(event)">
              </div>
              </div>
                         <input type="hidden" name="secID" id="secID" value="<?php echo $SECDetails[0]->ibd_id; ?>" />
              </div>
            </div>

            <!-- /.box-body -->
            <div class="card-body text-center">
                @if ($chapterList[0]->new_board_submitted != '1' )
                    <button type="submit" class="btn bg-gradient-primary" onclick="return PreSaveValidate(true)"><i class="fas fa-save" ></i>&nbsp; Submit</button>
                    @else
                    <button type="submit" class="btn bg-gradient-primary" onclick="return PreSaveValidate(true)"><i class="fas fa-save" ></i>&nbsp; Save</button>
                    @endif

              <button type="button" class="btn bg-gradient-primary" onclick="return PreSaveValidate(false)" ><i class="fas fa-user-plus" ></i>&nbsp; Activate Board</button>
              <a href="{{ route('eoyreports.eoyboardreport') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
            </div>

        </div>
    </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    </form>
    @endsection

  @section('customscript')
  <script>
    /* Disables Web Link Status options 0 and 1 */
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

//Boundary Visibility
ShowBoundaryError();

function ShowBoundaryError() {
    var selectedValue = document.querySelector('input[name="BoundaryStatus"]:checked').value;

    if (selectedValue == "1") {
        $('#BoundaryIssue').addClass('tx-cls');
        document.getElementById("divBoundaryIssue").style.display = 'block';
    } else {
        $('#BoundaryIssue').removeClass('tx-cls');
        document.getElementById("divBoundaryIssue").style.display = 'none';
    }
}

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

    $( document ).ready(function() {
    var check = <?php echo "\"" . $chapterList[0]->boundary_issues . "\""; ?>;
  });

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

  //submit validation function
  function PreSaveValidate(show_submit_message){
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
          if(show_submit_message){
                    //Okay, all validation passed, save the records to the database
                    alert ("Thank you for submitting the board information for this chapter.  The new board will not be able to login until the new board has been activated.");
                }
                else{
                    $("#submit_type").val('activate_board');
                    var result=confirm("Are you sure want to Activate Boards?");
                    if(result)
                        $("#board-info").submit();
                    else
                        return false;
                }

            return true;
    }

</script>
@endsection



