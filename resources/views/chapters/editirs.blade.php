@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'IRS Information')

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
    <form class="form-horizontal" method="POST" action='{{ route("chapters.updateirs", $chapterList[0]->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="ch_state" value="{{$chapterList[0]->state}}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">MOMS Club of {{ $chapterList[0]->name }}, {{$chapterList[0]->statename}}</h3>
                  <p class="text-center">{{ $chapterList[0]->confname }} Conference, {{ $chapterList[0]->regname }} Region
                  <br>
                  EIN: {{$chapterList[0]->ein}}
                  </p>
                    <div class="card-body text-center">
                        @if($chapterList[0]->ein_letter_path != null)
                  <button class="btn bg-gradient-primary btn-sm mb-3" onclick="window.open('{{ $chapterList[0]->ein_letter_path }}', '_blank')">View/Download EIN Letter</button>
              @else
                  <button class="btn bg-gradient-primary btn-sm mb-3 disabled">No EIN Letter on File</button>
              @endif
              <br>
                        <button type="button" class="btn bg-gradient-primary btn-sm" onclick="updateEIN()">Update EIN Number</button>
                        <button class="btn bg-gradient-primary btn-sm showFileUploadModal" data-ein-letter="{{ $chapterList[0]->ein_letter_path }}">Update EIN Letter</button>
                    </div>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">

                            <b>Founded:</b> <span class="float-right">{{ $chapterList[0]->startmonth }} {{ $chapterList[0]->start_year }}</span>

                            </li>                            <input type="hidden" id="ch_primarycor" value="{{ $chapterList[0]->primary_coordinator_id }}">
                            <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
                  </ul>
                  <div class="text-center">
                      @if ($chapterList[0]->is_active == 1 )
                          <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                      @else
                          <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                          Disband Date: <span class="date-mask">{{ $chapterList[0]->zap_date }}</span><br>
                          {{ $chapterList[0]->disband_reason }}
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
                <h3 class="profile-username">IRS Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">


                            <!-- /.form group -->
                            <div class="form-group row align-items-center mb-1">
                                <label class="col-sm-2 col-form-label">EIN Letter Received:</label>
                                <div class="col-sm-10 custom-control custom-switch">
                                    <input type="checkbox" name="ch_ein_letter_display" id="ch_ein_letter" class="custom-control-input" {{$chapterList[0]->ein_letter == 1 ? 'checked' : ''}} disabled>
                                    <label class="custom-control-label" for="ch_ein_letter"></label>
                                    <!-- Hidden input to submit the value -->
                                    <input type="hidden" name="ch_ein_letter" value="{{ $chapterList[0]->ein_letter }}">
                                </div>
                            </div>


                        <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">EIN Notes:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="ch_einnotes" id="ch_einnotes" class="form-control" value="{{ $chapterList[0]->ein_notes }}" placeholder="IRS/EIN Notes">
                                </div>
                            </div>
                            <hr>
                            <!-- /.form group -->
                            <div class="form-group row align-items-center mb-1">
                                <label class="col-sm-2 col-form-label">990N Verifed with IRS:</label>
                                <div class="col-sm-10 custom-control custom-switch">
                                    <input type="checkbox" name="check_current_990N_verified_IRS" id="check_current_990N_verified_IRS" class="custom-control-input" {{$financial_report_array->check_current_990N_verified_IRS == 1 ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="check_current_990N_verified_IRS"></label>
                                </div>
                            </div>

                        <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">990N Notes:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="check_current_990N_notes" id="check_current_990N_notes" class="form-control" value="{{ $financial_report_array->check_current_990N_notes }}" placeholder="990N Notes">
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
                    <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return PreSaveValidate();">Save IRS Information</button>
                @endif
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapreports.chaprpteinstatus') }}'">Back to IRS Status Report</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.view', ['id' => $chapterList[0]->id]) }}'">Back to Chapter Details</button>
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
    // Disable fields for chapters that are not active
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
