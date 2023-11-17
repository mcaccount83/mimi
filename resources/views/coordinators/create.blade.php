@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
      Add Coordinator
       <small>New</small>
       </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Add Coordinator</li>
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
    <form method="POST" id="create_form" action="{{ route('coordinator.store') }}" autocomplete="off">
    @csrf
    <section class="content">
		<div class="row">
		<div class="col-md-12">
			<div class="box card">
				<div class="box-header with-border mrg-t-10">
					<h3 class="box-title">Add New Coordinator</h3>
				</div>
				<div class="box-body">
				  <!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>First Name</label><span class="field-required">*</span>
						<input type="text" name="cord_fname" id="cord_fname" class="form-control my-colorpicker1" maxlength="50" required onkeypress="return isAlphanumeric(event)" autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Name</label><span class="field-required">*</span>
						<input type="text" name="cord_lname" id="cord_lname" class="form-control my-colorpicker1" maxlength="50" required onkeypress="return isAlphanumeric(event)" autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-12 col-xs-12">
						<div class="form-group">
							<label>Street Address</label><span class="field-required">*</span>
							<input autocomplete="nope" name="cord_addr" id="cord_addr" class="form-control my-colorpicker1" rows="4" maxlength="250" required >
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>City</label><span class="field-required">*</span>
							<input type="text" name="cord_city" id="cord_city" class="form-control my-colorpicker1" maxlength="50" required onkeypress="return isAlphanumeric(event)" autocomplete="nope">
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>State</label><span class="field-required">*</span>
						<select name="cord_state" id="cord_state" class="form-control select2" style="width: 100%;" required>
						<option value="">Select State</option>
							@foreach($stateArr as $state)
							  <option value="{{$state->state_short_name}}">{{$state->state_long_name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
					<!-- /.form group -->

					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>Country</label><span class="field-required">*</span>
						<?php $selectedvalue = 'USA' ?>
						<select id="cord_country" name="cord_country" id="cord_country" class="form-control select2" style="width: 100%;" required>
						<option value="">Select Country</option>
							@foreach($countryArr as $con)
							  <option value="{{$con->short_name}}" {{ $selectedvalue == $con->short_name ? 'selected="selected"' : '' }}>{{$con->name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>Zip</label><span class="field-required">*</span>
						<input type="text" name="cord_zip" id="cord_zip" class="form-control my-colorpicker1" maxlength="10" required onkeypress="return isNumber(event)" autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Email</label><span class="field-required">*</span>
						<input type="email" name="cord_email" id="cord_email" class="form-control my-colorpicker1" onblur="checkDuplicateEmail(this.value,this.id)" maxlength="50" required autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Secondary Email</label>
						<input type="sec_email" name="sec_email" id="sec_email" class="form-control my-colorpicker1" maxlength="50" autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Phone</label><span class="field-required">*</span>
						<input type="tel" name="cord_phone" id="cord_phone" class="form-control my-colorpicker1" maxlength="12" required onkeypress="return isPhone(event)" autocomplete="nope">
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Alternate Phone</label>
						<input type="tel" name="cord_altphone" id="cord_altphone" class="form-control my-colorpicker1" maxlength="12" onkeypress="return isPhone(event)" autocomplete="nope">
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Birthday Month</label><span class="field-required">*</span>
						<select name="cord_month" id="cord_month" class="form-control select2" style="width: 100%;" required>
						  <option value="">Select Month</option>
						  @foreach($foundedMonth as $key=>$val)
							  <option value="{{$key}}">{{$val}}</option>
						  @endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Birthday Day</label><span class="field-required">*</span>
						<input type="number" name="cord_day" id="cord_day" class="form-control my-colorpicker1" min="1" max="31" required>
						</div>
					</div>
				</div>
            </div>
		</div>

		<!-- /.box-body -->
		<div class="box-body text-center">
			<button type="submit" id="btn-save" class="btn btn-themeBlue margin">Save</button>
			<button type="button" class="btn btn-themeBlue margin" onclick="ConfirmCancel(this);">Reset</button>

			<a href="{{ route('coordinator.list') }}" class="btn btn-themeBlue margin">Back</a>
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
		alert("Please Enter Number Only");
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
  $( document ).ready(function() {
	var phoneListArr = ["cord_phone","cord_altphone"];
    for (var i = phoneListArr.length - 1; i >= 0; i--) {
        var inputValue = $("#"+phoneListArr[i]).val();
        if(inputValue.length > 10) inputValue = inputValue.substring(0,12);
        var reInputValue = inputValue.replace(/(\d{3})(\d{3})/, "$1-$2-");
        $("#"+phoneListArr[i]).val(reInputValue);
    }
	$("#cord_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
	$("#cord_altphone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });

	/*$('form#create_form').find('input').each(function(){
		if(!$(this).prop('required')){
			alert('not');
		} else {
			alert('yes');
		}
	});*/

  });


</script>
@endsection

