@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Website & Social Media')
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
    <form class="form-horizontal" method="POST" action='{{ route("chapters.updatewebsite", $chDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">
                <input type="hidden" name="ch_state" value="{{$stateShortName}}">
                <input type="hidden" name="ch_hid_webstatus" value="{{ $chDetails->website_status }}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                  <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName}} Region
                  </p>

                  <ul class="list-group list-group-unbordered mb-3">
                      <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
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
                <h3 class="profile-username">Website & Social Media Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Website:</label>
                                <div class="col-sm-7">
                                    <input type="text" name="ch_website" id="ch_website" class="form-control"
                                        value="{{$chDetails->website_url}}"
                                        placeholder="Chapter Website">
                                </div>
                            </div>

                            <!-- Website Status Container - Hidden by default -->
                            <div class="form-group row" id="ch_webstatus-container" style="display: none;">
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

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Website Notes:</label>
                                <div class="col-sm-8">
                                  <input type="text" name="ch_webnotes" id="ch_webnotes" class="form-control"  value="{{ $chDetails->website_notes}}" >
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Social Media:</label>
                                <div class="col-sm-3">
                                <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chDetails->egroup }}"  placeholder="Forum/Group/App" >
                                </div>
                                <div class="col-sm-3">
                                <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chDetails->social1 }}" placeholder="Facebook"  >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-3">
                                    <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chDetails->social2 }}"  placeholder="Twitter" >
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" name="ch_social3" id="ch_social3" class="form-control" value="{{ $chDetails->social3 }}"  placeholder="Instagram" >
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
                <button class="btn bg-gradient-primary mb-3" type="button" id="email-chapter" onclick="showChapterEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}', 'Website Review')">
                        <i class="fa fa-envelope mr-2"></i>Email Board</button>
                    <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save Website Information</button>
                <br>
                @endif
                 @if ($confId == $chConfId)
                        <button type="button" id="back-web" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapters.chapwebsite') }}'"><i class="fas fa-reply mr-2"></i>Back to Website Report</button>
                        <button type="button" id="back-social" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapters.chapsocialmedia') }}'"><i class="fas fa-reply mr-2"></i>Back to Social Media Report</button>
                @elseif ($confId != $chConfId)
                    <button type="button" id="back-web" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapters.chapwebsite', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Website Report</button>
                    <button type="button" id="back-social" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapters.chapsocialmedia', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Social Media Report</button>
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
    @include('layouts.scripts.disablefields', ['includeWebReviewCondition' => true])

@endsection
