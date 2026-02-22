@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinator Details')
@section('breadcrumb', 'Coordinator Details')

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("coordinators.updatedetails",$cdDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <div class="card card-primary card-outline">
               <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                        <h3 class="mb-0">{{ $cdDetails->first_name }}, {{ $cdDetails->last_name }}</h3>
                        <p class="mb-0">{{ $conferenceDescription }} Conference
                            @if ($regionLongName != "None")
                                , {{ $regionLongName }} Region
                            @endif
                        </p>
                    </div>
                  <ul class="list-group list-group-flush mb-3">
                      <li class="list-group-item">
                          <div class="row">
                            <div class="col-auto fw-bold">Supervising Coordinator:</div>
                            <div class="col text-end">
                                <a href="mailto:{{ $cdDetails->reportsTo?->email }}">{{ $ReportTo }} </a>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-auto fw-bold">Primary Position:</div>
                            <div class="col text-end">
                                {{ $displayPosition->long_title }}
                           </div>
                          </div>
                        <div class="row">
                            <div class="col-auto fw-bold">MIMI Position: <a href="javascript:void(0);" onclick="showPositionInformation()" title="Show Position Information">
                            <i class="bi bi-question-circle text-primary"></i></a></div>
                            <div class="col text-end">{{ $mimiPosition?->long_title }}</span>
                        </div>
                          </div>
                          <div class="row">
                            <div class="col-auto fw-bold">Secondary Positions:</div>
                            <div class="col text-end">
                                @forelse($cdDetails->secondaryPosition as $position)
                                    {{ $position->long_title }}@if(!$loop->last)<br>@endif
                                @empty
                                    None
                                @endforelse
                            </div>
                          </div>
                           @if ($ITCondition)
                        <div class="row">
                            <div class="col-auto fw-bold">MIMI Admin:</div>
                            <div class="col text-end">
                                {{ $cdAdminRole->admin_role }}
                                </div>
                          </div>
                        @endif
                      </li>
                      <li class="list-group-item mt-2">
                          <div class="row">
                            <div class="col-auto fw-bold">Start Date:</div>
                            <div class="col text-end">
                                {{ $cdDetails->coordinator_start_date }}
                                </div>
                          </div>
                          <div class="row">
                            <div class="col-auto fw-bold">Last Promotion Date:</div>
                            <div class="col text-end">
                                {{ $cdDetails->last_promoted }}
                           </div>
                          </div>
                          <div class="row">
                            <div class="col-auto fw-bold">Home Chapter:</div>
                            <div class="col text-end">
                                {{ $cdDetails->home_chapter }}
                                 </div>
                          </div>
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
                    <h3>Contact Information</h3>
                      </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label">Name:</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_fname" id="cord_fname" class="form-control" value="{{ $cdDetails->first_name }}"  required >
                                    </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_lname" id="cord_lname" class="form-control" value="{{ $cdDetails->last_name }}"  required >
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label">Email/Secondary:</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_email" id="cord_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  value="{{ $cdDetails->email }}"  required >
                                    <input type="hidden" id="cord_email_chk" value="{{ $cdDetails->email }}">
                                </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_sec_email"class="form-control" value="{{ $cdDetails->sec_email }}" placeholder="Secondary Email">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label">Phone/Alternate:</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_phone" id="cord_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $cdDetails->phone }}"  required >
                                    </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_altphone" id="cord_altphone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $cdDetails->alt_phone }}"  placeholder="Alternate Phone" >
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label">Address:</label>
                                    <div class="col-sm-10">
                                    <input type="text" name="cord_addr" id="cord_addr" class="form-control" value="{{ $cdDetails->address }}"  required >
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label"><br></label>
                                    <div class="col-sm-3">
                                    <input type="text" name="cord_city" id="cord_city" class="form-control" value="{{ $cdDetails->city }}"  required >
                                    </div>

                                    <div class="col-sm-3">
                                    <select name="cord_state" id="cord_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}"
                                            @if(isset($cdDetails->state_id) && $cdDetails->state_id == $state->id) selected @endif>
                                                    {{$state->state_long_name}}
                                                </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" name="cord_zip" id="cord_zip" class="form-control" value="{{ $cdDetails->zip }}" required >
                                </div>
                                 <div class="col-sm-2">
                                <select name="cord_country" id="cord_country" class="form-control" style="width: 100%;" required>
                                    <option value="">Select Country</option>
                                    @foreach($allCountries as $country)
                                            <option value="{{$country->id}}"
                                            @if(isset($cdDetails->country_id) && $cdDetails->country_id == $country->id) selected @endif>
                                                {{$country->name}}
                                            </option>
                                        @endforeach
                                </select>
                            </div>
                                </div>
                                 <div class="row mb-1">
                                    <label class="col-sm-2 col-form-label">Birthday:</label>
                                    <div class="col-sm-3">
                                        <select name="cord_month" class="form-control" style="width: 100%;" required>
                                            <option value="">Select Month</option>
                                                @foreach($allMonths as $month)
                                                <option value="{{$month->id}}"
                                                    @if($cdDetails->birthday_month_id == $month->id) selected @endif>
                                                    {{$month->month_long_name}}
                                                </option>
                                                @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                    <input type="text" name="cord_day" id="cord_day" class="form-control" value="{{ $cdDetails->birthday_day }}" required>
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
                <button type="submit" class="btn btn-primary bg-gradient mb-2" ><i class="bi bi-floppy-fill me-2"></i>Save</button>
                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('coordinators.view', ['id' => $cdDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-people-fill me-2"></i>Back to Coordinator Details</button>
            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
