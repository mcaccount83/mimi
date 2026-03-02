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
                    <p class="mb-0">{{ $conferenceDescription }} Conference, {{ $conferenceDescription }} Region
                  </p>
                    </div>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">
                            @include('partials.eoyreportinfo')
                        </li>
                        <li class="list-group-item">
                            @include('partials.reportreviewer')
                            @include('partials.coordinatorlist')
                        </li>
                        <li class="list-group-item mt-3">
                            @include('partials.chapterstatus')
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


