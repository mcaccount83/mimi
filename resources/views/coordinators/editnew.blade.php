@extends('layouts.coordinator_theme')

@section('page_title', 'New Coordinator Details')
@section('breadcrumb', 'New Coordinator')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("coordinators.updatenew") }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username ">Coordinator Information</h3>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Conference:</label>
                            <div class="col-sm-8">
                                <input type="text" name="cord_conf" id="cord_conf" class="form-control" placeholder="Home Chapter" value="{{ $conference[0]->short_name }} - {{ $conference[0]->conference_description }}" readonly>
                            </div>
                        </div>

                        <div class="form-group row mt-1">
                            <label class="col-sm-4 col-form-label">Region:</label>
                            <div class="col-sm-8">
                                @if($assistConferenceCoordinatorCondition)
                                    <select id="cord_region" name="cord_region" class="form-control" required>
                                        <option value="">Select Region</option>
                                        @foreach($regionList as $rl)
                                            <option value="{{$rl->id}}">{{$rl->long_name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" name="cord_region" id="cord_region" class="form-control" placeholder="Home Chapter" value="{{ $region[0]->long_name }}" readonly>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Position:</label>
                            <div class="col-sm-8">
                                <input type="text" name="cord_position" id="cord_position" class="form-control" placeholder="Home Chapter" value="Big Sister" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Reports To:</label>
                            <div class="col-sm-8">
                                <input type="text" name="cord_conf" id="cord_conf" class="form-control" placeholder="Home Chapter" value="{{ $corReportTo }}" readonly>
                            </div>
                        </div>

                        {{-- <div class="form-group row mt-1">
                            <label class="col-sm-4 col-form-label">Reports To:</label>
                            <div class="col-sm-8">
                                <select name="cord_report_pc" id="cord_report_pc" class="form-control" required>
                                    @foreach($primaryCoordinatorList as $pcl)
                                        <option value="{{ $pcl->cid }}" >{{ $pcl->cor_f_name }} {{ $pcl->cor_l_name }} ({{ $pcl->pos }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Home Chapter:</label>
                            <div class="col-sm-8">
                                <input type="text" name="cord_chapter" id="cord_chapter" class="form-control" placeholder="Home Chapter" required>
                            </div>
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
                                    <input type="text" name="cord_fname" id="cord_fname" class="form-control" placeholder="First Name" required >
                                    </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_lname" id="cord_lname" class="form-control" placeholder="Last Name" required >
                                    </div>
                                </div>
                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Email/Secondary:</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_email" id="cord_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" placeholder="Email"  required >
                                    <input type="hidden" id="cord_email_chk" >
                                </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_sec_email"class="form-control"  placeholder="Secondary Email">
                                    </div>
                                </div>
                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Phone/Alternate:</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_phone" id="cord_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask placeholder="Phone"  required >
                                    </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_altphone" id="cord_altphone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask placeholder="Alternate Phone" >
                                    </div>
                                </div>
                                <!-- /.form group -->
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Address:</label>
                                    <div class="col-sm-10">
                                    <input type="text" name="cord_addr" id="cord_addr" class="form-control" placeholder="Address" required >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label"><br></label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_city" id="cord_city" class="form-control" placeholder="City" required >
                                    </div>
                                    <div class="col-sm-3">
                                        <select id="cord_state" name="cord_state" class="form-control" style="width: 100%;" required>
                                            <option value="">Select State</option>
                                            @foreach($stateArr as $state)
                                                <option value="{{$state->id}}">{{$state->state_long_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" name="cord_zip" id="cord_zip" class="form-control" placeholder="Zip" required >
                                    </div>
                                </div>
                                 <!-- /.form group -->
                                 <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Birthday:</label>
                                    <div class="col-sm-3">
                                        <select name="cord_month" class="form-control" style="width: 100%;" required>
                                            <option value="">Select Month</option>
                                                @foreach($monthArr as $month)
                                                    <option value="{{$month->id}}" >{{$month->month_long_name}}</option>
                                                @endforeach
                                        </select>

                                    </div>
                                    <div class="col-sm-2">
                                    <input type="text" name="cord_day" id="cord_day" class="form-control" placeholder="Day" required>
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
                <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return validateEmailsBeforeSubmit();"><i class="fas fa-save mr-2"></i>Save New Coordinator</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.coordlist') }}'"><i class="fas fa-reply mr-2"></i>Back to Coordinator List</button>
            </div>
        </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
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
