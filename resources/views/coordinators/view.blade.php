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
                    {{-- <li class="list-group-item text-center">
                        <b> {{ $coordinatorDetails[0]->position }}</b>
                        @if ($coordinatorDetails[0]->sec_position != null )
                        <br>
                        <b> {{ $coordinatorDetails[0]->sec_position }}</b>
                        @endif
                    </li> --}}
                    <li class="list-group-item">
                        <b>Supervising Coordinator:</b> <span class="float-right">{{ $coordinatorDetails[0]->report_fname }} {{ $coordinatorDetails[0]->report_lname }}</span>
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
                        <b>Home Chapter:</b> <span class="float-right">{{ $coordinatorDetails[0]->home_chapter }}</span>
                    </li>
                </ul>
                <div class="text-center">
                    @if ($coordinatorDetails[0]->is_active == 1 && $coordinatorDetails[0]->on_leave != 1)
                        <b><span style="color: #28a745;">Coordinator is ACTIVE</span></b>
                    @elseif ($coordinatorDetails[0]->is_active == 1 && $coordinatorDetails[0]->on_leave == 1)
                        <b><span style="color: #ff851b;">Coordinator is ON LEAVE</span></b>
                        <br>
                        Leave Date: <span class="date-mask">{{ $coordinatorDetails[0]->leave_date }}</span><br>
                    @else
                        <b><span style="color: #dc3545;">Coordinator is RETIRED</span></b>
                        <br>
                        Retired Date: <span class="date-mask">{{ $coordinatorDetails[0]->zapped_date }}</span><br>
                        {{ $coordinatorDetails[0]->reason_retired }}
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
                <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">Chapters & Coordinators</a></li>
                  <li class="nav-item"><a class="nav-link" href="#contact" data-toggle="tab">Contact Information</a></li>
                  <li class="nav-item"><a class="nav-link" href="#recog" data-toggle="tab">Appreciation & Recognitions</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="general">
                    <div class="general-field">
                        <h3 class="profile-username">Chapters & Coordinators
                            <button class="btn bg-gradient-primary btn-xs" onclick="window.location.href='{{ route('coordreports.coordrptreportingtree') }}'">View Coordinator Reporting Tree</button>
                        </h3>
                        <div class="row">
                        <div class="col-sm-6">
							<div class="form-group">
							    <label class="mrg-b-25">Coordinators Directly Reporting to {{ $coordinatorDetails[0]->first_name }}:</label>

                                <table id="coordinator-list" width="100%">
                                    <thead>
                                        @if($directReportTo->isEmpty())
                                            <tr>
                                                <td colspan="3" class="text-center">No Coordinators Found</td>
                                            </tr>
                                        @else
                                        <tr>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Position</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($directReportTo as $coordinator)
                                            <tr>
                                                <td>{{ $coordinator->cor_f_name }}</td>
                                                <td>{{ $coordinator->cor_l_name }}</td>
                                                <td>{{ $coordinator->pos }}</td>
                                            </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
                                <label class="mrg-b-25">{{ $coordinatorDetails[0]->first_name }} is Primary Coordinator For:</label>
                                    <table id="coordinator-list" width="100%">
                                        <thead>
                                            @if($directChapterTo->isEmpty())
                                                <tr>
                                                    <td colspan="2" class="text-center">No Chapters Found</td>
                                                </tr>
                                            @else
                                            <tr>
                                                <th>State</th>
                                                <th>Chapter Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($directChapterTo as $chapter)
                                                <tr>
                                                    <td>{{ $chapter->st_name }}</td>
                                                    <td>{{ $chapter->ch_name }}</td>
                                                </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="contact">
                    <div class="contact-field">
                        <h3 class="profile-username">Contact Information</h3>
                        <div class="row">
                            <div class="col-md-6">
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
                                <br>
                                {{$coordinatorDetails[0]->address}}
                                <br>
                                {{$coordinatorDetails[0]->city}},{{$coordinatorDetails[0]->state}}&nbsp;{{$coordinatorDetails[0]->zip}}
                            </div>
                            <div class="col-md-6">
                                Birthday: {{$coordinatorDetails[0]->birthday_month}} {{$coordinatorDetails[0]->birthday_day}}<br>
                                Card Sent: <span class="date-mask">{{ $coordinatorDetails[0]->card_sent }}</span><br>
                                @if ($assistConferenceCoordinatorCondition)
                                    <button class="btn bg-gradient-primary btn-sm" onclick="window.location.href='{{ route('coordreports.coordrptbirthdaysview', ['id' => $coordinatorDetails[0]->id]) }}'">Update Birthday Card Sent</button>
                                @endif
                            </div>
                        </div>

                            <br>
                            <p>This will reset password to default "TempPass4You" for this user only.
                            <br>
                            <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $coordinatorDetails[0]->user_id }}">Reset President Password</button>
                            </p>
                      </div>
                    </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="recog">
                    <div class="recog-field">
                        <h3 class="profile-username">Appreciation & Recognitions</h3>
                        <dl class="row">
                            <dt class="col-sm-2">&lt; 1 Year</dt>
                            <dd class="col-sm-4">{{$coordinatorDetails[0]->recognition_year0}}</dd>
                            <dt class="col-sm-2">1 Year</dt>
                            <dd class="col-sm-4">{{$coordinatorDetails[0]->recognition_year1}}</dd>
                            <dt class="col-sm-2">2 Years</dt>
                            <dd class="col-sm-4">{{$coordinatorDetails[0]->recognition_year2}}</dd>
                            <dt class="col-sm-2">3 Years</dt>
                            <dd class="col-sm-4">{{$coordinatorDetails[0]->recognition_year3}}</dd>
                            <dt class="col-sm-2">4 Years</dt>
                            <dd class="col-sm-4">{{$coordinatorDetails[0]->recognition_year4}}</dd>
                            <dt class="col-sm-2">5 Years</dt>
                            <dd class="col-sm-4">{{$coordinatorDetails[0]->recognition_year5}}</dd>
                            <dt class="col-sm-2">6 Years</dt>
                            <dd class="col-sm-4">{{$coordinatorDetails[0]->recognition_year6}}</dd>
                            <dt class="col-sm-2">7 Years</dt>
                            <dd class="col-sm-4">{{$coordinatorDetails[0]->recognition_year7}}</dd>
                            <dt class="col-sm-2">8 Years</dt>
                            <dd class="col-sm-4">{{$coordinatorDetails[0]->recognition_year8}}</dd>
                            <dt class="col-sm-2">9 Years</dt>
                            <dd class="col-sm-4">{{$coordinatorDetails[0]->recognition_year9}}</dd>
                            <dt class="col-sm-2">Top Tier</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_toptier}}</dd>
                            <dt class="col-sm-2">MC Necklace</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_necklace == 1 ? 'YES' : 'NO' }}</dd>
                          </dl>
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
                @if ($regionalCoordinatorCondition)
                    <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.editrole', ['id' => $coordinatorDetails[0]->id]) }}'">Update Role, Chapters & Coordinators</button>
                    <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.editdetails', ['id' => $coordinatorDetails[0]->id]) }}'">Update Contact Information</button>
                @endif
                @if($assistConferenceCoordinatorCondition)
                    <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.editrecognition', ['id' => $coordinatorDetails[0]->id]) }}'">Update Appreciation & Recognition</button>
                    <br>
                    @if($corIsLeave != 1)
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="onLeaveCoordinator()">Put Coordinator On Leave</button>
                        {{-- <button class="btn bg-gradient-primary mb-3 coordOnLeave" data-onleave="{{ $coordinatorDetails[0]->on_leave }}" data-coordId="{{ $coordinatorDetails[0]->id }}">Put Coordinator On Leave</button> --}}
                    @elseif($corIsLeave == 1)
                        <button type="button" id="unretire" class="btn bg-gradient-primary mb-3" onclick="removeLeaveCoordinator()">Remove Coordinator From Leave</button>
                        {{-- <button type="button" class="btn bg-gradient-primary mb-3 coordRemoveLeave" data-onleave="{{ $coordinatorDetails[0]->on_leave }}" data-coordId="{{ $coordinatorDetails[0]->id }}">Remove Coordinator From Leave</button> --}}
                    @endif
                    @if($corIsActive == 1)
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="retireCoordinator()">Retire Coordinator</button>
                    @elseif($corIsActive != 1)
                        <button type="button" id="unretire" class="btn bg-gradient-primary mb-3" onclick="unRetireCoordinator()">UnRetire Coordinator</button>
                    @endif
                @endif
                <br>
                @if ($corConfId == $userConfId)
                    @if ($corIsActive == 1)
                        <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.coordlist') }}'">Back to Coordinator List</button>
                    @else
                        <button id="back-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.coordretired') }}'">Back to Retired Coordinator List</button>
                    @endif
                @elseif ($adminReportCondition  && ($corConfId != $userConfId))
                    @if ($corIsActive == 1)
                        <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intcoord') }}'">Back to International Coordinator List</button>
                    @else
                        <button id="back-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intcoordretired') }}'">Back to International Retired Coordinator List</button>
                    @endif
                @endif
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

