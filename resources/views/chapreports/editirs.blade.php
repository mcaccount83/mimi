@extends('layouts.coordinator_theme')

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
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                  <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName}} Region
                  <br>
                  EIN: {{$chDetails->ein}}
                  </p>
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">

                            <b>Founded:</b> <span class="float-right">{{ $startMonthName }} {{ $chDetails->start_year }}</span>

                            </li>                            <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
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
                <h3 class="profile-username">IRS Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
<!-- /.form group -->
<div class="form-group row align-items-center mb-3">
    <label class="col-sm-2 col-form-label">EIN Letter Received:</label>
    <div class="col-sm-10 custom-control custom-switch">
        <input type="checkbox" name="ch_ein_letter_display" id="ch_ein_letter" class="custom-control-input" {{$chDocuments->ein_letter == 1 ? 'checked' : ''}} disabled>
        <label class="custom-control-label" for="ch_ein_letter"></label>
        <!-- Hidden input to submit the value -->
        <input type="hidden" name="ch_ein_letter" value="{{ $chDocuments->ein_letter }}">
    </div>
</div>

                        <!-- /.form group -->
                        @if($chDetails->ein == null && ($conferenceCoordinatorCondition || $einCondition))
                           <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label mb-1">EIN Fax Coversheet:</label>
                                <div class="col-sm-10">
                                    <button id="GoodStanding" type="button" class="btn bg-primary mb-1 btn-sm" onclick="window.open('{{ route('pdf.newchapfaxcover', ['id' => $chDetails->id]) }}', '_blank')">EIN Fax Coversheet</button><br>
                                </div>
                            </div>
                        @endif

                       <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label mb-1">EIN Letter:</label>
                            <div class="col-sm-10">
                                @if($chDocuments->ein_letter_path != null)
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="ein-letter" onclick="openPdfViewer('{{ $chDocuments->ein_letter_path }}')">EIN Letter from IRS</button>
                                    {{-- <button class="btn bg-gradient-primary btn-sm" type="button" id="ein-letter" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->ein_letter_path }}'">EIN Letter from IRS</button> --}}
                                @else
                                    <button class="btn bg-gradient-primary btn-sm disabled">No EIN Letter on File</button>
                                @endif
                            </div>
                        </div>
                        <!-- /.form group -->
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label mb-1">EIN Notes:</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="ein_notes" id="ein_notes" class="form-control" value="{{ $chDocuments->ein_notes }}" placeholder="EIN Notes">
                                                    </div>
                                                </div>

                                                 <!-- /.form group -->
 @if($ITCondition == 1 )
    <div class="form-group row align-items-center  mb-3">
        <label class="col-sm-2 col-form-label">Added as Subordinate:</label>
        <div class="col-sm-10 custom-control custom-switch">
            <input type="checkbox" name="ein_sent" id="ein_sent" class="custom-control-input" {{$chDocuments->ein_sent == 1 ? 'checked' : ''}}>
            <label class="custom-control-label" for="ein_sent"></label>
        </div>
    </div>
@else
    <div class="form-group row align-items-center  mb-3">
        <label class="col-sm-2 col-form-label">Added as Subordinate:</label>
        <div class="col-sm-10 custom-control custom-switch">
            <input type="checkbox" name="ein_sent" id="ein_sent" class="custom-control-input" {{$chDocuments->ein_sent == 1 ? 'checked' : ''}} disabled>
            <label class="custom-control-label" for="ein_sent"></label>
            <!-- Hidden input to submit the value -->
            <input type="hidden" name="ein_sent" value="{{ $chDocuments->ein_sent }}">
        </div>
    </div>
@endif


 <!-- /.form group -->

 <div class="form-group row align-items-center  mb-3">
    <label class="col-sm-2 col-form-label">990N Verifed with IRS:</label>
    <div class="col-sm-10 custom-control custom-switch">
        <input type="checkbox" name="irs_verified" id="irs_verified" class="custom-control-input" {{$chDocuments->irs_verified == 1 ? 'checked' : ''}}>
        <label class="custom-control-label" for="irs_verified"></label>
    </div>
</div>

<!-- /.form group -->
                <div class="row">
                            <div class="col-sm-2 col-form-label mb-1">
                                <label>990N Submission:</label>
                            </div>
                            <div class="col-sm-10">
                                @if($chDocuments->irs_path != null)
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="ein-letter" onclick="openPdfViewer('{{ $chDocuments->irs_path }}')">View/Download 990N Submission</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm disabled">No 990N Submission on File</button>
                                @endif
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            <label class="col-sm-2 col-form-label">990N Submission:</label>
                            <div class="col-sm-10">
                                @if($chDocuments->irs_path != null)
                                <button class="btn bg-gradient-primary btn-sm" onclick="window.open('{{ $chDocuments->irs_path }}', '_blank')">View/Download 990N Submission</button>
                            @else
                                <button class="btn bg-gradient-primary btn-sm disabled">No 990N Submission on File</button>
                            @endif
                        </div>
                        </div> --}}



                                                <!-- /.form group -->
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label mb-1">990N Submission Notes:</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="irs_notes" id="irs_notes" class="form-control" value="{{ $chDocuments->irs_notes }}" placeholder="990N Submission Notes">
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
            <div class="card-body text-center">
                @if ($coordinatorCondition)
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="updateEIN('{{ $chDetails->id }}')"><i class="fas fa-university mr-2"></i>Update EIN Number</button>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="showFileUploadModal('{{ $chDetails->id }}')"><i class="fas fa-upload mr-2"></i>Update EIN Letter</button>
                    <br>
                    <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return PreSaveValidate();"><i class="fas fa-save mr-2"></i>Save IRS Information</button>
                @endif
                @if ($confId == $chConfId)
                    <button type="button" id="back-irs" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapreports.chaprpteinstatus') }}'"><i class="fas fa-reply mr-2"></i>Back to IRS Status Report</button>
                @elseif ($confId != $chConfId)
                    <button type="button" id="back-irs" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapreports.chaprpteinstatus', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International IRS Status Report</button>
                @endif
                <button type="button" id="back-details" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter Details</button>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
    @include('layouts.scripts.disablefields')

@endsection
