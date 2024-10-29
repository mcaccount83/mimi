@extends('layouts.coordinator_theme')
<style>

.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')


  <!-- Contains page content -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Coordinator Details</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
              <li class="breadcrumb-item active">Coordinator Details</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
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
                    <li class="list-group-item text-center">
                        <b> {{ $coordinatorDetails[0]->position }}</b>
                        @if ($coordinatorDetails[0]->sec_position != null )
                        <br>
                        <b> {{ $coordinatorDetails[0]->sec_position }}</b>
                        @endif
                    </li>
                    <li class="list-group-item">
                        <b>Start Date:</b> <span class="float-right date-mask">{{ $coordinatorDetails[0]->coordinator_start_date }}</span>
                        <br>
                        <b>Promotion Date:</b> <span class="float-right date-mask">{{ $coordinatorDetails[0]->last_promoted }}</span>
                        <br>
                        <b>Home Chapter:</b> <span class="float-right">{{ $coordinatorDetails[0]->home_chapter }}</span>
                    </li>
                    <input type="hidden" id="report_to" value="{{ $coordinatorDetails[0]->report_id }}">
                    <li id="display_corlist" class="list-group-item"></li>
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">General</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="general">
                    <div class="general-field">
                          <a href="mailto:{{ $coordinatorDetails[0]->email }}">{{ $coordinatorDetails[0]->email }}</a>
                          @if ($coordinatorDetails[0]->sec_email != null )
                          <br>
                          <a href="mailto:{{ $coordinatorDetails[0]->sec_email }}">{{ $coordinatorDetails[0]->sec_email }}</a>
                          @endif
                          <br>
                          <span class="phone-mask">{{$coordinatorDetails[0]->phone }}</span>
                          @if ($coordinatorDetails[0]->alt_phone != null )
                          <br>
                          <span class="phone-mask">{{$coordinatorDetails[0]->alt_phone }}</span>
                          @endif
                          <br><br>
                          {{$coordinatorDetails[0]->address}}
                          <br>
                          {{$coordinatorDetails[0]->city}},{{$coordinatorDetails[0]->state}}&nbsp;{{$coordinatorDetails[0]->zip}}
                          <br><br>
                          <p>This will reset password to default "TempPass4You" for this user only.
                          <br>
                          <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $coordinatorDetails[0]->user_id }}">Reset President Password</button>
                          </p>
                      </div>
                    </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center">

            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>

document.querySelectorAll('.reset-password-btn').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();

        const userId = this.getAttribute('data-user-id');
        const newPassword = "TempPass4You";

        $.ajax({
            url: '{{ route('updatepassword') }}',
            type: 'PUT',
            data: {
                user_id: userId,
                new_password: newPassword,
                _token: '{{ csrf_token() }}'
            },
            success: function(result) {
                Swal.fire({
                    title: 'Success!',
                    text: result.message.replace('<br>', '\n'),
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn-sm btn-success'
                    }
                });
            },
            error: function(jqXHR, exception) {
                console.log(jqXHR.responseText); // Log error response
            }
        });
    });
});


</script>
@endsection
