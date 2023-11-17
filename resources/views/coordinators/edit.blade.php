@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
      Coordinator List
       <small>Edit</small>
       </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Coordinator List</li>
      </ol>
    </section>
    <!-- Main content -->
    <form method="POST" action='{{ route("coordinator.update",$coordinatorDetails[0]->coordinator_id) }}'">
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
						<input type="text" name="cord_fname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->first_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Name</label><span class="field-required">*</span>
						<input type="text" name="cord_lname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-12 col-xs-12">
						<div class="form-group">
							<label>Street Address</label><span class="field-required">*</span>
							<input autocomplete="nope" name="cord_addr" class="form-control my-colorpicker1" rows="4" maxlength="250" required value="{{ $coordinatorDetails[0]->address }}	">
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>City</label><span class="field-required">*</span>
							<input type="text" name="cord_city" class="form-control my-colorpicker1" maxlength="50" value="{{ $coordinatorDetails[0]->city }}" required onkeypress="return isAlphanumeric(event)" autocomplete="nope">
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
						<input type="text" name="cord_zip" class="form-control my-colorpicker1" maxlength="10" value="{{ $coordinatorDetails[0]->zip }}" required onkeypress="return isNumber(event)" autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Email</label><span class="field-required">*</span>
						<input type="email" name="cord_email" id="cord_email" class="form-control my-colorpicker1" onblur="checkDuplicateEmail(this.value,this.id)" maxlength="50" value="{{ $coordinatorDetails[0]->email }}" required autocomplete="nope">
						<input type="hidden" id="cord_email_chk" value="{{ $coordinatorDetails[0]->email }}">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Secondary Email</label>
						<input type="email" name="cord_sec_email" id="cord_sec_email" class="form-control my-colorpicker1"   maxlength="50" value="{{ $coordinatorDetails[0]->sec_email }}" autocomplete="nope">
						<input type="hidden" id="cord_email_chk" value="{{ $coordinatorDetails[0]->sec_email }}">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Phone</label><span class="field-required">*</span>
						<input type="text" name="cord_phone" id="cord_phone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->phone }}" maxlength="12" required onkeypress="return isPhone(event)" autocomplete="nope">
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Alternate Phone</label>
						<input type="text" name="cord_altphone" id="cord_altphone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->alt_phone }}" maxlength="12" onkeypress="return isPhone(event)" autocomplete="nope">
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
				</div>
				<div class="box-header with-border mrg-t-10"></div>
					<div class="box-body">
						<div class="col-sm-6 col-xs-12">
							<div class="form-group">
							<label class="mrg-b-25">Coordinators Directly Reporting to {{ $coordinatorDetails[0]->first_name }} :</label>

							<table id="coordinator-list" width="100%">
								<thead>
								   <tr>
										<th>First Name</th>
										<th>Last Name</th>
										<th>Position</th>
										<th></th>
								   </tr>
								</thead>
								<tbody>
								<?php
								$row_count=count($directReportTo);
								for ($row = 0; $row < $row_count; $row++){
									echo "<tr>";
										echo "<td>".$directReportTo[$row]->cor_f_name."</td>";
										echo "<td>".$directReportTo[$row]->cor_l_name."</td>";
										echo "<td>".$directReportTo[$row]->pos."</td>";
									echo "</tr>";
								}
								?>
								</tbody>
							</table>
							</div>
						</div>
						<div class="col-sm-6 col-xs-12">
							<div class="form-group">
							<label class="mrg-b-25">Primary Coordinator For :</label>

							<table id="coordinator-list" width="100%">
								<thead>
								   <tr>
										<th>State</th>
										<th>Chapter Name</th>
								   </tr>
								</thead>
								<tbody>
								<?php
								$row_count=count($directChapterTo);
								for ($row = 0; $row < $row_count; $row++){
									echo "<tr>";
									echo "<td>".$directChapterTo[$row]->st_name."</td>";
										echo "<td>".$directChapterTo[$row]->ch_name."</td>";

									echo "</tr>";
								}
								?>
								</tbody>
							</table>
							</div>
						</div>

					</div>

				<div class="box-header with-border mrg-t-10"></div>
				<div class="box-body">
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

					<div class="col-sm-12 col-xs-12">
					  <div class="form-group">
						<label>Home Chapter</label>
						<input type="text" name="cord_chapter" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->home_chapter }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" autocomplete="nope" disabled>
					  </div>
					</div>

					<div class="col-sm-12 col-xs-12">
						<div class="form-group">
						<label>Reports To</label>
						<select name="cord_month" class="form-control select2" style="width: 100%;" disabled>
						  <option value=""></option>
						   @foreach($primaryCoordinatorList as $pcl)
							  <option value="{{$pcl->cid}}" {{$coordinatorDetails[0]->report_id == $pcl->cid  ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Coordinator Start Date</label>
						<input type="text" name="cord_phone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->coordinator_start_date }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Promotion Date</label>
						<input type="text" name="cord_altphone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_promoted }}" disabled>
					  </div>
					</div>


					<div class="col-sm-6 col-xs-12">
					<div class="radio-chk">
							<div class="form-group">
							<label>On Leave Of Absense</label>
							<label style="display: block;"><input disabled type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" {{$coordinatorDetails[0]->on_leave == '1'  ? 'checked' : ''}}/><div><div></div></div>
							</label>
						</div>
					</div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Leave Date</label>
						<input type="text" name="cord_altphone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->leave_date }}" disabled>
					  </div>
					</div>

					<div class="clearfix"></div>

					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Updated By</label>
						<input type="text" name="cord_phone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_updated_by }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Updated Date</label>
						<input type="text" name="cord_altphone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_updated_date }}" disabled>
					  </div>
					</div>
				</div>
            </div>
		</div>

		<!-- /.box-body -->
		<div class="box-body text-center">
			<button type="submit" class="btn btn-themeBlue margin">Save</button>

			<button type="button" class="btn btn-themeBlue margin" onclick="ConfirmCancel(this);">Reset</button>
			<a href="{{ route('coordinator.list') }}" class="btn btn-themeBlue margin">Back</a>
		</div>
		 <div class="box-body text-center">
			<a href="{{ route('coordinator.role',$coordinatorDetails[0]->coordinator_id) }}" class="btn btn-themeBlue margin">Change Role</a>
			<button type="button" class="btn btn-themeBlue margin" id="{{ $coordinatorDetails[0]->user_id }}" onclick="return resetPassword(this.id)">Reset Password</button>

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
		var chkid = id+"_chk";
		var oldVal = $("#"+id).val();
		var newVal = $("#"+chkid).val();
		if(oldVal != newVal){
		   $.ajax({
            url: '{{ url("/check.email/") }}' + '/' + email,
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
		new_password = prompt("Please enter new password for this Coordinator", "TempPass4You");
		if (new_password != null && userid !='') {
			//Verify the password entered is of an allowable size
			if(new_password.length < 7){
				alert("Password must be at least 7 characters.  The password has not been reset.");
				return false;
			}
			else{
               $.ajax({
                url: '{{ url("/chapter.resetpswd") }}',
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

                return false;
			}
		}

	function addPrezList(val){
    $("#prezListBtn").attr("disabled", true);
          $.ajax({
            url: '/mimi/cordprezlist/'+val,
            type: "GET",
            success: function(result) {
              alert('Your request to be added to the PrezList has been submitted');
              $("#prezListBtn").attr("disabled", false);
				return true;
            },
            error: function (jqXHR, exception) {

            }
        });

      }
	function addVolList(val){
		$("#volListBtn").attr("disabled", true);
          $.ajax({
            url: '/mimi/cordvollist/'+val,
            type: "GET",
            success: function(result) {
              alert('Your request to be added to the PrezList has been submitted');
              $("#volListBtn").attr("disabled", false);
				      return true;
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

  });
</script>
@endsection

