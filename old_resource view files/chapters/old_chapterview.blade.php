@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        Chapter List 
        <small>View</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter List</li>
      </ol>
    </section>
    @if ($message = Session::get('success'))

<div class="alert alert-success">

    <p>{{ $message }}</p>

</div>

@endif

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
                  <option value="1" {{$chapterList[0]->status == 1  ? 'selected' : ''}}>Operating OK</option>
                  <option value="4" {{$chapterList[0]->status == 4  ? 'selected' : ''}}>On Hold Do not Refer</option>
                  <option value="5" {{$chapterList[0]->status == 5  ? 'selected' : ''}}>Probation</option>
                  <option value="6" {{$chapterList[0]->status == 6  ? 'selected' : ''}}>Probation Do Not Link</option>
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
              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">AVP</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control my-colorpicker1" value="" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control my-colorpicker1" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_avp_email" id="ch_avp_email" class="form-control my-colorpicker1" disabled>
              </div>
              </div>
              <!-- /.form group -->
			  <div class="radio-chk">
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="AVPVacant" id="AVPVacant" class="ios-switch green bigswitch" checked onchange="ConfirmVacant(this.id)" disabled /><div><div></div></div>
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
                <input type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control my-colorpicker1" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control my-colorpicker1" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_mvp_email" id="ch_mvp_email" class="form-control my-colorpicker1" disabled>
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="MVPVacant" id="MVPVacant" class="ios-switch green bigswitch" checked onchange="ConfirmVacant(this.id)" disabled/><div><div></div></div>
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
                <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control my-colorpicker1" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control my-colorpicker1" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_trs_email" id="ch_trs_email" class="form-control my-colorpicker1" disabled>
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="TreasVacant" id="TreasVacant" class="ios-switch green bigswitch" checked onchange="ConfirmVacant(this.id)" disabled/><div><div></div></div>
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
                <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control my-colorpicker1" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control my-colorpicker1" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_sec_email" id="ch_sec_email" class="form-control my-colorpicker1" disabled>
              </div>
              </div>
              <!-- /.form group -->
               <div class="radio-chk">
				<div class="col-sm-6 col-xs-12">
					<div class="form-group">
                        <label>Vacant</label>
                        <label style="display: block;"><input type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" checked onchange="ConfirmVacant(this.id)" disabled/><div><div></div></div>
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
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
                <label class="headings">Link Status</label>
              </div>
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label class="span-t mrg-10">
                  <input type="radio" name="ch_linkstatus" class="minimal">
                  <span>Linked</span>
                </label>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label class="span-t mrg-10">
                  <input type="radio" name="ch_linkstatus" class="minimal">
                  <span>Add Link Requested</span>
                </label>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label class="span-t mrg-10">
                  <input type="radio" name="ch_linkstatus" class="minimal">
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
              <!--<div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Inquiries Note</label>
                <input type="text" name="ch_inqnote" class="form-control my-colorpicker1" value="{{ $chapterList[0]->inquiries_note}}" disabled>
              </div>
              </div>-->
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
              <!--<div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Notes</label>
                <input type="text" name="ch_notes" class="form-control my-colorpicker1" value="{{ $chapterList[0]->website_notes}}" disabled>
              </div>
              </div>-->
              
              </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title"></h3>
              </div>
              <div class="box-body">
                <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Founded Date</label>
                <select name="ch_founddate" class="form-control select2" style="width: 100%;" disabled>
                  <option value="">Select Date</option>
                  <option value="1">JAN</option>
                  <option value="2">FEB</option>
                  <option value="3">MAR</option>
                  <option value="4">APR</option>
                  <option value="5">MAY</option>
                  <option value="6">JUN</option>
                  <option value="7">JUL</option>
                  <option value="8">AUG</option>
                  <option value="9">SEP</option>
                  <option value="10">OCT</option>
                  <option value="11">NOV</option>
                  <option value="12">DEC</option>
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
                <input type="text" name="ch_dues" class="form-control my-colorpicker1" value="New" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Number of members registration</label>
                <input type="text" name="ch_memberpaid" class="form-control my-colorpicker1" value="0" disabled>
              </div>
              </div>
              </div>
             
          <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">International Moms Clubs Coordinators</h3>
              </div>
              <div class="box-body">
               
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Primary Coordinator (Changing this value will cause the page to refresh)</label>
                <select name="ch_primarycor" class="form-control select2" style="width: 100%;" disabled>
                <option value="">Select Primary Coordinator</option>
                @foreach($primaryCoordinatorList as $pcl)
                      <option value="{{$pcl->cid}}" {{$chapterList[0]->primary_coordinator_id == $pcl->cid  ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
                    @endforeach
                </select>
              </div>
              </div>
              </div>
              </div>
      </div>
            
            <!-- /.box-body -->
            <div class="box-body text-center">
            
              <button type="button" class="btn btn-grey margin" onclick="window.history.go(-1); return false;">Back</button>
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
  $( document ).ready(function() {
    var a = $('input[type=checkbox]').attr('checked'); 
    if(a ='checked'){
        $("#ch_avp_fname").prop("readonly",true);
        $("#ch_avp_lname").prop("readonly",true);
        $("#ch_avp_email").prop("readonly",true);

        $("#ch_mvp_fname").prop("readonly",true);
        $("#ch_mvp_lname").prop("readonly",true);
        $("#ch_mvp_email").prop("readonly",true);

        $("#ch_trs_fname").prop("readonly",true);
        $("#ch_trs_lname").prop("readonly",true);
        $("#ch_trs_email").prop("readonly",true);

        $("#ch_sec_fname").prop("readonly",true);
        $("#ch_sec_lname").prop("readonly",true);
        $("#ch_sec_email").prop("readonly",true);
   }
    
  });

  function ConfirmVacant(checkboxid) {
    switch(checkboxid){
					case "AVPVacant":
              if($("#AVPVacant").prop("checked") == true){
                $("#ch_avp_fname").prop("readonly",true);
                $("#ch_avp_lname").prop("readonly",true);
                $("#ch_avp_email").prop("readonly",true);
                $("#ch_avp_fname").val("");
                $("#ch_avp_lname").val("");
                $("#ch_avp_email").val("");
              }
              else{
                $("#ch_avp_fname").prop("readonly",false);
                $("#ch_avp_lname").prop("readonly",false);
                $("#ch_avp_email").prop("readonly",false);
                $("#ch_avp_fname").prop("required",true);
                $("#ch_avp_lname").prop("required",true);
                $("#ch_avp_email").prop("required",true);
              }  
            break; 
          case "MVPVacant":
              if($("#MVPVacant").prop("checked") == true){
                $("#ch_mvp_fname").prop("readonly",true);
                $("#ch_mvp_lname").prop("readonly",true);
                $("#ch_mvp_email").prop("readonly",true);
                $("#ch_mvp_fname").val("");
                $("#ch_mvp_lname").val("");
                $("#ch_mvp_email").val("");
              }
              else{
                $("#ch_mvp_fname").prop("readonly",false);
                $("#ch_mvp_lname").prop("readonly",false);
                $("#ch_mvp_email").prop("readonly",false);
                $("#ch_mvp_fname").prop("required",true);
                $("#ch_mvp_lname").prop("required",true);
                $("#ch_mvp_email").prop("required",true);
              }  
            break;
          case "TreasVacant":
              if($("#TreasVacant").prop("checked") == true){
                $("#ch_trs_fname").prop("readonly",true);
                $("#ch_trs_lname").prop("readonly",true);
                $("#ch_trs_email").prop("readonly",true);
                $("#ch_trs_fname").val("");
                $("#ch_trs_lname").val("");
                $("#ch_trs_email").val("");
              }
              else{
                $("#ch_trs_fname").prop("readonly",false);
                $("#ch_trs_lname").prop("readonly",false);
                $("#ch_trs_email").prop("readonly",false);
                $("#ch_trs_fname").prop("required",true);
                $("#ch_trs_lname").prop("required",true);
                $("#ch_trs_email").prop("required",true);
              }  
            break; 
          case "SecVacant":
              if($("#SecVacant").prop("checked") == true){
                $("#ch_sec_fname").prop("readonly",true);
                $("#ch_sec_lname").prop("readonly",true);
                $("#ch_sec_email").prop("readonly",true);
                $("#ch_sec_fname").val("");
                $("#ch_sec_lname").val("");
                $("#ch_sec_email").val("");
              }
              else{
                $("#ch_sec_fname").prop("readonly",false);
                $("#ch_sec_lname").prop("readonly",false);
                $("#ch_sec_email").prop("readonly",false);
                $("#ch_sec_fname").prop("required",true);
                $("#ch_sec_lname").prop("required",true);
                $("#ch_sec_email").prop("required",true);
              }  
            break;      
    }      
    
  }

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
