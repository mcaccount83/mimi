@extends('layouts.mimi_theme')

@section('page_title', 'Coordinator Details')
@section('breadcrumb', 'Coordinator Details')
<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}
</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("coordinators.updatepending",$cdDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="coordinator_id" value="{{ $cdDetails->id }}">
            <input type="hidden" name="OldReportPC" value="{{ $cdDetails->report_id }}">
            <input type="hidden" name="OldPosition" value="{{ $cdDetails->position_id }}">
            <input type="hidden" name="OldDisplayPosition" value="{{ $cdDetails->display_position_id }}">
            <input type="hidden" name="OldSecPosition" value="{{$cdDetails->sec_position_id}}">

            <input type="hidden" name="cord_fname" value="{{$cdDetails->first_name}}">
            <input type="hidden" name="cord_lname" value="{{$cdDetails->last_name}}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                        <h3 class="mb-0">{{ $cdDetails->first_name }}, {{ $cdDetails->last_name }}</h3>
                        <p class="mb-0">{{ $conferenceDescription }} Conference
                        </p>
                    </div>

                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item">
                      <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">Region:</label>
                            </div>
                            <div class="col-sm-6">
                                <select name="cord_region" id="cord_region" class="form-control" style="width: 100%;" required>
                                    @foreach($allRegions as $region)
                                        <option value="{{$region->id}}"
                                            @if($cdDetails->region_id == $region->id) selected @endif>
                                            {{$region->long_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
 <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">Reports To:</label>
                            </div>
                            <div class="col-sm-6">
                                <select name="cord_report_pc" id="cord_report_pc" class="form-control" style="width: 100%;" required>
                                    @foreach($rcDetails as $coordinator)
                                        <option value="{{ $coordinator['cid'] }}"
                                            @if($cdDetails->report_id == $coordinator['cid']) selected @endif
                                            data-region-id="{{ $coordinator['regid'] }}">
                                            {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">Display Position:</label>
                            </div>
                            <div class="col-sm-6">
                                <select name="cord_disp_pos" id="cord_disp_pos" class="form-control" style="width: 100%;" onChange="CheckPromotion(this)" required>
                                    @foreach($allPositions as $pos)
                                            <option value="{{ $pos->id }}" {{ $cdDetails->display_position_id == $pos->id ? 'selected' : '' }}>
                                                {{ $pos->long_title }}
                                            </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">MIMI Position:<a href="javascript:void(0);" onclick="showPositionInformation()" title="Show Position Information">
                                    <i class="bi bi-question-circle text-primary"></i></a></label>
                            </div>
                            <div class="col-sm-6">
                                <select name="cord_pos" id="cord_pos" class="form-control" style="width: 100%;" onChange="CheckPromotion(this)" required>
                                    @foreach($allPositions as $pos)
                                        @if($pos->id >= 1 && $pos->id <= 7)
                                            <option value="{{$pos->id}}" {{$cdDetails->position_id == $pos->id  ? 'selected' : ''}}>
                                                {{$pos->long_title}}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">Secondary Position:</label>
                            </div>
                            <div class="col-sm-6">
                                <select name="cord_sec_pos[]" id="cord_sec_pos" class="form-control" style="width: 100%;" onChange="CheckPromotion(this)" multiple>
                                    <option value="" {{ (!isset($cdDetails->secondaryPosition) || $cdDetails->secondaryPosition->isEmpty()) ? 'selected' : '' }}>None</option>
                                    @foreach($allPositions as $pos)
                                        @if($pos->id >= 9)
                                            <option value="{{$pos->id}}"
                                                {{ isset($cdDetails->secondaryPosition) && $cdDetails->secondaryPosition->contains('id', $pos->id) ? 'selected' : '' }}>
                                                {{$pos->long_title}}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- @if ($ITCondition)
                            <div class="row align-items-center">
                                <div class="col-sm-6 mt-1">
                                    <label class="col-form-label">MIMI Admin:</label>
                                </div>
                                <div class="col-sm-6">
                                    <select name="is_admin" id="is_admin" class="form-control" style="width: 100%;" required>
                                        @foreach($allAdminRoles as $admin)
                                                <option value="{{$admin->id}}" {{$cdUserAdmin == $admin->id  ? 'selected' : ''}}>
                                                    {{$admin->admin_role}}
                                                </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif --}}
                      </li>

                    <li class="list-group-item">
                      <div class="row">
                            <div class="col-auto fw-bold">Email:</div>
                            <div class="col text-end">
                                {{ $cdDetails->email }}
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Secondary Email::</div>
                            <div class="col text-end">
                                {{ $cdDetails->sec_email }}
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Phone:</div>
                            <div class="col text-end">
                                {{ $cdDetails->phone}}
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Alt Phone:</div>
                            <div class="col text-end">
                                {{ $cdDetails->alt_phone}}
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Address:</div>
                            <div class="col text-end">
                                {{ $cdDetails->address }}<br>
                                {{$cdDetails->city}}, {{$cdDetails->state->state_short_name}}&nbsp;{{$cdDetails->zip}}<br>
                                {{$cdDetails->country->short_name}}
                                </div>
                        </div>
                    </li>

                  <li class="list-group-item ">
                    <div class="row">
                        <div class="col-auto fw-bold">Application Date:</div>
                            <div class="col text-end date-mask">
                            {{ $cdDetails->coordinator_start_date }}
                        </div>
                    </div>

                   <div class="row">
                        <div class="col-auto fw-bold">Home Chapter:</div>
                            <div class="col text-end">
                                {{ $cdDetails->home_chapter }}
                        </div>
                        </div>
                   </li>
              <li class="list-group-item mt-3">
                     @include('coordinators.partials.coordinatorstatus')
                </li>
                @if ($cdDetails['active_status'] == '2')
                    <li class="list-group-item">
                        <div class="card-body text-center mt-3">
                            <button type="submit" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-floppy-fill me-2"></i>Save Updates</button>
                            <br>
                        Save all changes before approval, so information in emails will be correct!
                            <br>
                            <button type="button" class="btn btn-success bg-gradient mb-2" onclick="appApprove({{ $cdDetails->id }}, {{ $cdDetails->user_id }})"><i class="bi bi-check-lg me-2"></i>Approve Application</button>
                            <button type="button" class="btn btn-danger bg-gradient mb-2" onclick="appReject({{ $cdDetails->id }}, {{ $cdDetails->user_id }})"><i class="bi bi-x-circle me-2"></i>Reject Application</button>
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
                <div class="card-body">
                    <div class="card-header bg-transparent border-0">
                <h3>Coordinator Application</h3>
                     </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">How long have you been a MOMS Club Member?</label>
                                    <div class="col-sm-8">{{ $cdApp?->start_date }}</div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">What jobs/offices have you held with the chapter? What programs/activities have you started or led?</label>
                               <div class="col-sm-8">{{ $cdApp?->jobs_programs }}</div>
                            </div>

                             <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">How has the MOMS Club helped you?</label>
                                <div class="col-sm-8">{{ $cdApp?->helped_me }}</div>
                            </div>

                             <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Did you experience any problems during your time in the MOMS Club? If so, how were those problems resolved or what did you learn from them?</label>
                                <div class="col-sm-8">{{ $cdApp?->problems }}</div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Why do you want to be an International MOMS Club Volunteer?</label>
                                <div class="col-sm-8">{{ $cdApp?->why_volunteer }}</div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Do you volunteer for anyone else? Please list all your volunteer positions and when you did them?</label>
                                <div class="col-sm-8">{{ $cdApp?->other_volunteer }}</div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Do you have any special skills/talents/Hobbies (ie: other languages, proficient in any computer programs)?</label>
                                <div class="col-sm-8">{{ $cdApp?->special_skills }}</div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">What have you enjoyed most in previous volunteer experiences? Least?</label>
                               <div class="col-sm-8">{{ $cdApp?->enjoy_volunteering }}</div>
                            </div>

                             <div class="row mb-3">
                                <label class="col-sm-4 col-form-label">Referred by (if applicable):</label>
                                <div class="col-sm-8">{{ $cdApp?->referred_by }}</div>
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
                @if ($cdConfId == $confId)
                        @if ($cdActiveId == \App\Enums\CoordinatorStatusEnum::ACTIVE)
                            <button type="button" id="back-list" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordlist') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-people-fill me-2"></i>Back to Active Coordinator List</button>
                        @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::PENDING)
                            <button type="button" id="back-pending" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordpending') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-fill-add me-2"></i>Back to Pending Coordinator List</button>
                        @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::NOTAPPROVED)
                            <button type="button" id="back-declined" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordrejected') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-fill-x me-2"></i>Back to Not Approved Coordinator List</button>
                        @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::RETIRED)
                            <button type="button" id="back-zapped" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordretired') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-fill-slash me-2"></i>Back to Retired Coordinator List</button>
                        @endif
                    @else
                        @if ($cdConfId != $confId)
                            @if ($ITCondition)
                                @if ($cdActiveId == \App\Enums\CoordinatorStatusEnum::ACTIVE)
                                    <button type="button" id="back-list" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordlist', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-people-fill me-2"></i>Back to International Active Coordinator List</button>
                                @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::PENDING)
                                    <button type="button" id="back-pending" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordpending', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-fill-add me-2"></i>Back to International Pending Coordinator List</button>
                                @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::NOTAPPROVED)
                                    <button type="button" id="back-declined" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordrejected', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-fill-x me-2"></i>Back to International Not Approved Coordinator List</button>
                                @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::RETIRED)
                                    <button type="button" id="back-zapped" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordretired', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-fill-slash me-2"></i>Back to International Retired Coordinator List</button>
                                @endif
                            @endif
                        @endif
                    @endif
             </div>
            </div>
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
