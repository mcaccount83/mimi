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
                        <b>Supervising Coordinator:</b> <span class="float-right">{{ $ReportTo }}</span>
                        <br>
                        <b>Display Position:</b> <span class="float-right">{{ $displayPosition->long_title }}</span>
                        <br>
                        <b>MIMI Position:</b><a href="javascript:void(0);" onclick="showPositionInformation()" title="Show Position Information">
                            <i class="fas fa-circle-question text-primary"></i></a>
                        <span class="float-right">{{ $mimiPosition?->long_title }}</span>
                        <br>
                        <div style="display: flex; justify-content: space-between;">
                            <b>Secondary Positions:</b>
                            <span style="text-align: right;">
                                @forelse($cdDetails->secondaryPosition as $position)
                                    {{ $position->long_title }}@if(!$loop->last)<br>@endif
                                @empty
                                    None
                                @endforelse
                            </span>
                        </div>
                        @if ($ITCondition)
                        <b>MIMI Admin:</b> <span class="float-right">{{ $cdAdminRole->admin_role }}</span>
                        @endif

                    </li>
                    <li class="list-group-item">
                        <b>Start Date:</b> <span class="float-right date-mask">{{ $cdDetails->coordinator_start_date }}</span>
                        <br>
                        <b>Last Promotion Date:</b> <span class="float-right date-mask">{{ $cdDetails->last_promoted }}</span>
                        <br>
                        <b>Home Chapter:</b> <span class="float-right">{{ $cdDetails->home_chapter }}</span>
                    </li>
                </ul>
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
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">Chapters & Coordinators</a></li>
                  <li class="nav-item"><a class="nav-link" href="#contact" data-toggle="tab">Contact Information</a></li>
                  <li class="nav-item"><a class="nav-link" href="#subscriptions" data-toggle="tab">Subscriptions</a></li>
                  <li class="nav-item"><a class="nav-link" href="#recog" data-toggle="tab">Appreciation & Recognitions</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="general">
                    <div class="general-field">
                        <h3 class="profile-username">Chapters & Coordinators
                            <button class="btn bg-gradient-primary btn-xs ml-2" onclick="window.location.href='{{ route('coordreports.coordrptreportingtree') }}'">View Coordinator Reporting Tree</button>
                        </h3>
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
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="contact">
                    <div class="contact-field">
                        <h3 class="profile-username">Contact Information</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="mailto:{{ $cdDetails->email }}">{{ $cdDetails->email }}</a>
                                @if ($cdDetails->sec_email != null )
                                <br>
                                <a href="mailto:{{ $cdDetails->sec_email }}">{{ $cdDetails->sec_email }}</a>
                                @endif
                                <br>
                                <span class="phone-mask">{{$cdDetails->phone }}</span>
                                @if ($cdDetails->alt_phone != null )
                                <br>
                                <span class="phone-mask">{{$cdDetails->alt_phone }}</span>
                                @endif
                                <br>
                                {{$cdDetails->address}}
                                <br>
                                {{$cdDetails->city}}, {{$cdDetails->state->state_short_name}}&nbsp;{{$cdDetails->zip}}
                                <br>
                                {{$cdDetails->country->short_name}}
                            </div>
                            <div class="col-md-6">
                                Birthday: {{$cdDetails->birthdayMonth->month_long_name}} {{$cdDetails->birthday_day}}<br>
                                Card Sent: <span class="date-mask">{{ $cdDetails->card_sent }}</span><br>
                                @if ($assistConferenceCoordinatorCondition)
                                    <button class="btn bg-gradient-primary btn-sm" onclick="updateCardSent()">Update Birthday Card Sent</button>
                                @endif
                            </div>
                        </div>

                            <br>
                            <p>This will reset password to default "TempPass4You" for this user only.
                            <br>
                            <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $cdDetails->user_id }}">Reset Coordinator Password</button>
                            </p>
                      </div>
                    </div>
                     <!-- /.tab-pane -->
                <div class="tab-pane" id="subscriptions">
                    <div class="subscriptions-field">
                        <h3 class="profile-username">Subscriptions</h3>
                        <div class="row">
                            @php
                                $Subscriptions = $cdDetails->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            <dt class="col-sm-3">Public Announcements</dt>
                            <dd class="col-sm-2">{{ in_array(1, $Subscriptions) ? 'YES' : 'NO' }}</dd>
                            @if ($assistConferenceCoordinatorCondition)
                                <dd class="col-sm-6">
                                    @if (in_array(1, $Subscriptions))
                                        <button class="btn bg-gradient-primary btn-sm" onclick="unsubscribe(1, {{ $cdDetails->user_id }})">Unsubscribe</button>
                                    @else
                                        <button class="btn bg-gradient-primary btn-sm" onclick="subscribe(1, {{ $cdDetails->user_id }})">Subscribe</button>
                                    @endif
                                </dd>
                            @endif
                            @php
                                $Subscriptions = $cdDetails->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            <dt class="col-sm-3">CoordinatorList</dt>
                            <dd class="col-sm-2">{{ in_array(2, $Subscriptions) ? 'YES' : 'NO' }}</dd>
                            @if ($assistConferenceCoordinatorCondition)
                                <dd class="col-sm-6">
                                    @if (in_array(2, $Subscriptions))
                                        <button class="btn bg-gradient-primary btn-sm" onclick="unsubscribe(2, {{ $cdDetails->user_id }})">Unsubscribe</button>
                                    @else
                                        <button class="btn bg-gradient-primary btn-sm" onclick="subscribe(2, {{ $cdDetails->user_id }})">Subscribe</button>
                                    @endif
                                </dd>
                            @endif

                        </div>
                      </div>
                    </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="recog">
                    <div class="recog-field">
                        <h3 class="profile-username">Appreciation & Recognitions</h3>
                        <dl class="row">
                            <dt class="col-sm-2">&lt; 1 Year</dt>
                            <dd class="col-sm-4">{{$cdDetails->recognition_year0}}</dd>
                            <dt class="col-sm-2">1 Year</dt>
                            <dd class="col-sm-4">{{$cdDetails->recognition_year1}}</dd>
                            <dt class="col-sm-2">2 Years</dt>
                            <dd class="col-sm-4">{{$cdDetails->recognition_year2}}</dd>
                            <dt class="col-sm-2">3 Years</dt>
                            <dd class="col-sm-4">{{$cdDetails->recognition_year3}}</dd>
                            <dt class="col-sm-2">4 Years</dt>
                            <dd class="col-sm-4">{{$cdDetails->recognition_year4}}</dd>
                            <dt class="col-sm-2">5 Years</dt>
                            <dd class="col-sm-4">{{$cdDetails->recognition_year5}}</dd>
                            <dt class="col-sm-2">6 Years</dt>
                            <dd class="col-sm-4">{{$cdDetails->recognition_year6}}</dd>
                            <dt class="col-sm-2">7 Years</dt>
                            <dd class="col-sm-4">{{$cdDetails->recognition_year7}}</dd>
                            <dt class="col-sm-2">8 Years</dt>
                            <dd class="col-sm-4">{{$cdDetails->recognition_year8}}</dd>
                            <dt class="col-sm-2">9 Years</dt>
                            <dd class="col-sm-4">{{$cdDetails->recognition_year9}}</dd>
                            <dt class="col-sm-2">Top Tier</dt>
                            <dd class="col-sm-10">{{$cdDetails->recognition_toptier}}</dd>
                            <dt class="col-sm-2">MC Necklace</dt>
                            <dd class="col-sm-10">{{$cdDetails->recognition_necklace == 1 ? 'YES' : 'NO' }}</dd>
                          </dl>
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
                    <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.editrole', ['id' => $cdDetails->id]) }}'"><i class="fas fa-edit mr-2"></i>Update Role, Chapters & Coordinators</button>
                    <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.editdetails', ['id' => $cdDetails->id]) }}'"><i class="fas fa-edit mr-2"></i>Update Contact Information</button>
                @endif
                @if($assistConferenceCoordinatorCondition)
                    <button class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.editrecognition', ['id' => $cdDetails->id]) }}'"><i class="fas fa-edit mr-2"></i>Update Appreciation & Recognition</button>
                    <br>
                @endif
                @if($regionalCoordinatorCondition)
                @if ($cdPositionid == \App\Enums\CoordinatorPosition::BS && $startDate->greaterThanOrEqualTo($threeMonthsAgo))
                        <button id="BigSister" type="button" class="btn bg-gradient-primary mb-3" onclick="showBigSisterEmailModal({{ $cdDetails->id }})"><i class="fas fa-envelope mr-2"></i>Send Big Sister Welcome Email</button>
                    @endif

                    @if($cdLeave != 1)
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="onLeaveCoordinator({{ $cdDetails->id }})"><i class="fas fa-ban mr-2"></i>Put Coordinator On Leave</button>
                    @elseif($cdLeave == 1)
                        <button type="button" id="removeleave" class="btn bg-gradient-primary mb-3" onclick="removeLeaveCoordinator({{ $cdDetails->id }})"><i class="fas fa-undo mr-2"></i>Remove Coordinator From Leave</button>
                    @endif

                    @if($cdActiveId == 1)
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="retireCoordinator({{ $cdDetails->id }})"><i class="fas fa-ban mr-2"></i>Retire Coordinator</button>
                    @elseif($cdActiveId != 1)
                        <button type="button" id="unretire" class="btn bg-gradient-primary mb-3" onclick="unRetireCoordinator({{ $cdDetails->id }})"><i class="fas fa-undo mr-2"></i>UnRetire Coordinator</button>
                    @endif
                @endif
                <br>
                   @if ($cdConfId == $confId)
                        @if ($cdActiveId == \App\Enums\CoordinatorStatusEnum::ACTIVE)
                            <button type="button" id="back-list" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordlist') }}'"><i class="fas fa-reply mr-2"></i>Back to Active Coordinator List</button>
                        @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::PENDING)
                            <button type="button" id="back-pending" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordpending') }}'"><i class="fas fa-reply mr-2"></i>Back to Pending Coordinator List</button>
                        @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::NOTAPPROVED)
                            <button type="button" id="back-declined" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordrejected') }}'"><i class="fas fa-reply mr-2"></i>Back to Not Approved Coordinator List</button>
                        @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::RETIRED)
                            <button type="button" id="back-zapped" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordretired') }}'"><i class="fas fa-reply mr-2"></i>Back to Retired Coordinator List</button>
                        @endif
                    @else
                        @if ($cdConfId != $confId)
                            @if ($ITCondition)
                                @if ($cdActiveId == \App\Enums\CoordinatorStatusEnum::ACTIVE)
                                    <button type="button" id="back-list" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordlist', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Active Coordinator List</button>
                                @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::PENDING)
                                    <button type="button" id="back-pending" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordpending', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Pending Coordinator List</button>
                                @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::NOTAPPROVED)
                                    <button type="button" id="back-declined" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('coordinators.coordrejected', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Not Approved Coordinator List</button>
                                @elseif ($cdActiveId == \App\Enums\CoordinatorStatusEnum::RETIRED)
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
    <script>
        console.log('Page variables:');
        console.log('chActiveId:', @json($chActiveId ?? 'NOT SET'));
        console.log('coordinatorCondition:', @json($coordinatorCondition ?? 'NOT SET'));
        console.log('supervisingCoordinatorCondition:', @json($supervisingCoordinatorCondition ?? 'NOT SET'));
        console.log('ITCondition:', @json($ITCondition ?? 'NOT SET'));
        console.log('confId:', @json($confId ?? 'NOT SET'));
        console.log('cdConfId:', @json($cdConfId ?? 'NOT SET'));
    </script>
    @include('layouts.scripts.disablefields')

<script>

function updateCardSent() {
    const coordId = '{{ $cdDetails->id ?? '' }}'; // Use a fallback if `id` is null or undefined

    Swal.fire({
        title: 'Enter Date',
        html: `
            <p>Please enter the Date that the Birthday card was sent.</p>
            <div style="display: flex; align-items: center;">
                <input type="date" id="card_sent" name="card_sent" class="swal2-input" placeholder="Enter Date" required style="width: 100%;">
            </div>
            <input type="hidden" id="coord_id" name="coord_id" value="${coordId}">
            <br>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const card_sent = Swal.getPopup().querySelector('#card_sent').value;
            const coord_id = Swal.getPopup().querySelector('#coord_id').value;

            if (!card_sent) {
                Swal.showValidationMessage(`Please enter a date.`);
            }

            return {
                id: coord_id,
                card_sent: card_sent,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Perform the AJAX request to update the coordinator info
            $.ajax({
                url: '{{ route('coordinators.updatecardsent') }}',
                type: 'POST',
                data: {
                    id: data.id,
                    card_sent: data.card_sent,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function(jqXHR, exception) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong, Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    });
                }
            });
        }
    });
}

</script>
@endsection
