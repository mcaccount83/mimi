@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Board Information')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("chapters.updateboard", $chDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="ch_name" value="{{$chDetails->name}}">
            <input type="hidden" name="ch_state" value="{{$stateShortName}}">
            <input type="hidden" id="ch_pre_email_chk" value="{{ $PresDetails->email }}">
            <input type="hidden" id="ch_avp_email_chk" value="{{ $AVPDetails->email }}">
            <input type="hidden" id="ch_mvp_email_chk" value="{{ $MVPDetails->email }}">
            <input type="hidden" id="ch_trs_email_chk" value="{{ $TRSDetails->email }}">
            <input type="hidden" id="ch_sec_email_chk" value="{{ $SECDetails->email }}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                  <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region
                  </p>

                  <ul class="list-group list-group-unbordered mb-3">
                      <li class="list-group-item">
                          <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <label class="col-form-label mb-0 mr-2">President:</label>
                            </div>
                            <div class="ml-auto">
                                <span>{{ $PresDetails->first_name }} {{ $PresDetails->last_name }}</span>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <label class="col-form-label mb-0 mr-2">AVP:</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="AVPVacant" id="AVPVacant" class="custom-control-input"
                                           {{$AVPDetails->id == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
                                    <label class="custom-control-label" for="AVPVacant">Vacant</label>
                                </div>
                            </div>
                            <div class="avp-field ml-auto" style="display: {{ $AVPDetails->avp_fname == '' ? 'none' : 'block' }};">
                                <span>{{ $AVPDetails->first_name }} {{ $AVPDetails->last_name }}</span>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <label class="col-form-label mb-0 mr-2">MVP:</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="MVPVacant" id="MVPVacant" class="custom-control-input"
                                           {{$MVPDetails->id == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
                                    <label class="custom-control-label" for="MVPVacant">Vacant</label>
                                </div>
                            </div>
                            <div class="mvp-field ml-auto" style="display: {{ $MVPDetails->id == '' ? 'none' : 'block' }};">
                                <span>{{ $MVPDetails->first_name }} {{ $MVPDetails->last_name }}</span>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <label class="col-form-label mb-0 mr-2">Treasurer:</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="TreasVacant" id="TreasVacant" class="custom-control-input"
                                           {{$TRSDetails->id == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
                                    <label class="custom-control-label" for="TreasVacant">Vacant</label>
                                </div>
                            </div>
                            <div class="treas-field ml-auto" style="display: {{ $TRSDetails->id == '' ? 'none' : 'block' }};">
                                <span>{{ $TRSDetails->first_name }} {{ $TRSDetails->last_name }}</span>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <label class="col-form-label mb-0 mr-2">Secretary:</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="SecVacant" id="SecVacant" class="custom-control-input"
                                           {{$SECDetails->id == '' ? 'checked' : ''}} onchange="ConfirmVacant(this.id)">
                                    <label class="custom-control-label" for="SecVacant">Vacant</label>
                                </div>
                            </div>
                            <div class="sec-field ml-auto" style="display: {{ $SECDetails->id == '' ? 'none' : 'block' }};">
                                <span>{{ $SECDetails->first_name }} {{ $SECDetails->last_name }}</span>
                            </div>
                          </div>

                          <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                          <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
                  </ul>
                  <div class="text-center">
                      @if ($chDetails->is_active == 1 )
                          <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                      @else
                          <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                          Disband Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
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
                <h3 class="profile-username">Board Member Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 mb-1 col-form-label">President:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control" value="{{ $PresDetails->first_name }}" required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" value="{{ $PresDetails->last_name }}" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{ $PresDetails->email }}" required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $PresDetails->phone}}" required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" value="{{ $PresDetails->street_address }}"  required  placeholder="Address">
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control" value="{{ $PresDetails->city }}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_pre_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->state_short_name}}"
                                            @if($PresDetails->state == $state->state_short_name) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control" value="{{ $PresDetails->zip }}"  required placeholder="Zip">
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="avp-field form-group row">
                                <label class="col-sm-2 mb-1 col-form-label">AVP:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" value="{{$AVPDetails->first_name != ''  ? $AVPDetails->first_name : ''}}" required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" value="{{$AVPDetails->last_name != ''  ? $AVPDetails->last_name : ''}}" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_avp_email" id="ch_avp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$AVPDetails->email != ''  ? $AVPDetails->email : ''}}" required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$AVPDetails->phone != ''  ? $AVPDetails->phone : ''}}" required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="ch_avp_street" id="ch_avp_street" class="form-control" value="{{$AVPDetails->street_address != ''  ? $AVPDetails->street_address : ''}}"  required placeholder="Address">
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" value="{{$AVPDetails->city != ''  ? $AVPDetails->city : ''}}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_avp_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->state_short_name}}"
                                            @if($AVPDetails->state == $state->state_short_name) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" value="{{$AVPDetails->zip != ''  ? $AVPDetails->zip : ''}}"  required placeholder="Zip">
                                </div>
                            </div>

                             <!-- /.form group -->
                             <div class="mvp-field form-group row">
                                <label class="col-sm-2 mb-1 col-form-label">MVP:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control" value="{{$MVPDetails->first_name != ''  ? $MVPDetails->first_name : ''}}" required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" value="{{$MVPDetails->last_name != ''  ? $MVPDetails->last_name : ''}}" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_mvp_email" id="ch_mvp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$MVPDetails->email != ''  ? $MVPDetails->email : ''}}" required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$MVPDetails->phone != ''  ? $MVPDetails->phone : ''}}" required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control" value="{{$MVPDetails->street_address != ''  ? $MVPDetails->mvstreet_addressp_addr : ''}}"  required placeholder="Address">
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" value="{{$MVPDetails->city != ''  ? $MVPDetails->city : ''}}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_mvp_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->state_short_name}}"
                                            @if($MVPDetails->state == $state->state_short_name) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" class="form-control" value="{{$MVPDetails->zip != ''  ? $MVPDetails->zip : ''}}"  required placeholder="Zip">
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="treas-field form-group row">
                                <label class="col-sm-2 mb-1 col-form-label">Treasurer:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control" value="{{$TRSDetails->first_name != ''  ? $TRSDetails->first_name : ''}}" required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control" value="{{$TRSDetails->last_name != ''  ? $TRSDetails->last_name : ''}}" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_trs_email" id="ch_trs_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$TRSDetails->email != ''  ? $TRSDetails->email : ''}}" required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$TRSDetails->phone != ''  ? $TRSDetails->phone : ''}}" required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="ch_trs_street" id="ch_trs_street" class="form-control" value="{{$TRSDetails->street_address != ''  ? $TRSDetails->street_address : ''}}"  required placeholder="Address">
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" value="{{$TRSDetails->city != ''  ? $TRSDetails->city : ''}}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_trs_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->state_short_name}}"
                                            @if($TRSDetails->state == $state->state_short_name) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_trs_zip" id="ch_trs_zip" class="form-control" value="{{$TRSDetails->zip != ''  ? $TRSDetails->zip : ''}}"  required placeholder="Zip">
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="sec-field form-group row">
                                <label class="col-sm-2 mb-1 col-form-label">Secretary:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control" value="{{$SECDetails->first_name != ''  ? $SECDetails->first_name : ''}}" required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control" value="{{$SECDetails->last_name != ''  ? $SECDetails->last_name : ''}}" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_sec_email" id="ch_sec_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$SECDetails->email != ''  ? $SECDetails->email : ''}}" required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{$SECDetails->phone != ''  ? $SECDetails->phone : ''}}" required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="ch_sec_street" id="ch_sec_street" class="form-control" value="{{$SECDetails->street_address != ''  ? $SECDetails->street_address : ''}}"  required placeholder="Address">
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" value="{{$SECDetails->city != ''  ? $SECDetails->city : ''}}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_sec_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->state_short_name}}"
                                            @if($SECDetails->state == $state->state_short_name) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_sec_zip" id="ch_sec_zip" class="form-control" value="{{$SECDetails->zip != ''  ? $SECDetails->zip : ''}}"  required placeholder="Zip">
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
                @if ($coordinatorCondition)
                    <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return validateEmailsBeforeSubmit();"><i class="fas fa-save mr-2"></i>Save Board Information</button>
                @endif
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter Details</button>
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
// Disable fields, links and buttons
var $chIsActive = @json($chIsActive);
$(document).ready(function () {
    // Disable fields for chapters that are not active or EIN & Inquiries Coordinators who are not PC for the Chapter
    if (($chIsActive != 1)) {
        $('input, select, textarea, button').prop('disabled', true);

        $('a[href^="mailto:"]').each(function() {
            $(this).addClass('disabled-link'); // Add disabled class for styling
            $(this).attr('href', 'javascript:void(0);'); // Prevent navigation
            $(this).on('click', function(e) {
                e.preventDefault(); // Prevent link click
            });
        });
    }
});

