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
    <form class="form-horizontal" method="POST" action='{{ route("chapters.update", $chDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="ch_state" value="{{$stateShortName}}">
            <input type="hidden" name="ch_hid_webstatus" value="{{ $websiteLink }}">
            <input type="hidden" name="ch_hid_preknown" value="{{$chDetails->former_name}}">
            <input type="hidden" name="ch_hid_sistered" value="{{$chDetails->sistered_by}}">
            <input type="hidden" name="ch_hid_primarycor" value="{{$chDetails->primary_coordinator_id}}">
            <input type="hidden" name="ch_hid_status" value="{{ $chapterStatus }}">
            <input type="hidden" name="ch_hid_probation" value="{{ $probationReason }}">
            <input type="hidden" name="ch_hid_boundariesterry" value="{{ $chDetails->territory}}" >

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                  <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region
                  <br>
                  EIN: {{$chDetails->ein}}
                  </p>

                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                    <label class="col-form-label">EIN Notes:</label><input type="text" name="ein_notes" id="ein_notes" class="form-control float-right col-sm-8 mb-1 text-right" value="{{ $chDocuments->ein_notes }}" placeholder="EIN Notes">
                    </li>
                      <li class="list-group-item">
                          <b>Re-Registration Dues:</b><span class="float-right">
                              @if ($chPayments->rereg_members)
                                  <b>{{ $chPayments->rereg_members }} Members</b> on <b><span class="date-mask">{{ $chPayments->rereg_date }}</span></b>
                              @else
                                  No Payment Recorded
                              @endif
                          </span><br>
                          <b>M2M Donation:</b><span class="float-right">
                              @if ($chPayments->m2m_donation)
                                  <b>${{ $chPayments->m2m_donation }}</b> on <b><span class="date-mask">{{ $chPayments->m2m_date }}</span></b>
                              @else
                                  No Donation Recorded
                              @endif
                          </span><br>
                          <b>Sustaining Chapter Donation: </b><span class="float-right">
                              @if ($chPayments->sustaining_donation)
                                  <b>${{ $chPayments->sustaining_donation }}</b> on <b><span class="date-mask">{{ $chPayments->sustaining_date }}</span></b>
                              @else
                                  No Donation Recorded
                              @endif
                          </span>
                          <br>
                      </li>
                      <li class="list-group-item">
                        <label class="col-form-label mb-1">Founded:</label><span class="form-control-plaintext float-right col-sm-6 mb-1 text-right custom-span">{{ $startMonthName }} {{ $chDetails->start_year }}</span>
                           <br>
                          <label class="col-form-label mb-1">Formerly Known As:</label><input type="text" name="ch_preknown" id="ch_preknown" class="form-control float-right col-sm-6 mb-1 text-right" value="{{ $chDetails->former_name }}" placeholder="Former Chapter Name">
                          <br>
                          <label class="col-form-label">Sistered By:</label><input type="text" name="ch_sistered" id="ch_sistered" class="form-control float-right col-sm-6 text-right" value="{{ $chDetails->sistered_by }}" placeholder="Chapter Name">
                      </li>
                        @if($regionalCoordinatorCondition)
                        <li class="list-group-item">
                            <label class="ch_primarycor">Update Primary Coordinator:</label>
                            <select name="ch_primarycor" id="ch_primarycor" class="form-control float-right col-sm-6 text-right" style="width: 100%;" onchange="loadCoordinatorList(this.value)" required>
                                <option value="">Select Primary Coordinator</option>
                                @foreach($pcList as $coordinator)
                                    <option value="{{ $coordinator['cid'] }}"
                                        {{ isset($chDetails->primary_coordinator_id) && $chDetails->primary_coordinator_id == $coordinator['cid'] ? 'selected' : '' }}>
                                        {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                    </option>
                                @endforeach
                            </select>
                            <span id="display_corlist" style="display: block; margin-top: 10px;"></span>
                        </li>
                        @else
                        <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                        <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
                    @endif
                  </ul>
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
                                <div class="col-sm-10">
                                <input type="text" name="ch_name" id="ch_name" class="form-control" value="{{ $chDetails->name }}"  required onchange="PreviousNameReminder()">
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Boundaries:</label>
                                <div class="col-sm-10">
                                <input type="text" name="ch_boundariesterry" id="ch_boundariesterry" class="form-control" value="{{ $chDetails->territory }}"  required >
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Status:</label>
                                <div class="col-sm-3">
                                    <select name="ch_status" id="ch_status"class="form-control" style="width: 100%;" required>
                                        <option value="">Select Status</option>
                                        @foreach($allStatuses as $status)
                                            <option value="{{$status->id}}"
                                                @if($chDetails->status_id == $status->id) selected @endif>
                                                {{$status->chapter_status}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="col-sm-2 col-form-label ml-5" id="probationLabel" style="{{ $chDetails->status_id != 1 ? '' : 'display: none;' }}">Probation Reason:</label>
                                <div class="col-sm-3" id="probationField" style="{{ $chDetails->status_id != 1 ? '' : 'display: none;' }}">
                                    <select name="ch_probation" id="ch_probation" class="form-control" style="width: 100%;" {{ $chDetails->status_id != 1 ? 'required' : '' }}>
                                        <option value="">Select Reason</option>
                                        @foreach($allProbation as $probation)
                                            <option value="{{$probation->id}}"
                                                @if($chDetails->probation_id == $probation->id) selected @endif>
                                                {{$probation->probation_reason}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Status Notes:</label>

                                <div class="col-sm-8">
                                    <input type="text" name="ch_notes" id="ch_notes" class="form-control" value="{{ $chDetails->notes }}"  placeholder="Status Notes" >
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
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Additional Information:</label>
                                <div class="col-sm-10">
                                    <textarea name="ch_addinfo" class="form-control" rows="4" >{{ $chDetails->additional_info }}</textarea>
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Website:</label>
                                <div class="col-sm-7">
                                    <input type="text" name="ch_website" id="ch_website" class="form-control"
                                        value="{{$chDetails->website_url}}"
                                        placeholder="Chapter Website">
                                </div>
                            </div>

                            <!-- Website Status Container - Hidden by default -->
                            <div class="form-group row" id="ch_webstatus-container" style="display: none;">
                                <label class="col-sm-2 col-form-label">Website Status:</label>
                                <div class="col-sm-3">
                                    <select name="ch_webstatus" id="ch_webstatus" class="form-control" style="width: 100%;">
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
                                <label class="col-sm-2 col-form-label">Website Notes:</label>
                                <div class="col-sm-10">
                                <input type="text" name="ch_webnotes" id="ch_webnotes" class="form-control" value="{{ $chDetails->website_notes }}" placeholder="Website Linking Notes"  >
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Social Media:</label>
                                <div class="col-sm-3.3">
                                <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chDetails->egroup }}"  placeholder="Forum/Group/App" >
                                </div>
                                <div class="col-sm-3.3">
                                <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chDetails->social1 }}" placeholder="Facebook"  >
                                </div>
                                <div class="col-sm-3.3">
                                    <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chDetails->social2 }}"  placeholder="Twitter" >
                                </div>
                                <div class="col-sm-3.3">
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
          <div class="col-md-12">
            <div class="card-body text-center">
                @if ($coordinatorCondition)
                    <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save Chapter Information</button>
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
var $chActiveId = @json($chActiveId);
$(document).ready(function () {
    // Disable fields for chapters that are not active
    if (($chActiveId != 1)) {
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

document.addEventListener('DOMContentLoaded', function() {
    const websiteUrl = document.getElementById('ch_website');
    const statusContainer = document.getElementById('ch_webstatus-container');
    const websiteStatus = document.getElementById('ch_webstatus');

    // Only proceed if all elements exist
    if (websiteUrl && statusContainer && websiteStatus) {

        // Function to toggle status field visibility
        function toggleStatusField() {
            const urlValue = websiteUrl.value.trim();

            if (urlValue !== '' && urlValue !== 'http://') {
                // Show status field if URL has a meaningful value
                statusContainer.style.display = 'flex';
                websiteStatus.setAttribute('required', 'required');
            } else {
                // Hide status field if URL is empty or just the default "http://"
                statusContainer.style.display = 'none';
                websiteStatus.removeAttribute('required');
                websiteStatus.value = ""; // Clear the selection
            }
        }

        // Set initial state on page load
        toggleStatusField();

        // Add event listeners for real-time updates
        websiteUrl.addEventListener('input', toggleStatusField);
        websiteUrl.addEventListener('change', toggleStatusField);
    }
});

//If Chapter Name Change Warning
function PreviousNameReminder(){
    customWarningAlert("If you are changing the chapter name, please be sure to note the old name in the 'Previously Known As' field.");
}

//If Website URL Changes for Website Status Change
function updateWebsiteStatus() {
    customWarningAlert("If you are changing the chapter website url, please be sure to update the 'Link Status' accordingly.");
}

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

document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('ch_status');
    const probationLabel = document.getElementById('probationLabel');
    const probationField = document.getElementById('probationField');
    const probationSelect = document.getElementById('ch_probation');

    // Function to toggle probation section visibility
    function toggleProbationSection() {
        const selectedStatusId = parseInt(statusSelect.value);
        // if (selectedStatusId !== 1 && selectedStatusId !== '') {
        if (selectedStatusId >= 5 && selectedStatusId !== '') {
            probationLabel.style.display = '';
            probationField.style.display = '';
            probationSelect.setAttribute('required', 'required');
        } else {
            probationLabel.style.display = 'none';
            probationField.style.display = 'none';
            probationSelect.removeAttribute('required');
        }
    }

    // Initial toggle based on current value
    toggleProbationSection();

    // Add event listener for future changes
    statusSelect.addEventListener('change', toggleProbationSection);
});

</script>
@endsection
