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

</style>

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" action='{{ route("member.update",$chDetails->id) }}'>
                        @csrf

                        <input type="hidden" id="ch_name" value="{{$chDetails->name}}">
                        <input type="hidden" id="ch_state" value="{{$stateShortName}}">
                        <input type="hidden" name="ch_hid_webstatus" value="{{ $chDetails->website_status }}">
                        <input  type="hidden" id="bor_email_chk" value="{{ $borDetails->email }}">

                        <div class="col-md-12">
                            <div class="card card-widget widget-user">
                                <div class="widget-user-header bg-primary">
                                    <div class="widget-user-image">
                                        <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}theme/dist/img/logo.png" alt="MC" style="width: 115px; height: 115px;">
                                    </div>
                                </div>
                                <div class="card-body">
                                    @php
                                        $thisDate = \Illuminate\Support\Carbon::now();
                                    @endphp
                                    <div class="col-md-12"><br><br></div>
                                    <h2 class="text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                                    <h2 class="text-center">{{$borDetails->first_name}} {{$borDetails->last_name}}, {{$borDetails->position->position}}</h2>
                                    <p class="description text-center">
                                        Welcome to the MOMS information Management Interface, affectionately called MIMI!
                                        <br>Here you can view your chapter's information, update your profile, complete End of Year Reports, etc.
                                    </p>
                                    <div id="readOnlyText" class="description text-center">
                                        @if($thisDate->month >= 5 && $thisDate->month <= 7)
                                            <p><span style="color: red;">All Board Member Information is <strong>READ ONLY</strong> at this time.<br>
                                                @if($chDocuments->new_board_active != '1')
                                                    In order to add new board members to MIMI, please complete the Board Election Report.<br>
                                                @endif
                                                @if($chDocuments->new_board_active == '1')
                                                    If you need to make updates to your listed officers, please contact your Primary Coordinator.</span></p>
                                                <p>Incoming Board Members have been activated and have full MIMI access.<br>
                                                    Outgoing Board Members can still log in and access Financial Reports Only.</p>
                                                @endif
                                            @endif
                                    </div>
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

                            <h5>Board Info</h5>
                    <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-2 mb-1 col-form-label">{{$borDetails->position->position}}:</label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="bor_fname" id="bor_fname" class="form-control" value="{{ $borDetails->first_name }}" required placeholder="First Name" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="bor_lname" id="bor_lname" class="form-control" value="{{ $borDetails->last_name }}" required placeholder="Last Name">
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="bor_email" id="bor_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  value="{{ $borDetails->email }}" required placeholder="Email Address" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="bor_phone" id="bor_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $borDetails->phone }}" required placeholder="Phone Number" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-10 mb-1">
                            <input type="text" name="bor_addr" id="bor_addr" class="form-control" placeholder="Address" value="{{ $borDetails->street_address }}" required >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"><br></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="bor_city" id="bor_city" class="form-control" placeholder="City" value="{{ $borDetails->city }}" required >
                            </div>
                            <div class="col-sm-3 mb-1">
                                <select name="bor_state" id="bor_state" class="form-control select2" style="width: 100%;" required >
                                    <option value="">Select State</option>
                                    @foreach($allStates as $state)
                                    <option value="{{$state->state_short_name}}"
                                        @if($borDetails->state == $state->state_short_name) selected @endif>
                                        {{$state->state_long_name}}
                                    </option>
                                @endforeach
                                        {{-- @foreach($stateArr as $state)
                                          <option value="{{$state->state_short_name}}" {{$borDetails->state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                                        @endforeach --}}
                                    </select>
                            </div>
                            <div class="col-sm-2 mb-1">
                                <input type="text" name="bor_zip" id="bor_zip" class="form-control" value="{{ $borDetails->zip }}" placeholder="Zip" required >
                            </div>
                        </div>

                        <h5>Chapter Info</h5>
                        <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Email:</label>
                            <div class="col-sm-5">
                            <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" value="{{ $chDetails->inquiries_contact }}" placeholder="Inquiries Email Address" required>
                            </div>
                            <div class="col-sm-5">
                            <input type="text" name="ch_email" id="ch_email" class="form-control" value="{{ $chDetails->email }}" placeholder="Chapter Email Address">
                            </div>
                        </div>
                        <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Mailing:</label>
                            <div class="col-sm-10">
                            <input type="text" name="ch_pobox" id="ch_pobox" class="form-control" value="{{ $chDetails->po_box }}" placeholder="Chapter PO Box/Mailing Address" >
                            </div>
                        </div>

                        <hr>
                        <h5>Website & Social Media</h5>
                         <!-- URL Field -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Website:</label>
                            <div class="col-sm-10">
                                <input type="text" name="ch_website" id="ch_website" class="form-control"
                                    value="{{$chDetails->website_url}}"
                                    placeholder="Chapter Website">
                            </div>
                        </div>

                        <!-- Web Status Field -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Web Status</label>
                            <div class="col-sm-5">
                                <select name="ch_webstatus" id="ch_webstatus" class="form-control" style="width: 100%;"
                                @if($chDetails->website_url) required @endif>
                                <option value="">Select Status</option>
                                    @foreach($allWebLinks as $status)
                                    <option value="{{$status->id}}"
                                        @if($chDetails->website_status == $status->id) selected @endif>
                                        {{$status->link_status}}
                                    </option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Social Media:</label>
                            <div class="col-sm-5">
                            <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chDetails->egroup }}"  placeholder="Forum/Group/App" >
                            </div>
                            <div class="col-sm-5">
                            <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chDetails->social1 }}" placeholder="Facebook"  >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-5">
                                <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chDetails->social2 }}"  placeholder="Twitter" >
                            </div>
                            <div class="col-sm-5">
                                <input type="text" name="ch_social3" id="ch_social3" class="form-control" value="{{ $chDetails->social3 }}"  placeholder="Instagram" >
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

            <div class="col-md-4">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">EIN:</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $chDetails->ein}}</span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Founded:</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $startMonthName }} {{ $chDetails->start_year }}</span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Boundaries:</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $chDetails->territory}}</span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Dues Paid:</label>
                            <div class="col-sm-8">
                                <span class="float-right">
                                    @if ($chDetails->members_paid_for)
                                        <b>{{ $chDetails->members_paid_for }} Members</b> on <b><span class="date-mask">{{ $chDetails->dues_last_paid }}</span></b>
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">M2M Donation:</label>
                            <div class="col-sm-8">
                                <span class="float-right">
                                    @if ($chDetails->m2m_payment)
                                        <b>${{ $chDetails->m2m_payment }}</b> on <b><span class="date-mask">{{ $chDetails->m2m_date }}</span></b>
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Sustaining Donation:</label>
                            <div class="col-sm-8">
                                <span class="float-right">
                                    @if ($chDetails->sustaining_donation)
                                        <b>${{ $chDetails->sustaining_donation }}</b> on <b><span class="date-mask">{{ $chDetails->sustaining_date }}</span></b>
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>

                        <span style="color: red;">If anything in this section needs to be updated, please contact your Primary Coordinator.</span><br>

                        <ul class="list-group list-group-unbordered mt-2 mb-3">
                            <li class="list-group-item">

                        <h5>Re-Registration Dues</h5>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Anniversary Month</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $startMonthName }}</span>
                            </div>
                        </div>

                        @if ($thisDate->gte($due_date))
                            @if ($due_date->month === $thisDate->month)
                                <span style="color: green;">Your Re-registration payment is due now.<br>
                            @else
                                <span style="color: red;">Your Re-registration payment is now considered overdue.<br>
                            @endif
                            @if($user_type === 'coordinator')
                                <button type="button" class="btn btn-primary btn-sm mt-1 mb-1" onclick="window.location.href='{{ route('chapter.viewreregpayment', ['id' => $chDetails->id]) }}'">PAY HERE</button>
                            @else
                                <button type="button" class="btn btn-primary btn-sm mt-1 mb-1" onclick="window.location.href='{{ route('board.showreregpayment') }}'">PAY HERE</button>
                            @endif
                        @else
                            Your Re-registration payment is not due at this time.
                        @endif
                        </li>

                      <li class="list-group-item">
                        <h5>Resources</h5>
                            @if($chDocuments->ein_letter_path != null)
                                <button type="button" class="btn bg-primary btn-sm mb-1" onclick="window.open('{{ $chDocuments->ein_letter_path }}', '_blank')">View/Download EIN Letter</button><br>
                            @else
                                <button type="button" class="btn bg-primary btn-sm mb-1 disabled">No EIN Letter on File</button><br>
                            @endif
                            <button id="GoodStanding" type="button" class="btn bg-primary mb-1 btn-sm" onclick="window.open('{{ route('pdf.chapteringoodstanding', ['id' => $chDetails->id]) }}', '_blank')">Good Standing Chapter Letter</button><br>
                            <button id="eLearning" type="button"  onclick="window.open('https://momsclub.org/elearning/')" class="btn bg-primary mb-1 btn-sm">eLearning Library</button><br>
                            <button id="Resources" class="btn bg-primary mb-1 btn-sm" onclick="window.location='{{ route('board.resources') }}'">Chapter Resources</button>
                      </li>
                      <li class="list-group-item">
                            <h5>End of year Filing</h5>

                            @if($user_type === 'coordinator' && $chDocuments->new_board_active!='1')
                                @if($display_testing)
                                    <button id="BoardReport" type="button" class="btn btn-primary btn-sm mb-1" onclick="window.location.href='{{ route('viewas.viewchapterboardinfo', ['id' => $chDetails->id]) }}'">
                                        {{ date('Y') . '-' . (date('Y') + 1) }} Board Report *TESTING*
                                    </button><br>
                                @elseif ($display_live)
                                    <button id="BoardReport" type="button" class="btn btn-primary btn-sm mb-1" onclick="window.location.href='{{ route('viewas.viewchapterboardinfo', ['id' => $chDetails->id]) }}'">
                                        {{ date('Y') . '-' . (date('Y') + 1) }} Board Report
                                    </button><br>
                                @else
                                    <button id="BoardReport" class="btn btn-primary btn-sm mb-1 disabled">Board Report Not Available</button><br>
                                @endif
                            @elseif($user_type === 'coordinator' && $chDocuments->new_board_active =='1')
                                @if($display_testing)
                                    <button id="BoardReport" class="btn btn-primary btn-sm mb-1 disabled">Board Report Activated *TESTING*</button><br>
                                @else
                                    <button id="BoardReport" class="btn btn-primary btn-sm mb-1 disabled">Board Report Activated</button><br>
                                @endif
                            @elseif ($thisDate->month >= 5 && $thisDate->month <= 12 && $display_live)
                                @if($chDocuments->new_board_active!='1')
                                    <button id="BoardReport" type="button" class="btn btn-primary btn-sm mb-1" onclick="window.location.href='{{ route('boardinfo.showboardinfo', ['id' => $chDetails->id]) }}'">
                                        {{ date('Y') . '-' . (date('Y') + 1) }} Board Report
                                    </button><br>
                                @else
                                    <button id="BoardReport" class="btn btn-primary btn-sm mb-1 disabled">Board Report Activated</button><br>
                                @endif
                            @else
                                <button id="BoardReport" class="btn btn-primary btn-sm mb-1 disabled">Board Report Not Available</button><br>
                            @endif

                            @if($user_type === 'coordinator')
                                @if($display_testing)
                                    <button id="FinancialReport" type="button" class="btn btn-primary btn-sm mb-1" onclick="window.location.href='{{ route('viewas.viewchapterfinancial', ['id' => $chDetails->id]) }}'">
                                        {{ date('Y')-1 .'-'.date('Y') }} Financial Report *TESTING*
                                    </button><br>
                                @elseif (($display_live))
                                    <button id="FinancialReport" type="button" class="btn btn-primary btn-sm mb-1" onclick="window.location.href='{{ route('viewas.viewchapterfinancial', ['id' => $chDetails->id]) }}'">
                                        {{ date('Y')-1 .'-'.date('Y') }} Financial Report
                                    </button><br>
                                @else
                                    <button id="990NLink" class="btn btn-primary btn-sm mb-1 disabled">Financial Report Not Available</button>
                                @endif
                            @elseif($thisDate->month >= 6 && $thisDate->month <= 12 && $display_live)
                                <button id="FinancialReport" type="button" class="btn btn-primary btn-sm mb-1" onclick="window.location.href='{{ route('board.showfinancial', ['id' => $chDetails->id]) }}'">
                                    {{ date('Y')-1 .'-'.date('Y') }} Financial Report
                                </button><br>
                            @else
                                <button id="FinancialReport" class="btn btn-primary btn-sm mb-1 disabled">Financial Report Not Available</button><br>
                            @endif

                            @if($thisDate->month >= 7 && $thisDate->month <= 12)
                                <a href="https://sa.www4.irs.gov/sso/ial1?resumePath=%2Fas%2F5Ad0mGlkzW%2Fresume%2Fas%2Fauthorization.ping&allowInteraction=true&reauth=false&connectionId=SADIPACLIENT&REF=3C53421849B7D5B806E50960DF0AC7530889D9ADE9238D5D3B8B00000069&vnd_pi_requested_resource=https%3A%2F%2Fsa.www4.irs.gov%2Fepostcard%2F&vnd_pi_application_name=EPOSTCARD"
                                    class="btn btn-primary btn-sm mb-1" target="_blank" >{{ date('Y')-1 }} 990N IRS Online Filing</a>
                            @else
                                <button id="990NLink" class="btn btn-primary btn-sm mb-1 disabled">990N Not Available Until July 1st</button>
                            @endif
                    </li>
                  </ul>

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
        <button id="Save" type="submit" class="btn btn-primary" onclick="return PreSaveValidate()"><i class="fas fa-save" ></i>&nbsp; Save</button>

    </form>
        <button id="Password" type="button" class="btn btn-primary" onclick="showChangePasswordAlert()"><i class="fas fa-lock" ></i>&nbsp; Change Password</button>
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
    var currentDate = new Date();
    var currentMonth = currentDate.getMonth() + 1; // JavaScript months are 0-based
    var userType = @json($user_type);

    if (userType === 'coordinator') {
        // Disable all input fields, select elements, textareas, and buttons
        $('input, select, textarea').prop('disabled', true);
        $('#Save, #Password, #logout-btn, #eLearning, #Resources').prop('disabled', true);
        // Disable links by adding a class and modifying their behavior
        $('#display_corlist').addClass('disabled-link').attr('href', '#');

    } else if (currentMonth >= 5 && currentMonth <= 7) {
        // Disable all input fields, select elements, textareas, and Save button
        $('input, select, textarea').prop('disabled', true);
        $('#Save').prop('disabled', true);
    } else {
        // If the condition is not met, keep the fields active
        $('input, select, textarea').prop('disabled', false);
        $('#Save').prop('disabled', false);
    }

    // Check the disabled status of EOY Buttons and show the "fields are locked" description if necessary
    if ($('input, select, textarea').prop('disabled')) {
        $('.description').show();
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const websiteField = document.getElementById("ch_website");
    const statusField = document.getElementById("ch_webstatus");
    const hiddenStatus = document.querySelector('input[name="ch_hid_webstatus"]');

    // Get the current saved value
    const savedValue = hiddenStatus.value;
    const originalUrl = websiteField.value;

    // Function to disable options 0 and 1
    const disableRestrictedOptions = () => {
        Array.from(statusField.options).forEach(option => {
            if (option.value === "0" || option.value === "1") {
                option.disabled = true;
            }
        });
    };

    // Function to update status field requirements
    const updateStatusRequirement = () => {
        if (websiteField.value.trim() === '') {
            statusField.removeAttribute('required');
            statusField.value = ''; // Optionally clear the status
        } else {
            statusField.setAttribute('required', 'required');
        }
    };

    // Initial setup
    disableRestrictedOptions();
    updateStatusRequirement();

    // Ensure the saved value is selected if URL exists
    if (originalUrl && statusField.value) {
        statusField.value = savedValue;
    }

    // Add event listener for website URL changes
    websiteField.addEventListener("input", function() {
        if (this.value !== originalUrl) {
            statusField.value = "";
            disableRestrictedOptions();
        }
        updateStatusRequirement();
    });
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

function PreSaveValidate(){
    var errMessage="";
          if($("#bor_email").val() == ""){
              errMessage = "Email is required, please enter a valid email address.";
            }

            if($("#ch_inqemailcontact").val() == ""){
              errMessage = "Inquiries Email is required, please enter a valid email address.";
            }

          if(errMessage.length > 0){
            alert (errMessage);
            return false;
          }

    return true;
}

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

function showChangePasswordAlert() {
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
