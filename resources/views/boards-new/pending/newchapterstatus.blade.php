@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Chapter Profile')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

              <div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                            <h3>New Chapter Status</h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                          <div class="row">
                            <div class="col-md-12 mb-3">
                        <div class="col-md-12 mb-1">
                            <label class="me-2">Submitted:</label>
                            <span class="date-mask">{{ $chDetails->created_at }}</span>
                        </div>
                        <div class="col-md-12 mb-1">
                            <label class="me-2">Status:</label>
                            @if ($chDetails->active_status == 2)
                                <span class="badge bg-warning text-dark fs-7">Chapter is PENDING</span>
                            @elseif ($chDetails->active_status == 3)
                                <span class="badge bg-warning text-dark fs-7">Chapter was NOT APPROVED</span><br>
                            @endif
                        </div>
                        <div class="col-md-12 mb-1">
                                @include('boards-new.partials.coordinatorlist')
                        </div>
                        <div class="col-md-12 mb-1">
                            @if ($chDetails->active_status == 2)
                                <span style="color: #dc3545;"><b>Your chapter will NOT be moved to Active Status until you have made contact with your Coordinator.</b></span><br>
                            @elseif ($chDetails->active_status == 3)
                                <span style="color: #dc3545;"><b>Your application has been declined. Contact your Coordinator for more information.</b></span><br>
                            @endif
                        </div>

   </div>
                    </div>
                <br>

                {{-- Start of New Chapter Application --}}
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header"><strong>Application Details</strong>
                                </div>
                                <div class="card-body">

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

                    @if ($chDetails->active_status == \App\Enums\ChapterStatusEnum::PENDING)
                        <div class="col-md-12 mb-3">
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
                    </div>
                    @endif
                </div>

                    <div class="card-body text-center mt-3">
                        <button id="Password" type="button" class="btn btn-primary bg-gradient mb-2" onclick="showChangePasswordAlert('{{ $chDetails->pendingPresident->user_id }}')"><i class="bi bi-lock-fill me-2" ></i>Change Password</button>
                                </div>

               </div>
            </div>
        </div>
       </div>
       <!-- /.financial-container- -->

       </div>
            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->

    </form>

            </div>
            <!-- /.col -->
       </div>
    </div>
    <!-- /.container- -->

@endsection
@section('customscript')
@php $disableMode = 'disable-all'; @endphp
@include('layouts.scripts.disablefields')
@endsection
