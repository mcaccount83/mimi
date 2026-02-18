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
    <form class="form-horizontal" method="POST" action='{{ route("eoyreports.updateirssubmission", $chDetails->id) }}'>
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
                    <li class="list-group-item mt-2">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>New Board Submitted:</label>
                                <span class="float-end">{{ $chDetails->documentsEOY->new_board_submitted == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>New Board Activated:</label>
                                <span class="float-end">{{ $chDetails->documentsEOY->new_board_active == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Financial Report Received</label>
                                <span class="float-end">{{ $chDetails->documentsEOY->financial_report_received == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Financial Review Complete:</label>
                                <span class="float-end">{{ $chDetails->documentsEOY->financial_review_complete == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Report Extension Given:</label>
                                <span class="float-end">{{ $chDetails->documentsEOY->report_extension == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>990N Verifed on irs.gov:</label>
                                <span class="float-end">{{ $chDetails->documentsEOY->irs_verified == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>990N Filing issues:</label>
                                <span class="float-end">{{ $chDetails->documentsEOY->irs_issues == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item mt-2">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Assigned Reviewer:</label>
                                    @if($chFinancialReport->reviewer_id != null)
                                        <span class="float-end">{{ $chDetails->reportReviewer->first_name }} {{ $chDetails->reportReviewer->last_name }}</span>
                                    @else
                                        No Reviewer Assigned
                                    @endif
                            </div>
                        </div>
                </li>

                    <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                    <li class="list-group-item mt-2" id="display_corlist"></li>
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
                <h3 class="profile-username">{{ $fiscalYear }} 990N Filing Details</h3>
                    <!-- /.card-header -->

                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <label>990N Filing:</label>
                        </div>
                        <div class="col-sm-9">
                                @if (!empty($chDetails->documentsEOY->irs_path))
                                    <button class="btn btn-primary bg-gradient btn-sm mb-2" type="button" id="eoy-irs" onclick="openPdfViewer('{{ $chDetails->documentsEOY->irs_path }}')">View 990N Confirmation</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm mb-2" onclick="show990NUploadModal('{{ $chDetails->id }}')">Replace 990N Confirmation</button>
                                @else
                                    <button class="btn btn-primary bg-gradient btn-sm me-2 disabled" disabled>No file attached</button>
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm mb-2" onclick="show990NUploadModal('{{ $chDetails->id }}')">Upload 990N Confirmation</button>
                                @endif
                        </div>
                    </div>

                    <!-- /.form group -->

                    <div class="row mb-3 align-middle">
                        <label class="col-sm-3 col-form-label">990N Verified on IRS Website:</label>
                        <div class="col-sm-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="irs_verified" id="irs_verified" class="form-check-input"
                                {{ $chDetails->documentsEOY->irs_verified == 1 ? 'checked' : ''}}>
                                <label class="form-check-label" for="irs_verified"></label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3 align-middle">
                        <label class="col-sm-3 col-form-label">990N Submission Issues:<br>
                            <small>(Wrong Dates, Not Found, etc)</small></label>
                        <div class="col-sm-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="irs_issues" id="irs_issues" class="form-check-input"
                                {{ $chDetails->documentsEOY->irs_issues == 1 ? 'checked' : ''}}>
                                <label class="form-check-label" for="irs_issues"></label>
                            </div>
                        </div>
                    </div>

                    <!-- This row is hidden by default and shows when irs_issues is checked -->
                    <div class="row mb-3 align-middle" id="irs_details_row" style="display: {{ $chDetails->documentsEOY->irs_issues == 1 ? 'flex' : 'none' }};">
                        <label class="col-sm-2 col-form-label">Wrong Dates Listed:</label>
                        <div class="col-sm-1">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="irs_wrongdate" id="irs_wrongdate" class="form-check-input exclusive-toggle"
                                {{ $chDetails->documentsEOY->irs_wrongdate == 1 ? 'checked' : ''}}>
                                <label class="form-check-label" for="irs_wrongdate"></label>
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Chapter Not Found:</label>
                        <div class="col-sm-1">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="irs_notfound" id="irs_notfound" class="form-check-input exclusive-toggle"
                                {{ $chDetails->documentsEOY->irs_notfound == 1 ? 'checked' : ''}}>
                                <label class="form-check-label" for="irs_notfound"></label>
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label">FILED w/Wrong Dates</label>
                        <div class="col-sm-1">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="irs_filedwrong" id="irs_filedwrong" class="form-check-input exclusive-toggle"
                                {{ $chDetails->documentsEOY->irs_filedwrong == 1 ? 'checked' : ''}}>
                                <label class="form-check-label" for="irs_filedwrong"></label>
                            </div>
                        </div>

                        @if($ITCondition == 1 )
                            <label class="col-sm-2 col-form-label">IRS Notified:</label>
                            <div class="col-sm-1">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="irs_notified" id="irs_notified" class="form-check-input"
                                    {{ $chDetails->documentsEOY->irs_notified == 1 ? 'checked' : ''}} >
                                    <label class="form-check-label" for="irs_notified"></label>
                                </div>
                            </div>
                        @else
                            <label class="col-sm-2 col-form-label">IRS Notified:</label>
                            <div class="col-sm-1">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="irs_notified" id="irs_notified" class="form-check-input"
                                    {{ $chDetails->documentsEOY->irs_notified == 1 ? 'checked' : ''}} disabled>
                                    <label class="form-check-label" for="irs_notified"></label>
                                    <input type="hidden" name="irs_notified" value="{{ $chDetails->documentsEOY->irs_notified }}">
                                </div>
                            </div>
                        @endif
                    </div>
                         <!-- /.form group -->

                     <div class="row mb-3 align-middle mt-2">
                        <label class="col-sm-3 col-form-label">990 Submission Notes:</label>
                        <div class="col-sm-9">
                        <input type="text" name="irs_notes" id="irs_notes" class="form-control" value="{{ $chDetails->documentsEOY->irs_notes }}" >
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
                    <button type="submit" class="btn btn-primary bg-gradient mb-2" ><i class="bi bi-floppy-fill me-2"></i>Save Filing Information</button>
                    <br>
                @endif
                @if ($confId == $chConfId)
                    <button type="button" id="back-irs" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyirssubmission') }}'"><i class="bi bi-chevron-double-left me-2"></i>Back to Filing Report</button>
                @elseif ($confId != $chConfId)
                    @if ($einCondition || $ITCondition )
                        <button type="button" id="back-irs" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyirssubmission', ['check5' => 'yes']) }}'"><i class="bi bi-chevron-double-left me-2"></i>Back to International Filing Report</button>
                    @endif
                @endif
                <button type="button" id="back-eoy" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-chevron-double-left me-2"></i>Back to EOY Details</button>
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
document.addEventListener('DOMContentLoaded', function() {
    const irsIssuesToggle = document.getElementById('irs_issues');
    const irsDetailsRow = document.getElementById('irs_details_row');
    const exclusiveToggles = document.querySelectorAll('.exclusive-toggle');

    // Show/hide details row based on irs_issues toggle
    irsIssuesToggle.addEventListener('change', function() {
        if (this.checked) {
            irsDetailsRow.style.display = 'flex';
        } else {
            irsDetailsRow.style.display = 'none';
            // Optionally uncheck all exclusive toggles when hiding
            exclusiveToggles.forEach(toggle => {
                toggle.checked = false;
            });
        }
    });

    // Make the three issue toggles mutually exclusive
    exclusiveToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            if (this.checked) {
                exclusiveToggles.forEach(otherToggle => {
                    if (otherToggle !== this) {
                        otherToggle.checked = false;
                    }
                });
            }
        });
    });
});

</script>
@endsection
