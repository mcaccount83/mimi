@extends('layouts.coordinator_theme')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Details&nbsp;<small>(Create)</small></h1>
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
    <form method="POST" id="create_form" action="{{ route('chapters.updatechapnew') }}" autocomplete="off">
        @csrf

    <section class="content">
        <div class="container-fluid">

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
                <div class="col-sm-4">
              <div class="form-group">
                <label>MOMS Club of</label> <span class="field-required">*</span>
                <input type="text" name="ch_name" class="form-control" maxlength="200" required>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>State</label> <span class="field-required">*</span>
                <select id="ch_state" name="ch_state" class="form-control select2-bs4" style="width: 100%;" required>
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}">{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Country</label> <span class="field-required">*</span>
                <?php $selectedvalue = 'USA' ?>
                <select id="ch_country" name="ch_country" class="form-control select2-bs4" style="width: 100%;" required>
                <option value="">Select Country</option>
                    @foreach($countryArr as $con)
                      <option value="{{$con->short_name}}" {{ $selectedvalue == $con->short_name ? 'selected="selected"' : '' }}>{{$con->name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Region</label> <span class="field-required">*</span>
                <select id="ch_region" name="ch_region" class="form-control select2-bs4" style="width: 100%;" required>
                  <option value="">Select Region</option>
                    @foreach($regionList as $rl)
                      <option value="{{$rl->id}}">{{$rl->long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>EIN</label>
                <input type="text" id="ch_ein" name="ch_ein" class="form-control" maxlength="10">
              </div>
              </div>
                <!-- /.form group -->
                  <div class="col-sm-4">
                    <div class="form-group">
                    <label> </label>
                    <input type="text" id="" name="" class="form-control" maxlength="10" readonly>
                       </div>
                    </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Status</label> <span class="field-required">*</span>
                <select id="ch_status" name="ch_status" class="form-control select2-bs4" style="width: 100%;" required>
                  <option value="">Select Status</option>
                  <option value="1">Operating OK</option>
                  <option value="4">On Hold Do not Refer</option>
                  <option value="5">Probation</option>
                  <option value="6">Probation Do Not Refer</option>
                </select>
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-8">
                <div class="form-group">
                  <label>Status Note (not visible to board members)</label>
                  <input type="text" name="ch_notes" class="form-control" maxlength="50">
                </div>
                </div>

              <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">
                <label>Boundaries</label> <span class="field-required">*</span>
                <input type="text" name="ch_boundariesterry" class="form-control" maxlength="250" required>
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
                <input type="text" name="ch_pre_fname" class="form-control" maxlength="50" required >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Last Name</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_lname" class="form-control" maxlength="50" required >
              </div>
              </div>
			  <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Email</label> <span class="field-required">*</span>
                <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" maxlength="50" required>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Phone</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">
                <label>Street Address</label> <span class="field-required">*</span>
                <input name="ch_pre_street" class="form-control" rows="4" maxlength="250" required>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>City</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_city" class="form-control" maxlength="50" required >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>State</label> <span class="field-required">*</span>
                <select name="ch_pre_state" class="form-control select2-bs4" style="width: 100%;" required>
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}">{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>Zip</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_zip" class="form-control" maxlength="10" required >
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
                    <input type="checkbox" name="AVPVacant" id="AVPVacant" class="custom-control-input" checked onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="AVPVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 avp-field">&nbsp;</div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_avp_email" id="ch_avp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" maxlength="50">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask >
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 avp-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_avp_street" id="ch_avp_street" class="form-control" rows="4" maxlength="250">
              </div>
              </div>
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_avp_state" id="ch_avp_state" class="form-control select2-bs4" style="width: 100%;">
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}">{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 avp-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" maxlength="10" >
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
                    <input type="checkbox" name="MVPVacant" id="MVPVacant" class="custom-control-input" checked onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="MVPVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 mvp-field">&nbsp;</div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control" maxlength="50">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" maxlength="50">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask >
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 mvp-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_mvp_street" id="ch_mvp_street" class="form-control" rows="4" maxlength="250">
              </div>
              </div>
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_mvp_state" id="ch_mvp_state" class="form-control select2-bs4" style="width: 100%;">
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}">{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 mvp-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" class="form-control" maxlength="10" >
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
                    <input type="checkbox" name="TreasVacant" id="TreasVacant" class="custom-control-input" checked onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="TreasVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 treas-field">&nbsp;</div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_trs_email" id="ch_trs_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" maxlength="50">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask >
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 treas-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_trs_street" id="ch_trs_street" class="form-control" rows="4" maxlength="250">
              </div>
              </div>
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_trs_state" id="ch_trs_state" class="form-control select2-bs4" style="width: 100%;">
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}">{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 treas-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_trs_zip" id="ch_trs_zip" class="form-control" maxlength="10" >
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
                    <input type="checkbox" name="SecVacant" id="SecVacant" class="custom-control-input" checked onchange="ConfirmVacant(this.id)" />
                    <label class="custom-control-label" for="SecVacant">Vacant</label>
                </div>
                </div>
                <div class="col-sm-12 sec-field">&nbsp;</div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" maxlength="50" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_sec_email" id="ch_sec_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" maxlength="50">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask >
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12 sec-field">
              <div class="form-group">
                <label>Street Address</label>
                <input name="ch_sec_street" id="ch_sec_street" class="form-control" rows="4" maxlength="250">
              </div>
              </div>
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>City</label>
                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" maxlength="50">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>State</label>
                <select name="ch_sec_state" id="ch_sec_state" class="form-control select2-bs4" style="width: 100%;">
                <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->state_short_name}}">{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 sec-field">
              <div class="form-group">
                <label>Zip</label>
                <input type="text" name="ch_sec_zip" id="ch_sec_zip" class="form-control" maxlength="10" >
              </div>
              </div>
              </div>
            </div>

            <div class="card-header">
                <h3 class="card-title">Additional Information</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Chapter Email Address</label>
                <input type="email" name="ch_email" class="form-control" maxlength="30">
              </div>
              </div>
                <!-- /.form group -->
                <div class="col-sm-6">
                    <div class="form-group">
                      <label>PO Box</label>
                      <input type="text" name="ch_pobox" class="form-control" maxlength="30">
                    </div>
                    </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Inquiries Email Address</label> <span class="field-required">*</span>
                <input type="email" name="ch_inqemailcontact" class="form-control" maxlength="50" required>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Inquiries Notes (not visible to board members)</label>
                <input type="text" name="ch_inqnote" class="form-control" maxlength="50">
              </div>
              </div>
               <!-- /.form group -->
               <div class="col-sm-12">
                <div class="form-group">
                  <label>Additional Information (not visible to board members)</label>
                  <textarea name="ch_addinfo" class="form-control" rows="4" maxlength="250"></textarea>
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
                <label>Founded Month</label>
                <select name="ch_founddate" class="form-control select2-bs4" style="width: 100%;" required>
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
                <input type="text" name="ch_foundyear" class="form-control" maxlength="4" value={{$currentYear}} required readonly>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Re-Registration dues last paid</label>
                <input type="text" name="ch_dues" class="form-control" value="" readonly>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Last number of members registered</label>
                <input type="text" name="ch_memberpaid" class="form-control" value="" readonly>
              </div>
              </div>
              </div>

            </div>

            <div class="card-header">
                <h3 class="card-title">International MOMS Club Coordinators</h3>
            </div>
             <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
              <!-- /.form group -->
              <div class="col-sm-4">
                <div class="form-group">
                <label>Primary Coordinator</label> <span class="field-required">*</span>
                <select name="ch_primarycor" id="ch_primarycor" class="form-control select2-bs4" style="width: 100%;" required>
                <option value="">Select Primary Coordinator</option>
                @foreach($primaryCoordinatorList as $pcl)
                      <option value="{{$pcl->cid}}">{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
                    @endforeach
                </select>
              </div>
              <div id="display_corlist"> </div>
              </div>
              </div>
              </div>
            </div>
        </div>

                <!-- /.box-body -->
                <div class="card-body text-center">
              <button type="submit" class="btn bg-gradient-primary" onclick="return PreSaveValidate()"><i class="fas fa-save" ></i>&nbsp; Save</button>
              <button type="button" class="btn bg-gradient-primary" onclick="ConfirmCancel(this);"><i class="fas fa-undo" ></i>&nbsp; Reset</button>
              <a href="{{ route('chapters.chaplist') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
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

$(document).ready(function() {
    var a = $('input[type=checkbox]').attr('checked');
    if(a ='checked'){
        $("#ch_avp_fname").prop("readonly",true);
        $("#ch_avp_lname").prop("readonly",true);
        $("#ch_avp_email").prop("readonly",true);
        $("#ch_avp_street").prop("readonly",true);
        $("#ch_avp_city").prop("readonly",true);
        $("#ch_avp_zip").prop("readonly",true);
        $("#ch_avp_phone").prop("readonly",true);
        $("#ch_avp_state").prop("disabled",true);


        $("#ch_mvp_fname").prop("readonly",true);
        $("#ch_mvp_lname").prop("readonly",true);
        $("#ch_mvp_email").prop("readonly",true);
        $("#ch_mvp_street").prop("readonly",true);
        $("#ch_mvp_city").prop("readonly",true);
        $("#ch_mvp_zip").prop("readonly",true);
        $("#ch_mvp_phone").prop("readonly",true);
        $("#ch_mvp_state").prop("disabled",true);

        $("#ch_trs_fname").prop("readonly",true);
        $("#ch_trs_lname").prop("readonly",true);
        $("#ch_trs_email").prop("readonly",true);
        $("#ch_trs_street").prop("readonly",true);
        $("#ch_trs_city").prop("readonly",true);
        $("#ch_trs_zip").prop("readonly",true);
        $("#ch_trs_phone").prop("readonly",true);
        $("#ch_trs_state").prop("disabled",true);

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

  function ConfirmVacant(checkboxid) {
    switch(checkboxid){
					case "AVPVacant":
              if($("#AVPVacant").prop("checked") == true){
                $("#ch_avp_fname").prop("readonly",true);
                $("#ch_avp_lname").prop("readonly",true);
                $("#ch_avp_email").prop("readonly",true);
                $("#ch_avp_street").prop("readonly",true);
                $("#ch_avp_city").prop("readonly",true);
                $("#ch_avp_zip").prop("readonly",true);
                $("#ch_avp_phone").prop("readonly",true);
                $("#ch_avp_state").prop("disabled",true);
                $("#ch_avp_fname").val("");
                $("#ch_avp_lname").val("");
                $("#ch_avp_email").val("");
                $("#ch_avp_street").val("");
                $("#ch_avp_city").val("");
                $("#ch_avp_zip").val("");
                $("#ch_avp_phone").val("");
              }
              else{
                $("#ch_avp_fname").prop("readonly",false);
                $("#ch_avp_lname").prop("readonly",false);
                $("#ch_avp_email").prop("readonly",false);
                $("#ch_avp_street").prop("readonly",false);
                $("#ch_avp_city").prop("readonly",false);
                $("#ch_avp_zip").prop("readonly",false);
                $("#ch_avp_phone").prop("readonly",false);
                $("#ch_avp_state").prop("disabled",false);

                $("#ch_avp_fname").prop("required",true);
                $("#ch_avp_lname").prop("required",true);
                $("#ch_avp_email").prop("required",true);
                $("#ch_avp_street").prop("required",true);
                $("#ch_avp_city").prop("required",true);
                $("#ch_avp_zip").prop("required",true);
                $("#ch_avp_phone").prop("required",true);
                $("#ch_avp_state").prop("required",true);
              }
            break;
          case "MVPVacant":
              if($("#MVPVacant").prop("checked") == true){
                $("#ch_mvp_fname").prop("readonly",true);
                $("#ch_mvp_lname").prop("readonly",true);
                $("#ch_mvp_email").prop("readonly",true);
                $("#ch_mvp_street").prop("readonly",true);
                $("#ch_mvp_city").prop("readonly",true);
                $("#ch_mvp_zip").prop("readonly",true);
                $("#ch_mvp_phone").prop("readonly",true);
                $("#ch_mvp_state").prop("disabled",true);
                $("#ch_mvp_fname").val("");
                $("#ch_mvp_lname").val("");
                $("#ch_mvp_email").val("");
                $("#ch_mvp_street").val("");
                $("#ch_mvp_city").val("");
                $("#ch_mvp_zip").val("");
                $("#ch_mvp_phone").val("");
              }
              else{
                $("#ch_mvp_fname").prop("readonly",false);
                $("#ch_mvp_lname").prop("readonly",false);
                $("#ch_mvp_email").prop("readonly",false);
                $("#ch_mvp_street").prop("readonly",false);
                $("#ch_mvp_city").prop("readonly",false);
                $("#ch_mvp_zip").prop("readonly",false);
                $("#ch_mvp_phone").prop("readonly",false);
                $("#ch_mvp_state").prop("disabled",false);
                $("#ch_mvp_fname").prop("required",true);
                $("#ch_mvp_lname").prop("required",true);
                $("#ch_mvp_email").prop("required",true);
                $("#ch_mvp_street").prop("required",true);
                $("#ch_mvp_city").prop("required",true);
                $("#ch_mvp_zip").prop("required",true);
                $("#ch_mvp_phone").prop("required",true);
                $("#ch_mvp_state").prop("required",true);
              }
            break;
          case "TreasVacant":
              if($("#TreasVacant").prop("checked") == true){
                $("#ch_trs_fname").prop("readonly",true);
                $("#ch_trs_lname").prop("readonly",true);
                $("#ch_trs_email").prop("readonly",true);
                $("#ch_trs_street").prop("readonly",true);
                $("#ch_trs_city").prop("readonly",true);
                $("#ch_trs_zip").prop("readonly",true);
                $("#ch_trs_phone").prop("readonly",true);
                $("#ch_trs_state").prop("disabled",true);
                $("#ch_trs_fname").val("");
                $("#ch_trs_lname").val("");
                $("#ch_trs_email").val("");
                $("#ch_trs_street").val("");
                $("#ch_trs_city").val("");
                $("#ch_trs_zip").val("");
                $("#ch_trs_phone").val("");
              }
              else{
                $("#ch_trs_fname").prop("readonly",false);
                $("#ch_trs_lname").prop("readonly",false);
                $("#ch_trs_email").prop("readonly",false);
                $("#ch_trs_street").prop("readonly",false);
                $("#ch_trs_city").prop("readonly",false);
                $("#ch_trs_zip").prop("readonly",false);
                $("#ch_trs_phone").prop("readonly",false);
                $("#ch_trs_state").prop("disabled",false);
                $("#ch_trs_fname").prop("required",true);
                $("#ch_trs_lname").prop("required",true);
                $("#ch_trs_email").prop("required",true);
                $("#ch_trs_street").prop("required",true);
                $("#ch_trs_city").prop("required",true);
                $("#ch_trs_zip").prop("required",true);
                $("#ch_trs_phone").prop("required",true);
                $("#ch_trs_state").prop("required",true);

              }
            break;
          case "SecVacant":
              if($("#SecVacant").prop("checked") == true){
                $("#ch_sec_fname").prop("readonly",true);
                $("#ch_sec_lname").prop("readonly",true);
                $("#ch_sec_email").prop("readonly",true);
                $("#ch_sec_street").prop("readonly",true);
                $("#ch_sec_city").prop("readonly",true);
                $("#ch_sec_zip").prop("readonly",true);
                $("#ch_sec_phone").prop("readonly",true);
                $("#ch_sec_state").prop("disabled",true);
                $("#ch_sec_fname").val("");
                $("#ch_sec_lname").val("");
                $("#ch_sec_email").val("");
                $("#ch_sec_street").val("");
                $("#ch_sec_city").val("");
                $("#ch_sec_zip").val("");
                $("#ch_sec_phone").val("");
              }
              else{
                $("#ch_sec_fname").prop("readonly",false);
                $("#ch_sec_lname").prop("readonly",false);
                $("#ch_sec_email").prop("readonly",false);
                $("#ch_sec_street").prop("readonly",false);
                $("#ch_sec_city").prop("readonly",false);
                $("#ch_sec_zip").prop("readonly",false);
                $("#ch_sec_phone").prop("readonly",false);
                $("#ch_sec_state").prop("disabled",false);
                $("#ch_sec_fname").prop("required",true);
                $("#ch_sec_lname").prop("required",true);
                $("#ch_sec_email").prop("required",true);
                $("#ch_sec_street").prop("required",true);
                $("#ch_sec_city").prop("required",true);
                $("#ch_sec_zip").prop("required",true);
                $("#ch_sec_phone").prop("required",true);
                $("#ch_sec_state").prop("required",true);
              }
            break;
    }

  }

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

  //submit validation function
  function PreSaveValidate(){
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

      function checkDuplicateEmail(email,id){
       $.ajax({
        url: '{{ url("/check.email/") }}' + '/' + email,
            type: "GET",
            success: function(result) {
                if(result.exists){
                    alert('This Email already used in the system. Please try with new one.');
                    $("#"+id).val('');
                    $("#"+id).focus();
               }
            },
            error: function (jqXHR, exception) {
            }
        });
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
</script>
@endsection
