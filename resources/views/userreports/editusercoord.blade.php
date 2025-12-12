@extends('layouts.coordinator_theme')

@section('page_title', 'User Details')
@section('breadcrumb', 'Edit User')
<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("userreports.updateusercoord",$cdDetails->id) }}'>
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

                          <div class="form-group row mt-2">
                            <label class="col-form-label col-sm-6">Active Status:</label>
                            <div class="col-sm-6">
                                <select id="status" name="status" class="form-control float-right text-right"required>
                                    @foreach($AllUserStatus as $status)
                                        <option value="{{$status->id}}"
                                            @if($userDetails->is_active == $status->id) selected @endif>
                                            {{$status->user_status}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                           <div class="form-group row mt-1">
                            <label class="col-form-label col-sm-6">User Type:</label>
                            <div class="col-sm-6">
                                <select id="type" name="type" class="form-control float-right text-right"required>
                                    @foreach($AllUserType as $type)
                                        <option value="{{$type->id}}"
                                            @if($userDetails->type_id == $type->id) selected @endif>
                                            {{$type->user_type}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                           <div class="form-group row mt-1">
                            <label class="col-form-label col-sm-6">Admin Role:</label>
                            <div class="col-sm-6">
                                <select id="role" name="role" class="form-control float-right text-right"required>
                                    @foreach($AllAdminRole as $role)
                                        <option value="{{$role->id}}"
                                            @if($userDetails->is_admin == $role->id) selected @endif>
                                            {{$role->admin_role}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </li>
                    {{-- <li class="list-group-item">
                        <b>Start Date:</b> <span class="float-right date-mask">{{ $cdDetails->coordinator_start_date }}</span>
                        <br>
                        <b>Last Promotion Date:</b> <span class="float-right date-mask">{{ $cdDetails->last_promoted }}</span>
                        <br>
                        <label>Home Chapter:</label><input type="text" name="cord_chapter" id="cord_chapter" class="form-control float-right col-sm-6 text-right" value="{{ $cdDetails->home_chapter }}" required placeholder="Home Chapter">
                        <br>
                    </li> --}}
                </ul>
                <div class="text-center">
                     @if ($cdDetails->active_status == 1 && $cdDetails->on_leave == 1)
                        <b><span style="color: #ff851b;">Coordinator is ON LEAVE</span></b>
                        <br>
                        Leave Date: <span class="date-mask">{{ $cdDetails->leave_date }}</span><br>
                    @else
                        @if ($cdDetails->active_status == 1 && $cdDetails->on_leave != 1)
                            <b><span style="color: #28a745;">Coordinator is ACTIVE</span></b>
                        @elseif ($cdDetails->active_status == 2)
                        <b><span style="color: #ff851b;">Coordinator is PENDING</span></b>
                        @elseif ($cdDetails->active_status == 3)
                        <b><span style="color: #dc3545;">Coordinator was NOT APPROVED</span></b><br>
                            Rejected Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
                            {{ $cdDetails->reason_retired }}
                        @elseif ($cdDetails->active_status == 0)
                            <b><span style="color: #dc3545;">Coordinator is RETIRED</span></b><br>
                            Retired Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
                            {{ $cdDetails->reason_retired }}
                        @endif
                    @endif
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                <h3 class="profile-username">User Information</h3>
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

</script>
@endsection
