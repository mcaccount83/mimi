@extends('layouts.board_theme')

@section('content')
<div class="container">
        <div class="row">
            <div class="col-md-12">

            <div class="col-md-12">
                <div class="card">
                    <div class="card bg-primary">
                        <div class="card-body text-center">
                            <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                        </div>
                    </div>
                    <div class="card-body">
                        <h2 class="text-center"> MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                        <h3 class="text-center">{{ $financialReportName }}</h3>
                        <br>
                        @if ($chEOYDocuments->financial_report_received != '1')
                            <p class="text-center">
                                Please complete the report below with finanacial information about your chapter.<br>
                                Reports are due by July 15th.
                            </p>
                        @else
                            <p class="text-center" style="color: red">
                                Your chapter's Financial Report has been Submitted!<br>
                                Please save a copy of the PDF for your records.</span><br>
                            </p>
                            <br>
                            <button type="button" id="btn-download-pdf" class="btn bg-primary" onclick="openPdfViewer('{{ $chEOYDocuments->$yearColumnName  }}')">View/Download PDF</button>
                        @endif
                        </div>
                </div>
                <!-- /.card -->
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
            <div class="card-body">
                <!-- /.card-header -->
            <div class="card-body">
                <div class="row">

                <div class="card-body">
                    <form id="financial_report" name="financial_report" role="form" data-bs-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("board.updatefinancialreport", $chDetails->id) }}'>
                    @csrf

                    @include('boards.financial_accordion', ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userTypeId' => $userTypeId,
                        'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chEOYDocuments' => $chEOYDocuments, 'stateShortName' => $stateShortName, 'chActiveId' => $chActiveId,
                        'lastyear' => $lastYear, 'currentYear' => $currentYear, 'irsFilingName' => $irsFilingName
                    ])

                    </form>

            </div>
            </div>
        </div>
        </div>

                <div class="card-body text-center mt-3">
                    @if ($userTypeId != \App\Enums\UserTypeEnum::OUTGOING && $userTypeId != \App\Enums\UserTypeEnum::DISBANDED)
                        @if ($userTypeId == \App\Enums\UserTypeEnum::COORD)
                            <button type="button" id="btn-back" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board.editprofile', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Profile</button>
                        @else
                            <a href="{{ route('home') }}" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Profile</a>
                        @endif
                    @endif

                    @if($chEOYDocuments->financial_report_received !='1')
                        <button type="button" id="btn-save" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-floppy-fill me-2"></i>Save</button>
                    @endif

                    @if($chEOYDocuments->financial_report_received =='1')
                        <button type="button" id="btn-download-pdf" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chEOYDocuments->$yearColumnName  }}'"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Download PDF</button>
                    @endif
                 </div>

        </div>
        <!-- /.card -->
         </div>
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

       if (userTypeId == 1 && userAdmin != 1) {
            $('button, input, select, textarea').not('#btn-back').prop('disabled', true);
        }

        });

</script>
@endsection


