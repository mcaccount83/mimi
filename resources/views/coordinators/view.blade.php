@extends('layouts.coordinator_theme')
<style>

.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')


  <!-- Contains page content -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Coordinator Details</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
              <li class="breadcrumb-item active">Coordinator Details</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

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
                    {{-- <li class="list-group-item text-center">
                        <b> {{ $coordinatorDetails[0]->position }}</b>
                        @if ($coordinatorDetails[0]->sec_position != null )
                        <br>
                        <b> {{ $coordinatorDetails[0]->sec_position }}</b>
                        @endif
                    </li> --}}
                    <li class="list-group-item">
                        <b>Supervising Coordinator:</b> <span class="float-right">{{ $coordinatorDetails[0]->report_fname }} {{ $coordinatorDetails[0]->report_lname }}</span>
                        <br>
                        <b>Display Position:</b> <span class="float-right">{{ $coordinatorDetails[0]->display_position }}</span>
                        <br>
                        <b>Primary Positon for MIMI Purposes:</b> <span class="float-right">{{ $coordinatorDetails[0]->position }}</span>
                        <br>
                        <b>Secondary Position:</b> <span class="float-right">{{ $coordinatorDetails[0]->sec_position }}</span>

                    </li>
                    <li class="list-group-item">
                        <b>Start Date:</b> <span class="float-right date-mask">{{ $coordinatorDetails[0]->coordinator_start_date }}</span>
                        <br>
                        <b>Last Promotion Date:</b> <span class="float-right date-mask">{{ $coordinatorDetails[0]->last_promoted }}</span>
                        <br>
                        <b>Home Chapter:</b> <span class="float-right">{{ $coordinatorDetails[0]->home_chapter }}</span>
                    </li>
                </ul>
                <div class="text-center">
                    @if ($coordinatorDetails[0]->is_active == 1 && $coordinatorDetails[0]->on_leave != 1)
                        <b><span style="color: #28a745;">Coordinator is ACTIVE</span></b>
                    @elseif ($coordinatorDetails[0]->is_active == 1 && $coordinatorDetails[0]->on_leave == 1)
                        <b><span style="color: #ff851b;">Coordinator is ON LEAVE</span></b>
                        <br>
                        Leave Date: <span class="date-mask">{{ $coordinatorDetails[0]->leave_date }}</span><br>
                    @else
                        <b><span style="color: #dc3545;">Coordinator is RETIRED</span></b>
                        <br>
                        Retired Date: <span class="date-mask">{{ $coordinatorDetails[0]->zapped_date }}</span><br>
                        {{ $coordinatorDetails[0]->reason_retired }}
                    @endif
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">Chapters & Coordinators</a></li>
                  <li class="nav-item"><a class="nav-link" href="#contact" data-toggle="tab">Contact Information</a></li>
                  <li class="nav-item"><a class="nav-link" href="#recog" data-toggle="tab">Appreciation & Recognitions</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="general">
                    <div class="general-field">
                        <h3 class="profile-username">Chapters & Coordinators</h3>
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
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="contact">
                    <div class="contact-field">
                        <h3 class="profile-username">Contact Information</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="mailto:{{ $coordinatorDetails[0]->email }}">{{ $coordinatorDetails[0]->email }}</a>
                                @if ($coordinatorDetails[0]->sec_email != null )
                                <br>
                                <a href="mailto:{{ $coordinatorDetails[0]->sec_email }}">{{ $coordinatorDetails[0]->sec_email }}</a>
                                @endif
                                <br>
                                <span class="phone-mask">{{$coordinatorDetails[0]->phone }}</span>
                                @if ($coordinatorDetails[0]->alt_phone != null )
                                <br>
                                <span class="phone-mask">{{$coordinatorDetails[0]->alt_phone }}</span>
                                @endif
                                <br>
                                {{$coordinatorDetails[0]->address}}
                                <br>
                                {{$coordinatorDetails[0]->city}},{{$coordinatorDetails[0]->state}}&nbsp;{{$coordinatorDetails[0]->zip}}
                            </div>
                            <div class="col-md-6">
                                Birthday: {{$birthMonthWords}} {{$coordinatorDetails[0]->birthday_day}}<br>
                                Card Sent: <span class="date-mask">{{ $coordinatorDetails[0]->card_sent }}</span><br>
                                <button class="btn bg-gradient-primary btn-sm" onclick="window.location.href='{{ route('coordreports.coordrptbirthdaysview', ['id' => $coordinatorDetails[0]->id]) }}'">Update Birthday Card Sent</button>
                            </div>
                        </div>

                            <br>
                            <p>This will reset password to default "TempPass4You" for this user only.
                            <br>
                            <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $coordinatorDetails[0]->user_id }}">Reset President Password</button>
                            </p>
                      </div>
                    </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="recog">
                    <div class="recog-field">
                        <h3 class="profile-username">Appreciation & Recognitions</h3>
                        <dl class="row">
                            <dt class="col-sm-2">&lt; 1 Year</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_year0}}</dd>
                            <dt class="col-sm-2">1 Year</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_year1}}</dd>
                            <dt class="col-sm-2">2 Years</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_year2}}</dd>
                            <dt class="col-sm-2">3 Years</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_year3}}</dd>
                            <dt class="col-sm-2">4 Years</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_year4}}</dd>
                            <dt class="col-sm-2">5 Years</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_year5}}</dd>
                            <dt class="col-sm-2">6 Years</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_year6}}</dd>
                            <dt class="col-sm-2">7 Years</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_year7}}</dd>
                            <dt class="col-sm-2">8 Years</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_year8}}</dd>
                            <dt class="col-sm-2">9 Years</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_year9}}</dd>
                            <dt class="col-sm-2">Top Tier</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_toptier}}</dd>
                            <dt class="col-sm-2">MC Necklace</dt>
                            <dd class="col-sm-10">{{$coordinatorDetails[0]->recognition_necklace == 1 ? 'YES' : 'NO' }}</dd>
                          </dl>
                          <button class="btn bg-gradient-primary btn-sm" onclick="window.location.href='{{ route('coordreports.coordrptappreciationview', ['id' => $coordinatorDetails[0]->id]) }}'">Update Recognition Gifts</button>
                    </div>
                  </div>
                 <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center">
                @if ($regionalCoordinatorCondition)
                    <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.coordroleview', ['id' => $coordinatorDetails[0]->id]) }}'">Update Coordinator Role Information</button>
                    <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.coordview', ['id' => $coordinatorDetails[0]->id]) }}'">Update Coordinator Contact Information</button>
                    <br>
                    @if ($corConfId == $userConfId)
                        @if ($corIsActive == 1)
                            <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.coordlist') }}'">Back to Coordinator List</button>
                        @else
                            <button id="back-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.coordretired') }}'">Back to Retired Coordinator List</button>
                        @endif
                    @elseif ($einCondition && ($corConfId != $userConfId) || $inquiriesCondition  && ($corConfId != $userConfId) || $adminReportCondition  && ($corConfId != $userConfId))
                        @if ($corIsActive == 1)
                            <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intcoord') }}'">Back to International Coordinator List</button>
                        @else
                            <button id="back-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intcoordretired') }}'">Back to International Retired Coordinator List</button>
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
<script>

var $corIsActive = {{ $corIsActive }};

$(document).ready(function () {
    // Disable fields for chapters that are not active
    if ($corIsActive != 1)
        $('input, select, textarea, button').prop('disabled', true);

        $('a[href^="mailto:"]').each(function() {
            $(this).addClass('disabled-link'); // Add disabled class for styling
            $(this).attr('href', 'javascript:void(0);'); // Prevent navigation
            $(this).on('click', function(e) {
                e.preventDefault(); // Prevent link click
            });
        });

        $('#back-zapped').prop('disabled', false); // Enable Back-Zapped Button

});

document.querySelectorAll('.reset-password-btn').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();

        const userId = this.getAttribute('data-user-id');
        const newPassword = "TempPass4You";

        $.ajax({
            url: '{{ route('updatepassword') }}',
            type: 'PUT',
            data: {
                user_id: userId,
                new_password: newPassword,
                _token: '{{ csrf_token() }}'
            },
            success: function(result) {
                Swal.fire({
                    title: 'Success!',
                    text: result.message.replace('<br>', '\n'),
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn-sm btn-success'
                    }
                });
            },
            error: function(jqXHR, exception) {
                console.log(jqXHR.responseText); // Log error response
            }
        });
    });
});


</script>
@endsection
