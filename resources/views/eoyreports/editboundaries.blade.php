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
    <form class="form-horizontal" method="POST" action='{{ route("eoyreports.updateboundaries", $chDetails->id) }}'>
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
                <h3 class="profile-username">{{ $fiscalYear }} Boundary Issues</h3>
                    <!-- /.card-header -->
                    <div class="row mb-3 align-middle">
                        <label class="col-sm-2 col-form-label">Boundary Issues Reported by Chapter:</label>
                        <div class="col-sm-10">
                        <input type="text" name="ch_issue" id="ch_issue" class="form-control" value="{{ $chDetails->boundary_issue_notes }}" disabled>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <div class="row mb-3 align-middle">
                        <label class="col-sm-2 col-form-label">Current Recorded Boundaries:</label>
                        <div class="col-sm-10">
                        <input type="text" name="ch_old_territory" id="ch_old_territory" class="form-control" value="{{ $chDetails->territory }}" disabled>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <div class="row mb-3 align-middle">
                        <label class="col-sm-2 col-form-label">Update Boundaries:</label>
                        <div class="col-sm-10">
                        <input type="text" name="ch_territory" id="ch_territory" class="form-control">
                        </div>
                    </div>
                    <!-- /.form group -->

                    <div class="row mb-3 align-middle">
                        <label class="col-sm-2 col-form-label">Boundary Issues Resolved:</label>
                        <div class="col-sm-10">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="ch_resolved" id="ch_resolved" class="form-check-input"
                                {{$chDetails->boundary_issue_resolved == 1 ? 'checked' : ''}}>
                                <label class="form-check-label" for="ch_resolved"></label>
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
            <div class="card-body text-center mt-3">
                @if ($coordinatorCondition)
                    <button type="submit" class="btn btn-primary bg-gradient mb-2" ><i class="bi bi-floppy-fill me-2"></i>Save Boundary Information</button>
                    <br>
                @endif
                @if ($confId == $chConfId)
                    <button type="button" id="back-boundaries" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyboundaries') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-pin-map-fill me-2"></i>Back to Boundaries Report</button>
                @elseif ($confId != $chConfId && $ITCondition)
                    <button type="button" id="back-boundaries" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyboundaries', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-pin-map-fill me-2"></i>Back to International Boundaries Report</button>
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