var $corIsActive = {{ $corIsActive }};

$(document).ready(function () {
    // Disable fields for chapters that are not active
    if ($corIsActive != 1)
        $('input, select, textarea, button').prop('disabled', true);

        $('a[href^="mailto:"]').each(function() {
            $(this).addClass('disabled-link'); // Add disabled class for styling
            $(this).attr('href', 'javascript:void(0);'); // Prevent navigation
            $(this).on('click', function(e) {
                e.preventDefault(); // Prevent link click
            });
        });

        $('#back-zapped').prop('disabled', false);
        $('#unretire').prop('disabled', false);

});

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

function onLeaveCoordinator(coordId) {
    Swal.fire({
        title: 'Coordinator On Leave',
        html: `
            <p>This will mark the coordinator On Leave. Please confirm by pressing OK.</p>

            <input type="hidden" id="coord_id" name="coord_id" value="{{ $coordinatorDetails[0]->id }}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const coordId = Swal.getPopup().querySelector('#coord_id').value;

            return {
                coord_id: coordId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Perform the AJAX request
            $.ajax({
                url: '{{ route('coordinators.updateonleave') }}',
                type: 'POST',
                data: {
                        coord_id: data.coord_id,
                        _token: '{{ csrf_token() }}'
                    },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,  // Automatically close without "OK" button
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function(jqXHR, exception) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong, Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    });
                }
            });
        }
    });
}

function removeLeaveCoordinator(coordId) {
    Swal.fire({
        title: 'Coordinator Remove from Leave',
        html: `
            <p>This will remove the coordinator from Leave. Please confirm by pressing OK.</p>

            <input type="hidden" id="coord_id" name="coord_id" value="{{ $coordinatorDetails[0]->id }}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const coordId = Swal.getPopup().querySelector('#coord_id').value;

            return {
                coord_id: coordId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Perform the AJAX request
            $.ajax({
                url: '{{ route('coordinators.updateremoveleave') }}',
                type: 'POST',
                data: {
                        coord_id: data.coord_id,
                        _token: '{{ csrf_token() }}'
                    },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,  // Automatically close without "OK" button
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function(jqXHR, exception) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong, Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    });
                }
            });
        }
    });
}

