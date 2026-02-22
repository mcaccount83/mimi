@extends('layouts.coordinator_theme')

@section('page_title', $title)
@section('breadcrumb', $breadcrumb)
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
    <form class="form-horizontal" method="POST" action='{{ route("eoyreports.updateattachments", $chDetails->id) }}'>
        @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                    <h3 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                    <p class="mb-0">{{ $chDetails->confname }} Conference, {{ $chDetails->regname }} Region
                  </p>
                </div>

                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-auto fw-bold">New Board Submitted:</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->new_board_submitted == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">New Board Activated:</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->new_board_active == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Financial Report Received</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->financial_report_received == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Financial Review Complete:</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->financial_review_complete == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Report Extension Given:</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->report_extension == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">990N Verifed on irs.gov:</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->irs_verified == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-auto fw-bold">Assigned Reviewer:</div>
                            <div class="col text-end">
                                    @if($chFinancialReport->reviewer_id != null)
                                    {{ $chDetails->reportReviewer->first_name }} {{ $chDetails->reportReviewer->last_name }}
                                    @else
                                        No Reviewer Assigned
                                    @endif
                            </div>
                        </div>
                    </li>

                     <li class="list-group-item">
                          <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                            <div class="row mb-2">
                          <span id="display_corlist"></span>
                            </div>
                        </li>
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
                        <h3>{{ $fiscalYear }} Report Attachments</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                    <div class="row mt-2">
                       <div class="row mb-2">
                        <div class="col-sm-3">
                            <label>Chapter Roster File:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chEOYDocuments->roster_path))
                                    <button class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-roster" onclick="openPdfViewer('{{ $chEOYDocuments->roster_path }}')">View Chapter Roster</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showRosterUploadModal('{{ $chDetails->id }}')">Replace Roster File</button>
                                @else
                                    <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
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
                                <button class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-statement-1" onclick="openPdfViewer('{{ $chEOYDocuments->statement_1_path }}')">View Bank Statement</button>
                                <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showStatement1UploadModal('{{ $chDetails->id }}')">Replace Bank Statement</button>
                            @else
                                <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
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
                                    <button class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-statement-2" onclick="openPdfViewer('{{ $chEOYDocuments->statement_2_path }}')">View Additional Bank Statement</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showStatement2UploadModal('{{ $chDetails->id }}')">Replace Additional Bank Statement</button>
                                @else
                                    <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showStatement2UploadModal('{{ $chDetails->id }}')">Upload Additional Bank Statement</button>
                                @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <label class="col-sm-3 col-form-label">990 Submission Notes:</label>
                        <div class="col-sm-9">
                        <input type="text" name="irs_notes" id="irs_notes" class="form-control" value="{{ $chDetails->documentsEOY->irs_notes }}" >
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-3">
                            <label></label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chEOYDocuments->irs_path))
                                    <button class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-irs" onclick="openPdfViewer('{{ $chEOYDocuments->irs_path }}')">View 990N Confirmation</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="show990NUploadModal('{{ $chDetails->id }}')">Replace 990N Confirmation</button>
                                @else
                                    <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="show990NUploadModal('{{ $chDetails->id }}')">Upload 990N Confirmation</button>
                                @endif
                        </div>
                    </div>

                    <div class="row mb-2 align-middle">
                        <label class="col-sm-3 col-form-label">990N Verified on IRS Website:</label>
                        <div class="col-sm-9">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="irs_verified" id="irs_verified" class="form-check-input"
                                {{ $chDetails->documentsEOY->irs_verified == 1 ? 'checked' : ''}}>
                                <label class="form-check-label" for="irs_verified"></label>
                            </div>
                        </div>
                    </div>
                     <div class="row mb-2">
                        <label class="col-sm-3 ">990N Submission Issues::</label>
                        <div class="col-sm-9">
                            <button type="button" id="irs-issues" class="btn btn-primary bg-gradient btn-sm" onclick="window.location.href='{{ route('eoyreports.editirssubmission', ['id' => $chDetails->id]) }}'">Report 990N Verification Issues</button>
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
                    <button type="submit" class="btn btn-primary bg-gradient mb-2" ><i class="bi bi-floppy-fill me-2"></i>Save Attachment Information</button>
                    <br>
                @endif
                @if ($confId == $chConfId)
                    <button type="button" id="back-attachments" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyattachments') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-stack me-2"></i>Back to Attachments Report</button>
                @elseif ($confId != $chConfId && $ITCondition)
                    <button type="button" id="back-attachments" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyattachments', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-stack me-2"></i>Back to International Attachments Report</button>
                @endif
                <button type="button" id="back-eoy" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Back to EOY Details</button>
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

@endsection
