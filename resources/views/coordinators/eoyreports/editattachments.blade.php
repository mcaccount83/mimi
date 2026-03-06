@extends('layouts.mimi_theme')

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
                    <p class="mb-0">{{ $conferenceDescription }} Conference, {{ $conferenceDescription }} Region
                  </p>
                </div>

                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">
                            @include('coordinators.partials.eoyreportinfo')
                        </li>
                        <li class="list-group-item">
                            @include('coordinators.partials.reportreviewer')
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

                    <div class="row mb-2 align-middle">
                        <label class="col-sm-3 col-form-label">990N Verified on IRS Website:</label>
                        <div class="col-sm-9">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="irs_verified" id="irs_verified" class="form-check-input"
                                {{ $chEOYDocuments->irs_verified == 1 ? 'checked' : ''}}>
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
