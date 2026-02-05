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
                        <h2 class="text-center"> MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                        <h4 class="text-center">Mother-to-Mother Fund Grant Requests</h4>

                    <div class="col-md-12"><br></div>

                        </div>
                    </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>

           <div class="col-md-12">
                        <div class="card card-primary card-outline">
                    <div class="card-body">
	                    <div class="row">

                @if($grantList->count() > 0)
                    @foreach($grantList as $grant)
                    <div class="card mb-2">
                        <div class="card-body">
                            Date: {{ date('m/d/Y', strtotime($grant->submitted_at)) }}<br>
                            Member in Need: {{ $grant->first_name }} {{ $grant->last_name }}<br>
                            Grant Submitted:
                                @if ($grant->submitted == '1')
                                    Submitted<br>
                                @else
                                    Draft<br>
                                @endif
                            @if ($grant->submitted == '1')
                                Grant Status:
                                 @if ($grant->grant_approved == '1')
                                    Grant Approved<br>
                                @elseif ($grant->grant_approved == '0')
                                    Grant Denied<br>
                                @else
                                    Review not Complete<br>
                                @endif
                            @endif
                            <button type="button" class="btn bg-gradient-primary btn-xs mb-1 mt-1 keep-enabled" onclick="window.location.href='{{ route('board.viewgrantdetails', ['id' => $grant->id]) }}'"></i>View Grant Details</button>
                            @if ($grant->submitted == '1')
                                <button type="button" id="btn-download-pdf" class="btn bg-primary btn-xs mb-1 mt-1 keep-enabled" onclick="openPdfViewer('{{ $grant->grant_pdf_path  }}')">View/Download PDF</button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    No Grant Requests
                @endif

                    </div>
</div>

            <div class="card-body text-center">
                <button type="button" id="btn-back" class="btn btn-primary m-1" onclick="window.location='{{ route('board.newgrantrequest', ['id' => $chDetails->id]) }}'"><i class="fas fa-hand-holding-heart mr-2" ></i>Start a New Grant</button>
                @if ($userTypeId != \App\Enums\UserTypeEnum::OUTGOING && $userTypeId != \App\Enums\UserTypeEnum::DISBANDED)
                    @if ($userTypeId == \App\Enums\UserTypeEnum::COORD)
                        <button type="button" id="btn-back" class="btn btn-primary m-1" onclick="window.location.href='{{ route('board.editprofile', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2" ></i>Back to Profile</button>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-primary m-1"><i class="fas fa-reply mr-2" ></i>Back to Profile</a>
                    @endif
                @endif

            </div>

        <!-- End Modal Popups -->
    </div>
</div>
</div>
@endsection
@section('customscript')
<script>
  /* Disable fields and buttons  */
    $(document).ready(function () {
            var userTypeId = @json($userTypeId);
            var userAdmin = @json($userAdmin);

       if (userTypeId == 1 && userAdmin != 1) {
            $('button, input, select, textarea').not('#btn-back').prop('disabled', true);
        }

        });

</script>
@endsection


