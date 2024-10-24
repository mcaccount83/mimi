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

</style>

@section('content')

<div class="container">
<div class="row">
    <div class="col-md-12">
 <!-- Widget: user widget style 1 -->
 <div class="card card-widget widget-user">
    <!-- Add the bg color to the header using any of the bg-* classes -->
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
                    <h2 class="text-center"> MOMS Club of {{ $chapterDetails->name }}, {{$chapterState}} </h2>
                    <h4 class="text-center"> EIN: {{ $chapterDetails->ein }} </h4>
                    <h4 class="text-center">Boundaries: {{ $chapterDetails->territory }} </h4>
                <div class="col-md-12"><br><br></div>
                    <h4 class="text-center"> {{$borDetails->first_name}} {{$borDetails->last_name}}, {{$boardPositionAbbreviation}}</h4>
                    <p class="description text-center">
                           Welcome to the MOMS information Management Interface, affectionately called MIMI!
                           </br>Here you can view your chapter's information, update your profile, complete End of Year Reports, etc.
                        </p>
                <div id="readOnlyText" class="description text-center">
                        @if($thisDate->month >= 5 && $thisDate->month <= 7)
                            <p><span style="color: red;">All Board Member Information is <strong>READ ONLY</strong> at this time.<br>
                                @if($chapterList[0]->new_board_active != '1')
                                In order to add new board members to MIMI, please complete the Board Election Report.<br>
                            @endif
                            @if($chapterList[0]->new_board_active == '1')
                                If you need to make updates to your listed officers, please contact your Primary Coordinator.</span></p>
                            @endif
                            @if($chapterList[0]->new_board_active == '1')
                                <p>Incoming Board Members have been activated and have full MIMI access.<br>
                                    Outgoing Board Members can still log in and access Financial Reports Only.</p>
                            @endif
                        @endif
                </div>
                </div>

        @php
            $admin = DB::table('admin')
                ->select('admin.*',
                    DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'),)
                ->leftJoin('coordinators as cd', 'admin.updated_id', '=', 'cd.id')
                ->orderBy('admin.id', 'desc') // Assuming 'id' represents the order of insertion
                ->first();

            $eoy_boardreport = $admin->eoy_boardreport;
            $eoy_financialreport = $admin->eoy_financialreport;
            $boardreport_yes = ($eoy_boardreport == 1);
            $financialreport_yes = ($eoy_financialreport == 1);
        @endphp

                    <div class="card-body">
	                    <div class="row">
                        @if ($thisDate->gte($due_date))
                        <div class=" col-md-12 text-center">
                            @if ($due_date->month === $thisDate->month)
                            <p><span style="color: green;">
                                Your chapter's anniversary month is <strong>{{ $startMonth }}</strong>.&nbsp; Your Re-registration payment is due now.
                            </p>
                            @else
                            <p><span style="color: red;">
                                Your chapter's anniversary month was <strong>{{ $startMonth }}</strong>.&nbsp; Your Re-registration payment is now considered overdue.
                            </p>
                            @endif
                                <a href="{{ route('board.showreregpayment') }}" class="btn btn-primary">
                                    <i class="fas fa-dollar-sign"></i>&nbsp; PAY HERE
                                </a>
                            </div>
                            <div class="col-md-12"><br><br></div>
                        @endif
                        <div class="col-md-12 text-center">
                            <p><span >
                                Reports and Letters available for your chapter can be viewed/downloaded here.
                            </p>
                                @if($chapterDetails->ein_letter=='1')
                                    <a class="btn btn-primary" href="{{ $chapterDetails->ein_letter_path }}" target="blank">
                                        <i class="fas fa-university"></i>&nbsp; Chapter EIN Letter
                                    </a>
                                @else
                                    <a class="btn btn-primary disabled" href="#">
                                        <i class="fas fa-university"></i>&nbsp; No EIN Letter on File
                                    </a>
                                @endif
                                <button id="GoodStanding" type="button" class="btn btn-primary" onclick="window.open('{{ route('pdf.chapteringoodstanding', ['id' => $chapterDetails->id]) }}', '_blank')">
                                    <i class="fas fa-home"></i>&nbsp; Good Standing Chapter Letter
                                </button>

                                @if($financial_report_array->financial_pdf_path!=null)
                                    <a id="btn-download-pdf" href="https://drive.google.com/uc?export=download&id=<?php echo $financial_report_array['financial_pdf_path']; ?>" class="btn btn-primary" ><i class="fas fa-download" ></i>&nbsp; Financial Report PDF</a>
                                @else
                                    <button id="ReportPDF" type="button" class="btn btn-primary" onclick="">
                                        <i class="fas fa-file-pdf"></i>&nbsp; No Financial Report on File
                                    </button>
                                @endif
                        </div>
                        <div class="col-md-12"><br></div>

                        <div class="col-md-12 text-center">
                            <p><span >
                                End of Year Filing for your chapter should be done here.
                            </p>
                                 @if($thisDate->month >= 6 && $thisDate->month <= 12 && $boardreport_yes)
                                        @if($chapterDetails->new_board_active!='1')
                                            <button id="BoardReport" type="button" class="btn btn-primary" onclick="window.location.href='{{ route('boardinfo.showboardinfo', ['id' => $chapterDetails->id]) }}'">
                                                <i class="fas fa-users"></i>&nbsp; {{ date('Y') . '-' . (date('Y') + 1) }} Board Report
                                            </button>
                                        @else
                                            <a class="btn btn-primary disabled" href="#">
                                                <i class="fas fa-users"></i>&nbsp; Board Report Activated
                                            </a>
                                        @endif
                                @else
                                    <a class="btn btn-primary disabled" href="#">
                                        <i class="fas fa-users"></i>&nbsp; Board Report Not Available
                                    </a>
                                @endif
                                @if($thisDate->month >= 6 && $thisDate->month <= 12 && $financialreport_yes)
                                        <button id="FinancialReport" type="button" class="btn btn-primary" onclick="window.location.href='{{ route('board.showfinancial', ['id' => $chapterDetails->id]) }}'">
                                            <i class="fas fa-file-invoice-dollar"></i>&nbsp; {{ date('Y')-1 .'-'.date('Y') }} Financial Report
                                        </button>
                                @else
                                    <a class="btn btn-primary disabled" href="#">
                                        <i class="fas fa-file-invoice-dollar"></i>&nbsp; Financial Report Not Available
                                    </a>
                                @endif
                                @if($thisDate->month >= 7 && $thisDate->month <= 12)
                                    <a href="https://sa.www4.irs.gov/sso/ial1?resumePath=%2Fas%2F5Ad0mGlkzW%2Fresume%2Fas%2Fauthorization.ping&allowInteraction=true&reauth=false&connectionId=SADIPACLIENT&REF=3C53421849B7D5B806E50960DF0AC7530889D9ADE9238D5D3B8B00000069&vnd_pi_requested_resource=https%3A%2F%2Fsa.www4.irs.gov%2Fepostcard%2F&vnd_pi_application_name=EPOSTCARD"
                                        class="btn btn-primary" target="_blank" ><i class="fas fa-globe" ></i>&nbsp; {{ date('Y')-1 }} 990N IRS Online Filing</a>
                                @else
                                    <a class="btn btn-primary disabled" href="#" >
                                        <i class="fas fa-globe"></i>&nbsp; 990N Not Available Until July 1st
                                    </a>
                                @endif
                        </div>

                </div>
            </div>

        </div>
    </div>
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <form method="POST" action='{{ route("member.update",$borDetails->chapter_id) }}'>
				@csrf

                <input  type="hidden" class="form-control" name="bor_positionid" value="{{$borPositionId}}">

				@if($borPositionId == '2')
                <div class="card-header">
                    <h4 class="card-title">AVP</h4>
                </div>
                @endif

				@if($borPositionId == '3')
                <div class="card-header">
                    <h4 class="card-title">MVP</h4>
                </div>
                @endif

				@if($borPositionId == '4')
                <div class="card-header">
                    <h4 class="card-title">TREASURER</h4>
                </div>
                @endif

				@if($borPositionId == '5')
                <div class="card-header">
                    <h4 class="card-title">SECRETARY</h4>
                </div>
        		@endif

        	   <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>First Name</label> <span class="field-required">*</span>
                            <input   type="text" name="bor_fname" id="bor_fname" class="form-control" placeholder="First Name" value="{{ $borDetails->first_name }}"  required  onkeypress="return isAlphanumeric(event)" >
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Last Name</label> <span class="field-required">*</span>
                            <input   type="text" name="bor_lname" id="bor_lname" class="form-control" placeholder="Last Name" value="{{ $borDetails->last_name }}"  required onkeypress="return isAlphanumeric(event)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label> <span class="field-required">*</span>
                            <input  type="email" name="bor_email" id="bor_email" class="form-control" placeholder="Email ID" value="{{ $borDetails->email }}"  required >
                            <input  type="hidden" id="bor_email_chk" value="{{ $borDetails->email }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone</label> <span class="field-required">*</span>
                            <input  type="text" name="bor_phone" id="bor_phone" class="form-control" placeholder="Phone" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $borDetails->phone }}" >
                        </div>
                    </div>
                    </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Street Address</label> <span class="field-required">*</span>
                            <input  type="text" name="bor_addr" id="bor_addr" class="form-control" placeholder="Street Address" value="{{ $borDetails->street_address }}"  required >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 pr-1">
                        <div class="form-group">
                            <label>City</label> <span class="field-required">*</span>
                            <input  type="text" name="bor_city" id="bor_city" class="form-control" placeholder="City" value="{{ $borDetails->city }}"  required onkeypress="return isAlphanumeric(event)" >
                        </div>
                    </div>
                    <div class="col-md-4 pr-1">
                        <div class="form-group">
                            <label>State</label> <span class="field-required">*</span>
                            <select name="bor_state" id="bor_state" class="form-control select2" style="width: 100%;" required >
                                <option value="">Select State</option>
                                    @foreach($stateArr as $state)
                                      <option value="{{$state->state_short_name}}" {{$borDetails->state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                                    @endforeach
                                </select>
                        </div>
                    </div>
                    <div class="col-md-4 pl-1">
                        <div class="form-group">
                            <label>Zip Code</label> <span class="field-required">*</span>
                            <input  type="text" name="bor_zip" id="bor_zip" class="form-control" placeholder="ZIP Code" value="{{ $borDetails->zip }}"  required onkeypress="return isNumber(event)" >
                        </div>
                    </div>
                </div>

                </div>
                <div class="card-header">
                    <h4 class="card-title">CHAPTER INFORMATION</h4>
                </div>
                <div class="card-body">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Chapter Website</label>
                                <p>{{ $chapterDetails->website_url ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>ONLINE CHAPTER DISCUSSION GROUP (MEETUP, GOOGLE GROUPS, ETC)</label>
                                <p>{{ $chapterDetails->egroup ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>FACEBOOK</label>
                                <p>{{ $chapterDetails->social1 ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>TWITTER</label>
                                <p>{{ $chapterDetails->social2 ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>INSTAGRAM</label>
                                <p>{{ $chapterDetails->social3 ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>CHAPTER E-MAIL ADDRESS</label>
                                    <p>{{ $chapterDetails->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>E-MAIL ADDRESS TO GIVE TO MOMS INTERESTED IN JOINING YOUR CHAPTER</label>
                                    <p>{{ $chapterDetails->inquiries_contact ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>PO BOX</label>
                                    <p>{{ $chapterDetails->po_box ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>
                    <div class="card-header">
                        <h4 class="card-title">READ ONLY - PLEASE CONTACT PC IF INCORRECT</h4>
                    </div>
                    <div class="card-body">
                <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>FOUNDED MONTH</label>
                                    <p>{{$currentMonthAbbreviation}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>FOUNDED YEAR</label>
                                    <p>{{$chapterDetails->start_year}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>RE-REGISTRATION DUES LAST PAID</label>
                                    <p>{{\Carbon\Carbon::parse($chapterDetails->dues_last_paid)->format('m-d-Y')}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>LAST NUMBER OF MEMBERS REGISTERED</label>
                                    <p>{{$chapterDetails->members_paid_for}}</p>
                                </div>
                            </div>
                        </div>

                            <div class="row">
                                <div class="col-md-6 BoardInfoStatus">
                                    <div class="form-group">
                                        <label><?php echo $a = date('Y'); echo "-"; echo $a + 1;?> Board Info Received</label>
                                        <p>{{$chapterDetails->new_board_submitted == '1' ? 'Received' : 'Not Received'}}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 FinancialReportStatus">
                                    <div class="form-group">
                                        <label><?php echo date('Y') - 1 . '-' . date('Y');?> Financial Report Received</label>
                                        <p>{{$chapterDetails->financial_report_received == '1' ? 'Received' : 'Not Received'}}</p>
                                    </div>
                                </div>
                    </div>
                </div>
            </div>
                <div class="card-header">
                    <h4 class="card-title">INTERNATIONAL MOMS CLUB COORDINATORS</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                    <div class="col-md-6">
						<input  type="hidden" id="pcid" value="{{ $chapterDetails->primary_coordinator_id}}">
						<div id="display_corlist">
						</div>
                    </div>
                </div>

                <div class="card-body card-b"><hr></div>
                <div class="box-body text-center">
                 <button id="Save" type="submit" class="btn btn-primary" ><i class="fas fa-save" ></i>&nbsp; Save</button>
             </form>
             <button type="button" class="btn btn-primary" onclick="showChangePasswordAlert()"><i class="fas fa-lock" ></i>&nbsp; Change Password</button>
             <button id="logout-btn" class="btn btn-primary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-undo" ></i>&nbsp; Logout</button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
             </div><br>
                 <div class="box-body text-center">
                 {{-- <button type="button" class="btn btn-primary" onclick="window.open('https://groups.google.com/a/momsclub.org/g/2023-24boardlist)"><i class="fa fa-list fa-fw" aria-hidden="true" ></i>&nbsp; BoardList Forum</button> --}}
                 <button type="button"  onclick="window.open('https://momsclub.org/elearning/')" class="btn btn-primary"><i class="fa fa-graduation-cap fa-fw" aria-hidden="true" ></i>&nbsp; eLearning Library</button>
                 <a href="{{ route('board.resources') }}" class="btn btn-primary"><i class="fa fa-briefcase fa-fw" aria-hidden="true" ></i>&nbsp; Chapter Resources</a>
             </div>

             </div>
         </div>
@endsection
@section('customscript')
<script>
/* Disable fields and buttons  */
$(document).ready(function () {
    var currentDate = new Date();
    var currentMonth = currentDate.getMonth() + 1; // JavaScript months are 0-based

    if (currentMonth >= 5 && currentMonth <= 7) {
        // Disable all input fields, select elements, textareas, and Save button except the logout elements
        $('input, select, textarea').not('#logout-form input, #logout-form select, #logout-form textarea').prop('disabled', true);
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

var originalWebsiteUrl = "{{$chapterDetails->website_url}}"; // Original value from the database

function checkWebsiteChanged() {
    var currentValue = document.getElementById('validate_url').value;

    if (currentValue !== originalWebsiteUrl) {
        document.getElementById('staticStatusField').style.display = 'none';
        document.getElementById('editableStatusField').style.display = 'block';
    } else {
        document.getElementById('staticStatusField').style.display = 'block';
        document.getElementById('editableStatusField').style.display = 'none';
    }
}

document.getElementById('ch_webstatus').addEventListener('change', function() {
        // Update hidden input field with the new value only if the selected option is not disabled
        var selectedOption = this.options[this.selectedIndex];
        if (!selectedOption.disabled) {
            document.getElementById('ch_hid_webstatus').value = this.value;
        }
    });

    // Ensure the hidden field is updated with the selected value on form submission
    document.forms[0].addEventListener('submit', function() {
        var selectedOption = document.getElementById('ch_webstatus').options[document.getElementById('ch_webstatus').selectedIndex];
        if (selectedOption.disabled) {
            document.getElementById('ch_hid_webstatus').value = selectedOption.value;
        }
    });

function is_url() {
        var str = $("#validate_url").val().trim(); // Trim leading and trailing whitespace
        var chWebStatusSelect = document.querySelector('select[name="ch_webstatus"]');

        if (str === "") {
            chWebStatusSelect.value = '0'; // Set to 0 if the input is blank
            chWebStatusSelect.disabled = true; // Disable the select field
            return true; // Field is empty, so no validation needed
        }

        var regexp = /^(https?:\/\/)([a-z0-9-]+\.(com|org))$/;

        if (regexp.test(str)) {
            chWebStatusSelect.disabled = false; // Enable the select field if a valid URL is entered
            return true;
        } else {
            alert("Please Enter URL, Should be http://xxxxxxxx.xxx format");
            chWebStatusSelect.value = '0'; // Set to 0 if an invalid URL is entered
            chWebStatusSelect.disabled = true; // Disable the select field
            return false;
        }
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
