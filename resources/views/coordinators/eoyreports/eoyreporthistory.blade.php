@extends('layouts.mimi_theme')

@if ($ITCondition && !$displayEOYTESTING && !$displayEOYLIVE)
    @section('page_title', $reportYearRange.' EOY Details *ADMIN*')
    @section('breadcrumb', 'Chapter Award History')
@elseif ($eoyTestCondition && $displayEOYTESTING)
    @section('page_title', $reportYearRange.' EOY Details *TESTING*')
    @section('breadcrumb', 'Chapter Award History')
@else
    @section('page_title', $reportYearRange.' EOY Details')
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
                    <p class="mb-0">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region
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
                <div class="card-body">
                    <h3>{{ $reportYearRange }} Financial Reports</h3>
                    @if (count($financialReportPdfs) > 0)
                        @foreach ($financialReportPdfs as $year => $path)
                            <br><button type="button" class="btn btn-primary bg-gradient btn-sm me-1 mb-1 keep-enabled" onclick="openPdfViewer('{{ $path }}')">{{ $year - 1 }}-{{ $year }} Financial Report</button>
                        @endforeach
                    @else
                        <p class="text-muted">No historical reports available.</p>
                    @endif
                </div>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

        <div class="col-md-12">
            <div class="card-body text-center mt-3">
                    @if ($confId == $chConfId)
                        <button type="button" id="back-eoy" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyfinancialreport') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-calculator-fill me-2"></i>Back to Financial Report</button>
                    @elseif ($confId != $chConfId && $ITCondition)
                        <button type="button" id="back-awards" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyfinancialreport', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-calculator-fill me-2"></i>Back to International Financial Reports</button>
                    @endif
                    <button type="button" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Back to EOY Details</button>
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
