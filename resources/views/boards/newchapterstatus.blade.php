@extends('layouts.board_theme')


<style>
    .ml-2 {
        margin-left: 0.5rem !important; /* Adjust the margin to control spacing for Vacant Buttons */
    }

    .custom-control-input:checked ~ .custom-control-label {
        color: black; /* Label color when toggle is ON for Vacant Buttons */
    }

    .custom-control-input:not(:checked) ~ .custom-control-label {
        color: #b0b0b0; /* Subdued label color when toggle is OFF for Vacant Buttons */
        opacity: 0.6;   /* Optional: Adds a subdued effectfor Vacant Buttons */
    }

    .disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #6c757d; /* Muted color */
}

.board-info {
    display: table;
    width: 100%;
    margin-bottom: 15px;
}
.info-row {
    display: table-row;
}
.info-label, .info-label-empty {
    display: table-cell;
    width: 150px;
    padding: 2px 10px 2px 0;
    vertical-align: top;
}
.info-label {
    font-weight: bold;
}
.info-data {
    display: table-cell;
    padding: 2px 0;
}

</style>

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                      <div class="col-md-12">
                            <div class="card card-widget widget-user">
                                <div class="widget-user-header bg-primary">
                                    <div class="widget-user-image">
                                        <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div class="col-md-12"><br><br></div>
                                    <h2 class="text-center">New Chapter Application</h2>
                                    {{-- <p class="description text-center">
                                        All chapters are in PENDING status until reviewed by our Coordintaor Team.<br>
                                        After review, you will receive an email communication from your Coordinator.<br>
                                        If you have not heard from your Coordintor within 5 days of your application, please reach out to them.<br>
                                        Sometimes messages may have ended up in spam or junk folders.  Their name and contact informatin is listed below.
                                    </p> --}}

                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>

    <div class="row">
            <div class="col-md-8">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                         <!-- /.card-header -->

                         <div class="row">
                            <div class="col-md-12">
                        <h5>Application Information</h5>

                        <div class="board-info">
                            <div class="info-row">
                                <div class="info-label">Name:</div>
                                <div class="info-data"> {{ $chDetails->name }}, {{$stateShortName}}</div>
                            </div>
                        </div>

                        <div class="board-info">
                            <div class="info-row">
                                <div class="info-label">Boundaries:</div>
                                <div class="info-data"> {{ $chDetails->territory}}</div>
                            </div>
                        </div>

                      <!-- /. group -->
                    <div class="board-info">
                        <div class="info-row">
                            <div class="info-label">Founder:</div>
                            <div class="info-data">{{ $chDetails->pendingPresident->first_name }} {{ $chDetails->pendingPresident->last_name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label-empty"></div>
                            <div class="info-data"><a href="mailto:{{ $chDetails->pendingPresident->email }}">{{ $chDetails->pendingPresident->email }}</a></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label-empty"></div>
                            <div class="info-data">{{ $chDetails->pendingPresident->phone }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label-empty"></div>
                            <div class="info-data">{{ $chDetails->pendingPresident->street_address }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label-empty"></div>
                            <div class="info-data">{{ $chDetails->pendingPresident->city }},
                                @if(is_numeric($chDetails->pendingPresident->state))
                                    @foreach($allStates as $state)
                                        @if($state->id == $chDetails->pendingPresident->state)
                                            {{ $state->state_short_name }}
                                        @endif
                                    @endforeach
                                @else
                                    {{ $chDetails->pendingPresident->state }}
                                @endif
                                {{ $chDetails->pendingPresident->zip }}</div>
                        </div>
                    </div>
                    </div>

                    @if ($chDetails->activeStatus->active_status === 'Pending')
                    <p>
                        <br>
                        Here are a few things to keep in mind as you start your MOMS Club journey.
                        <ul>
                            <li>All chapters are in PENDING status until reviewed by our Coordintaor Team.</li>
                            <li>After review, you will receive an email from your Coordinator to establish initial communication as well as verify/set your official chapter name
                                and boundaries.</li>
                            <li>After communication has been established, your credit card will be charged and your chapter will move to ACTIVE status</li>
                            <li>You will also see your Coordinator's contact information listed here in MIMI.  If you do not hear from them within a week of submitting your application, please reach out to them
                                directly as sometimes messages do end up in spam.</li>
                            <li>After your chapter has moved to ACTIVE status you'll see your MIMI options change to allow more access and infomration, but your login credentials will remain the same.</li>
                        </ul>
                    </p>
                    @endif

                </div>

            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->

            <div class="col-md-4">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">


                        <h5>Application Status</h5>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Submitted</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $chDetails->created_at }}</span>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Status</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $chDetails->activeStatus->active_status }}</span>
                            </div>
                        </div>

                        @if ($chDetails->activeStatus->active_status === 'Pending')
                            <span style="color: #dc3545;"><b>Your chapter will NOT be moved to Active Status until you have made contact with your Coordinator.</b></span><br>
                        @elseif ($chDetails->activeStatus->active_status === 'Not Approved')
                            <span style="color: #dc3545;"><b>Your application has been declined. Contact your Coordinator for more information.</b></span><br>
                        @endif

               <hr>
                        <h5>Coordinators</h5>
                  <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                  <input  type="hidden" id="pcid" value="{{ $chDetails->primary_coordinator_id}}">
                  <div id="display_corlist" ></div>

                    </div>
                <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>

    <div class="card-body text-center">

        <button id="Password" type="button" class="btn btn-primary" onclick="showChangePasswordAlert('{{ $chDetails->pendingPresident->user_id }}')"><i class="fas fa-lock" ></i>&nbsp; Change Password</button>
        <button id="logout-btn" class="btn btn-primary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-undo" ></i>&nbsp; Logout</button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

    </div>
    <!-- /.container- -->
