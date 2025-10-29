@extends('layouts.coordinator_theme')

@section('page_title', 'New Chapter Details')
@section('breadcrumb', 'New Chapter')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("chapters.updatenew") }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username ">Chapter Information</h3>
                  <div class="form-group row">
                    <label class="col-sm-4 col-form-label">MOMS Club of:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_name" id="ch_name" class="form-control" placeholder="Chapter Name" required>
                    </div>
                </div>

                <div class="form-group row mt-1">
                    <label class="col-sm-4 col-form-label">State:</label>
                    <div class="col-sm-8">
                        <select id="ch_state" name="ch_state" class="form-control" required>
                            <option value="">Select State</option>
                            @foreach($allStates as $state)
                                <option value="{{$state->id}}">
                                    {{$state->state_long_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mt-1" id="country-container" style="display: none;">
                    <label class="col-sm-4 col-form-label">Country:</label>
                    <div class="col-sm-8">
                        <select id="ch_country" name="ch_country" class="form-control" required>
                            <option value="">Select Country</option>
                            @foreach($allCountries as $country)
                                <option value="{{$country->id}}" >
                                    {{$country->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="form-group row mt-1">
                    <label class="col-sm-4 col-form-label">Region:</label>
                    <div class="col-sm-8">
                        <select id="ch_region" name="ch_region" class="form-control" required>
                            <option value="">Select Region</option>
                            @foreach($allRegions as $region)
                                <option value="{{$region->id}}">
                                    {{$region->long_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">EIN:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_ein" id="ch_ein" class="form-control" placeholder="EIN Number">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Boundaries:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_boundariesterry" id="ch_boundariesterry" class="form-control" placeholder="Boundaries" required>
                    </div>
                </div>

                <div class="form-group row mt-1">
                    <label class="col-sm-4 col-form-label">Status:</label>
                    <div class="col-sm-8">
                        <select id="ch_status" name="ch_status" class="form-control" required>
                            <option value="">Select Status</option>
                            @foreach($allStatuses as $status)
                                <option value="{{$status->id}}">
                                    {{$status->chapter_status}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Inquiries Email:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" placeholder="Inquiries Email" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Primary Coordinator</label>
                    <div class="col-sm-8">
                        <select name="ch_primarycor" id="ch_primarycor" class="form-control select2-bs4" style="width: 100%;" required>
                            <option value="">Select Primary Coordinator</option>
                            @foreach($pcDetails as $coordinator)
                            <option value="{{ $coordinator['cid'] }}" data-region-id="{{ $coordinator['regid'] }}">
                                {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                  </div>
                  <div id="display_corlist"> </div>

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
                            <label class="col-sm-2 mb-1 col-form-label">Founder:</label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control" required placeholder="First Name" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" required placeholder="Last Name">
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  required placeholder="Email Address" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required placeholder="Phone Number" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-10 mb-1">
                            <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" placeholder="Address"  required >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"><br></label>
                            <div class="col-sm-3 mb-1">
                             <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_pre_state" id="ch_pre_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}">
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control"   required placeholder="Zip">
                                </div>
                                <div class="col-sm-2" id="ch_pre_country-container" style="display: none;">
                                    <select name="ch_pre_country" id="ch_pre_country" class="form-control" style="width: 100%;" required>
                                        <option value="">Select Country</option>
                                        @foreach($allCountries as $country)
                                        <option value="{{$country->id}}">
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
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
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="showChapterSetupModalBlank()"><i class="fas fa-envelope mr-2"></i>Send Chapter Startup Email</button>
                <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return validateEmailsBeforeSubmit();"><i class="fas fa-save mr-2"></i>Save New Chapter</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chaplist') }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter List</button>
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
    document.addEventListener('DOMContentLoaded', function() {
    // Chapter state and country
    const stateDropdown = document.getElementById('ch_state');
    const countryContainer = document.getElementById('country-container');
    const countrySelect = document.getElementById('ch_country');

    // Check if elements exist before adding listeners
    if (stateDropdown && countryContainer && countrySelect) {
        // Initially set country field requirement based on state selection
        toggleCountryField();

        // Add event listener to the state dropdown
        stateDropdown.addEventListener('change', toggleCountryField);

        function toggleCountryField() {
            const selectedStateId = parseInt(stateDropdown.value) || 0;
            const specialStates = [52, 53, 54, 55]; // States that should show the country field

            if (specialStates.includes(selectedStateId)) {
                countryContainer.style.display = 'flex'; // or 'block' depending on your layout
                countrySelect.setAttribute('required', 'required');
            } else {
                countryContainer.style.display = 'none';
                countrySelect.removeAttribute('required');
                // Optionally clear the country selection when hidden
                countrySelect.value = "";
            }
        }
    }
});

//     document.addEventListener('DOMContentLoaded', function() {
//     // Define the sections we need to handle
//     const sections = ['pre', 'avp', 'mvp', 'trs', 'sec'];

//     // Special state IDs that should show the country field
//     const specialStates = [52, 53, 54, 55];

//     // Process each section
//     sections.forEach(section => {
//         const stateDropdown = document.getElementById(`ch_${section}_state`);
//         const countryContainer = document.getElementById(`ch_${section}_country-container`);
//         const countrySelect = document.getElementById(`ch_${section}_country`);

//         // Only proceed if all elements exist
//         if (stateDropdown && countryContainer && countrySelect) {
//             // Function to toggle country field visibility
//             function toggleCountryField() {
//                 const selectedStateId = parseInt(stateDropdown.value) || 0;

//                 if (specialStates.includes(selectedStateId)) {
//                     countryContainer.style.display = 'flex';
//                     countrySelect.setAttribute('required', 'required');
//                 } else {
//                     countryContainer.style.display = 'none';
//                     countrySelect.removeAttribute('required');
//                     countrySelect.value = "";
//                 }
//             }

//             // Set initial state
//             toggleCountryField();

//             // Add event listener
//             stateDropdown.addEventListener('change', toggleCountryField);
//         }
//     });
// });

// // Function to handle show/hide logic for vacant checkboxes
// function handleVacantCheckbox(checkboxId, fieldClass) {
//     var fields = $("." + fieldClass);

//     $("#" + checkboxId).change(function () {
//         if ($(this).prop("checked")) {
//             fields.hide().find('input, select, textarea').prop('required', false).val(null);
//         } else {
//             fields.show().find('input, select, textarea').prop('required', true);
//         }
//     });

//     // Initial show/hide logic on page load
//     if ($("#" + checkboxId).prop("checked")) {
//         fields.hide().find('input, select, textarea').prop('required', false).val(null);
//     } else {
//         fields.show().find('input, select, textarea').prop('required', true);
//     }
// }

// Function to filter the coordinator dropdown
function filterCoordinators() {
    const regionDropdown = document.getElementById('ch_region');
    const selectedRegion = regionDropdown.value; // Get the selected region ID
    const primaryCorDropdown = document.getElementById('ch_primarycor'); // Coordinator dropdown

    // Filter options based on the selected region
    Array.from(primaryCorDropdown.options).forEach(option => {
        if (
            option.value == "" || // Always show the default empty option
            option.dataset.regionId == selectedRegion || // Match the selected region
            option.dataset.regionId == "0" // Always include region_id = 0
        ) {
            option.style.display = "block";
        } else {
            option.style.display = "none";
        }
    });

    // Reset the selected value if it's no longer valid
    if (primaryCorDropdown.value != "" &&
        primaryCorDropdown.querySelector(`option[value="${primaryCorDropdown.value}"]`).style.display == "none") {
        primaryCorDropdown.value = "";
    }
}

// Attach the event listener to the region dropdown
document.getElementById('ch_region').addEventListener('change', filterCoordinators);

// Run the filtering logic on page load
document.addEventListener('DOMContentLoaded', filterCoordinators);


// $(document).ready(function() {
//     // Function to load the coordinator list based on the selected value
//     function loadCoordinatorList(id) {
//         if(id != "") {
//             $.ajax({
//                 url: '{{ url("/load-coordinator-list") }}' + '/' + id,
//                 type: "GET",
//                 success: function(result) {
//                 $("#display_corlist").html(result);
//                 },
//                 error: function (jqXHR, exception) {
//                 console.log("Error: ", jqXHR, exception);
//                 }
//             });
//         }
//     }

//     // Get the selected coordinator ID on page load
//     var selectedCorId = $("#ch_primarycor").val();
//         loadCoordinatorList(selectedCorId);

//         // Update the coordinator list when the dropdown changes
//         $("#ch_primarycor").change(function() {
//             var selectedValue = $(this).val();
//             loadCoordinatorList(selectedValue);
//     });
// });

// function checkDuplicateEmail(email, id) {
//         $.ajax({
//             url: '{{ url("/checkemail/") }}' + '/' + email,
//             type: "GET",
//             success: function(result) {
//                 if (result.exists) {
//                     alert('This Email already used in the system. Please try with new one.');
//                     $("#" + id).val('');
//                     $("#" + id).focus();
//                 }
//             },
//             error: function(jqXHR, exception) {
//                 console.error("Error checking email: ", exception);
//             }
//         });
//     }

// function validateEmailsBeforeSubmit() {
//     // Get the values from the input fields
//     const emails = [
//         $('#ch_pre_email').val().trim(),
//     ];

//     // Filter out empty emails and check for duplicates
//     const emailSet = new Set();
//     const duplicateEmails = [];

//     emails.forEach(email => {
//         if (email != '') {
//             if (emailSet.has(email)) {
//                 // Check if the duplicate email is already in the array to avoid listing it multiple times
//                 if (!duplicateEmails.includes(email)) {
//                     duplicateEmails.push(email);
//                 }
//             } else {
//                 emailSet.add(email);
//             }
//         }
//     });

//     // If duplicates are found, show an alert
//     if (duplicateEmails.length > 0) {
//         Swal.fire({
//             icon: 'error',
//             title: 'Duplicate Emails Found',
//             html: 'The following emails are duplicates: <br>' + duplicateEmails.join('<br>') + '<br>Please correct them before submitting.',
//             confirmButtonText: 'OK',
//             customClass: {
//                 confirmButton: 'btn-sm btn-success'
//             }
//         });
//         return false;
//     }
//     return true;
// }

</script>
@endsection
