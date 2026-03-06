@extends('layouts.mimi_theme')

@if ($ITCondition && !$displayTESTING && !$displayLIVE)
    @section('page_title', 'EOY Details *ADMIN*')
    @section('breadcrumb', 'Chapter Award History')
@elseif ($eoyTestCondition && $displayTESTING)
    @section('page_title', 'EOY Details *TESTING*')
    @section('breadcrumb', 'Chapter Award History')
@else
    @section('page_title', 'EOY Details')
    @section('breadcrumb', 'Chapter Award History')
@endif

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
                    <p class="mb-0">{{ $conferenceDescription }} Conference, {{ $conferenceDescription }} Region
                  </p>
                </div>

                  <ul class="list-group list-group-flush mb-3">
                      <li class="list-group-item">
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
