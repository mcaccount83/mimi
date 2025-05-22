@extends('layouts.coordinator_theme')

@section('page_title', $title)
@section('breadcrumb', $breadcrumb)

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("eoyreports.update", $chDetails->id) }}'>
        @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">
            <input type="hidden" name="hid_extension_notes" value="{{$chDocuments->extension_notes}}">
            <input type="hidden" name="hid_irs_notes" value="{{ $chFinancialReport->check_current_990N_notes }}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">New Board Submitted:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="new_board_submitted" id="new_board_submitted" class="custom-control-input"
                                        {{$chDetails->documents->new_board_submitted == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="new_board_submitted"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">New Board Activated:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="new_board_active" id="new_board_active" class="custom-control-input"
                                        {{$chDetails->documents->new_board_active == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="new_board_active"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">Financial Report Received:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="financial_report_received" id="financial_report_received" class="custom-control-input"
                                        {{$chDetails->documents->financial_report_received == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="financial_report_received"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">Financial Review Complete:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="financial_review_complete" id="financial_review_complete" class="custom-control-input"
                                        {{$chDetails->documents->financial_review_complete == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="financial_review_complete"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">Report Extension Given:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="report_extension" id="report_extension" class="custom-control-input"
                                       onchange="toggleExtensionNotes()"
                                       {{$chDetails->documents->report_extension == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="report_extension"></label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">990N Verifed on irs.gov:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="irs_verified" id="irs_verified" class="custom-control-input"
                                       onchange="toggleIRSVerified()"
                                       {{$chFinancialReport->check_current_990N_verified_IRS == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="irs_verified"></label>
                            </div>
                        </div>

                    </li>

                    <li class="list-group-item">

                        <label class="ch_reportrev">Assigned Reviewer:</label>
                        <select name="ch_reportrev" id="ch_reportrev" class="form-control float-right col-sm-6 text-right" style="width: 100%;" >
                            <option value="">Select Coordinator</option>
                            @foreach($rrList as $coordinator)
                                <option value="{{ $coordinator['cid'] }}"
                                    {{ isset($chFinancialReport->reviewer_id) && $chFinancialReport->reviewer_id == $coordinator['cid'] ? 'selected' : '' }}>
                                    {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                </option>
                            @endforeach
                        </select>
                </li>

                    <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                    <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
                </ul>
                <div class="text-center">
                    @if ($chDetails->active_status == 1 )
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
                <h3 class="profile-username">{{ (date('Y') - 1) . '-' . date('Y') }} End of Year Information</h3>
                    <!-- /.card-header -->
                    @if ($displayTESTING == '1')
                    *TESTING*
                    @endif
                    @if ($displayTESTING == '1' || $displayLIVE == '1')
                        <div class="row">
                            <div class="col-sm-3">
                                <label>Boundary Issues:</label>
                            </div>
                            @if ($chDetails->boundary_issues != null)
                                <div class="col-sm-6">
                                    Chapter has reported boundary issues.
                                </div>
                                <div class="col-sm-3">
                                    <label class="mr-2">Resolved:</label>{{ $chDetails->boundary_issue_resolved == 1 ? 'YES' : 'NO' }}
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
                                @if($chDocuments->new_board_active != '1')
                                    <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='{{ route('eoyreports.editboardreport', ['id' => $chDetails->id]) }}'">View Board Election Report</button>
                                        @if($chDetails->new_board_submitted == '1')
                                            <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="return PreSaveValidate(false)" >Activate Board</button>
                                        @else
                                            <button type="button" class="btn bg-gradient-primary btn-sm mr-2" disabled >Report Not Submitted</button>
                                        @endif
                                    @else
                                    Board Report is no longer available after activation.
                                @endif
                            </div>
                        </div>
                    @else
                        Report information is not available at this time.
                    @endif

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Financial Report:</label>
                        </div>
                        <div class="col-sm-9">
                            <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='{{ route('eoyreports.reviewfinancialreport', ['id' => $chDetails->id]) }}'">View Financial Report</button>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Extension:<label>

                </div>
                <div class="col-sm-9">
                    @if ($chDocuments->report_extension != null && $chDocuments->extension_notes != null)
                    {{ $chDocuments->extension_notes }}
                 @elseif ($chDocuments->report_extension != null && $chDocuments->extension_notes == null)
                     Chapter been given an extension, but no notes are recorded.
                @else
                    Chapter has not been given an extension.
                @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Chapter Roster File:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chDocuments->roster_path))
                                    {{-- <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->roster_path }}'">View Chapter Roster</button> --}}
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-roster" onclick="openPdfViewer('{{ $chDocuments->roster_path }}')">View Chapter Roster</button>
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
                            @if (!empty($chDocuments->statement_1_path))
                                {{-- <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->statement_1_path }}'">View Bank Statement</button> --}}
                                <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-statement-1" onclick="openPdfViewer('{{ $chDocuments->statement_1_path }}')">View Bank Statement</button>
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
                                @if (!empty($chDocuments->statement_2_path))
                                    {{-- <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->statement_2_path }}'">View Additional Bank Statement</button> --}}
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-statement-2" onclick="openPdfViewer('{{ $chDocuments->statement_2_path }}')">View Additional Bank Statement</button>
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
                    @if ($chFinancialReport->check_current_990N_notes != null)
                        {{ $chFinancialReport->check_current_990N_notes }}
                     @else
                         Chapter has no 990 filing notes.
                    @endif
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-sm-3">
                            <label></label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chDocuments->irs_path))
                                    {{-- <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->irs_path }}'">View 990N Confirmation</button> --}}
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-irs" onclick="openPdfViewer('{{ $chDocuments->irs_path }}')">View 990N Confirmation</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal()">Replace 990N Confirmation</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm mr-2 disabled">No file attached</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal()">Upload 990N Confirmation</button>
                                @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Awards:</label>
                        </div>
                        <div class="col-sm-9">
                            <table width="100%" style="border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #333;">
                                        <td width="20%">Award Category</td>
                                        <td width="65%">Description/Information</td>
                                        <td width="15%">Approval</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $chapter_awards = null;

                                    if (isset($chFinancialReport['chapter_awards']) && !empty($chFinancialReport['chapter_awards'])) {
                                        $blobData = base64_decode($chFinancialReport['chapter_awards']);
                                        $chapter_awards = unserialize($blobData);

                                        if ($chapter_awards === false) {
                                            echo "<tr><td colspan='3'>Error: Failed to unserialize data.</td></tr>";
                                        } elseif (is_array($chapter_awards) && count($chapter_awards) > 0) {
                                            foreach ($chapter_awards as $row) {
                                                echo "<tr style='border-bottom: 1px solid #ddd;'>";

                                                // Get award type name instead of just ID
                                                $awardType = "Unknown";
                                                foreach($allAwards as $award) {
                                                    if($award->id == $row['awards_type']) {
                                                        $awardType = $award->type_short_name;
                                                        break;
                                                    }
                                                }

                                                echo "<td>" . htmlspecialchars($awardType) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['awards_desc']) . "</td>";
                                                echo "<td>" . ($row['awards_approved'] == 1 ? 'Yes' : 'No') . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='3'>No awards.</td></tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3'>No awards.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
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
                    <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save EOY Information</button>
                    @if ($chDetails->boundary_issues != null)
                        <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.editboundaries', ['id' => $chDetails->id]) }}'"><i class="fas fa-edit mr-2"></i>Update Boundary Issues</button>
                    @else
                        <button class="btn bg-gradient-primary mb-3 disabled"><i class="fas fa-edit mr-2"></i>Update Boundary Issues</button>
                    @endif
                        <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.editawards', ['id' => $chDetails->id]) }}'"><i class="fas fa-edit mr-2"></i>Update Award Information</button>
                    <br>
                    @endif
                    <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.eoystatus') }}'"><i class="fas fa-reply mr-2"></i>Back to EOY Status Report</button>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter Details</button>

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
    var $coorId = @json($coorId);
    var $confId = @json($confId);

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

function showRosterUploadModal() {
    var chapter_id = "{{ $chDetails->id }}";

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
    var chapter_id = "{{ $chDetails->id }}";

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
    var chapter_id = "{{ $chDetails->id }}";

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
    var chapter_id = "{{ $chDetails->id }}";

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


 //submit validation function
 function PreSaveValidate(show_submit_message){
    var errMessage="";
          if($("#ch_pre_email").val() != ""){
            if($("#ch_pre_email").val() == $("#ch_avp_email").val() || $("#ch_pre_email").val() == $("#ch_mvp_email").val() || $("#ch_pre_email").val() == $("#ch_trs_email").val() || $("#ch_pre_email").val() == $("#ch_sec_email").val()) {
              errMessage = "The e-mail address provided for the Chapter President was also provided for a different position.  Please enter a unique e-mail address for each board member or mark the position as vacant.";
            }
          }
          if($("#ch_avp_email").val() != ""){
            if($("#ch_avp_email").val() == $("#ch_mvp_email").val() || $("#ch_avp_email").val() == $("#ch_trs_email").val() || $("#ch_avp_email").val() == $("#ch_sec_email").val()) {
              errMessage = "The e-mail address provided for the Chapter AVP was provided for a different position.  Please enter a unique e-mail address for each board member or mark the position as vacant.";
            }
          }
          if($("#ch_mvp_email").val() != ""){
            if($("#ch_mvp_email").val() == $("#ch_trs_email").val() || $("#ch_mvp_email").val() == $("#ch_sec_email").val()) {
              errMessage = "The e-mail address provided for the Chapter MVP was provided for a different position.  Please enter a unique e-mail address for each board member or mark the position as vacant.";
            }
          }
          if($("#ch_trs_email").val() != ""){
            if($("#ch_trs_email").val() == $("#ch_sec_email").val()) {
              errMessage = "The e-mail address provided for the Chapter Treasurer was provided for a different position.  Please enter a unique e-mail address for each board member or mark the position as vacant.";
            }
          }

          if(errMessage.length > 0){
            alert (errMessage);
            return false;
          }
          if(show_submit_message){
                    //Okay, all validation passed, save the records to the database
                    alert ("Thank you for submitting the board information for this chapter.  The new board will not be able to login until the new board has been activated.");
                }
                else{
                    $("#submit_type").val('activate_board');
                    var result=confirm("Are you sure want to Activate Boards?");
                    if(result)
                        $("#board-info").submit();
                    else
                        return false;
                }

            return true;
    }

</script>
@endsection
