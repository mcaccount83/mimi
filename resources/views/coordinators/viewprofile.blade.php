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
                  <h3 class="profile-username text-center">{{ $coordinatorDetails[0]->first_name }}, {{ $coordinatorDetails[0]->last_name }}</h3>
                  <p class="text-center">{{ $coordinatorDetails[0]->confname }} Conference
                      @if ($coordinatorDetails[0]->regname != "None")
                      , {{ $coordinatorDetails[0]->regname }} Region
                      @else
                      @endif
                  </p>
                  <ul class="list-group list-group-unbordered mb-3">
                      <li class="list-group-item">
                          <b>Supervising Coordinator:</b> <span class="float-right"><a href="mailto:{{ $coordinatorDetails[0]->report_email }}">{{ $coordinatorDetails[0]->report_fname }} {{ $coordinatorDetails[0]->report_lname }}</a></span>
                          <br>
                          <b>Primary Position:</b> <span class="float-right">{{ $coordinatorDetails[0]->display_position }}</span>
                          <br>
                          <b>Secondary Position:</b> <span class="float-right">{{ $coordinatorDetails[0]->sec_position }}</span>

                      </li>
                      <li class="list-group-item">
                          <b>Start Date:</b> <span class="float-right date-mask">{{ $coordinatorDetails[0]->coordinator_start_date }}</span>
                          <br>
                          <b>Last Promotion Date:</b> <span class="float-right date-mask">{{ $coordinatorDetails[0]->last_promoted }}</span>
                          <br>
                          <b>Home Chapter:</b><span class="float-right">{{ $coordinatorDetails[0]->home_chapter }}</span>
                          <br>
                      </li>
                      <li class="list-group-item">
                        <b>Birthday:</b><span class="float-right">{{$coordinatorDetails[0]->birthday_month}} {{$coordinatorDetails[0]->birthday_day}}</span>
                        <br>
                        <b>Email:</b><span class="float-right"><a href="mailto:{{ $coordinatorDetails[0]->email }}">{{ $coordinatorDetails[0]->email }}</a></span>
                        @if ($coordinatorDetails[0]->sec_email != null )
                        <br>
                        <b>Secondary Email:</b><span class="float-right"><a href="mailto:{{ $coordinatorDetails[0]->sec_email }}">{{ $coordinatorDetails[0]->sec_email }}</a></span>
                        @endif
                        <br>
                        <b>Phone:</b><span class="phone-mask float-right">{{$coordinatorDetails[0]->phone }}</span>
                        @if ($coordinatorDetails[0]->alt_phone != null )
                        <br>
                        <b>Atl Phone:</b><span class="phone-mask float-right">{{$coordinatorDetails[0]->alt_phone }}</span>
                        @endif
                        <br>
                        <b>Address:</b><span class="float-right">{{$coordinatorDetails[0]->address}}
                        <br>
                        {{$coordinatorDetails[0]->city}},{{$coordinatorDetails[0]->state}}&nbsp;{{$coordinatorDetails[0]->zip}}</span>
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
							    <label class="mrg-b-25">Coordinators Directly Reporting to {{ $coordinatorDetails[0]->first_name }}:</label>

                                <table id="coordinator-list" width="100%">
                                    <thead>
                                        @if($directReportTo->isEmpty())
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
                                        @foreach($directReportTo as $coordinator)
                                            <tr>
                                                <td>{{ $coordinator->cor_f_name }}</td>
                                                <td>{{ $coordinator->cor_l_name }}</td>
                                                <td>{{ $coordinator->pos }}</td>
                                            </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
							</div>
						</div>

						<div class="col-sm-6">
							<div class="form-group">
                                <label class="mrg-b-25">{{ $coordinatorDetails[0]->first_name }} is Primary Coordinator For:</label>
                                    <table id="coordinator-list" width="100%">
                                        <thead>
                                            @if($directChapterTo->isEmpty())
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
                                            @foreach($directChapterTo as $chapter)
                                                <tr>
                                                    <td>{{ $chapter->st_name }}</td>
                                                    <td>{{ $chapter->ch_name }}</td>
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
