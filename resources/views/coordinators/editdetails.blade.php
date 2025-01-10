@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinator Details')
@section('breadcrumb', 'Coordinator Details')

<style>

.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("coordinators.updatedetails",$cdDetails->id) }}'>
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
                        <b>Supervising Coordinator:</b> <span class="float-right">{{ $ReportTo }}</span>
                        <br>
                        <b>Primary/Display Position:</b> <span class="float-right">{{ $displayPosition->long_title }}</span>
                        <br>
                        <b>Primary Positon for MIMI Purposes:</b> <span class="float-right">{{ $mimiPosition?->long_title }}</span>
                        <br>
                        <b>Secondary Position:</b> <span class="float-right">{{ $secondaryPosition?->long_title }}</span>

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
                <div class="text-center">
                    @if ($cdDetails->is_active == 1 && $cdDetails->on_leave != 1)
                        <b><span style="color: #28a745;">Coordinator is ACTIVE</span></b>
                    @elseif ($cdDetails->is_active == 1 && $cdDetails->on_leave == 1)
                        <b><span style="color: #ff851b;">Coordinator is ON LEAVE</span></b>
                        <br>
                        Leave Date: <span class="date-mask">{{ $cdDetails->leave_date }}</span><br>
                    @else
                        <b><span style="color: #dc3545;">Coordinator is RETIRED</span></b>
                        <br>
                        Retired Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
                        {{ $cdDetails->reason_retired }}
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
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_city" id="cord_city" class="form-control" value="{{ $cdDetails->city }}"  required >
                                    </div>
                                    <div class="col-sm-3">
                                        <select name="cord_state" class="form-control" style="width: 100%;" required>
                                            <option value="">Select State</option>
                                            @foreach($allStates as $state)
                                            <option value="{{$state->state_short_name}}"
                                                    @if($cdDetails->state == $state->state_short_name) selected @endif>
                                                    {{$state->state_long_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" name="cord_zip" id="cord_zip" class="form-control" value="{{ $cdDetails->zip }}"  required >
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
                <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2" ></i>Save</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.view', ['id' => $cdDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to Coordinator Details</button>
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

</script>
@endsection
