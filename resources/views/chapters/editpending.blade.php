@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Board Information')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

.custom-span {
    border: none !important;
    background-color: transparent !important;
    padding: 0.375rem 0 !important; /* Match the vertical padding of form-control */
    box-shadow: none !important;
}


</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("chapters.updatepending", $chDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="ch_state" value="{{$stateShortName}}">
            <input type="hidden" name="ch_hid_primarycor" value="{{$chDetails->primary_coordinator_id}}">
            <input type="hidden" id="ch_pre_email_chk" value="{{ $chDetails->pendingPresident->email }}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                  <p class="text-center">{{ $conferenceDescription }} Conference
                  </p>
                  <div class="form-group row mt-1">
                    <label class="col-form-label col-sm-6">Region:</label>
                    <div class="col-sm-6">
                        <select id="ch_region" name="ch_region" class="form-control float-right text-right"required>
                            <option value="">Select Region</option>
                            @foreach($allRegions as $region)
                                <option value="{{$region->id}}"
                                    @if($chDetails->region_id == $region->id) selected @endif>
                                    {{$region->long_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                  <ul class="list-group list-group-unbordered mb-3">

                      @if($regionalCoordinatorCondition)
                      <li class="list-group-item">
                          <label class="ch_primarycor">Update Primary Coordinator:</label>
                          <select name="ch_primarycor" id="ch_primarycor" class="form-control float-right col-sm-6 text-right" style="width: 100%;" onchange="loadCoordinatorList(this.value)" required>
                              <option value="">Select Primary Coordinator</option>
                              @foreach($pcDetails as $coordinator)
                              <option value="{{ $coordinator['cid'] }}"
                                  {{ isset($chDetails->primary_coordinator_id) && $chDetails->primary_coordinator_id == $coordinator['cid'] ? 'selected' : '' }}
                                  data-region-id="{{ $coordinator['regid'] }}">
                                  {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                  </option>
                              @endforeach
                          </select>
                          <hr>
                          <h3 class="profile-username">Coordinator Team</h3>
                          <span id="display_corlist" style="display: block; margin-top: 10px;"></span>
                      </li>
                      @else
                      <li class="list-group-item" id="display_corlist" ></li>
                      @endif
                             <li class="list-group-item">

                  <div class="text-center">
                      @if ($chDetails->active_status == 1 )
                          <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                      @elseif ($chDetails->active_status == 2)
                        <b><span style="color: #ff851b;">Chapter is PENDING</span></b>
                      @elseif ($chDetails->active_status == 3)
                        <b><span style="color: #dc3545;">Chapter was NOT APPROVED</span></b><br>
                          Declined Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
                      @elseif ($chDetails->active_status == 0)
                          <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                          Disband Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
                      @endif
                  </div>
                </li>

                   @if ($chDetails->active_status == '2')
                    <li class="list-group-item">
                        <div class="card-body text-center">
                            <button type="button" class="btn bg-gradient-primary mb-3" onclick="showChapterSetupModal()"><i class="fas fa-envelope mr-2"></i>Send Startup Email</button>
                            <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save Updates</button>
                            <br>
                            Save all changes before approval!
                            <br>
                            <button type="button" class="btn bg-gradient-success" id="chap-approve"><i class="fas fa-check mr-2"></i>Approve Chapter</button>
                            <button type="button" class="btn bg-gradient-danger" id="chap-decline"><i class="fas fa-times mr-2"></i>Decline Chaper</button>
                    </li>
                @endif
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
                <h3 class="profile-username">General Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Chapter Name:</label>
                                <div class="col-sm-5">
                                <input type="text" name="ch_name" id="ch_name" class="form-control" value="{{ $chDetails->name }}"  >
                                </div>

                                <label class="col-sm-2 col-form-label">State:</label>
                                <div class="col-sm-3">
                                    <select id="ch_state" name="ch_state" class="form-control" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                            <option value="{{$state->id}}"
                                                @if($chDetails->state_id == $state->id) selected @endif>
                                                {{$state->state_long_name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Boundaries:</label>
                                <div class="col-sm-10">
                                <input type="text" name="ch-territory" id="ch-territory" class="form-control" value="{{ $chDetails->territory }}"  required >
                                </div>
                            </div>

                             <!-- /.form group -->
                             <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Email/Mailing:</label>
                                <div class="col-sm-3">
                                <input type="text" name="ch_email" id="ch_email" class="form-control" value="{{ $chDetails->email }}"  placeholder="Chapter Email Address" >
                                </div>
                                <div class="col-sm-7">
                                <input type="text" name="cch_pobox" id="ch_pobox" class="form-control" value="{{ $chDetails->po_box }}"  placeholder="Chapter PO Box/Mailing Address" >
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Inquiries:</label>
                                <div class="col-sm-3">
                                <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" value="{{ $chDetails->inquiries_contact }}"  required >
                                </div>
                                <div class="col-sm-7">
                                <input type="text" name="ch_inqnote" id="ch_inqnote" class="form-control" value="{{ $chDetails->inquiries_note }}"  placeholder="Inquiries Notes" >
                                </div>
                            </div>

                        </div>
                    </div>

                    <hr>

                <h3 class="profile-username">Founder Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 mb-1 col-form-label">Name:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control" value="{{ $chDetails->pendingPresident->first_name }}" required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" value="{{ $chDetails->pendingPresident->last_name }}" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label">Contact:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{ $chDetails->pendingPresident->email }}" required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $chDetails->pendingPresident->phone}}" required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label">Address:</label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" value="{{ $chDetails->pendingPresident->street_address }}"  required  placeholder="Address">
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="col-sm-3 mb-1">
                                <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control" value="{{ $chDetails->pendingPresident->city }}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_pre_state" id="ch_pre_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}"
                                            @if($PresDetails->state_id == $state->id) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control" value="{{ $chDetails->pendingPresident->zip }}"  required placeholder="Zip">
                                </div>
                                <div class="col-sm-2" id="ch_pre_country-container" style="display: none;">
                                    <select name="ch_pre_country" id="ch_pre_country" class="form-control" style="width: 100%;" required>
                                        <option value="">Select Country</option>
                                        @foreach($allCountries as $country)
                                        <option value="{{$country->id}}"
                                            @if($PresDetails->country_id == $country->id) selected @endif>
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
              @if($coordinatorCondition)
                    @if ($confId == $chConfId)
                        @if ($chActiveId == '2')
                            <button type="button" id="back-pending" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chaplistpending') }}'"><i class="fas fa-reply mr-2"></i>Back to Pending Chapter List</button>
                        @elseif ($chActiveId == '3')
                            <button type="button" id="back-declined" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chaplistdeclined') }}'"><i class="fas fa-reply mr-2"></i>Back to Not Approved Chapter List</button>
                        @endif
                     @elseif ($confId != $chConfId)
                        @if ($userAdmin )
                            @if ($chActiveId == '2')
                                <button type="button" id="back-pending" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intchaplistpending') }}'"><i class="fas fa-reply mr-2"></i>Back to International Pending Chapter List</button>
                            @elseif ($chActiveId == '3')
                                <button type="button" id="back-declined" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intchaplistdeclined') }}'"><i class="fas fa-reply mr-2"></i>Back to International Not Approved Chapter List</button>
                            @endif
                        @endif
                    @endif
                    <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $chDetails->pendingPresident->user_id }}">Reset Founder Password</button>

                @endif
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
    var $chActiveId = @json($chActiveId);
    $(document).ready(function () {
        // Disable fields for chapters that are not active
        if (($chActiveId != 2)) {
            $('input, select, textarea, button').prop('disabled', true);

            $('a[href^="mailto:"]').each(function() {
                $(this).addClass('disabled-link'); // Add disabled class for styling
                $(this).attr('href', 'javascript:void(0);'); // Prevent navigation
                $(this).on('click', function(e) {
                    e.preventDefault(); // Prevent link click
                });
            });

            // Re-enable the specific "Back" buttons
            $('#back-list').prop('disabled', false);
            $('#back-pending').prop('disabled', false);
            $('#back-declined').prop('disabled', false);
            $('#back-zapped').prop('disabled', false);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
    // Define the sections we need to handle
    const sections = ['pre'];

    // Special state IDs that should show the country field
    const specialStates = [52, 53, 54, 55];

    // Process each section
    sections.forEach(section => {
        const stateDropdown = document.getElementById(`ch_${section}_state`);
        const countryContainer = document.getElementById(`ch_${section}_country-container`);
        const countrySelect = document.getElementById(`ch_${section}_country`);

        // Only proceed if all elements exist
        if (stateDropdown && countryContainer && countrySelect) {
            // Function to toggle country field visibility
            function toggleCountryField() {
                const selectedStateId = parseInt(stateDropdown.value) || 0;

                if (specialStates.includes(selectedStateId)) {
                    countryContainer.style.display = 'flex';
                    countrySelect.setAttribute('required', 'required');
                } else {
                    countryContainer.style.display = 'none';
                    countrySelect.removeAttribute('required');
                    countrySelect.value = "";
                }
            }

            // Set initial state
            toggleCountryField();

            // Add event listener
            stateDropdown.addEventListener('change', toggleCountryField);
        }
    });
});

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

$(document).ready(function() {
    // Function to load the coordinator list based on the selected value
    function loadCoordinatorList(id) {
        if(id != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list") }}' + '/' + id,
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

function showChapterSetupModal() {
    Swal.fire({
        title: 'Chapter Startup Details',
        html: `
            <p>This will send the initial chapter startup email to the potential founder to facilitate the discussion on boundaries and name. Please enter additional boundary and name details to include in the email and press OK to send.</p>
            <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <textarea id="boundary_details" name="boundary_details" class="swal2-textarea" placeholder="Boundary Details" required style="width: 100%; height: 80px; margin: 0 !important; box-sizing: border-box;"></textarea>
            </div>
            <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <textarea id="name_details" name="name_details" class="swal2-textarea" placeholder="Name Details" required style="width: 100%; height: 80px; margin: 0 !important; box-sizing: border-box;"></textarea>
            </div>
            <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chDetails->id }}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
            const boundaryDetails = Swal.getPopup().querySelector('#boundary_details').value;
            const nameDetails = Swal.getPopup().querySelector('#name_details').value;

            if (!boundaryDetails) {
                Swal.showValidationMessage('Please enter the boundary details.');
                return false;
            }
            if (!nameDetails) {
                Swal.showValidationMessage('Please enter the chapter name details.');
                return false;
            }

            return {
                chapter_id: chapterId,
                boundary_details: boundaryDetails,
                name_details: nameDetails,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                didOpen: () => {
                    Swal.showLoading();

                    // Perform the AJAX request
                    $.ajax({
                        url: '{{ route('chapters.sendstartup') }}',
                        type: 'POST',
                        data: {
                            chapterId: data.chapter_id,
                            boundaryDetails: data.boundary_details,
                            nameDetails: data.name_details,
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
                                location.reload(); // Reload the page to reflect changes
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
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('chap-approve').addEventListener('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            html: `
                <p>Approving a chapter application will create their MIMI login, request their @momsclub.org email address, add them to the BoardList, and PublicList as well as give
                    them access to Board elearning. Please verify this is what you want to do by pressing OK.</p>
                <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chDetails->id }}">
                <input type="hidden" id="ch_region" name="ch_region" value="{{ $chDetails->region_id }}">
            `,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            preConfirm: () => {
                const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
                const chapterRegion = Swal.getPopup().querySelector('#ch_region').value;

                if (chapterRegion == 0 || chapterRegion == null) {
                    Swal.showValidationMessage('Please select region.');
                    return false;
                }

                return {
                    chapter_id: chapterId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                // Perform the AJAX request
                $.ajax({
                    url: '{{ route('chapters.updateapprove') }}',
                    type: 'POST',
                    data: {
                        chapter_id: data.chapter_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Check if response is JSON (success) or HTML (redirect with error)
                        if (response && typeof response == 'object') {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                }).then(() => {
                                    if (response.redirect) {
                                        window.location.href = response.redirect;
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                });
                            }
                        } else {
                            // If response is not JSON, it's likely a redirect (success case)
                            // Check if the response contains success indicators
                            Swal.fire({
                                title: 'Success!',
                                text: 'Chapter approved successfully.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                window.location.href = '{{ route('chapters.view', ['id' => 'chapterId']) }}';
                            });
                        }
                    },
                    error: function(jqXHR, exception) {
                        let errorMessage = 'Something went wrong, Please try again.';

                        // Try to parse error response
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            errorMessage = jqXHR.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
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
    });

    document.getElementById('chap-decline').addEventListener('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            html: `
                <p>Declining a chapter application will mark them as inactive and remove them from all chapter lists. Please enter the reason for declining and press OK.</p>
                <div style="display: flex; align-items: center; ">
                    <input type="text" id="disband_reason" name="disband_reason" class="swal2-input" placeholder ="Enter Reason" required style="width: 100%;">
                </div>
                <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chDetails->id }}">
            `,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            preConfirm: () => {
                const disbandReason = Swal.getPopup().querySelector('#disband_reason').value;
                const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

                if (!disbandReason) {
                    Swal.showValidationMessage('Please enter the reason for declining.');
                    return false;
                }

                return {
                    disband_reason: disbandReason,
                    chapter_id: chapterId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                // Perform the AJAX request
                $.ajax({
                    url: '{{ route('chapters.updatedecline') }}',
                    type: 'POST',
                    data: {
                        disband_reason: data.disband_reason,
                        chapter_id: data.chapter_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Check if response is JSON (success) or HTML (redirect with error)
                        if (response && typeof response == 'object') {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                }).then(() => {
                                    location.reload(); // Reload the page to reflect changes
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                });
                            }
                        } else {
                            // If response is not JSON, it's likely a redirect (success case)
                            // Check if the response contains success indicators
                            Swal.fire({
                                title: 'Success!',
                                text: 'Chapter declined.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        }
                    },
                    error: function(jqXHR, exception) {
                        let errorMessage = 'Something went wrong, Please try again.';

                        // Try to parse error response
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            errorMessage = jqXHR.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
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
    });

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

</script>
@endsection
