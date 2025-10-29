@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Board Information')
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
    <form class="form-horizontal" method="POST" action='{{ route("chapters.updatepending", $chDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="ch_state" value="{{$stateShortName}}">
            <input type="hidden" name="ch_hid_primarycor" value="{{$chDetails->primary_coordinator_id}}">
            <input type="hidden" id="ch_pre_email_chk" value="{{ $chDetails->pendingPresident->email }}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                  <p class="text-center">{{ $conferenceDescription }} Conference
                  </p>
                  <div class="form-group row mt-1">
                    <label class="col-form-label col-sm-6">Region:</label>
                    <div class="col-sm-6">
                        <select id="ch_region" name="ch_region" class="form-control float-right text-right"required>
                            <option value="">Select Region</option>
                            @foreach($allRegions as $region)
                                <option value="{{$region->id}}"
                                    @if($chDetails->region_id == $region->id) selected @endif>
                                    {{$region->long_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                  <ul class="list-group list-group-unbordered mb-3">

                      @if($regionalCoordinatorCondition)
                      <li class="list-group-item">
                          <label class="ch_primarycor">Update Primary Coordinator:</label>
                          <select name="ch_primarycor" id="ch_primarycor" class="form-control float-right col-sm-6 text-right" style="width: 100%;" onchange="loadCoordinatorList(this.value)" required>
                              <option value="">Select Primary Coordinator</option>
                              @foreach($pcDetails as $coordinator)
                              <option value="{{ $coordinator['cid'] }}"
                                  {{ isset($chDetails->primary_coordinator_id) && $chDetails->primary_coordinator_id == $coordinator['cid'] ? 'selected' : '' }}
                                  data-region-id="{{ $coordinator['regid'] }}">
                                  {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                  </option>
                              @endforeach
                          </select>
                          <hr>
                          <h3 class="profile-username">Coordinator Team</h3>
                          <span id="display_corlist" style="display: block; margin-top: 10px;"></span>
                      </li>
                      @else
                      <li class="list-group-item" id="display_corlist" ></li>
                      @endif
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

                   @if ($chDetails->active_status == '2')
                    <li class="list-group-item">
                        <div class="card-body text-center">
                                <button type="button" class="btn bg-gradient-primary mb-3"
                                    onclick="showChapterSetupEmailModal({{ $chDetails->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')">
                                    <i class="fas fa-envelope mr-2"></i>Send Startup Email</button>
                            <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save Updates</button>
                            <br>
                            Save all changes before approval!
                            <br>
                             <button type="button" class="btn bg-gradient-success" onclick="chapApprove({{ $chDetails->id }}, '{{ $chDetails->region_id }}')"><i class="fas fa-check mr-2"></i>Approve Chapter</button>
                            <button type="button" class="btn bg-gradient-danger" onclick="chapDecline({{ $chDetails->id }})"><i class="fas fa-times mr-2"></i>Decline Chaper</button>
                    </li>
                @endif
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
                <h3 class="profile-username">General Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Chapter Name:</label>
                                <div class="col-sm-5">
                                <input type="text" name="ch_name" id="ch_name" class="form-control" value="{{ $chDetails->name }}"  >
                                </div>

                                <label class="col-sm-2 col-form-label">State:</label>
                                <div class="col-sm-3">
                                    <select id="ch_state" name="ch_state" class="form-control" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                            <option value="{{$state->id}}"
                                                @if($chDetails->state_id == $state->id) selected @endif>
                                                {{$state->state_long_name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Boundaries:</label>
                                <div class="col-sm-10">
                                <input type="text" name="ch-territory" id="ch-territory" class="form-control" value="{{ $chDetails->territory }}"  required >
                                </div>
                            </div>

                             <!-- /.form group -->
                             <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Email/Mailing:</label>
                                <div class="col-sm-3">
                                <input type="text" name="ch_email" id="ch_email" class="form-control" value="{{ $chDetails->email }}"  placeholder="Chapter Email Address" >
                                </div>
                                <div class="col-sm-7">
                                <input type="text" name="cch_pobox" id="ch_pobox" class="form-control" value="{{ $chDetails->po_box }}"  placeholder="Chapter PO Box/Mailing Address" >
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Inquiries:</label>
                                <div class="col-sm-3">
                                <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" value="{{ $chDetails->inquiries_contact }}"  required >
                                </div>
                                <div class="col-sm-7">
                                <input type="text" name="ch_inqnote" id="ch_inqnote" class="form-control" value="{{ $chDetails->inquiries_note }}"  placeholder="Inquiries Notes" >
                                </div>
                            </div>

                        </div>
                    </div>

                    <hr>

                <h3 class="profile-username">Founder Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 mb-1 col-form-label">Name:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control" value="{{ $chDetails->pendingPresident->first_name }}" required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" value="{{ $chDetails->pendingPresident->last_name }}" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label">Contact:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" value="{{ $chDetails->pendingPresident->email }}" required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $chDetails->pendingPresident->phone}}" required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label">Address:</label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" value="{{ $chDetails->pendingPresident->street_address }}"  required  placeholder="Address">
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="col-sm-3 mb-1">
                                <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control" value="{{ $chDetails->pendingPresident->city }}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_pre_state" id="ch_pre_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}"
                                            @if($PresDetails->state_id == $state->id) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control" value="{{ $chDetails->pendingPresident->zip }}"  required placeholder="Zip">
                                </div>
                                <div class="col-sm-2" id="ch_pre_country-container" style="display: none;">
                                    <select name="ch_pre_country" id="ch_pre_country" class="form-control" style="width: 100%;" required>
                                        <option value="">Select Country</option>
                                        @foreach($allCountries as $country)
                                        <option value="{{$country->id}}"
                                            @if($PresDetails->country_id == $country->id) selected @endif>
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
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
              @if($coordinatorCondition)
                    @if ($confId == $chConfId)
                        @if ($chActiveId == '2')
                            <button type="button" id="back-pending" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapters.chaplistpending') }}'"><i class="fas fa-reply mr-2"></i>Back to Pending Chapter List</button>
                        @elseif ($chActiveId == '3')
                            <button type="button" id="back-declined" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapters.chaplistdeclined') }}'"><i class="fas fa-reply mr-2"></i>Back to Not Approved Chapter List</button>
                        @endif
                     @elseif ($confId != $chConfId)
                        @if ($ITCondition )
                            @if ($chActiveId == '2')
                                <button type="button" id="back-pending" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapters.chaplistpending', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Pending Chapter List</button>
                            @elseif ($chActiveId == '3')
                                <button type="button" id="back-declined" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapters.chaplistdeclined', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Not Approved Chapter List</button>
                            @endif
                        @endif
                    @endif
                    <button type="button" class="btn bg-gradient-primary mb-3 reset-password-btn" data-user-id="{{ $chDetails->pendingPresident->user_id }}"><i class="fas fa-lock mr-2"></i>Reset Founder Password</button>

                @endif
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
