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
            <input type="hidden" name="hid_extension_notes" value="{{$chEOYDocuments->extension_notes}}">
            <input type="hidden" name="hid_irs_notes" value="{{ $chEOYDocuments->irs_notes }}">

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
                                @if($regionalCoordinatorCondition)
                                        {{$chDetails->documentsEOY->new_board_submitted == 1 ? 'checked' : ''}}>
                                @else
                                    {{$chDetails->documentsEOY->new_board_submitted == 1 ? 'checked' : ''}} disabled>
                                @endif
                                <label class="custom-control-label" for="new_board_submitted"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">New Board Activated:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="new_board_active" id="new_board_active" class="custom-control-input"
                                @if($regionalCoordinatorCondition)
                                        {{$chDetails->documentsEOY->new_board_active == 1 ? 'checked' : ''}}>
                                        @else
                                        {{$chDetails->documentsEOY->new_board_active == 1 ? 'checked' : ''}} disabled>
                                        @endif
                                <label class="custom-control-label" for="new_board_active"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">Financial Report Received:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="financial_report_received" id="financial_report_received" class="custom-control-input"
                                @if($regionalCoordinatorCondition)
                                    {{$chDetails->documentsEOY->financial_report_received == 1 ? 'checked' : ''}}>
                                    @else
                                    {{$chDetails->documentsEOY->financial_report_received == 1 ? 'checked' : ''}} disabled>
                                     @endif
                                <label class="custom-control-label" for="financial_report_received"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">Financial Review Complete:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="financial_review_complete" id="financial_review_complete" class="custom-control-input"
                                @if($regionalCoordinatorCondition)
                                    {{$chDetails->documentsEOY->financial_review_complete == 1 ? 'checked' : ''}}>
                                    @else
                                    {{$chDetails->documentsEOY->financial_review_complete == 1 ? 'checked' : ''}} disabled>
                                     @endif
                                <label class="custom-control-label" for="financial_review_complete"></label>
                            </div>
                          </div>

                          <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">Report Extension Given:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="report_extension" id="report_extension" class="custom-control-input"
                                       onchange="toggleExtensionNotes()"
                                       @if($regionalCoordinatorCondition)
                                            {{$chDetails->documentsEOY->report_extension == 1 ? 'checked' : ''}}>
                                            @else
                                            {{$chDetails->documentsEOY->report_extension == 1 ? 'checked' : ''}} disabled>
                                             @endif
                                <label class="custom-control-label" for="report_extension"></label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 mr-2">990N Verifed on irs.gov:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="irs_verified" id="irs_verified" class="custom-control-input"
                                       onchange="toggleIRSVerified()"
                                       @if($regionalCoordinatorCondition)
                                            {{$chEOYDocuments->check_current_990N_verified_IRS == 1 ? 'checked' : ''}}>
                                            @else
                                            {{$chEOYDocuments->check_current_990N_verified_IRS == 1 ? 'checked' : ''}} disabled>
                                             @endif
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
                    <input type="hidden" id="ch_reportrev" value="{{ $chFinancialReport->reviewer_id }}">

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
                <h3 class="profile-username">{{ $fiscalYear }} End of Year Information</h3>
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
                                @if($chEOYDocuments->new_board_active != '1')
                                    <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='{{ route('eoyreports.editboardreport', ['id' => $chDetails->id]) }}'">View Board Election Report</button>
                                    @if($chEOYDocuments->new_board_submitted == '1')
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
                            <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="openPdfViewer('{{ $chEOYDocuments->$yearColumnName }}')">View/Download Financial PDF</button>
                            <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="generateFinancialReport()">Regenerate Financial PDF</button>
                        </div>
                                    </div>


                    <div class="form-group row align-middle mt-2">
                        <label class="col-sm-3 col-form-label">Extension Notes:</label>
                        <div class="col-sm-9">
                        <input type="text" name="extension_notes" id="extension_notes" class="form-control" value="{{ $chEOYDocuments->extension_notes }}" >
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Chapter Roster File:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chEOYDocuments->roster_path))
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-roster" onclick="openPdfViewer('{{ $chEOYDocuments->roster_path }}')">View Chapter Roster</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal('{{ $chDetails->id }}')">Replace Roster File</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm mr-2 disabled" disabled>No file attached</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal('{{ $chDetails->id }}')">Upload Roster File</button>
                                @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Primary Bank Statement:</label>
                        </div>
                        <div class="col-sm-9">
                            @if (!empty($chEOYDocuments->statement_1_path))
                                <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-statement-1" onclick="openPdfViewer('{{ $chEOYDocuments->statement_1_path }}')">View Bank Statement</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal('{{ $chDetails->id }}')">Replace Bank Statement</button>
                            @else
                                <button class="btn bg-gradient-primary btn-sm mr-2 disabled" disabled>No file attached</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal('{{ $chDetails->id }}')">Upload Bank Statement</button>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Primary Bank Statement:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chEOYDocuments->statement_2_path))
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-statement-2" onclick="openPdfViewer('{{ $chEOYDocuments->statement_2_path }}')">View Additional Bank Statement</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal('{{ $chDetails->id }}')">Replace Additional Bank Statement</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm mr-2 disabled" disabled>No file attached</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal('{{ $chDetails->id }}')">Upload Additional Bank Statement</button>
                                @endif
                        </div>
                    </div>
                    <div class="form-group row align-middle mt-2">
                        <label class="col-sm-3 col-form-label">IRS Notes:</label>
                        <div class="col-sm-9">
                        <input type="text" name="irs_notes" id="irs_notes" class="form-control" value="{{ $chEOYDocuments->irs_notes }}" >
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-sm-3">
                            <label></label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chEOYDocuments->irs_path))
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-irs" onclick="openPdfViewer('{{ $chEOYDocuments->irs_path }}')">View 990N Confirmation</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal('{{ $chDetails->id }}')">Replace 990N Confirmation</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm mr-2 disabled" disabled>No file attached</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal('{{ $chDetails->id }}')">Upload 990N Confirmation</button>
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
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center">
                @if ($coordinatorCondition)
                    <button class="btn bg-gradient-primary mb-3" type="button" id="email-chapter" onclick="showChapterEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}', 'EOY Reports')">
                        <i class="fa fa-envelope mr-2"></i>Email Board</button>
                        <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save EOY Information</button>
                    @if ($chDetails->boundary_issues != null)
                        <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.editboundaries', ['id' => $chDetails->id]) }}'"><i class="fas fa-edit mr-2"></i>Update Boundary Issues</button>
                    @else
                        <button class="btn bg-gradient-primary mb-3 disabled" disabled><i class="fas fa-edit mr-2"></i>Update Boundary Issues</button>
                    @endif
                        <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.editawards', ['id' => $chDetails->id]) }}'"><i class="fas fa-edit mr-2"></i>Update Award Information</button>
                    <br>
                    @endif
                      @if ($confId == $chConfId)
                        <button type="button" id="back-eoystatus" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoystatus') }}'"><i class="fas fa-reply mr-2"></i>Back to EOY Status Report</button>
                    @elseif ($confId != $chConfId && $ITCondition)
                        <button type="button" id="back-eoystatus" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoystatus', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International EOY Status Report</button>
                    @endif
                    <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter Details</button>

            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
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
