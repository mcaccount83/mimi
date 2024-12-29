@extends('layouts.coordinator_theme')

@section('page_title', 'EOY Details')
@section('breadcrumb', 'EOY Boundary Issues')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

.align-bottom {
        display: flex;
        align-items: flex-end;
    }

    .align-middle {
        display: flex;
        align-items: center;
    }

</style>

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("eoyreports.updateboundaries", $chDetails->id) }}'>
        @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>New Board Submitted:</label>
                                <span class="float-right">{{ $chDetails->documents->new_board_submitted == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>New Board Activated:</label>
                                <span class="float-right">{{ $chDetails->documents->new_board_active == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Financial Report Received</label>
                                <span class="float-right">{{ $chDetails->documents->financial_report_received == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Financial Review Complete:</label>
                                <span class="float-right">{{ $chDetails->documents->financial_report_complete == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Report Extension Given:</label>
                                <span class="float-right">{{ $chDetails->documents->report_extension == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>990N Verifed on irs.gov:</label>
                                <span class="float-right">{{ $chFinancialReport->check_current_990N_verified_IRS == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Assigned Reviewer:</label>
                                    @if($chFinancialReport->reviewer_id != null)
                                        <span class="float-right">{{ $chDetails->reportReviewer->first_name }} {{ $chDetails->reportReviewer->last_name }}</span>
                                    @else
                                        No Reviewer Assigned
                                    @endif
                            </div>
                        </div>
                </li>

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
                <h3 class="profile-username">{{ (date('Y') - 1) . '-' . date('Y') }} Boundary Issues</h3>
                    <!-- /.card-header -->
                    <div class="form-group row align-middle">
                        <label class="col-sm-2 col-form-label">Boundary Issues Reported by Chapter:</label>
                        <div class="col-sm-10">
                        <input type="text" name="ch_issue" id="ch_issue" class="form-control" value="{{ $chDetails->boundary_issue_notes }}" disabled>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <div class="form-group row align-middle">
                        <label class="col-sm-2 col-form-label">Current Recorded Boundaries:</label>
                        <div class="col-sm-10">
                        <input type="text" name="ch_old_territory" id="ch_old_territory" class="form-control" value="{{ $chDetails->territory }}" disabled>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <div class="form-group row align-middle">
                        <label class="col-sm-2 col-form-label">Update Boundaries:</label>
                        <div class="col-sm-10">
                        <input type="text" name="ch_territory" id="ch_territory" class="form-control">
                        </div>
                    </div>
                    <!-- /.form group -->

                    <div class="form-group row align-middle">
                        <label class="col-sm-2 col-form-label">Boundary Issues Resolved:</label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="ch_resolved" id="ch_resolved" class="custom-control-input"
                                {{$chDetails->boundary_issue_resolved == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="ch_resolved"></label>
                            </div>
                        </div>
                    </div>
                    <!-- /.form group -->

                  </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center">
                @if ($coordinatorCondition)
                    <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save Boundary Information</button>
                    <br>
                    @endif
                    <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.eoyboundaries') }}'"><i class="fas fa-reply mr-2"></i>Back to Boundaries Report</button>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to EOY Details</button>

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
    var $chIsActive = @json($chIsActive);
    var $einCondition = @json($einCondition);
    var $inquiriesCondition = @json($inquiriesCondition);
    var $chPcId = @json($chPcId);
    var $cdId = @json($cdId);
    var $cdConfId = @json($cdConfId);

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

        // Re-enable the specific "Back" buttons
        $('#back-eoy').prop('disabled', false);
    }
});

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


</script>
@endsection
