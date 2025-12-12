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
                        <h4 class="text-center">{{ $financialReportName }}</h4>
                    <div class="col-md-12"><br></div>
                    @if ($chEOYDocuments->financial_report_received != '1')
                        <p class="description text-center">
                            Please complete the report below with finanacial information about your chapter.<br>
                            Reports are due by July 15th.
                        </p>
                    @else
                         <p class="description text-center">
                            <span style="color: #dc3545">Your chapter's Financial Report has been Submitted!<br>
                            Please save a copy of the PDF for your records.</span><br>
                        </p>
                        <br>
                        <button type="button" id="btn-download-pdf" class="btn bg-primary" onclick="openPdfViewer('{{ $chEOYDocuments->$yearColumnName  }}')">View/Download PDF</button>
                    @endif
                        </div>
                    </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>

        <div class="container-fluid">
                    <form id="financial_report" name="financial_report" role="form" data-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("board.updatefinancialreport", $chDetails->id) }}'>
                    @csrf

            <div class="row">

                @include('boards.financial_accordion', ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userType' => $userType,
                'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chEOYDocuments' => $chEOYDocuments, 'stateShortName' => $stateShortName, 'chActiveId' => $chActiveId,
                'lastyear' => $lastYear, 'currentYear' => $currentYear, 'irsFilingName' => $irsFilingName
               ])

            </form>

            <div class="card-body text-center">

                @if ($userType != 'outgoing' && $userType != 'disbanded')
                    @if ($userType == 'coordinator')
                        <button type="button" id="btn-back" class="btn btn-primary" onclick="window.location.href='{{ route('board.editprofile', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2" ></i>Back to Profile</button>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-reply mr-2" ></i>Back to Profile</a>
                    @endif
                @endif

                @if($chEOYDocuments->financial_report_received !='1')
                    <button type="button" id="btn-save" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Save</button>
                @endif

                @if($chEOYDocuments->financial_report_received =='1')
                    <button type="button" id="btn-download-pdf" class="btn btn-primary" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chEOYDocuments->$yearColumnName  }}'"><i class="fas fa-file-pdf"></i>&nbsp; Download PDF</button>
                @endif
            </div>

        <!-- End Modal Popups -->
    </div>
</div>
@endsection
@section('customscript')
<script>
  /* Disable fields and buttons  */
    $(document).ready(function () {
            var userType = @json($userType);
            var userAdmin = @json($userAdmin);

       if (userType == 'coordinator' && userAdmin != 1) {
            $('button, input, select, textarea').not('#btn-back').prop('disabled', true);
        }

        });

</script>
@endsection


