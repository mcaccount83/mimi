@extends('layouts.board_theme')

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                      <div class="col-md-12">
                            <div class="card card-widget widget-user">
                                <div class="widget-user-header bg-primary">
                                    <div class="widget-user-image">
                                        <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div class="col-md-12"><br><br></div>
                                    <h2 class="text-center">New Chapter Application</h2>
                                    {{-- <p class="description text-center">
                                        All chapters are in PENDING status until reviewed by our Coordintaor Team.<br>
                                        After review, you will receive an email communication from your Coordinator.<br>
                                        If you have not heard from your Coordintor within 5 days of your application, please reach out to them.<br>
                                        Sometimes messages may have ended up in spam or junk folders.  Their name and contact informatin is listed below.
                                    </p> --}}

                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>

    <div class="row">
            <div class="col-md-8">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                         <!-- /.card-header -->

                         <div class="row">
                            <div class="col-md-12">
                        <h5>Application Information</h5>

                        <div class="board-info">
                            <div class="info-row">
                                <div class="info-label">Name:</div>
                                <div class="info-data"> {{ $chDetails->name }}, {{$stateShortName}}</div>
                            </div>
                        </div>

                        <div class="board-info">
                            <div class="info-row">
                                <div class="info-label">Boundaries:</div>
                                <div class="info-data"> {{ $chDetails->territory}}</div>
                            </div>
                        </div>

                      <!-- /. group -->
                    <div class="board-info">
                        <div class="info-row">
                            <div class="info-label">Founder:</div>
                            <div class="info-data">{{ $chDetails->pendingPresident->first_name }} {{ $chDetails->pendingPresident->last_name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label-empty"></div>
                            <div class="info-data"><a href="mailto:{{ $chDetails->pendingPresident->email }}">{{ $chDetails->pendingPresident->email }}</a></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label-empty"></div>
                            <div class="info-data">{{ $chDetails->pendingPresident->phone }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label-empty"></div>
                            <div class="info-data">{{ $chDetails->pendingPresident->street_address }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label-empty"></div>
                            <div class="info-data">{{ $chDetails->pendingPresident->city }},
                                @if(is_numeric($chDetails->pendingPresident->state))
                                    @foreach($allStates as $state)
                                        @if($state->id == $chDetails->pendingPresident->state)
                                            {{ $state->state_short_name }}
                                        @endif
                                    @endforeach
                                @else
                                    {{ $chDetails->pendingPresident->state->state_short_name }}
                                @endif
                                {{ $chDetails->pendingPresident->zip }}</div>
                        </div>
                    </div>
                    </div>

                    @if ($chDetails->active_status == \App\Enums\ChapterStatusEnum::PENDING)
                    <p>
                        <br>
                        Here are a few things to keep in mind as you start your MOMS Club journey.
                        <ul>
                            <li>All chapters are in PENDING status until reviewed by our Coordintaor Team.</li>
                            <li>After review, you will receive an email from your Coordinator to establish initial communication as well as verify/set your official chapter name
                                and boundaries.</li>
                            <li>After communication has been established, your credit card will be charged and your chapter will move to ACTIVE status</li>
                            <li>You will also see your Coordinator's contact information listed here in MIMI.  If you do not hear from them within a week of submitting your application, please reach out to them
                                directly as sometimes messages do end up in spam.</li>
                            <li>After your chapter has moved to ACTIVE status you'll see your MIMI options change to allow more access and infomration, but your login credentials will remain the same.</li>
                        </ul>
                    </p>
                    @endif

                </div>

            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->

            <div class="col-md-4">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">


                        <h5>Application Status</h5>

                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Submitted</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $chDetails->created_at }}</span>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <label class="col-sm-4 col-form-label">Status</label>
                            <div class="col-sm-8">
                                <span class="float-right">{{ $chDetails->activeStatus->active_status }}</span>
                            </div>
                        </div>

                        @if ($chDetails->active_status == \App\Enums\ChapterStatusEnum::PENDING)
                            <span style="color: #dc3545;"><b>Your chapter will NOT be moved to Active Status until you have made contact with your Coordinator.</b></span><br>
                        @elseif ($chDetails->active_status == \App\Enums\ChapterStatusEnum::NOTAPPROVED)
                            <span style="color: #dc3545;"><b>Your application has been declined. Contact your Coordinator for more information.</b></span><br>
                        @endif

               <hr>
                        <h5>Coordinators</h5>
                  <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                  <input  type="hidden" id="pcid" value="{{ $chDetails->primary_coordinator_id}}">
                  <div id="display_corlist" ></div>

                    </div>
                <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>

    <div class="card-body text-center">

        <button id="Password" type="button" class="btn btn-primary" onclick="showChangePasswordAlert('{{ $chDetails->pendingPresident->user_id }}')"><i class="fas fa-lock" ></i>&nbsp; Change Password</button>
        <button id="logout-btn" class="btn btn-primary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-undo" ></i>&nbsp; Logout</button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    <!-- /.container- -->
@endsection
@section('customscript')
<script>
/* Disable fields and buttons  */
$(document).ready(function () {
    var userTypeId = @json($userTypeId);
    var userAdmin = @json($userAdmin);

    if (userAdmin == 1) {
        $('#Password, #logout-btn').prop('disabled', true);
    }else if (userTypeId == 1 && userAdmin != 1) {
        // Disable all input fields, select elements, textareas, and buttons
        $('input, select, textarea').prop('disabled', true);
        $('#Save, #Password, #logout-btn').prop('disabled', true);
        // Disable links by adding a class and modifying their behavior
        $('#display_corlist').addClass('disabled-link').attr('href', '#');
    }

    // Check the disabled status of EOY Buttons and show the "fields are locked" description if necessary
    if ($('input, select, textarea').prop('disabled')) {
        $('.description').show();
    }
});

</script>
@endsection
