@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinator Details')
@section('breadcrumb', 'Coordinator Details')

@section('content')
    <!-- Main content -->
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
                <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#general" data-bs-toggle="tab">Chapters & Coordinators</a></li>
                  <li class="nav-item"><a class="nav-link" href="#contact" data-bs-toggle="tab">Contact Information</a></li>
                  <li class="nav-item"><a class="nav-link" href="#subscriptions" data-bs-toggle="tab">Subscriptions</a></li>
                  <li class="nav-item"><a class="nav-link" href="#recog" data-bs-toggle="tab">Appreciation & Recognitions</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="general">
                    <div class="general-field">
                            <div class="card-header bg-transparent border-0">
                        <h3>Chapters & Coordinators
                            <button class="btn btn-primary bg-gradient btn-xs ms-2" onclick="window.location.href='{{ route('coordreports.coordrptreportingtree') }}'">View Coordinator Reporting Tree</button>
                        </h3>
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
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.tab-pane -->
                <div class="tab-pane" id="contact">
                    <div class="contact-field">
                        <div class="card-header bg-transparent border-0">
                        <h3>Contact Information</h3>
                         </div>
                                <!-- /.card-header -->
                            <div class="card-body">
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
                                    <button class="btn btn-primary bg-gradient btn-sm" onclick="updateCardSent()">Update Birthday Card Sent</button>
                                @endif
                            </div>
                        </div>

                            <br>
                            <p>This will reset password to default "TempPass4You" for this user only.
                            <br>
                            <button type="button" class="btn btn-primary bg-gradient btn-xs reset-password-btn" data-user-id="{{ $cdDetails->user_id }}">Reset Coordinator Password</button>
                            </p>
                      </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.tab-pane -->
                <div class="tab-pane" id="subscriptions">
                    <div class="subscriptions-field">
                                <div class="card-header bg-transparent border-0">
                        <h3>Subscriptions</h3>
                         </div>
                                <!-- /.card-header -->
                            <div class="card-body">
                        <div class="row">
                            @php
                                $Subscriptions = $cdDetails->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            <dt class="col-sm-3">Public Announcements</dt>
                            <dd class="col-sm-2">{{ in_array(1, $Subscriptions) ? 'YES' : 'NO' }}</dd>
                            @if ($assistConferenceCoordinatorCondition)
                                <dd class="col-sm-6">
                                    @if (in_array(1, $Subscriptions))
                                        <button class="btn btn-primary bg-gradient btn-sm" onclick="unsubscribe(1, {{ $cdDetails->user_id }})">Unsubscribe</button>
                                    @else
                                        <button class="btn btn-primary bg-gradient btn-sm" onclick="subscribe(1, {{ $cdDetails->user_id }})">Subscribe</button>
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
                                        <button class="btn btn-primary bg-gradient btn-sm" onclick="unsubscribe(2, {{ $cdDetails->user_id }})">Unsubscribe</button>
                                    @else
                                        <button class="btn btn-primary bg-gradient btn-sm" onclick="subscribe(2, {{ $cdDetails->user_id }})">Subscribe</button>
                                    @endif
                                </dd>
                            @endif

                        </div>
                        </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.tab-pane -->
                <div class="tab-pane" id="recog">
                    <div class="recog-field">
                        <div class="card-header bg-transparent border-0">
                        <h3>Appreciation & Recognitions</h3>
                         </div>
                                <!-- /.card-header -->
                            <div class="card-body">
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
                    <!-- /.card-body -->
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
            <div class="card-body text-center mt-3">
                @if ($regionalCoordinatorCondition)
                    <button class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('coordinators.editrole', ['id' => $cdDetails->id]) }}'"><i class="bi bi-person-workspace me-2"></i>Update Role, Chapters & Coordinators</button>
                    <button class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('coordinators.editdetails', ['id' => $cdDetails->id]) }}'"><i class="bi bi-person-vcard-fill me-2"></i>Update Contact Information</button>
                @endif
                @if($assistConferenceCoordinatorCondition)
                    <button class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('coordinators.editrecognition', ['id' => $cdDetails->id]) }}'"><i class="bi bi-gift-fill me-2"></i>Update Appreciation & Recognition</button>
                    <br>
                @endif
                @if($regionalCoordinatorCondition)
                @if ($cdPositionid == \App\Enums\CoordinatorPosition::BS && $startDate->greaterThanOrEqualTo($threeMonthsAgo))
                        <button id="BigSister" type="button" class="btn btn-primary bg-gradient mb-2" onclick="showBigSisterEmailModal({{ $cdDetails->id }})"><i class="bi bi-envelope-fill me-2"></i>Send Big Sister Welcome Email</button>
                    @endif

                    @if($cdLeave != 1)
                        <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="onLeaveCoordinator({{ $cdDetails->id }})"><i class="bi bi-ban me-2"></i>Put Coordinator On Leave</button>
                    @elseif($cdLeave == 1)
                        <button type="button" id="removeleave" class="btn btn-primary bg-gradient mb-2" onclick="removeLeaveCoordinator({{ $cdDetails->id }})"><i class="bi bi-arrow-counterclockwise me-2"></i>Remove Coordinator From Leave</button>
                    @endif

                    @if($cdActiveId == 1)
                        <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="retireCoordinator({{ $cdDetails->id }})"><i class="bi bi-ban me-2"></i>Retire Coordinator</button>
                    @elseif($cdActiveId != 1)
                        <button type="button" id="unretire" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="unRetireCoordinator({{ $cdDetails->id }})"><i class="bi bi-arrow-counterclockwise me-2"></i>UnRetire Coordinator</button>
                    @endif
                @endif
                <br>
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
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
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