@endsection
@section('customscript')
<script>
/* Disable fields and buttons  */
$(document).ready(function () {
    var userType = @json($userType);
    var userAdmin = @json($userAdmin);

    if (userAdmin == 1) {
        $('#Password, #logout-btn').prop('disabled', true);
    }else if (userType == 'coordinator' && userAdmin != 1) {
        // Disable all input fields, select elements, textareas, and buttons
        $('input, select, textarea').prop('disabled', true);
        $('#Save, #Password, #logout-btn').prop('disabled', true);
        // Disable links by adding a class and modifying their behavior
        $('#display_corlist').addClass('disabled-link').attr('href', '#');
    }

    // Check the disabled status of EOY Buttons and show the "fields are locked" description if necessary
    if ($('input, select, textarea').prop('disabled')) {
        $('.description').show();
    }
});

$( document ).ready(function() {
    var pcid = $("#pcid").val();
    if (pcid != "") {
        $.ajax({
            url: '{{ url("/load-coordinator-list/") }}' + '/' + pcid,
            type: "GET",
            success: function (result) {
                console.log("AJAX result:", result);
                $("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {
                console.log("AJAX error:", exception);
            }
        });
    }

    $('.cls-pswd').on('keypress', function(e) {
    if (e.which == 32)
        return false;
    });

});

function showChangePasswordAlert(user_id) {
    Swal.fire({
        title: 'Change Password',
        html: `
            <form id="changePasswordForm">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="swal2-input" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="swal2-input" required>
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="swal2-input" required>
                </div>
            <input type="hidden" id="user_id" name="user_id" value="${user_id}">
            </form>
        `,
        confirmButtonText: 'Update Password',
        cancelButtonText: 'Cancel',
        showCancelButton: true,
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const user_id = Swal.getPopup().querySelector('#user_id').value;
            const currentPassword = Swal.getPopup().querySelector('#current_password').value;
            const newPassword = Swal.getPopup().querySelector('#new_password').value;
            const confirmNewPassword = Swal.getPopup().querySelector('#new_password_confirmation').value;

            // Validate input fields
            if (!currentPassword || !newPassword || !confirmNewPassword) {
                Swal.showValidationMessage('Please fill out all fields');
                return false;
            }

            if (newPassword !== confirmNewPassword) {
                Swal.showValidationMessage('New passwords do not match');
                return false;
            }

            // Return the AJAX call as a promise to let Swal wait for it
            return $.ajax({
                url: '{{ route("checkpassword") }}',  // Check current password route
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    current_password: currentPassword
                }
            }).then(response => {
                if (!response.isValid) {
                    Swal.showValidationMessage('Current password is incorrect');
                    return false;
                }
                return {
                    user_id: user_id,
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: confirmNewPassword
                };
            }).catch(() => {
                Swal.showValidationMessage('Error verifying current password');
                return false;
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn-sm btn-success'
                },
                didOpen: () => Swal.showLoading()
            });

            // Send the form data via AJAX to update the password
            $.ajax({
                url: '{{ route("updatepassword") }}',
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: result.value.user_id,
                    current_password: result.value.current_password,
                    new_password: result.value.new_password,
                    new_password_confirmation: result.value.new_password_confirmation
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Your password has been updated.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    });
                },
                error: function(jqXHR) {
                    Swal.fire({
                        title: 'Error!',
                        text: `Something went wrong: ${jqXHR.responseText}`,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-danger'
                        }
                    });
                }
            });
        }
    });
}

</script>
@endsection
