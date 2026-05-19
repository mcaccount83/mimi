@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Donation History')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header p-2">

                {{-- Tab Headers --}}
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#awards-current" data-bs-toggle="tab">{{ $reportYearRange }}</a>
                    </li>
                    @foreach($chAwards as $yearId  => $awards)
                        <li class="nav-item">
                            <a class="nav-link" href="#awards-{{ $yearId  }}" data-bs-toggle="tab">{{ $awards->first()->fiscalYear->fiscal_year }}</a>
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
                            <h3>{{ $reportYearRange }} Chapter Awards</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @if(count($currentApprovedAwards) > 0)
                                @foreach($currentApprovedAwards as $award)
    @php $badge = $badgeLookup->get($financialReport->report_year_id.'_'.$award['awards_type']); @endphp
    <div class="card mb-2">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 80px; flex-shrink: 0; text-align: center;">
                @if($badge)
                    <img src="https://drive.google.com/thumbnail?id={{ $badge->file_path }}&sz=w80"
                         style="max-height: 80px; cursor: pointer;"
                         onclick="openImageViewer('{{ $badge->file_path }}', '{{ addslashes($badge->eoyAward->award_type) }}', '{{ addslashes($badge->fiscalYear->fiscal_year) }}')">
                @else
                    <div style="width: 80px; height: 80px;"></div>
                @endif
            </div>
            <div>
                <strong>{{ $awardTypes[$award['awards_type']]->award_type ?? 'Unknown' }}</strong><br>
                Description: {{ $award['awards_desc'] }}
            </div>
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
                    @foreach($chAwards as $yearId  => $awards)
                        <div class="tab-pane" id="awards-{{ $yearId  }}">
                            <div class="card-header bg-transparent border-0">
                                <h3>{{ $awards->first()->fiscalYear->fiscal_year }} Chapter Awards</h3>
                            </div>
                        <!-- /.card-header -->
                            <div class="card-body">
                                @foreach($awards as $award)
    @php $badge = $badgeLookup->get($award->report_year_id.'_'.$award->awards_type); @endphp
    <div class="card mb-2">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 80px; flex-shrink: 0; text-align: center;">
                @if($badge)
                    <img src="https://drive.google.com/thumbnail?id={{ $badge->file_path }}&sz=w80"
                         style="max-height: 80px; cursor: pointer;"
                         onclick="openImageViewer('{{ $badge->file_path }}', '{{ addslashes($badge->eoyAward->award_type) }}', '{{ addslashes($badge->fiscalYear->fiscal_year) }}')">
                @else
                    <div style="width: 80px; height: 80px;"></div>
                @endif
            </div>
            <div>
                <strong>{{ $award->awardtype->award_type }}</strong><br>
                Description: {{ $award['awards_desc'] }}<br>
                <b><small>Approved: {{ date('m/d/Y', strtotime($award->approved_at)) }} by {{ $award->approved_by }}</small></b>
            </div>
        </div>
    </div>
@endforeach
                            </div>
                        </div>
                    @endforeach

                </div>
                <!-- /.tab-content -->
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@section('customscript')
    @include('layouts.scripts.disablefields')
@endsection
