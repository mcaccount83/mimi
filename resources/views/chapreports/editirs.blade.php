@extends('layouts.mimi_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'IRS Information')
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
    <form class="form-horizontal" method="POST" action='{{ route("chapreports.updateirs", $chDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="ch_state" value="{{$stateShortName}}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                    <h3 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                    <p class="mb-0">{{ $conferenceDescription }} Conference, {{ $conferenceDescription }} Region
                    <br>
                  EIN: {{$chDetails->ein}}
                  </p>
                </div>

                <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item">
                            @include('partials.founderhistory')
                        </li>
                        <li class="list-group-item">
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
                <div class="card-body">
                    <div class="card-header bg-transparent border-0">
                        <h3>IRS Information</h3>
                     </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row mb-3 row align-items-center">
                            <label class="col-sm-2 col-form-label">EIN Letter Received:</label>
                            <div class="col-sm-10 form-check form-switch">
                                <input type="checkbox" name="ch_ein_letter_display" id="ch_ein_letter" class="form-check-input" {{$chEOYDocuments->ein_letter == 1 ? 'checked' : ''}} disabled>
                                <label class="form-check-label" for="ch_ein_letter"></label>
                                <!-- Hidden input to submit the value -->
                                <input type="hidden" name="ch_ein_letter" value="{{ $chEOYDocuments->ein_letter }}">
                            </div>
                        </div>

                        @if($chDetails->ein == null && ($conferenceCoordinatorCondition || $einCondition))
                           <div class="row mb-3">
                                                    <label class="col-sm-2 col-form-label mb-1">EIN Fax Coversheet:</label>
                                <div class="col-sm-10">
                                    <button id="GoodStanding" type="button" class="btn btn-primary bg-gradient btn-sm" onclick="window.open('{{ route('pdf.newchapfaxcover', ['id' => $chDetails->id]) }}', '_blank')">EIN Fax Coversheet</button><br>
                                </div>
                            </div>
                        @endif

                       <div class="row mb-3">
                                                    <label class="col-sm-2 col-form-label mb-1">EIN Letter:</label>
                            <div class="col-sm-10">
                                @if($chEOYDocuments->ein_letter_path != null)
                                    <button class="btn btn-primary bg-gradient btn-sm" type="button" id="ein-letter" onclick="openPdfViewer('{{ $chEOYDocuments->ein_letter_path }}')">EIN Letter from IRS</button>
                                @else
                                    <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No EIN Letter on File</button>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label mb-1">EIN Notes:</label>
                            <div class="col-sm-10">
                                <input type="text" name="ein_notes" id="ein_notes" class="form-control" value="{{ $chEOYDocuments->ein_notes }}" placeholder="EIN Notes">
                            </div>
                        </div>

                        @if($ITCondition == 1 )
                            <div class="row mb-3 row align-items-center  mb-3">
                                <label class="col-sm-2 col-form-label">Added as Subordinate:</label>
                                <div class="col-sm-10 form-check form-switch">
                                    <input type="checkbox" name="ein_sent" id="ein_sent" class="form-check-input" {{$chEOYDocuments->ein_sent == 1 ? 'checked' : ''}}>
                                    <label class="form-check-label" for="ein_sent"></label>
                                </div>
                            </div>
                        @else
                            <div class="row mb-3 row align-items-center  mb-3">
                                <label class="col-sm-2 col-form-label">Added as Subordinate:</label>
                                <div class="col-sm-10 form-check form-switch">
                                    <input type="checkbox" name="ein_sent" id="ein_sent" class="form-check-input" {{$chEOYDocuments->ein_sent == 1 ? 'checked' : ''}} disabled>
                                    <label class="form-check-label" for="ein_sent"></label>
                                    <!-- Hidden input to submit the value -->
                                    <input type="hidden" name="ein_sent" value="{{ $chEOYDocuments->ein_sent }}">
                                </div>
                            </div>
                        @endif

                        <div class="row mb-3 row align-items-center  mb-3">
                            <label class="col-sm-2 col-form-label">990N Verifed with IRS:</label>
                            <div class="col-sm-10 form-check form-switch">
                                <input type="checkbox" name="irs_verified" id="irs_verified" class="form-check-input" {{$chEOYDocuments->irs_verified == 1 ? 'checked' : ''}}>
                                <label class="form-check-label" for="irs_verified"></label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2 col-form-label mb-1">
                                <label>990N Submission:</label>
                            </div>
                            <div class="col-sm-10">
                                @if($chEOYDocuments->irs_path != null)
                                    <button class="btn btn-primary bg-gradient btn-sm" type="button" id="ein-letter" onclick="openPdfViewer('{{ $chEOYDocuments->irs_path }}')">View/Download 990N Submission</button>
                                @else
                                    <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No 990N Submission on File</button>
                                @endif
                            </div>
                        </div>

                    <div class="row mb-3 row">
                        <label class="col-sm-2 col-form-label mb-1">990N Submission Notes:</label>
                        <div class="col-sm-10">
                            <input type="text" name="irs_notes" id="irs_notes" class="form-control" value="{{ $chEOYDocuments->irs_notes }}" placeholder="990N Submission Notes">
                        </div>
                    </div>
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
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="updateEIN('{{ $chDetails->id }}')"><i class="bi bi-bank me-2"></i>Update EIN Number</button>
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="showFileUploadModal('{{ $chDetails->id }}')"><i class="bi bi-upload me-2"></i>Update EIN Letter</button>
                    <br>
                    <button type="submit" class="btn btn-primary bg-gradient mb-2" onclick="return PreSaveValidate();"><i class="bi bi-floppy-fill me-2"></i>Save IRS Information</button>
                @endif
                @if ($confId == $chConfId)
                    <button type="button" id="back-irs" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapreports.chaprpteinstatus') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-bank me-2"></i>Back to IRS Status Report</button>
                @elseif ($confId != $chConfId)
                    <button type="button" id="back-irs" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapreports.chaprpteinstatus', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-bank me-2"></i>Back to International IRS Status Report</button>
                @endif
                <button type="button" id="back-details" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Chapter Details</button>
       </div>
            </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
    @include('layouts.scripts.disablefields')

@endsection
