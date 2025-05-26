@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Website & Social Media')

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
    <form class="form-horizontal" method="POST" action='{{ route("chapters.updatewebsite", $chDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">
                <input type="hidden" name="ch_state" value="{{$stateShortName}}">
                <input type="hidden" name="ch_hid_webstatus" value="{{ $chDetails->website_status }}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                  <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName}} Region
                  </p>

                  <ul class="list-group list-group-unbordered mb-3">
                      <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                      <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
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
                <h3 class="profile-username">Website & Social Media Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Website:</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="ch_website" id="ch_website" class="form-control"
                                        value="{{$chDetails->website_url}}"
                                        onchange="updateWebsiteStatus()" placeholder="Chapter Website">
                                    </div>
                            </div>

                         <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-3">
                                    <select name="ch_webstatus" id="ch_webstatus"class="form-control" style="width: 100%;" required>
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

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Website Notes:</label>
                                <div class="col-sm-8">
                                  <input type="text" name="ch_webnotes" id="ch_webnotes" class="form-control"  value="{{ $chDetails->website_notes}}" >
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Social Media:</label>
                                <div class="col-sm-3">
                                <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chDetails->egroup }}"  placeholder="Forum/Group/App" >
                                </div>
                                <div class="col-sm-3">
                                <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chDetails->social1 }}" placeholder="Facebook"  >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-3">
                                    <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chDetails->social2 }}"  placeholder="Twitter" >
                                </div>
                                <div class="col-sm-3">
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
                @php
                    $emailData = app('App\Http\Controllers\UserController')->loadEmailDetails($chDetails->id);
                    $emailListChap = implode(',', $emailData['emailListChap']); // Convert array to comma-separated string
                    $emailListCoord = implode(',', $emailData['emailListCoord']); // Convert array to comma-separated string
                @endphp
                <button type="button" class="btn bg-gradient-primary mb-3"
                            onclick="window.location.href='mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('MOMS Club of ' . $chDetails->name . ', ' . $stateShortName) }}'">
                            <i class="fas fa-envelope mr-2"></i>Email Board</button>
                    <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save Website Information</button>
                <br>
                @endif
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chapwebsite') }}'"><i class="fas fa-reply mr-2"></i>Back to Website Report</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapreports.chaprptsocialmedia') }}'"><i class="fas fa-reply mr-2"></i>Back to Social Media Report</button>

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

//If Website URL Changes for Website Status Change
function updateWebsiteStatus() {
    customWarningAlert("If you are changing the chapter website url, please be sure to update the 'Link Status' accordingly.");
}

$(document).ready(function() {
    // Function to load the coordinator list based on the selected value
    function loadCoordinatorList(corId) {
        if(corId != "") {
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

</script>
@endsection
