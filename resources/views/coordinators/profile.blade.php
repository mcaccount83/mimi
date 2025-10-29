@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinator Profile')
@section('breadcrumb', 'Coordinator Profile')
<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("coordinators.profileupdate",$cdDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">{{ $cdDetails->first_name }}, {{ $cdDetails->last_name }}</h3>
                    <p class="text-center">{{ $conferenceDescription }} Conference
                    @if ($regionLongName != "None")
                    , {{ $regionLongName }} Region
                        @else
                    @endif
                </p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Supervising Coordinator:</b> <span class="float-right"><a href="mailto:{{ $cdDetails->reportsTo?->email }}">{{ $ReportTo }} </a></span>
                        <br>
                        <b>Primary Position:</b> <span class="float-right">{{ $displayPosition->long_title }}</span>
                        <br>
                        <div style="display: flex; justify-content: space-between;">
                            <b>Secondary Positions:</b>
                            <span style="text-align: right;">
                                @forelse($cdDetails->secondaryPosition as $position)
                                    {{ $position->long_title }}@if(!$loop->last)<br>@endif
                                @empty
                                    None
                                @endforelse
                            </span>
                        </div>

                    </li>
                    <li class="list-group-item">
                        <b>Start Date:</b> <span class="float-right date-mask">{{ $cdDetails->coordinator_start_date }}</span>
                        <br>
                        <b>Last Promotion Date:</b> <span class="float-right date-mask">{{ $cdDetails->last_promoted }}</span>
                        <br>
                        <label>Home Chapter:</label><input type="text" name="cord_chapter" id="cord_chapter" class="form-control float-right col-sm-6 text-right" value="{{ $cdDetails->home_chapter }}" required placeholder="Home Chapter">
                        <br>
                    </li>
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                <h3 class="profile-username">Contact Information</h3>
                    <!-- /.card-header -->
                        <div class="row">
                            <div class="col-md-12">
                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Name:</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_fname" id="cord_fname" class="form-control" value="{{ $cdDetails->first_name }}"  required >
                                    </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_lname" id="cord_lname" class="form-control" value="{{ $cdDetails->last_name }}"  required >
                                    </div>
                                </div>
                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Email/Secondary:</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_email" id="cord_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  value="{{ $cdDetails->email }}"  required >
                                    <input type="hidden" id="cord_email_chk" value="{{ $cdDetails->email }}">
                                </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_sec_email"class="form-control" value="{{ $cdDetails->sec_email }}" placeholder="Secondary Email">
                                    </div>
                                </div>
                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Phone/Alternate:</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_phone" id="cord_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $cdDetails->phone }}"  required >
                                    </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_altphone" id="cord_altphone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $cdDetails->alt_phone }}"  placeholder="Alternate Phone" >
                                    </div>
                                </div>
                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Address:</label>
                                    <div class="col-sm-10">
                                    <input type="text" name="cord_addr" id="cord_addr" class="form-control" value="{{ $cdDetails->address }}"  required >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label"><br></label>
                                    <div class="col-sm-3">
                                    <input type="text" name="cord_city" id="cord_city" class="form-control" value="{{ $cdDetails->city }}"  required >
                                    </div>
                                    <div class="col-sm-3">
                                        <select name="cord_state" id="cord_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}"
                                            @if(isset($cdDetails->state_id) && $cdDetails->state_id == $state->id) selected @endif>
                                                    {{$state->state_long_name}}
                                                </option>
                                    @endforeach
                                    </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" name="cord_zip" id="cord_zip" class="form-control" value="{{ $cdDetails->zip }}"  required >
                                    </div>
                                    <div class="col-sm-2">
                                <select name="cord_country" id="cord_country" class="form-control" style="width: 100%;" required>
                                    <option value="">Select Country</option>
                                    @foreach($allCountries as $country)
                                            <option value="{{$country->id}}"
                                            @if(isset($cdDetails->country_id) && $cdDetails->country_id == $country->id) selected @endif>
                                                {{$country->name}}
                                            </option>
                                        @endforeach
                                </select>
                            </div>
                                </div>
                                 <!-- /.form group -->
                                 <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Birthday:</label>
                                    <div class="col-sm-3">
                                        <select name="cord_month" class="form-control" style="width: 100%;" required>
                                            <option value="">Select Month</option>
                                            @foreach($allMonths as $month)
                                                <option value="{{$month->id}}"
                                                    @if($cdDetails->birthday_month_id == $month->id) selected @endif>
                                                    {{$month->month_long_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                    <input type="text" name="cord_day" id="cord_day" class="form-control" value="{{ $cdDetails->birthday_day }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
              <!-- /.card-body -->
                        </div>
            <!-- /.card -->
                      </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center">
                <button type="submit" class="btn bg-gradient-primary" onclick="return PreSaveValidate();"><i class="fas fa-save mr-2" ></i>Save</button>
                <button type="button" class="btn btn-primary" onclick="showChangePasswordAlert('{{ $cdDetails->user_id }}')"><i class="fas fa-lock mr-2" ></i>Change Password</button>
            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>

// function checkDuplicateEmail(email, id) {
//         $.ajax({
//             url: '{{ url("/checkemail/") }}' + '/' + email,
//             type: "GET",
//             success: function(result) {
//                 if (result.exists) {
//                     alert('This Email already used in the system. Please try with new one.');
//                     $("#" + id).val('');
//                     $("#" + id).focus();
//                 }
//             },
//             error: function(jqXHR, exception) {
//                 console.error("Error checking email: ", exception);
//             }
//         });
//     }

  //submit validation function
  function PreSaveValidate(){
          var NewPassword=document.getElementById("cord_pswd").value;
				//They changed their password
				if(document.getElementById("cord_pswd").value != document.getElementById("cord_pswd").getAttribute("value")){
					if(document.getElementById("cord_pswd").value != document.getElementById("cord_pswd_cnf").value){  //Make sure the password and confirmation match
						alert ("The provided passwords do not match, please re-enter your password.");
						document.getElementById("cord_pswd_cnf").focus();
						return false;
					}
					// Make sure the password is the right length
					else if(NewPassword.length < 7){
						alert("Password must be at least 7 characters.");
						document.getElementById("cord_pswd").focus();
						return false;
					}
					else{
						document.getElementById("cord_pswd_chg").value="1";
					}
                }
		//Okay, all validation passed, save the records to the database
		return true;
	}


//     function showChangePasswordAlert(user_id) {
//     Swal.fire({
//         title: 'Change Password',
//         html: `
//             <form id="changePasswordForm">
//                 <div class="form-group">
//                     <label for="current_password">Current Password</label>
//                     <input type="password" name="current_password" id="current_password" class="swal2-input" required>
//                 </div>
//                 <div class="form-group">
//                     <label for="new_password">New Password</label>
//                     <input type="password" name="new_password" id="new_password" class="swal2-input" required>
//                 </div>
//                 <div class="form-group">
//                     <label for="new_password_confirmation">Confirm New Password</label>
//                     <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="swal2-input" required>
//                 </div>
//              <input type="hidden" id="user_id" name="user_id" value="${user_id}">
//             </form>
//         `,
//         confirmButtonText: 'Update Password',
//         cancelButtonText: 'Cancel',
//         showCancelButton: true,
//         customClass: {
//             confirmButton: 'btn-sm btn-success',
//             cancelButton: 'btn-sm btn-danger'
//         },
//         preConfirm: () => {
//             const user_id = Swal.getPopup().querySelector('#user_id').value;
//             const currentPassword = Swal.getPopup().querySelector('#current_password').value;
//             const newPassword = Swal.getPopup().querySelector('#new_password').value;
//             const confirmNewPassword = Swal.getPopup().querySelector('#new_password_confirmation').value;

//             // Validate input fields
//             if (!currentPassword || !newPassword || !confirmNewPassword) {
//                 Swal.showValidationMessage('Please fill out all fields');
//                 return false;
//             }

//             if (newPassword != confirmNewPassword) {
//                 Swal.showValidationMessage('New passwords do not match');
//                 return false;
//             }

//             // Return the AJAX call as a promise to let Swal wait for it
//             return $.ajax({
//                 url: '{{ route("checkpassword") }}',  // Check current password route
//                 type: 'POST',
//                 data: {
//                     _token: '{{ csrf_token() }}',
//                     current_password: currentPassword
//                 }
//             }).then(response => {
//                 if (!response.isValid) {
//                     Swal.showValidationMessage('Current password is incorrect');
//                     return false;
//                 }
//                 return {
//                     user_id: user_id,
//                     current_password: currentPassword,
//                     new_password: newPassword,
//                     new_password_confirmation: confirmNewPassword
//                 };
//             }).catch(() => {
//                 Swal.showValidationMessage('Error verifying current password');
//                 return false;
//             });
//         }
//     }).then((result) => {
//         if (result.isConfirmed) {
//             Swal.fire({
//                 title: 'Processing...',
//                 text: 'Please wait while we process your request.',
//                 allowOutsideClick: false,
//                 customClass: {
//                     confirmButton: 'btn-sm btn-success'
//                 },
//                 didOpen: () => Swal.showLoading()
//             });

//             // Send the form data via AJAX to update the password
//             $.ajax({
//                 url: '{{ route("updatepassword") }}',
//                 type: 'PUT',
//                 data: {
//                     _token: '{{ csrf_token() }}',
//                     user_id: result.value.user_id,
//                     current_password: result.value.current_password,
//                     new_password: result.value.new_password,
//                     new_password_confirmation: result.value.new_password_confirmation
//                 },
//                 success: function(response) {
//                     Swal.fire({
//                         title: 'Success!',
//                         text: 'Your password has been updated.',
//                         icon: 'success',
//                         confirmButtonText: 'OK',
//                         customClass: {
//                             confirmButton: 'btn-sm btn-success'
//                         }
//                     });
//                 },
//                 error: function(jqXHR) {
//                     Swal.fire({
//                         title: 'Error!',
//                         text: `Something went wrong: ${jqXHR.responseText}`,
//                         icon: 'error',
//                         confirmButtonText: 'OK',
//                         customClass: {
//                             confirmButton: 'btn-sm btn-danger'
//                         }
//                     });
//                 }
//             });
//         }
//     });
// }

</script>
@endsection
