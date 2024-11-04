@extends('layouts.coordinator_theme')
<style>

.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')

 <!-- Content Wrapper. Contains page content -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Coordinator Profile</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Coordinator Profile</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("coordinators.updaterecognition",$coordinatorDetails[0]->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">{{ $coordinatorDetails[0]->first_name }}, {{ $coordinatorDetails[0]->last_name }}</h3>
                <p class="text-center">{{ $coordinatorDetails[0]->confname }} Conference
                    @if ($coordinatorDetails[0]->regname != "None")
                    , {{ $coordinatorDetails[0]->regname }} Region
                    @else
                    @endif
                </p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Supervising Coordinator:</b> <span class="float-right"><a href="mailto:{{ $coordinatorDetails[0]->report_email }}">{{ $coordinatorDetails[0]->report_fname }} {{ $coordinatorDetails[0]->report_lname }}</a></span>
                        <br>
                        <b>Display Position:</b> <span class="float-right">{{ $coordinatorDetails[0]->display_position }}</span>
                        <br>
                        <b>Primary Positon for MIMI Purposes:</b> <span class="float-right">{{ $coordinatorDetails[0]->position }}</span>
                        <br>
                        <b>Secondary Position:</b> <span class="float-right">{{ $coordinatorDetails[0]->sec_position }}</span>

                    </li>
                    <li class="list-group-item">
                        <b>Start Date:</b> <span class="float-right date-mask">{{ $coordinatorDetails[0]->coordinator_start_date }}</span>
                        <br>
                        <b>Last Promotion Date:</b> <span class="float-right date-mask">{{ $coordinatorDetails[0]->last_promoted }}</span>
                        <br>
                        <label>Home Chapter:</label><input type="text" name="cord_chapter" id="cord_chapter" class="form-control float-right col-sm-6 text-right" value="{{ $coordinatorDetails[0]->home_chapter }}" required placeholder="Home Chapter">
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
                <h3 class="profile-username">Appreciation & Recognition Information</h3>
                    <!-- /.card-header -->

                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">&lt; 1 Year:</label>
                            <div class="col-sm-4 mr-5">
                                <input type="text" name="recognition_year0"class="form-control" value="{{ $coordinatorDetails[0]->recognition_year0 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">1 Year:</label>
                            <div class="col-sm-4">
                                <input type="text" name="recognition_year1"class="form-control" value="{{ $coordinatorDetails[0]->recognition_year1 }}" >
                            </div>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">2 Years:</label>
                            <div class="col-sm-4 mr-5">
                                <input type="text" name="recognition_year2"class="form-control" value="{{ $coordinatorDetails[0]->recognition_year2 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">3 Years:</label>
                            <div class="col-sm-4">
                                <input type="text" name="recognition_year3"class="form-control" value="{{ $coordinatorDetails[0]->recognition_year3 }}" >
                            </div>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">4 Years:</label>
                            <div class="col-sm-4 mr-5">
                                <input type="text" name="recognition_year4"class="form-control" value="{{ $coordinatorDetails[0]->recognition_year4 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">5 Years:</label>
                            <div class="col-sm-4">
                                <input type="text" name="recognition_year5"class="form-control" value="{{ $coordinatorDetails[0]->recognition_year5 }}" >
                            </div>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">6 Years:</label>
                            <div class="col-sm-4 mr-5">
                                <input type="text" name="recognition_year6"class="form-control" value="{{ $coordinatorDetails[0]->recognition_year6 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">7 Years:</label>
                            <div class="col-sm-4">
                                <input type="text" name="recognition_year7"class="form-control" value="{{ $coordinatorDetails[0]->recognition_year7 }}" >
                            </div>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">8 Years:</label>
                            <div class="col-sm-4 mr-5">
                                <input type="text" name="recognition_year8"class="form-control" value="{{ $coordinatorDetails[0]->recognition_year8 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">9 Years:</label>
                            <div class="col-sm-4">
                                <input type="text" name="recognition_year9"class="form-control" value="{{ $coordinatorDetails[0]->recognition_year9 }}" >
                            </div>

                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">10 Years+ or Top Tier:</label>
                            <div class="col-sm-9">
                                <input type="text" name="recognition_toptier"class="form-control" value="{{ $coordinatorDetails[0]->recognition_toptier }}" >
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="ml-2 col-form-label mr-2">MC Necklace:</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="recognition_necklace" id="recognition_necklace" class="custom-control-input"
                                    {{$coordinatorDetails[0]->recognition_necklace == 1 ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="recognition_necklace"></label>
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
                <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save" ></i>&nbsp;&nbsp;&nbsp;Save</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordreports.coordrptappreciation') }}'">Back to Appreciation Report</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.view', ['id' => $coordinatorDetails[0]->id]) }}'">Back to Coordinator Details</button>
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
