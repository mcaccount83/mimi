@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Zapped Chapter Details&nbsp;<small>(View)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Zapped Chapter Details</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>


    <!-- Main content -->
    <form method="POST" action='{{ route("chapters.updatechapzapped",$chapterList[0]->id) }}'>
        @csrf
        <section class="content">
            <div class="container-fluid">
          <div class="row">
		<div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Chapter</h3>
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
                <select id="ch_state" name="ch_state" class="form-control selecct2-bs4" style="width: 100%;" disabled>
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Country</label>
                <select id="ch_country" name="ch_country" class="form-control selecct2-bs4" style="width: 100%;" disabled>
                <option value="">{{ $chapterList[0]->country }}</option>
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Region</label>
                <select id="ch_region" name="ch_region" class="form-control selecct2-bs4" style="width: 100%;" disabled>
                  <option value="">Select Region</option>
                    @foreach($regionList as $rl)
                      <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}}>{{$rl->long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>EIN</label>
                <input type="text" id="ch_ein" name="ch_ein" class="form-control" value="{{ $chapterList[0]->ein }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Status</label>
                <select id="ch_status" name="ch_status" class="form-control selecct2-bs4" style="width: 100%;" disabled>
                  <option value="">Select Status</option>
                  <option value="1">Operating OK</option>
                  <option value="4">On Hold Do not Refer</option>
                  <option value="5">Probation</option>
                  <option value="6">Probation Do Not Link</option>
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">
                <label>Boundaries</label>
                <input type="text" name="ch_boundariesterry" class="form-control" rows="4" value="{{ $chapterList[0]->territory }} "disabled>
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
                <label>First Name</label>
                <input type="text" name="ch_pre_fname" class="form-control" value="{{ $chapterList[0]->first_name }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_pre_lname" class="form-control" value="{{ $chapterList[0]->last_name }}" disabled>
              </div>
              </div>
			  <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control" value="{{ $chapterList[0]->bd_email }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $chapterList[0]->phone }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="ch_pre_street" class="form-control" value="{{ $chapterList[0]->street_address }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_pre_city" class="form-control" value="{{ $chapterList[0]->city }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>State</label>
                <select name="ch_pre_state" class="form-control selecct2-bs4" style="width: 100%;" disabled>
                <option value="">{{ $chapterList[0]->bd_state }}</option>

                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_pre_zip" class="form-control" value="{{ $chapterList[0]->zip }}" disabled>
              </div>
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
                    <input type="checkbox" name="AVPVacant" id="AVPVacant" class="custom-control-input" {{$AVPDetails[0]->avp_fname == ''  ? 'checked' : ''}} />
                    <label class="custom-control-label" for="AVPVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 avp-field">&nbsp;</div>
                  <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" value="{{$AVPDetails[0]->avp_fname}}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" value="{{$AVPDetails[0]->avp_lname}}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_avp_email" id="ch_avp_email" class="form-control" value="{{$AVPDetails[0]->avp_email}}" disabled>
              </div>
              </div>
			  <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$AVPDetails[0]->avp_phone}}"disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-12 avp-field">
              <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="ch_avp_street" id="ch_avp_street" class="form-control" value="{{ $AVPDetails[0]->avp_addr }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" value="{{ $AVPDetails[0]->avp_city }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_avp_state" id="ch_avp_state" class="form-control selecct2-bs4" style="width: 100%;" disabled>
                <option value="">{{ $AVPDetails[0]->avp_state }}</option>

                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" value="{{ $AVPDetails[0]->avp_zip }}" disabled>
              </div>
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
                    <input type="checkbox" name="MVPVacant" id="MVPVacant" class="custom-control-input" {{$MVPDetails[0]->mvp_fname == ''  ? 'checked' : ''}} />
                    <label class="custom-control-label" for="MVPVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 mvp-field">&nbsp;</div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control" value="{{ $MVPDetails[0]->mvp_fname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" value="{{ $MVPDetails[0]->mvp_lname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control" value="{{ $MVPDetails[0]->mvp_email }}" disabled>
              </div>
              </div>
			   <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" maxlength="20" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$MVPDetails[0]->mvp_phone }}" disabled>
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 mvp-field">
              <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control" value="{{$MVPDetails[0]->mvp_addr}}" disabled>
              </div>
              </div>
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" value="{{ $MVPDetails[0]->mvp_city }}" maxlength="30" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_mvp_state" id="ch_mvp_state" class="form-control selecct2-bs4" style="width: 100%;" disabled>
                   <option value="">{{ $MVPDetails[0]->mvp_state }}</option>
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" maxlength="20" class="form-control" value="{{$MVPDetails[0]->mvp_zip }}" disabled>
              </div>
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
                    <input type="checkbox" name="TreasVacant" id="TreasVacant" class="custom-control-input" {{$TRSDetails[0]->trs_fname == ''  ? 'checked' : ''}} />
                    <label class="custom-control-label" for="TreasVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 treas-field">&nbsp;</div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control" value="{{ $TRSDetails[0]->trs_fname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" value="{{ $TRSDetails[0]->trs_lname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_trs_email" id="ch_trs_email" class="form-control" value="{{ $TRSDetails[0]->trs_email }}" disabled>
              </div>
              </div>
			  <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_trs_phone" id="ch_trs_phone" maxlength="20" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $TRSDetails[0]->trs_phone }}" disabled>
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 treas-field">
              <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="ch_trs_street" id="ch_trs_street" class="form-control" value="{{$TRSDetails[0]->trs_addr}}" disabled>
              </div>
              </div>
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" value="{{ $TRSDetails[0]->trs_city }}" maxlength="30" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_trs_state" id="ch_trs_state" class="form-control selecct2-bs4" style="width: 100%;" disabled>
                   <option value="">{{ $TRSDetails[0]->trs_state }}</option>
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_trs_zip" id="ch_trs_zip" maxlength="20" class="form-control" value="{{$TRSDetails[0]->trs_zip }}" disabled>
              </div>
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
                    <input type="checkbox" name="SecVacant" id="SecVacant" class="custom-control-input" {{$SECDetails[0]->sec_fname == ''  ? 'checked' : ''}} />
                    <label class="custom-control-label" for="SecVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 sec-field">&nbsp;</div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" value="{{ $SECDetails[0]->sec_fname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" value="{{ $SECDetails[0]->sec_lname }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_sec_email" id="ch_sec_email" class="form-control" value="{{ $SECDetails[0]->sec_email }}" disabled>
              </div>
              </div>
			   <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_sec_phone" id="ch_sec_phone" maxlength="20" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $SECDetails[0]->sec_phone }}" disabled>
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 sec-field">
              <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="ch_sec_street" id="ch_sec_street" class="form-control" value="{{$SECDetails[0]->sec_addr}}" disabled>
              </div>
              </div>
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" value="{{ $SECDetails[0]->sec_city }}" maxlength="30" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_sec_state" id="ch_sec_state" class="form-control selecct2-bs4" style="width: 100%;" disabled>
                   <option value="">{{ $SECDetails[0]->sec_state }}</option>
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_sec_zip" id="ch_sec_zip" maxlength="20" class="form-control" value="{{$SECDetails[0]->sec_zip }}" disabled>
              </div>
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
                          <input type="text" name="ch_website" class="form-control" placeholder="http://www.momsclubofchaptername.com" value="{{$chapterList[0]->website_url}}" maxlength="50" id="validate_url" >
                        </div>
                        </div>
                          <!-- /.form group -->
                          <div class="col-sm-6">
                            <div class="form-group">
                                <label>Website Link Status</label> <span class="field-required">*</span>
                                <select id="ch_webstatus" name="ch_webstatus" class="form-control selecct2-bs4" style="width: 100%;" required>
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
                    <h3 class="card-title">&nbsp;</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Founded Month</label>
                <select name="ch_founddate" class="form-control selecct2-bs4" style="width: 100%;" disabled>
                 <option value="">Select Month</option>
                  @foreach($foundedMonth as $key=>$val)
                  <option value="{{$key}}" {{$currentMonth == $key  ? 'selected' : ''}}>{{$val}}</option>
                 @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Founded Year</label>
                <input type="text" name="ch_foundyear" class="form-control" value="{{ $chapterList[0]->start_year}}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Re-Registration dues last paid</label>
                <input type="date" name="ch_dues" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{$chapterList[0]->dues_last_paid }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Last number of members registered</label>
                <input type="text" name="ch_memberpaid" class="form-control" value="{{ $chapterList[0]->members_paid_for }}" readonly>
              </div>
              </div>
              </div>
            </div>

                <div class="card-header">
                    <h3 class="card-title">International MOMS Club Coordiantors</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 float-left">
                            <input  type="hidden" id="pcid" value="{{ $chapterList[0]->primary_coordinator_id}}">
                            <div id="display_corlist">
                            </div>
                        </div>

              </div>
            </div>

                <div class="card-header">
                    <h3 class="card-title">&nbsp;</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                <!-- /.form group -->
				<div class="col-sm-12">
              <div class="form-group">
                <label>Zap Date</label>
                <input type="text" name="ch_memberpaid" class="form-control" value="{{$chapterList[0]->zap_date}}" disabled>
              </div>
              </div>
			   <!-- /.form group -->
				  <div class="col-sm-6">
				  <div class="form-group">
					<label>Last Updated By</label>
					<input type="text" class="form-control" value="{{$chapterList[0]->last_updated_by}}" readonly>
				  </div>
				  </div>
				  <!-- /.form group -->
				  <div class="col-sm-6">
				  <div class="form-group">
					<label>Last Updated Date</label>
					<input type="text" class="form-control" value="{{$chapterList[0]->last_updated_date}}" readonly>
				  </div>
				  </div>
			  </div>

              </div>

            <!-- /.box-body -->
            <div class="card-body text-center">
                @if ($regionalCoordinatorCondition)
               <a href="<?php echo url("/chapter/unzap/{$chapterList[0]->id}") ?>" class="btn bg-gradient-primary"><i class="fas fa-undo"  ></i>&nbsp; UnZap</a>
               @endif
              <a href="{{ route('chapters.chapzapped') }}" class="btn bg-gradient-primary"><i class="fa fa-reply" ></i>&nbsp; Back</a>
              </div>
              <div class="card-body text-center">
                @if ($regionalCoordinatorCondition)
                    <button type="button" class="btn bg-gradient-primary" onclick="return UpdateEmail()"><i class="fas fa-envelope" ></i>&nbsp; Update President Email</button>
                    <button type="submit" class="btn bg-gradient-primary" ><i class="fas fa-save"></i>&nbsp; Save Email Change</button>
                    @endif

            <!-- /.box-body -->

          </div>
          <!-- /.box -->
        </div>
      </div>
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
	var avp = $("#ch_avp_fname").val();
    var mvp = $("#ch_mvp_fname").val();
    var trs = $("#ch_trs_fname").val();
    var sec = $("#ch_sec_fname").val();
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