function retireCoordinator(coordId) {
    Swal.fire({
        title: 'Coordinator Retire Reason',
        html: `
            <p>Marking a coordinator as retired will remove their login. Please enter the reason for retiring and press OK.</p>
            <div style="display: flex; align-items: center; ">
                <input type="text" id="reason_retired" name="reason_retired" class="swal2-input" placeholder ="Enter Reason" required style="width: 100%;">
            </div>
            <input type="hidden" id="coord_id" name="coord_id" value="{{ $coordinatorDetails[0]->id }}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const retireReason = Swal.getPopup().querySelector('#reason_retired').value;
            const coordId = Swal.getPopup().querySelector('#coord_id').value;

            if (!retireReason) {
                Swal.showValidationMessage('Please enter the reason for retiring.');
                return false;
            }

            return {
                reason_retired: retireReason,
                coord_id: coordId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Perform the AJAX request
            $.ajax({
                url: '{{ route('coordinators.updateretire') }}',
                type: 'POST',
                data: {
                        reason_retired: data.reason_retired,
                        coord_id: data.coord_id,
                        _token: '{{ csrf_token() }}'
                    },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,  // Automatically close without "OK" button
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function(jqXHR, exception) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong, Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    });
                }
            });
        }
    });
}

function unRetireCoordinator(coordId) {
    Swal.fire({
        title: 'Reactivate Coordinator',
        html: `
            <p>Reactivating a coordinator as retired will reset their login. Please verify this is what you want to do by pressing OK.</p>
            <input type="hidden" id="coord_id" name="coord_id" value="{{ $coordinatorDetails[0]->id }}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const coordId = Swal.getPopup().querySelector('#coord_id').value;

            return {
                coord_id: coordId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Perform the AJAX request
            $.ajax({
                url: '{{ route('coordinators.updateunretire') }}',
                type: 'POST',
                data: {
                        coord_id: data.coord_id,
                        _token: '{{ csrf_token() }}'
                    },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,  // Automatically close without "OK" button
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function(jqXHR, exception) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong, Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    });
                }
            });
        }
    });
}



</script>
@endsection