// Function to handle show/hide logic for vacant checkboxes
function handleVacantCheckbox(checkboxId, fieldClass) {
    var fields = $("." + fieldClass);

    $("#" + checkboxId).change(function () {
        if ($(this).prop("checked")) {
            fields.hide().find('input, select, textarea').prop('required', false).val(null);
        } else {
            fields.show().find('input, select, textarea').prop('required', true);
        }
    });

    // Initial show/hide logic on page load
    if ($("#" + checkboxId).prop("checked")) {
        fields.hide().find('input, select, textarea').prop('required', false).val(null);
    } else {
        fields.show().find('input, select, textarea').prop('required', true);
    }
}

// Apply the logic for each checkbox with a specific class
handleVacantCheckbox("MVPVacant", "mvp-field");
handleVacantCheckbox("AVPVacant", "avp-field");
handleVacantCheckbox("SecVacant", "sec-field");
handleVacantCheckbox("TreasVacant", "treas-field");

$(document).ready(function() {
    // Function to load the coordinator list based on the selected value
    function loadCoordinatorList(corId) {
        if (corId != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list") }}' + '/' + corId,
                type: "GET",
                success: function(result) {
                    $("#display_corlist").html(result);
                },
                error: function (jqXHR, exception) {
                    console.log("Error: ", jqXHR, exception);
                }
            });
        }
    }

    // Get the selected coordinator ID on page load
    var selectedCorId = $("#ch_primarycor").val();
    loadCoordinatorList(selectedCorId);

    // Update the coordinator list when the dropdown changes
    $("#ch_primarycor").change(function() {
        var selectedValue = $(this).val();
        loadCoordinatorList(selectedValue);
    });
});

