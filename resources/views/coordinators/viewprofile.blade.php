@extends('layouts.mimi_theme')

@section('page_title', 'Coordinator Profile')
@section('breadcrumb', 'Coordinator Profile')

@section('content')
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
            <div class="row g-4">

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
                            <div class="col-auto fw-bold">Secondary Positions:</div>
                            <div class="col text-end">
                                @forelse($cdDetails->secondaryPosition as $position)
                                    {{ $position->long_title }}@if(!$loop->last)<br>@endif
                                @empty
                                    None
                                @endforelse
                            </div>
                          </div>
                      </li>
                      <li class="list-group-item">
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
                            <div class="col-auto fw-bold">Home Chapter:
                                {{ $cdDetails->home_chapter }}
                                 </div>
                          </div>
                      </li>
                      <li class="list-group-item mt-2">
                        <div class="row">
                            <div class="col-auto fw-bold">Birthday:</div>
                            <div class="col text-end">
                                {{$cdDetails->birthdayMonth->month_long_name}} {{$cdDetails->birthday_day}}
                        </div>
                          </div>
                          <div class="row">
                            <div class="col-auto fw-bold">Email:</div>
                            <div class="col text-end">
                                <a href="mailto:{{ $cdDetails->email }}">{{ $cdDetails->email }}</a>
                        @if ($cdDetails->sec_email != null )
                        </div>
                          </div>
                          <div class="row">
                            <div class="col-auto fw-bold">Secondary Email:</div>
                            <div class="col text-end">
                                <a href="mailto:{{ $cdDetails->sec_email }}">{{ $cdDetails->sec_email }}</a>
                        @endif
                        </div>
                          </div>
                          <div class="row">
                            <div class="col-auto fw-bold">Phone:</div>
                            <div class="col text-end phone-mask">
                                {{$cdDetails->phone }}
                        @if ($cdDetails->alt_phone != null )
                        </div>
                          </div>
                          <div class="row">
                            <div class="col-auto fw-bold">Atl Phone:</div>
                            <div class="col text-end phone-mask">
                                {{$cdDetails->alt_phone }}
                        @endif
                        </div>
                          </div>
                          <div class="row">
                            <div class="col-auto fw-bold">Address:</div>
                            <div class="col text-end">
                                {{$cdDetails->address}}<br>
                        {{$cdDetails->city}}, {{$cdDetails->state->state_short_name}}&nbsp;{{$cdDetails->zip}}
                      </div>
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
                            <h3>Chapters & Coordinators</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-6">
							<div class="mb-3">
							    <label class="meg-b-25">Coordinators Directly Reporting to {{ $cdDetails->first_name }}:</label>

                                <table id="coordinator-list" width="100%">
                                    <thead>
                                        @if($drList->isEmpty())
                                            <tr>
                                                <td colspan="3" class="text-center">No Coordinators Found</td>
                                            </tr>
                                        @else
                                        <tr>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Position</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($drList as $coordinator)
                                            <tr>
                                                <td>{{ $coordinator->first_name }}</td>
                                                <td>{{ $coordinator->last_name }}</td>
                                                @if ( $coordinator->on_leave == 1 )
                                                    <td style="background-color: #ffc107;">ON LEAVE</td>
                                                @else
                                                <td>
                                                    {{ $coordinator->displayPosition->short_title }}
                                                    @if (!empty($coordinator->secondaryPosition) && $coordinator->secondaryPosition->count() > 0)
                                                        /{{ $coordinator->secondaryPosition->pluck('short_title')->implode('/') }}
                                                    @endif
                                                </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="mb-3">
                                <label class="meg-b-25">{{ $cdDetails->first_name }} is Primary Coordinator For:</label>
                                    <table id="coordinator-list" width="100%">
                                        <thead>
                                            @if($chList->isEmpty())
                                                <tr>
                                                    <td colspan="2" class="text-center">No Chapters Found</td>
                                                </tr>
                                            @else
                                            <tr>
                                                <th>State</th>
                                                <th>Chapter Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($chList as $chapter)
                                                <tr>
                                                    <td>{{ $chapter->state->state_short_name }}</td>
                                                    <td>{{ $chapter->name }}</td>
                                                </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
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
                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('coordinators.profile') }}'"><i class="bi bi-person-circle me-2"></i>Update Profile</button>
                @if ($coordinatorCondition || $ITCondition)
                    <button type="button" id="back" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('chapters.chaplist') }}'"><i class="bi bi-house-fill me-2"></i>View Chapter List</button>
                @elseif($inquiriesCondition || ($ITCondition && !$coordinatorCondition))
                    <button type="button" id="back-inquiries" class="btn btn-primary bg-gradient mb-2" onclick="window.location.window.location.href='{{ route('chapters.chapinquiries') }}'"><i class="bi bi-eye-fill me-2"></i>View Inquiries Chapter List</button>
                @elseif($einCondition || $ITCondition)
                    <button type="button" id="back-list" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('chapters.chaplist', ['check5' => 'yes']) }}'"><i class="bi bi-house-fill me-2"></i>View International Active Chapter List</button>
                @elseif($inquiriesInternationalCondition || ($ITCondition && !$coordinatorCondition))
                    <button type="button" id="back-inquiries" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('chapters.chapinquiries', ['check5' => 'yes']) }}'"><i class="bi bi-pin-map-fill me-2"></i>View International Active Inquiries List</button>
                @endif

                @if ($supervisingCoordinatorCondition || $ITCondition)
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('coordinators.coordlist') }}'"><i class="bi bi-people-fill me-2"></i>View Coordinator List</button>
                @elseif($ITCondition && !$supervisingCoordinatorCondition)
                    <button type="button" id="back-list" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('coordinators.coordlist', ['check5' => 'yes']) }}'"><i class="bi bi-people-fill me-2"></i>View International Active Coordinator List</button>
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
