@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinator Profile')
@section('breadcrumb', 'Coordinator Profile')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">{{ $cdDetails->first_name }}, {{ $cdDetails->last_name }}</h3>
                  <p class="text-center">{{ $conferenceDescription }} Conference
                      @if ($regionLongName != "None")
                      , {{ $regionLongName }} Region
                      @else
                      @endif
                  </p>
                  <ul class="list-group list-group-unbordered mb-3">
                      <li class="list-group-item">
                          <b>Supervising Coordinator:</b> <span class="float-right"><a href="mailto:{{ $cdDetails->reportsTo->email }}">{{ $ReportTo }} </a></span>
                          <br>
                          <b>Primary Position:</b> <span class="float-right">{{ $displayPosition->long_title }}</span>
                          <br>
                          <b>Secondary Position:</b> <span class="float-right">{{ $secondaryPosition?->long_title }}</span>

                      </li>
                      <li class="list-group-item">
                          <b>Start Date:</b> <span class="float-right date-mask">{{ $cdDetails->coordinator_start_date }}</span>
                          <br>
                          <b>Last Promotion Date:</b> <span class="float-right date-mask">{{ $cdDetails->last_promoted }}</span>
                          <br>
                          <b>Home Chapter:</b><span class="float-right">{{ $cdDetails->home_chapter }}</span>
                          <br>
                      </li>
                      <li class="list-group-item">
                        <b>Birthday:</b><span class="float-right">{{$cdDetails->birthdayMonth->month_long_name}} {{$cdDetails->birthday_day}}</span>
                        <br>
                        <b>Email:</b><span class="float-right"><a href="mailto:{{ $cdDetails->email }}">{{ $cdDetails->email }}</a></span>
                        @if ($cdDetails->sec_email != null )
                        <br>
                        <b>Secondary Email:</b><span class="float-right"><a href="mailto:{{ $cdDetails->sec_email }}">{{ $cdDetails->sec_email }}</a></span>
                        @endif
                        <br>
                        <b>Phone:</b><span class="phone-mask float-right">{{$cdDetails->phone }}</span>
                        @if ($cdDetails->alt_phone != null )
                        <br>
                        <b>Atl Phone:</b><span class="phone-mask float-right">{{$cdDetails->alt_phone }}</span>
                        @endif
                        <br>
                        <b>Address:</b><span class="float-right">{{$cdDetails->address}}
                        <br>
                        {{$cdDetails->city}},{{$cdDetails->state}}&nbsp;{{$cdDetails->zip}}</span>
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
                <h3 class="profile-username">Chapters & Coordinators</h3>
                    <!-- /.card-header -->
                        <div class="row">
                            <div class="col-sm-6">
							<div class="form-group">
							    <label class="mrg-b-25">Coordinators Directly Reporting to {{ $cdDetails->first_name }}:</label>

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
                                                <td>{{ $coordinator->displayPosition->short_title }}</td>
                                            </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
                                <label class="mrg-b-25">{{ $cdDetails->first_name }} is Primary Coordinator For:</label>
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
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center">

                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.profile') }}'"><i class="fas fa-edit mr-2"></i>Update Profile</button>

                @if ($coordinatorCondition)
                    <button type="button" id="back" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chaplist') }}'"><i class="fas fa-eye mr-2"></i>View Chapter List</button>
                @elseif($inquiriesCondition)
                    <button type="button" id="back-inquiries" class="btn bg-gradient-primary mb-3" onclick="window.location.window.location.href='{{ route('chapters.chapinquiries') }}'"><i class="fas fa-eye mr-2"></i>View Inquiries Chapter List</button>
                @elseif($einCondition || $inquiriesCondition || $adminReportCondition)
                    <button type="button" id="back-international"class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intchapter') }}'"><i class="fas fa-eye mr-2"></i>View International Chapter List</button>
                @endif

                @if ($coordinatorCondition)
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.coordlist') }}'"><i class="fas fa-eye mr-2"></i>View Coordinator List</button>
                @elseif($adminReportCondition)
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intcoord') }}'"><i class="fas fa-eye mr-2"></i>View International Coordinator List</button>
                @endif

        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>



</script>
@endsection
