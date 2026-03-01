@extends('layouts.coordinator_theme')

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
                <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#general" data-bs-toggle="tab">General</a></li>
                    <li class="nav-item"><a class="nav-link" href="#board" data-bs-toggle="tab">Executive Board</a></li>
                    <li class="nav-item"><a class="nav-link" href="#online" data-bs-toggle="tab">Online/Social Media</a></li>
                    <li class="nav-item"><a class="nav-link" href="#rereg" data-bs-toggle="tab">Re-Registration</a></li>
                    <li class="nav-item"><a class="nav-link" href="#donation" data-bs-toggle="tab">Donations/Grants</a></li>
                    <li class="nav-item"><a class="nav-link" href="#documents" data-bs-toggle="tab">Documents</a></li>
                    <li class="nav-item"><a class="nav-link" href="#eoy" data-bs-toggle="tab">End of Year</a></li>
                </ul>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">

                  <div class="active tab-pane" id="general">
                    <div class="general-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>General Information</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="me-2">EIN:</label>{{$chDetails->ein}}<br>
                                    <label class="me-2">Conference:</label>{{ $conferenceDescription }}<br>
                                    <label class="me-2">Region:</label>{{ $regionLongName }}<br>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="me-2">Boundaries:</label>{{ $chDetails->territory}}<br>
                                    <label class="me-2">Status:</label>{{$chapterStatus}}<br>
                                    @if ($chDetails->status_id != \App\Enums\OperatingStatusEnum::OK)
                                        <label class="me-2">Probation Reason:</label>{{$probationReason}}<br>
                                    @endif
                                    <label class="me-2">Founded:</label>{{ $startMonthName }} {{ $chDetails->start_year }}<br>
                                </div>
                                <div class="col-4 mb-3">
                                    @include('partials.coordinatorlist')
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="board">
                    <div class="board-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>Executive Board</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-3">
                                    <label class="me-2"><h3>President</h3></label>
                                </div>
                                <div class="col-9 ">
                                    @include('boards.partials.presinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-3">
                                    <label class="me-2"><h3>AVP</h3></label>
                                </div>
                                <div class="col-9 ">
                                    @include('boards.partials.avpinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-3">
                                    <label class="me-2"><h3>MVP</h3></label>
                                </div>
                                <div class="col-9 ">
                                    @include('boards.partials.mvpinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-3">
                                    <label class="me-2"><h3>Treasurer</h3></label>
                                </div>
                                <div class="col-9 ">
                                    @include('boards.partials.trsinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-3">
                                    <label class="me-2"><h3>Secretary</h3></label>
                                </div>
                                <div class="col-9 ">
                                    @include('boards.partials.secinfo')
                                </div>
                            </div>


                             </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="online">
                    <div class="online-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>Online Information</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">

                            <div class="row">
                              <div class="col-12 mb-3">
                                <label class="me-2">Website:</label>
                                    @if($chDetails->website_url == 'http://' || empty($chDetails->website_url))
                                        No Website<br>
                                    @else
                                        <a href="{{$chDetails->website_url}}" target="_blank">{{$chDetails->website_url}}</a><br>
                                    @endif
                                <label class="me-2">Webiste Link Status:</label> {{$websiteLink}}
                            </div>
                            <div class="col-12 mb-3">
                                <label class="me-2">Forum/Group/App:</label> {{ $chDetails->egroup}}<br>
                                <label class="me-2">Facebook:</label> {{ $chDetails->social1}}<br>
                                <label class="me-2">Twitter:</label> {{ $chDetails->social2}}<br>
                                <label class="me-2">Instagram:</label> {{ $chDetails->social3}}<br>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.tab-pane -->

                <div class="tab-pane" id="rereg">
                    <div class="rereg-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>Re-Registration History</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4 mb-3">
                                    @include('partials.paymentinfo')
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="me-2">Boundaries:</label>{{ $chDetails->territory}}<br>
                                    <label class="me-2">Status:</label>{{$chapterStatus}}<br>
                                    @if ($chDetails->status_id != \App\Enums\OperatingStatusEnum::OK)
                                        <label class="me-2">Probation Reason:</label>{{$probationReason}}<br>
                                    @endif
                                    <label class="me-2">Founded:</label>{{ $startMonthName }} {{ $chDetails->start_year }}<br>
                                </div>
                            </div>

                             </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="donation">
                    <div class="donation-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>Donation History</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">

                             </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="documents">
                    <div class="documents-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>Chapter Documents</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">

                             </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="eoy">
                    <div class="eoy-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>End of Year Information</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">

                             </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->



                </div>
                <!-- /.tab-content -->
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