// function validateEmailsBeforeSubmit() {
//     var errMessage = "";

//     // Get email values and trim whitespace
//     var preEmail = $("#ch_pre_email").val().trim();
//     var avpEmail = $("#ch_avp_email").val().trim();
//     var mvpEmail = $("#ch_mvp_email").val().trim();
//     var trsEmail = $("#ch_trs_email").val().trim();
//     var secEmail = $("#ch_sec_email").val().trim();

//     // Create an array of emails
//     var emails = [preEmail, avpEmail, mvpEmail, trsEmail, secEmail];

//     // Use a Set to identify duplicates
//     var uniqueEmails = new Set(emails.filter(email => email !== "")); // filter out empty values

//     // Check for duplicates
//     if (uniqueEmails.size !== emails.filter(email => email !== "").length) {
//         errMessage = "You entered the same email address for more than one board member. Please enter a unique e-mail address for each board member or mark the position as vacant.";
//     }

//     if (errMessage.length > 0) {
//         customErrorAlert(errMessage);
//         return false;
//     }

//     return true;
// }

function validateEmailsBeforeSubmit() {
    // Get the values from the input fields
    const emails = [
        $('#ch_pre_email').val().trim(),
        $('#ch_avp_email').val().trim(),
        $('#ch_mvp_email').val().trim(),
        $('#ch_trs_email').val().trim(),
        $('#ch_sec_email').val().trim()
    ];

    // Filter out empty emails and check for duplicates
    const emailSet = new Set();
    const duplicateEmails = [];

    emails.forEach(email => {
        if (email !== '') {
            if (emailSet.has(email)) {
                // Check if the duplicate email is already in the array to avoid listing it multiple times
                if (!duplicateEmails.includes(email)) {
                    duplicateEmails.push(email);
                }
            } else {
                emailSet.add(email);
            }
        }
    });

    // If duplicates are found, show an alert
    if (duplicateEmails.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Duplicate Emails Found',
            html: 'The following emails are duplicates: <br>' + duplicateEmails.join('<br>') + '<br>Please correct them before submitting.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success'
            }
        });
        return false;
    }
    return true;
}



</script>
@endsection
