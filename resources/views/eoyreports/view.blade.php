@extends('layouts.coordinator_theme')
<style>

.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')


  <!-- Contains page content -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>EOY Details</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
              <li class="breadcrumb-item active">EOY Details</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("eoyreports.update", $chapterList[0]->id) }}'>
        @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chapterList[0]->name }}, {{$chapterList[0]->statename}}</h3>
                <p class="text-center">{{ $chapterList[0]->confname }} Conference, {{ $chapterList[0]->regname }} Region

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">New Board Submitted:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="new_board_submitted" id="new_board_submitted" class="custom-control-input"
                                        {{$chapterList[0]->new_board_submitted == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="new_board_submitted"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">New Board Activated:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="new_board_active" id="new_board_active" class="custom-control-input"
                                        {{$chapterList[0]->new_board_active == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="new_board_active"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">Financial Report Received:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="financial_report_received" id="financial_report_received" class="custom-control-input"
                                        {{$chapterList[0]->financial_report_received == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="financial_report_received"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">Financial Review Complete:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="financial_report_complete" id="financial_report_complete" class="custom-control-input"
                                        {{$chapterList[0]->financial_report_complete == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="financial_report_complete"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">Report Extension Given:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="report_extension" id="report_extension" class="custom-control-input"
                                       onchange="toggleExtensionNotes()"
                                       {{$chapterList[0]->report_extension == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="report_extension"></label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">990N Verifed on irs.gov:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="irs_verified" id="irs_verified" class="custom-control-input"
                                       onchange="toggleIRSVerified()"
                                       {{$financial_report_array->check_current_990N_verified_IRS == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="irs_verified"></label>
                            </div>
                        </div>

                    </li>

                    <li class="list-group-item">

                        <label class="ch_reportrev">Assigned Reviewer:</label>
                        <select name="ch_reportrev" id="ch_reportrev" class="form-control float-right col-sm-6 text-right" style="width: 100%;" >
                            <option value="">Select Coordinator</option>
                            @foreach($reportReviewerList as $revl)
                                <option value="{{$revl->cid}}" {{$financial_report_array->reviewer_id == $revl->cid ? 'selected' : ''}}>{{$revl->rfname}} {{$revl->rlname}} ({{$revl->pos}})</option>
                            @endforeach
                        </select>
                </li>

                    <input type="hidden" id="ch_primarycor" value="{{ $chapterList[0]->primary_coordinator_id }}">
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
                <h3 class="profile-username">{{ (date('Y') - 1) . '-' . date('Y') }} End of Year Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Boundary Issues:</label>
                        </div>
                        @if ($chapterList[0]->boundary_issues != null)
                            <div class="col-sm-6">
                                Chapter has reported boundary issues.
                            </div>
                            <div class="col-sm-3">
                                <label class="mr-2">Resolved:</label>{{ $chapterList[0]->boundary_issue_resolved == 1 ? 'YES' : 'NO' }}
                            </div>
                        @else
                            <div class="col-sm-9">
                                Chpater has not reported any boundary issues.
                            </div>
                        @endif
                        </div>

                        <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Board Report:</label>
                        </div>
                        <div class="col-sm-9">
                            @if($chapterList[0]->new_board_active != '1')
                                <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='{{ route('eoyreports.eoyboardreportview', ['id' => $chapterList[0]->id]) }}'">View Board Election Report</button>
                                <button type="button" class="btn bg-gradient-primary btn-sm" onclick="window.location.href='{{ route('chapters.chapwebsiteview', ['id' => $chapterList[0]->id]) }}'">Activate Board</button>
                            @else
                                Board Report is no longer available after activation.
                                {{-- <button class="btn bg-gradient-primary btn-sm mr-2 disabled">Board Report Not Available</button>
                                <button class="btn bg-gradient-primary btn-sm disabled">Board Activated</button> --}}
                            @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Financial Report:</label>
                        </div>
                        <div class="col-sm-9">
                            <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='{{ route('eoyreports.eoyfinancialreportview', ['id' => $chapterList[0]->id]) }}'">View Financial Report</button>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Extension:<label>

                </div>
                <div class="col-sm-9">
                            <input type="text" name="extension_notes" id="extension_notes" class="form-control" value="{{ $chapterList[0]->extension_notes }}" placeholder="Extension Notes">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Chapter Roster File:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($financial_report_array->roster_path))
                                    <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $financial_report_array->roster_path }}'">View Chapter Roster</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal()">Replace Roster File</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm mr-2 disabled">No file attached</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal()">Upload Roster File</button>
                                @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Primary Bank Statement:</label>
                        </div>
                        <div class="col-sm-9">
                            @if (!empty($financial_report_array->bank_statement_included_path))
                                <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $financial_report_array->bank_statement_included_path }}'">View Bank Statement</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal()">Replace Bank Statement</button>
                            @else
                                <button class="btn bg-gradient-primary btn-sm mr-2 disabled">No file attached</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal()">Upload Bank Statement</button>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Primary Bank Statement:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($financial_report_array->bank_statement_2_included_path))
                                    <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $financial_report_array->bank_statement_2_included_path }}'">View Additional Bank Statement</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal()">Replace Additional Bank Statement</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm mr-2 disabled">No file attached</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal()">Upload Additional Bank Statement</button>
                                @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>990N Filing:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($financial_report_array->file_irs_path))
                                    <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $financial_report_array->file_irs_path }}'">View 990N Confirmation</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal()">Replace 990N Confirmation</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm mr-2 disabled">No file attached</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal()">Upload 990N Confirmation</button>
                                @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label></label>
                    </div>
                    <div class="col-sm-9">
                           <input type="text" name="irs_notes" id="irs_notes" class="form-control" value="{{ $financial_report_array->check_current_990N_notes }}" placeholder="990N Filing Notes">
                        </div>
                    </div>


                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Award #1 Status:</label>
                        </div>
                        <div class="col-sm-9">
                            {{ is_null($financial_report_array['check_award_1_approved']) ? 'N/A' : ($financial_report_array['check_award_1_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_1_approved'] == 1 ? 'YES' : 'N/A')) }}
                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_1_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_1_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                : ($financial_report_array['award_1_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_1_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_1_nomination_type'] == 4 ? 'Outstanding Spirit'
                                : ($financial_report_array['award_1_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_1_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_1_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <label>Award #2 Status:</label>
                        </div>
                        <div class="col-sm-9">
                            {{ is_null($financial_report_array['check_award_2_approved']) ? 'N/A' : ($financial_report_array['check_award_2_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_2_approved'] == 1 ? 'YES' : 'N/A')) }}
                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_2_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_2_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                : ($financial_report_array['award_2_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_2_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_2_nomination_type'] == 4 ? 'Outstanding Spirit'
                                : ($financial_report_array['award_2_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_2_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_2_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <label>Award #3 Status:</label>
                        </div>
                        <div class="col-sm-9">
                            {{ is_null($financial_report_array['check_award_3_approved']) ? 'N/A' : ($financial_report_array['check_award_3_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_3_approved'] == 1 ? 'YES' : 'N/A')) }}
                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_3_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_3_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                : ($financial_report_array['award_3_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_3_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_3_nomination_type'] == 4 ? 'Outstanding Spirit'
                                : ($financial_report_array['award_3_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_3_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_3_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <label>Award #4 Status:</label>
                        </div>
                        <div class="col-sm-9">
                            {{ is_null($financial_report_array['check_award_4_approved']) ? 'N/A' : ($financial_report_array['check_award_4_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_4_approved'] == 1 ? 'YES' : 'N/A')) }}
                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_4_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_4_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                : ($financial_report_array['award_4_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_4_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_4_nomination_type'] == 4 ? 'Outstanding Spirit'
                                : ($financial_report_array['award_4_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_4_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_4_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                         </div>
                        </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <label> Award #5 Status:</label>
                        </div>
                        <div class="col-sm-9">
                            {{ is_null($financial_report_array['check_award_5_approved']) ? 'N/A' : ($financial_report_array['check_award_5_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_5_approved'] == 1 ? 'YES' : 'N/A')) }}
                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_5_nomination_type']) ? 'No Award Selected' : ($financial_report_array['award_5_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                : ($financial_report_array['award_5_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_5_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_5_nomination_type'] == 4 ? 'Outstanding Spirit'
                                : ($financial_report_array['award_5_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_5_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_5_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
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
                    <button type="submit" class="btn bg-gradient-primary mb-3" >Save EOY Information</button>
                    @if ($chapterList[0]->boundary_issues != null)
                        <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.editboundaries', ['id' => $chapterList[0]->id]) }}'">Update Boundary Issues</button>
                    @else
                        <button class="btn bg-gradient-primary mb-3 disabled">Update Boundary Issues</button>
                    @endif
                        <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.editawards', ['id' => $chapterList[0]->id]) }}'">Update Award Information</button>
                    <br>
                    @endif
                    <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.eoystatus') }}'">Back to EOY Status Report</button>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.view', ['id' => $chapterList[0]->id]) }}'">Back to Chapter Details</button>

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
    var $chPCid = @json($chPCid);
    var $coordId = @json($coordId);
    var $corConfId = @json($corConfId);

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

document.addEventListener("DOMContentLoaded", function() {
    // Initialize the state when the page loads
    toggleIRSVerified();

    document.getElementById('irs_verified').addEventListener('change', toggleIRSVerified);
});

function toggleIRSVerified() {
    const irsVerified = document.getElementById('irs_verified');
    const irsNotes = document.getElementById('irs_notes');

    if (irsVerified.checked) {
        irsNotes.setAttribute('readonly', true);
    } else {
        irsNotes.removeAttribute('readonly');
    }
}

document.addEventListener("DOMContentLoaded", function() {
        // Initialize the state when the page loads
        toggleExtensionNotes();
    });

    function toggleExtensionNotes() {
        const reportExtension = document.getElementById('report_extension');
        const extensionNotes = document.getElementById('extension_notes');

        if (reportExtension.checked) {
            extensionNotes.removeAttribute('readonly');
        } else {
            extensionNotes.setAttribute('readonly', true);
        }
    }

$(document).ready(function() {
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

    var selectedCorId = $("#ch_primarycor").val();
    loadCoordinatorList(selectedCorId);

    $("#ch_primarycor").change(function() {
        var selectedValue = $(this).val();
        loadCoordinatorList(selectedValue);
    });
});

function showRosterUploadModal() {
    var chapter_id = "{{ $chapterList[0]->id }}";

    Swal.fire({
        title: 'Upload Chapter Roster',
        html: `
            <form id="uploadRosterForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadRosterForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeRoster', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Roster uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

function showStatement1UploadModal() {
    var chapter_id = "{{ $chapterList[0]->id }}";

    Swal.fire({
        title: 'Upload Statement',
        html: `
            <form id="uploadStatement1Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadStatement1Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeStatement1', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Statement uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

function showStatement2UploadModal() {
    var chapter_id = "{{ $chapterList[0]->id }}";

    Swal.fire({
        title: 'Upload Additional Statement',
        html: `
            <form id="uploadStatement2Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadStatement2Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeStatement2', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Additional Statement uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

function show990NUploadModal() {
    var chapter_id = "{{ $chapterList[0]->id }}";

    Swal.fire({
        title: 'Upload 990N',
        html: `
            <form id="upload990NForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('upload990NForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/store990n', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: '990N uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}


</script>
@endsection