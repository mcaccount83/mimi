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
                                <span class="float-right">{{ $chDetails->documents->financial_review_complete == 1 ? 'YES' : 'NO' }}</span>
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
                <h3 class="profile-username">{{ (date('Y') - 1) . '-' . date('Y') }} Report Attachments</h3>
                    <!-- /.card-header -->
                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Chapter Roster File:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chDetails->documents->roster_path))
                                    {{-- <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDetails->documents->roster_path }}'">View Chapter Roster</button> --}}
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-roster" onclick="openPdfViewer('{{ $chDetails->documents->roster_path }}')">View Chapter Roster</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal('{{ $chDetails->id }}')">Replace Roster File</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm mr-2 disabled">No file attached</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal('{{ $chDetails->id }}')">Upload Roster File</button>
                                @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Primary Bank Statement:</label>
                        </div>
                        <div class="col-sm-9">
                            @if (!empty($chDetails->documents->statement_1_path))
                                {{-- <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDetails->documents->statement_1_path }}'">View Bank Statement</button> --}}
                                <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-statement-1" onclick="openPdfViewer('{{ $chDetails->documents->statement_1_path }}')">View Bank Statement</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal('{{ $chDetails->id }}')">Replace Bank Statement</button>
                            @else
                                <button class="btn bg-gradient-primary btn-sm mr-2 disabled">No file attached</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal('{{ $chDetails->id }}')">Upload Bank Statement</button>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>Primary Bank Statement:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty( $chDetails->documents->statement_2_path))
                                    {{-- <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDetails->documents->statement_2_path }}'">View Additional Bank Statement</button> --}}
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-statement-2" onclick="openPdfViewer('{{ $chDetails->documents->statement_2_path }}')">View Additional Bank Statement</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal('{{ $chDetails->id }}')">Replace Additional Bank Statement</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm mr-2 disabled">No file attached</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal('{{ $chDetails->id }}')">Upload Additional Bank Statement</button>
                                @endif
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>990N Filing:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chDetails->documents->irs_path))
                                    {{-- <button type="button" class="btn bg-gradient-primary btn-sm mr-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDetails->documents->irs_path }}'">View 990N Confirmation</button> --}}
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="eoy-irs" onclick="openPdfViewer('{{ $chDetails->documents->irs_path }}')">View 990N Confirmation</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal('{{ $chDetails->id }}')">Replace 990N Confirmation</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm mr-2 disabled">No file attached</button>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal('{{ $chDetails->id }}')">Upload 990N Confirmation</button>
                                @endif
                        </div>
                    </div>
                     <!-- /.form group -->

                     <div class="form-group row align-middle mt-2">
                        <label class="col-sm-3 col-form-label">990 Submission Notes:</label>
                        <div class="col-sm-9">
                        <input type="text" name="irs_notes" id="irs_notes" class="form-control" value="{{ $chDetails->documents->irs_notes }}" >
                        </div>
                    </div>
                    <!-- /.form group -->

                    <div class="form-group row align-middle">
                        <label class="col-sm-3 col-form-label">990N Verified on IRS Website:</label>
                        <div class="col-sm-9">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="irs_verified" id="irs_verified" class="custom-control-input"
                                {{ $chDetails->financialReport->check_current_990N_verified_IRS == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="irs_verified"></label>
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
                    <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save Attachment Information</button>
                    <br>
                @endif
                @if ($confId == $chConfId)
                    <button type="button" id="back-attachments" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyattachments') }}'"><i class="fas fa-reply mr-2"></i>Back to Attachments Report</button>
                @elseif ($confId != $chConfId && $ITCondition)
                    <button type="button" id="back-attachments" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyattachments', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Attachments Report</button>
                @endif
                <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to EOY Details</button>
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
