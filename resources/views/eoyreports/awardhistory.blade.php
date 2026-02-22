@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Payments & Donations')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

.custom-span {
    border: none !important;
    background-color: transparent !important;
    padding: 0.375rem 0 !important; /* Match the vertical padding of form-control */
    box-shadow: none !important;
}
</style>

@section('content')
    <!-- Main content -->
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
                      {{-- <li class="list-group-item">

                        <div class="row">
                            <div class="col-auto fw-bold">{{ $fiscalYear }} Chapter Awards:</div>
                            <div class="col text-end">
                                <button type="button" id="back-eoy" class="btn btn-primary bg-gradient btn-sm" onclick="window.location.href='{{ route('eoyreports.editawards', ['id' => $chDetails->id]) }}'">View/Update Award Information</button>
                            </div>
                        </div>

                      </li> --}}

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
            <div class="card-header p-2">
                {{-- Tab Headers --}}
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#awards-current" data-bs-toggle="tab">{{ $fiscalYear }}</a>
                    </li>
                    @foreach($chAwards as $year => $awards)
                        <li class="nav-item">
                            <a class="nav-link" href="#awards-{{ $year }}" data-bs-toggle="tab">{{ $year }}</a>
                        </li>
                    @endforeach
                </ul>
                </div>
                {{-- Tab Content --}}
                <div class="card-body">
                <div class="tab-content">

                    {{-- Current Year Tab --}}
                    <div class="active tab-pane" id="awards-current">
                        <div class="card-header bg-transparent border-0">
                            <h3>{{ $fiscalYear }} Chapter Awards</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if(count($currentApprovedAwards) > 0)
                                @foreach($currentApprovedAwards as $award)
                                    <div class="card mb-2">
                                        <div class="card-body">
                                            <strong>{{ $awardTypes[$award['awards_type']]->award_type ?? 'Unknown' }}</strong><br>
                                            Description: {{ $award['awards_desc'] }}<br>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                            <div class="card-body">
                                <p class="text-muted">No approved awards for the current year.</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Historical Tabs --}}
                    @foreach($chAwards as $year => $awards)
                        <div class="tab-pane" id="awards-{{ $year }}">
                            <div class="card-header bg-transparent border-0">
                                <h3>{{ $year }} Chapter Awards</h3>
                            </div>
                        <!-- /.card-header -->
                            <div class="card-body">
                                @foreach($awards as $award)
                                    <div class="card mb-2">
                                        <div class="card-body">
                                            <strong>{{ $award->awardtype->award_type }}</strong><br>
                                            Description: {{ $award['awards_desc'] }}<br>
                                            <b><small>Approved: {{ date('m/d/Y', strtotime($award->approved_at)) }} by {{ $award->approved_by }}</small></b><br>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

        <div class="col-md-12">
            <div class="card-body text-center mt-3">
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('eoyreports.editawards', ['id' => $chDetails->id]) }}'"><i class="bi bi-award-fill me-2"></i>Update {{ $fiscalYear }} Awards</button><br>
                    @if ($confId == $chConfId)
                        <button type="button" id="back-awards" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyawards') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-award-fill me-2"></i>Back to Awards Report</button>
                    @elseif ($confId != $chConfId && $ITCondition)
                        <button type="button" id="back-awards" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyawards', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-award-fill me-2"></i>Back to International Awards Report</button>
                    @endif
                    <button type="button" id="back-details" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Chapter Details</button>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@section('customscript')
    @include('layouts.scripts.disablefields')
@endsection
