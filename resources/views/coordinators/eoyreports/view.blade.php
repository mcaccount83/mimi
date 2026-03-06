@extends('layouts.mimi_theme')

@if ($ITCondition && !$displayTESTING && !$displayLIVE)
    @section('page_title', 'EOY Details *ADMIN*')
    @section('breadcrumb', 'EOY Details *ADMIN*')
@elseif ($eoyTestCondition && $displayTESTING)
    @section('page_title', 'EOY Details *TESTING*')
    @section('breadcrumb', 'EOY Details *TESTING*')
@else
    @section('page_title', 'EOY Details')
    @section('breadcrumb', 'EOY Details')
@endif

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("eoyreports.update", $chDetails->id) }}'>
        @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">
            <input type="hidden" name="hid_extension_notes" value="{{$chEOYDocuments->extension_notes}}">
            <input type="hidden" name="hid_irs_notes" value="{{ $chEOYDocuments->irs_notes }}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                    <h3 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                    <p class="mb-0">{{ $conferenceDescription }} Conference, {{ $conferenceDescription }} Region
                  </p>
                </div>

                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 me-2">New Board Submitted:</label>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="new_board_submitted" id="new_board_submitted" class="form-check-input"
                                @if($regionalCoordinatorCondition)
                                        {{$chEOYDocuments->new_board_submitted == 1 ? 'checked' : ''}}>
                                @else
                                    {{$chEOYDocuments->new_board_submitted == 1 ? 'checked' : ''}} disabled>
                                @endif
                                <label class="form-check-label" for="new_board_submitted"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 me-2">New Board Activated:</label>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="new_board_active" id="new_board_active" class="form-check-input"
                                @if($regionalCoordinatorCondition)
                                        {{$chEOYDocuments->new_board_active == 1 ? 'checked' : ''}}>
                                        @else
                                        {{$chEOYDocuments->new_board_active == 1 ? 'checked' : ''}} disabled>
                                        @endif
                                <label class="form-check-label" for="new_board_active"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 me-2">Financial Report Received:</label>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="financial_report_received" id="financial_report_received" class="form-check-input"
                                @if($regionalCoordinatorCondition)
                                    {{$chEOYDocuments->financial_report_received == 1 ? 'checked' : ''}}>
                                    @else
                                    {{$chEOYDocuments->financial_report_received == 1 ? 'checked' : ''}} disabled>
                                     @endif
                                <label class="form-check-label" for="financial_report_received"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 me-2">Financial Review Complete:</label>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="financial_review_complete" id="financial_review_complete" class="form-check-input"
                                @if($regionalCoordinatorCondition)
                                    {{$chEOYDocuments->financial_review_complete == 1 ? 'checked' : ''}}>
                                    @else
                                    {{$chEOYDocumentsY->financial_review_complete == 1 ? 'checked' : ''}} disabled>
                                     @endif
                                <label class="form-check-label" for="financial_review_complete"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 me-2">Report Extension Given:</label>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="report_extension" id="report_extension" class="form-check-input"
                                       onchange="toggleExtensionNotes()"
                                       @if($regionalCoordinatorCondition)
                                            {{$chEOYDocuments->report_extension == 1 ? 'checked' : ''}}>
                                            @else
                                            {{$chEOYDocuments->report_extension == 1 ? 'checked' : ''}} disabled>
                                             @endif
                                <label class="form-check-label" for="report_extension"></label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 me-2">990N Verifed on irs.gov:</label>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="irs_verified" id="irs_verified" class="form-check-input"
                                       onchange="toggleIRSVerified()"
                                       @if($regionalCoordinatorCondition)
                                            {{$chEOYDocuments->irs_verified == 1 ? 'checked' : ''}}>
                                            @else
                                            {{$chEOYDocuments->documentsEOY->irs_verified == 1 ? 'checked' : ''}} disabled>
                                             @endif
                                <label class="form-check-label" for="irs_verified"></label>
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item">
                        <div class="row">
                        <label  class="col-sm-6 col-form-label">Assigned Reviewer:</label>
                        <div class="col-sm-6">
                        <select name="ch_reportrev" id="ch_reportrev" class="form-control float-end col-sm-6 text-end" style="width: 100%;" >
                            <option value="">Select Reviewer</option>
                            @foreach($rrList as $coordinator)
                                <option value="{{ $coordinator['cid'] }}"
                                    {{ isset($chFinancialReport->reviewer_id) && $chFinancialReport->reviewer_id == $coordinator['cid'] ? 'selected' : '' }}>
                                    {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                        </div>
                        <input type="hidden" id="ch_reportrev" value="{{ $chFinancialReport->reviewer_id }}">
                    </li>
                        <li class="list-group-item">
                            @include('coordinators.partials.coordinatorlist')
                        </li>
                        <li class="list-group-item mt-3">
                            @include('coordinators.partials.chapterstatus')
                        </li>
                  </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="card-header bg-transparent border-0">
                <h3>{{ $fiscalYear }} End of Year Information</h3>
                    @if ($displayTESTING == '1')
                    *TESTING*
                    @endif
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                    @if ($displayTESTING == '1' || $displayLIVE == '1' || $ITCondition)
                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <label>Boundary Issues:</label>
                            </div>
                            @if ($chDetails->boundary_issues != null)
                                <div class="col-sm-6">
                                    Chapter has reported boundary issues.
                                </div>
                                <div class="col-sm-3">
                                    <label class="me-2">Resolved:</label>{{ $chDetails->boundary_issue_resolved == 1 ? 'YES' : 'NO' }}
                                </div>
                            @else
                                <div class="col-sm-9">
                                    Chapter has not reported any boundary issues.
                                </div>
                            @endif
                            </div>

                            <div class="row mb-2">
                            <div class="col-sm-3">
                                <label>Board Report:</label>
                            </div>
                            <div class="col-sm-9">
                                @if($chEOYDocuments->new_board_active != '1')
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="window.location.href='{{ route('eoyreports.editboardreport', ['id' => $chDetails->id]) }}'">View Board Election Report</button>
                                    @if($chEOYDocuments->new_board_submitted == '1')
                                        <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="return PreSaveValidate(false)" >Activate Board</button>
                                    @else
                                        <button type="button" class="btn btn-primary bg-gradient btn-sm" disabled >Report Not Submitted</button>
                                    @endif
                                @else
                                    Board Report is no longer available after activation.
                                @endif
                            </div>
                        </div>
                    @else
                        Report information is not available at this time.
                    @endif

                    <div class="row mb-2">
                        <div class="col-sm-3">
                            <label>Financial Report:</label>
                        </div>
                        <div class="col-sm-9">
                            <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="window.location.href='{{ route('eoyreports.editfinancialreview', ['id' => $chDetails->id]) }}'">View Financial Report</button>
                            <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="openPdfViewer('{{ $chEOYDocuments->$yearColumnName }}')">View/Download Financial PDF</button>
                            <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="generateFinancialReport()">Regenerate Financial PDF</button>
                        </div>
                                    </div>


                    <div class="row mb-2">
                        <label class="col-sm-3 col-form-label">Extension Notes:</label>
                        <div class="col-sm-9">
                        <input type="text" name="extension_notes" id="extension_notes" class="form-control" value="{{ $chEOYDocuments->extension_notes }}" >
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-3">
                            <label>Chapter Roster File:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chEOYDocuments->roster_path))
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-roster" onclick="openPdfViewer('{{ $chEOYDocuments->roster_path }}')">View Chapter Roster</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showRosterUploadModal('{{ $chDetails->id }}')">Replace Roster File</button>
                                @else
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showRosterUploadModal('{{ $chDetails->id }}')">Upload Roster File</button>
                                @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-3">
                            <label>Primary Bank Statement:</label>
                        </div>
                        <div class="col-sm-9">
                            @if (!empty($chEOYDocuments->statement_1_path))
                                <button type="button" class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-statement-1" onclick="openPdfViewer('{{ $chEOYDocuments->statement_1_path }}')">View Bank Statement</button>
                                <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showStatement1UploadModal('{{ $chDetails->id }}')">Replace Bank Statement</button>
                            @else
                                <button type="button" class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
                                <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showStatement1UploadModal('{{ $chDetails->id }}')">Upload Bank Statement</button>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-3">
                            <label>Additional Bank Statement:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chEOYDocuments->statement_2_path))
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-statement-2" onclick="openPdfViewer('{{ $chEOYDocuments->statement_2_path }}')">View Additional Bank Statement</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showStatement2UploadModal('{{ $chDetails->id }}')">Replace Additional Bank Statement</button>
                                @else
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showStatement2UploadModal('{{ $chDetails->id }}')">Upload Additional Bank Statement</button>
                                @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="col-sm-3 col-form-label">990 Submission Notes:</label>
                        <div class="col-sm-9">
                        <input type="text" name="irs_notes" id="irs_notes" class="form-control" value="{{ $chEOYDocuments->irs_notes }}" >
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-3">
                            <label></label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chEOYDocuments->irs_path))
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-irs" onclick="openPdfViewer('{{ $chEOYDocuments->irs_path }}')">View 990N Confirmation</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="show990NUploadModal('{{ $chDetails->id }}')">Replace 990N Confirmation</button>
                                @else
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="show990NUploadModal('{{ $chDetails->id }}')">Upload 990N Confirmation</button>
                                @endif
                        </div>
                    </div>

                    <div class="row mb-2">
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
                                    @php
                                        $chapter_awards = null;

                                        if (isset($chFinancialReport['chapter_awards']) && !empty($chFinancialReport['chapter_awards'])) {
                                            $blobData = base64_decode($chFinancialReport['chapter_awards']);
                                            $chapter_awards = unserialize($blobData);
                                        }
                                    @endphp

                                    @if ($chapter_awards === false)
                                        <tr><td colspan='3'>Error: Failed to unserialize data.</td></tr>
                                    @elseif (is_array($chapter_awards) && count($chapter_awards) > 0)
                                        @foreach ($chapter_awards as $row)
                                            @php
                                                // Get award type name instead of just ID
                                                $awardType = "Unknown";
                                                foreach($allAwards as $award) {
                                                    if($award->id == $row['awards_type']) {
                                                        $awardType = $award->type_short_name;
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            <tr style='border-bottom: 1px solid #ddd;'>
                                                <td>{{ $awardType }}</td>
                                                <td>{{ $row['awards_desc'] }}</td>
                                                <td>{{ $row['awards_approved'] == 1 ? 'Yes' : 'No' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr><td colspan='3'>No awards.</td></tr>
                                    @endif
                                </tbody>
                            </table>
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
            <div class="card-body text-center mt-3">
                @if ($coordinatorCondition)
                    <button type="button" class="btn btn-primary bg-gradient mb-2" type="button" id="email-chapter" onclick="showChapterEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}', 'EOY Reports')">
                        <i class="bi bi-envelope-fill me-2"></i>Email Board</button>
                        <button type="submit" class="btn btn-primary bg-gradient mb-2" ><i class="bi bi-floppy-fill me-2"></i>Save EOY Information</button>
                    @if ($chDetails->boundary_issues != null)
                        <button type="button" id="back-eoy" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('eoyreports.editboundaries', ['id' => $chDetails->id]) }}'"><i class="bi bi-pin-map-fill me-2"></i>Update Boundary Issues</button>
                    @else
                        <button type="button" class="btn btn-primary bg-gradient mb-2 disabled" disabled><i class="bi bi-pin-map-fill me-2"></i>Update Boundary Issues</button>
                    @endif
                        <button type="button" id="back-eoy" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('eoyreports.editawards', ['id' => $chDetails->id]) }}'"><i class="bi bi-award-fill me-2"></i>Update Award Information</button>
                    <br>
                    @endif
                      @if ($confId == $chConfId)
                        <button type="button" id="back-eoystatus" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoystatus') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Back to EOY Status Report</button>
                    @elseif ($confId != $chConfId && $ITCondition)
                        <button type="button" id="back-eoystatus" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoystatus', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Back to International EOY Status Report</button>
                    @endif
                    <button type="button" id="back-eoy" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i></i>Back to Chapter Details</button>

            </div>
        </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
    @include('layouts.scripts.disablefields', ['includeEoyConditions' => true])

<script>
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

    const chapterId = @json($chDetails->id);
    const chActiveId = @json($chActiveId);

   function generateFinancialReport() {
        Swal.fire({
            title: 'Generate Financial Report',
            html: `
                <p>This will generate the financial report PDF.</p>
                <input type="hidden" id="chapter_id" value="${chapterId}">
                <input type="hidden" id="active_id" value="${chActiveId}">
            `,
            showCancelButton: true,
            confirmButtonText: 'Generate Letter',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            preConfirm: () => {
                const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
                const chActiveId = Swal.getPopup().querySelector('#active_id').value;
                return { chapterId, chActiveId };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we generate your letter.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();

                        $.ajax({
                            url: '{{ route('pdf.generatefinancialreport') }}',
                            type: 'POST',
                            data: {
                                chapterId: data.chapterId,
                                chActiveId: data.chActiveId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
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
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong. Please try again.',
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
