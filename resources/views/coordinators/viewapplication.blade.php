@extends('layouts.coordinator_theme')

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
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">{{ $cdDetails->first_name }}, {{ $cdDetails->last_name }}</h3>
                <p class="text-center">{{ $conferenceDescription }} Conference
                </p>

<div class="d-flex mb-2">
                        <b class="me-3" style="min-width: 200px;">Region:</b>
                  <select name="cord_region" id="cord_region" class="form-control" style="width: 100%;" required>
                                    @foreach($allRegions as $region)
                                        <option value="{{$region->id}}"
                                            @if($cdDetails->region_id == $region->id) selected @endif>
                                            {{$region->long_name}}
                                        </option>
                                    @endforeach
                                </select>
                                </div>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                    <div class="d-flex mb-2">
                        <b class="me-3" style="min-width: 200px;">Email:</b>
                        <input type="text" name="cord_email" id="cord_email" class="form-control" value="{{ $cdDetails->email }}" placeholder="Email Address" required>
                    </div>
                    <div class="d-flex mb-2">
                        <b class="me-3" style="min-width: 200px;">Secondary Email:</b>
                        <input type="text" name="cord_sec_email" id="cord_sec_email" class="form-control" value="{{ $cdDetails->sec_email }}" placeholder="Secondary Email" required>
                    </div>
                    <div class="d-flex mb-2">
                        <b class="me-3" style="min-width: 200px;">Phone:</b>
                        <input type="text" name="cord_phone" id="cord_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $cdDetails->phone}}" required placeholder="Phone Number" >
                    </div>
                    <div class="d-flex mb-2">
                        <b class="me-3" style="min-width: 200px;">Alt Phone:</b>
                        <input type="text" name="cord_altphone" id="cord_altphone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $cdDetails->alt_phone}}" placeholder="Alternative Phone" >
                    </div>



                    <div class="d-flex">
                        <b class="me-3" style="min-width: 200px;">Address:</b>
                        <span>{{ $cdDetails->address }}</span>
                    </div>
                    <div class="d-flex">
                        <b class="me-3" style="min-width: 200px;">&nbsp;</b>
                        <span>{{$cdDetails->city}}, {{$cdDetails->state->state_short_name}}&nbsp;{{$cdDetails->zip}}</span>
                    </div>
                    <div class="d-flex">
                        <b class="me-3" style="min-width: 200px;">&nbsp;</b>
                        <span>{{$cdDetails->country->short_name}}</span>
                    </div>
                </li>
                  <li class="list-group-item">
                    <div class="d-flex mb-2">
                        <b class="me-3 mb-3" style="min-width: 200px;">Application Date:</b>
                        <span class="date-mask">{{ $cdDetails->coordinator_start_date }}</span>
                    </div>
                      <div class="d-flex mb-2">
                        <b class="me-3" style="min-width: 200px;">Display Position:</b>
                                <select name="cord_disp_pos" id="cord_disp_pos" class="form-control" style="width: 100%;" onChange="CheckPromotion(this)" required>
                                    @foreach($allPositions as $pos)
                                            <option value="{{ $pos->id }}" {{ $cdDetails->display_position_id == $pos->id ? 'selected' : '' }}>
                                                {{ $pos->long_title }}
                                            </option>
                                    @endforeach
                                </select>
                        </div>

                        <div class="d-flex mb-2">
                        <b class="me-3" style="min-width: 200px;">MIMI Position:<a href="javascript:void(0);" onclick="showPositionInformation()" title="Show Position Information">
                                    <i class="fas fa-circle-question text-primary"></i></a></b>
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

                         <div class="d-flex mb-2">
                        <b class="me-3" style="min-width: 200px;">Secondary Position:</b>
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

                    <div class="d-flex mb-2">
                        <b class="me-3" style="min-width: 200px;">Home Chapter:</b>
                                <input type="text" name="cord_chapter" id="cord_chapter" class="form-control" value="{{ $cdDetails->home_chapter }}" placeholder="Home Chapter" required>
                        </div>

                      <div class="d-flex">
                        <b class="me-3" style="min-width: 200px;">Supervising Coordinator:</b>
                           <select name="cord_report_pc" id="cord_report_pc" class="form-control" style="width: 100%;" required>
                                    @foreach($rcDetails as $coordinator)
                                        <option value="{{ $coordinator['cid'] }}"
                                            @if($cdDetails->report_id == $coordinator['cid']) selected @endif
                                            data-region-id="{{ $coordinator['regid'] }}">
                                            {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                        </option>
                                    @endforeach
                                </select>

                </li>
            <li class="list-group-item">
                <div class="text-center">
                     @if ($cdDetails->active_status == 1 && $cdDetails->on_leave == 1)
                        <b><span style="color: #ff851b;">Coordinator is ON LEAVE</span></b>
                        <br>
                        Leave Date: <span class="date-mask">{{ $cdDetails->leave_date }}</span><br>
                    @else
                        @if ($cdDetails->active_status == 1 && $cdDetails->on_leave != 1)
                            <b><span style="color: #28a745;">Coordinator is ACTIVE</span></b>
                        @elseif ($cdDetails->active_status == 2)
                        <b><span style="color: #ff851b;">Coordinator is PENDING</span></b>
                        @elseif ($cdDetails->active_status == 3)
                        <b><span style="color: #dc3545;">Coordinator was NOT APPROVED</span></b><br>
                            Rejected Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
                            {{ $cdDetails->reason_retired }}
                        @elseif ($cdDetails->active_status == 0)
                            <b><span style="color: #dc3545;">Coordinator is RETIRED</span></b><br>
                            Retired Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
                            {{ $cdDetails->reason_retired }}
                        @endif
                    @endif
                </div>
                 </li>
                @if ($cdDetails['active_status'] == '2')
                    <li class="list-group-item">
                        <div class="card-body text-center">
                            <button type="submit" class="btn bg-gradient-primary mb-3"><i class="fas fa-save mr-2" ></i>Save Updates</button>
                            <br>
                            Save all changes before approval!
                            <br>
                            <button type="button" class="btn bg-gradient-success" onclick="appApprove({{ $cdDetails->id }}, {{ $cdDetails->user_id }})"><i class="fas fa-check mr-2"></i>Approve Application</button>
                            <button type="button" class="btn bg-gradient-danger" onclick="appReject({{ $cdDetails->id }}, {{ $cdDetails->user_id }})"><i class="fas fa-times mr-2"></i>Reject Application</button>
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
                <h3 class="profile-username">Coordinator Application</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">How long have you been a MOMS Club Member?</label>
                                    <div class="col-sm-8">{{ $cdApp?->start_date }}</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">What jobs/offices have you held with the chapter? What programs/activities have you started or led?</label>
                               <div class="col-sm-8">{{ $cdApp?->jobs_programs }}</div>
                            </div>

                             <div class="form-group row">
                                <label class="col-sm-4 col-form-label">How has the MOMS Club helped you?</label>
                                <div class="col-sm-8">{{ $cdApp?->helped_me }}</div>
                            </div>

                             <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Did you experience any problems during your time in the MOMS Club? If so, how were those problems resolved or what did you learn from them?</label>
                                <div class="col-sm-8">{{ $cdApp?->problems }}</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Why do you want to be an International MOMS Club Volunteer?</label>
                                <div class="col-sm-8">{{ $cdApp?->why_volunteer }}</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Do you volunteer for anyone else? Please list all your volunteer positions and when you did them?</label>
                                <div class="col-sm-8">{{ $cdApp?->other_volunteer }}</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Do you have any special skills/talents/Hobbies (ie: other languages, proficient in any computer programs)?</label>
                                <div class="col-sm-8">{{ $cdApp?->special_skills }}</div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">What have you enjoyed most in previous volunteer experiences? Least?</label>
                               <div class="col-sm-8">{{ $cdApp?->enjoy_volunteering }}</div>
                            </div>

                             <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Referred by (if applicable):</label>
                                <div class="col-sm-8">{{ $cdApp?->referred_by }}</div>
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
                @if ($cdConfId == $confId)
                        @if ($cdActiveId == '1')
                            <button type="button" id="back-list" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordlist') }}'"><i class="fas fa-reply mr-2"></i>Back to Active Coordinator List</button>
                        @elseif ($cdActiveId == '2')
                            <button type="button" id="back-pending" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordpending') }}'"><i class="fas fa-reply mr-2"></i>Back to Pending Coordinator List</button>
                        @elseif ($cdActiveId == '3')
                            <button type="button" id="back-declined" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordrejected') }}'"><i class="fas fa-reply mr-2"></i>Back to Not Approved Coordinator List</button>
                        @elseif ($cdActiveId == '0')
                            <button type="button" id="back-zapped" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordretired') }}'"><i class="fas fa-reply mr-2"></i>Back to Retired Coordinator List</button>
                        @endif
                    @else
                        @if ($cdConfId != $confId)
                            @if ($ITCondition)
                                @if ($cdActiveId == '1')
                                    <button type="button" id="back-list" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordlist', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Active Coordinator List</button>
                                @elseif ($cdActiveId == '2')
                                    <button type="button" id="back-pending" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordpending', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Pending Coordinator List</button>
                                @elseif ($cdActiveId == '3')
                                    <button type="button" id="back-declined" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordrejected', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Not Approved Coordinator List</button>
                                @elseif ($cdActiveId == '0')
                                    <button type="button" id="back-zapped" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordretired', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Retired Coordinator List</button>
                                @endif
                            @endif
                        @endif
                    @endif
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
