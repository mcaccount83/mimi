@extends('layouts.mimi_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Board Information')

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("chapters.update", $chDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="ch_state" value="{{$stateShortName}}">
            <input type="hidden" name="ch_hid_webstatus" value="{{ $websiteLink }}">
            <input type="hidden" name="ch_hid_preknown" value="{{$chDetails->former_name}}">
            <input type="hidden" name="ch_hid_sistered" value="{{$chDetails->sistered_by}}">
            <input type="hidden" name="ch_hid_primarycor" value="{{$chDetails->primary_coordinator_id}}">
            <input type="hidden" name="ch_hid_status" value="{{ $chapterStatus }}">
            <input type="hidden" name="ch_hid_probation" value="{{ $probationReason }}">
            <input type="hidden" name="ch_hid_boundariesterry" value="{{ $chDetails->territory}}" >

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

                  <ul class="list-group list-group-flush mb-2">
                    <li class="list-group-item">
                    <div class="row">
                            <label class="col-sm-6 col-form-label">EIN Notes:</label>
                            <div class="col-sm-6"><input type="text" name="ein_notes" id="ein_notes" class="form-control float-end col-sm-8 mb-1 text-end" value="{{ $chEOYDocuments->ein_notes }}" placeholder="EIN Notes">
                            </div>
                    </div>
                    </li>
                    <li class="list-group-item">
                            @include('coordinators.partials.paymentinfo')
                            @include('coordinators.partials.donationinfo')
                        </li>
                        <li class="list-group-item">
                        <div class="row">
                            <div class="col-auto fw-bold">Founded:</div>
                            <div class="col text-end">
                                {{ $startMonthName }} {{ $chDetails->start_year }}
                            </div>
                        </div>
                        <div class="row">
                                <label class="col-sm-6 col-form-label">Formerly Known As:</label>
                                <div class="col-sm-6">
                                    <input type="text" name="ch_preknown" id="ch_preknown" class="form-control float-end col-sm-6 mb-1 text-end" value="{{ $chDetails->former_name }}" placeholder="Former Chapter Name">
                                </div>
                            </div>
                          <div class="row">
                            <label class="col-sm-6 col-form-label">Sistered By:</label>
                            <div class="col-sm-6"><input type="text" name="ch_sistered" id="ch_sistered" class="form-control float-end col-sm-6 text-end" value="{{ $chDetails->sistered_by }}" placeholder="Chapter Name">
                      </div>
                        </div>
                        </li>
                        <li class="list-group-item">
                            @if($regionalCoordinatorCondition)
                                @include('coordinators.partials.coordinatorlistupdate')
                            @else
                                @include('coordinators.partials.coordinatorlist')
                            @endif
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
                    <div class="card-header bg-transparent border-0">
                        <h3>General Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                            <div class="row mb-1">
                                <label class="col-sm-2 col-form-label">Chapter Name:</label>
                                <div class="col-sm-10">
                                <input type="text" name="ch_name" id="ch_name" class="form-control" value="{{ $chDetails->name }}"  required disabled onchange="PreviousNameReminder()">
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="row mb-1">
                                <label class="col-sm-2 col-form-label">Boundaries:</label>
                                <div class="col-sm-10">
                                <input type="text" name="ch_boundariesterry" id="ch_boundariesterry" class="form-control" value="{{ $chDetails->territory }}"  required >
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="row mb-1">
                                <label class="col-sm-2 col-form-label">Status:</label>
                                <div class="col-sm-3">
                                    <select name="ch_status" id="ch_status"class="form-control" style="width: 100%;" required>
                                        <option value="">Select Status</option>
                                        @foreach($allStatuses as $status)
                                            <option value="{{$status->id}}"
                                                @if($chDetails->status_id == $status->id) selected @endif>
                                                {{$status->chapter_status}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <label class="col-sm-2 col-form-label ms-5" id="probationLabel" style="{{ $chDetails->status_id != \App\Enums\OperatingStatusEnum::OK ? '' : 'display: none;' }}">Probation Reason:</label>
                                <div class="col-sm-3" id="probationField" style="{{ $chDetails->status_id != \App\Enums\OperatingStatusEnum::OK ? '' : 'display: none;' }}">
                                    <select name="ch_probation" id="ch_probation" class="form-control" style="width: 100%;" {{ $chDetails->status_id != \App\Enums\OperatingStatusEnum::OK ? 'required' : '' }}>
                                        <option value="">Select Reason</option>
                                        @foreach($allProbation as $probation)
                                            <option value="{{$probation->id}}"
                                                @if($chDetails->probation_id == $probation->id) selected @endif>
                                                {{$probation->probation_reason}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="row mb-1">
                                <label class="col-sm-2 col-form-label">Status Notes:</label>

                                <div class="col-sm-8">
                                    <input type="text" name="ch_notes" id="ch_notes" class="form-control" value="{{ $chDetails->notes }}"  placeholder="Status Notes" >
                                    </div>
                            </div>
                             <!-- /.form group -->
                             <div class="row mb-1">
                                <label class="col-sm-2 col-form-label">Email/Mailing:</label>
                                <div class="col-sm-3">
                                <input type="text" name="ch_email" id="ch_email" class="form-control" value="{{ $chDetails->email }}"  placeholder="Chapter Email Address" >
                                </div>
                                <div class="col-sm-7">
                                <input type="text" name="cch_pobox" id="ch_pobox" class="form-control" value="{{ $chDetails->po_box }}"  placeholder="Chapter PO Box/Mailing Address" >
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="row mb-1">
                                <label class="col-sm-2 col-form-label">Inquiries:</label>
                                <div class="col-sm-3">
                                <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" value="{{ $chDetails->inquiries_contact }}"  required >
                                </div>
                                <div class="col-sm-7">
                                <input type="text" name="ch_inqnote" id="ch_inqnote" class="form-control" value="{{ $chDetails->inquiries_note }}"  placeholder="Inquiries Notes" >
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="row mb-1">
                                <label class="col-sm-2 col-form-label">Additional Information:</label>
                                <div class="col-sm-10">
                                    <textarea name="ch_addinfo" class="form-control" rows="4" >{{ $chDetails->additional_info }}</textarea>
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="row mb-1">
                                <label class="col-sm-2 col-form-label">Website:</label>
                                <div class="col-sm-7">
                                    <input type="text" name="ch_website" id="ch_website" class="form-control"
                                        value="{{$chDetails->website_url}}"
                                        placeholder="Chapter Website">
                                </div>
                            </div>

                            <!-- Website Status Container - Hidden by default -->
                            <div class="row mb-1" id="ch_webstatus-container" style="display: none;">
                                <label class="col-sm-2 col-form-label">Website Status:</label>
                                <div class="col-sm-3">
                                    <select name="ch_webstatus" id="ch_webstatus" class="form-control" style="width: 100%;">
                                        <option value="">Select Status</option>
                                        @foreach($allWebLinks as $status)
                                            <option value="{{$status->id}}"
                                                @if($chDetails->website_status == $status->id) selected @endif>
                                                {{$status->link_status}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="row mb-1">
                                <label class="col-sm-2 col-form-label">Website Notes:</label>
                                <div class="col-sm-10">
                                <input type="text" name="ch_webnotes" id="ch_webnotes" class="form-control" value="{{ $chDetails->website_notes }}" placeholder="Website Linking Notes"  >
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="row mb-1">
                                <label class="col-sm-2 col-form-label">Social Media:</label>
                                <div class="col-sm-2">
                                <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chDetails->egroup }}"  placeholder="Forum/Group/App" >
                                </div>
                                <div class="col-sm-2">
                                <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chDetails->social1 }}" placeholder="Facebook"  >
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chDetails->social2 }}"  placeholder="Twitter" >
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" name="ch_social3" id="ch_social3" class="form-control" value="{{ $chDetails->social3 }}"  placeholder="Instagram" >
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
                    <button type="submit" class="btn btn-primary bg-gradient mb-2" ><i class="bi bi-floppy-fill me-2"></i>Save Chapter Information</button>
                    @if($conferenceCoordinatorCondition)
                        <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="updateName('{{ $chDetails->id }}')"><i class="bi bi-house-up-fill me-2"></i>Update Chapter Name</button>
                    @endif
                @endif
                <button type="button" id="back-details" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Chapter Details</button>
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

<script>

//If Chapter Name Change Warning
function PreviousNameReminder(){
    customWarningAlert("If you are changing the chapter name, please be sure to note the old name in the 'Previously Known As' field.");
}

</script>
@endsection
