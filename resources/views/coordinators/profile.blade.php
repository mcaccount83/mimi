@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
      Coordinator Profile
       <small>Update</small>
       </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Coordinator Profile</li>
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
    <form method="POST" action='{{ route("coordinator.updateprofile",$coordinatorDetails[0]->coordinator_id) }}'">
    @csrf
    <section class="content">
		<div class="row">
		<div class="col-md-12">
			<div class="box card">
				<div class="box-header with-border mrg-t-10">
					<h3 class="box-title">Coordinator</h3>
				</div>
				<div class="box-body">
				  <!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>First Name</label><span class="field-required">*</span>
						<input type="text" name="cord_fname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->first_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Name</label><span class="field-required">*</span>
						<input type="text" name="cord_lname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Primary Position</label>
						<select name="cord_month" class="form-control select2" style="width: 100%;" disabled>
						   <option value=""></option>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Secondary Position</label>
						<select name="cord_month" class="form-control select2" style="width: 100%;" disabled>
						  <option value=""></option>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->sec_position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Region</label>
						<select name="cord_month" class="form-control select2" style="width: 100%;" disabled>
						 <option value=""></option>
							@foreach($regionList as $reg)
							  <option value="{{$reg->id}}" {{$coordinatorDetails[0]->region_id == $reg->id  ? 'selected' : ''}}>{{$reg->long_name}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Conference</label>
						<select name="cord_month" class="form-control select2" style="width: 100%;" disabled>
						  <option value=""></option>
						  @foreach($confList as $con)
							  <option value="{{$con->id}}" {{$coordinatorDetails[0]->conference_id == $con->id  ? 'selected' : ''}}>{{$con->conference_name}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-12 col-xs-12">
						<div class="form-group">
							<label>Street Address</label><span class="field-required">*</span>
							<input name="cord_addr" class="form-control my-colorpicker1" rows="4" maxlength="250" value="{{ $coordinatorDetails[0]->address }}" required>
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>City</label><span class="field-required">*</span>
							<input type="text" name="cord_city" class="form-control my-colorpicker1" maxlength="50" value="{{ $coordinatorDetails[0]->city }}" required onkeypress="return isAlphanumeric(event)">
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>State</label><span class="field-required">*</span>
						<select name="cord_state" class="form-control select2" style="width: 100%;" required>
						<option value="">Select State</option>
							@foreach($stateArr as $state)
							  <option value="{{$state->state_short_name}}" {{$coordinatorDetails[0]->state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
					<!-- /.form group -->
					
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>Country</label><span class="field-required">*</span>
						<select id="cord_country" name="cord_country" class="form-control select2" style="width: 100%;" required>
						<option value="">Select Country</option>
							@foreach($countryArr as $con)
							  <option value="{{$con->short_name}}" {{$coordinatorDetails[0]->country == $con->short_name  ? 'selected' : ''}}>{{$con->name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>Zip</label><span class="field-required">*</span>
						<input type="text" name="cord_zip" class="form-control my-colorpicker1" maxlength="10" value="{{ $coordinatorDetails[0]->zip }}" required onkeypress="return isNumber(event)">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>E-mail</label><span class="field-required">*</span>
						<input type="email" name="cord_email" id="cord_email" class="form-control my-colorpicker1" onblur="checkDuplicateEmail(this.value,this.id)" maxlength="50" value="{{ $coordinatorDetails[0]->email }}" required>
						<input type="hidden" id="cord_email_chk" value="{{ $coordinatorDetails[0]->email }}">
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Secondary E-mail</label>
						<input type="email" name="cord_sec_email" id="cord_sec_email" class="form-control my-colorpicker1"  maxlength="50" value="{{ $coordinatorDetails[0]->sec_email }}">
						 </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Phone</label><span class="field-required">*</span>
						<input type="text" name="cord_phone" id="cord_phone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->phone }}" maxlength="12" required onkeypress="return isPhone(event)">
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Alternate Phone</label>
						<input type="text" name="cord_altphone" id="cord_altphone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->alt_phone }}" maxlength="12" onkeypress="return isPhone(event)">
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Birthday Month</label><span class="field-required">*</span>
						<select name="cord_month" class="form-control select2" style="width: 100%;" required>
						  <option value="">Select Month</option>
						  @foreach($foundedMonth as $key=>$val)
							  
							  <option value="{{$key}}" {{$currentMonth == $key  ? 'selected' : ''}}>{{$val}}</option>
						  @endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Birthday Day</label><span class="field-required">*</span>
						<input type="number" name="cord_day" class="form-control my-colorpicker1" min="1" max="31" value="{{ $coordinatorDetails[0]->birthday_day }}" required>
						</div>
					</div>
					<div class="col-sm-12 col-xs-12">
					  <div class="form-group">
						<label>Coordinator Start Date</label>
						<input type="text" name="cord_phone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->coordinator_start_date }}" disabled>
					  </div>
					</div>
				</div>
				
				<div class="box-header with-border mrg-t-10">
					<h3 class="box-title">Supervising Coordinator Info</h3>
				</div>
				<div class="box-body">
					
					<div class="col-sm-12 col-xs-12">
						<div class="form-group">
						<label>Name</label>
						<input type="text" name="cord_altphone" class="form-control my-colorpicker1" value="{{ $primaryCoordinatorList[0]->cor_f_name }} {{$primaryCoordinatorList[0]->cor_l_name}}" disabled>
						</div>
					</div>
					<div class="col-sm-12 col-xs-12">
						<div class="form-group">
						<label>Email</label>
						<input type="text" name="cord_altphone" class="form-control my-colorpicker1" value="{{ $primaryCoordinatorList[0]->cor_email }} " disabled>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Phone</label>
						<input type="text" name="cord_altphone" class="form-control my-colorpicker1" value="{{ $primaryCoordinatorList[0]->cor_phone }}" disabled>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Alternate Phone</label>
						<input type="text" name="cord_altphone" class="form-control my-colorpicker1" value="{{ $primaryCoordinatorList[0]->cor_altphone }} " disabled>
						</div>
					</div>
					
				</div>
            </div>
		</div>
         
		<!-- /.box-body -->
		<div class="box-body text-center">
			<button type="submit" class="btn btn-themeBlue margin">Save</button>
			<button type="button" class="btn btn-themeBlue margin" onclick="ConfirmCancel(this);">Reset</button>
			<a href="{{ route('home') }}" class="btn btn-themeBlue margin">Back</a>
		</div>
		 <div class="box-body text-center">
			
			<button type="button" class="btn btn-themeBlue margin" id="{{ $coordinatorDetails[0]->user_id }}" onclick="return resetPassword(this.id)">Change Password</button>
			<button type="button" class="btn btn-themeBlue margin" onclick="window.open('https://momsclub.org/coordinator-toolkit/')">Coordinator Toolkit</button>
			<button type="button" class="btn btn-themeBlue margin" onclick="window.open('https://momsclub.org/elearning/')">eLearning Library</button>
		</div>  
        <!-- /.box-body -->
        </div>
    </section>
</form>
 
@endsection
@section('customscript')
<script>
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
		alert("Please Enter Number Only, Should be xxx-xxx-xxxx format");
		return false;
	}
} 
   function ConfirmCancel(element){
		var result=confirm("Any unsaved changes will be lost. Do you want to continue?");
		if(result)
			location.reload()
		else
			return false;
	}	
	function checkDuplicateEmail(email,id){
		var chkid = id+"_chk";
		var oldVal = $("#"+id).val();
		var newVal = $("#"+chkid).val();
		if(oldVal != newVal){	
		   $.ajax({
				url: '/mimi/checkemail/'+email,
				type: "GET",
			    success: function(result) {
					if(result.exists){
						alert('This Email already used in system. Please try with new one.');
						$("#"+id).val('');
						$("#"+id).focus();
					}
				},
				error: function (jqXHR, exception) {

				}
			});
		}else{
			return false;
		}			
    }  
	
	
	function resetPassword(userid){
      	var new_password="";
				new_password = prompt("Please enter new password for this Coordinator");
				
				if( new_password.indexOf(" ") !== -1 )
				{
					alert("Space not allowed");
					return false;
				}
				if (new_password != null && userid !='') {
					//Verify the password entered is of an allowable size
					if(new_password.length < 7){
						alert("Password must be at least 7 characters.  The password has not been reset. Please try again");
						return false;
					}
					else{
               $.ajax({
                  url: '/mimi/chapter/resetpswd',
                  type: "POST",
                  data: { pswd:new_password,user_id:userid, _token: '{{csrf_token()}}' },
                  success: function(result) {
                    alert('Password has been changed successfully');
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
	$( document ).ready(function() {	
	$("#cord_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
	$("#cord_altphone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
  });
</script>
@endsection

