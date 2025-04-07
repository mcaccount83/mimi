@extends('layouts.board_theme')

@section('content')

<div class="container" id="test">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
         <!-- Widget: user widget style 1 -->
         <div class="card card-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-primary">
                <div class="widget-user-image">
                    <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                  </div>
                        </div>
                        <div class="card-body">
                    @php
                        $thisDate = \Carbon\Carbon::now();
                    @endphp
                    <div class="col-md-12"><br><br></div>
                        <h2 class="text-center"> MOMS Club of {{ $chDetails->name }}, {{ $stateShortName }} </h2>
                        <h4 class="text-center"> <?php echo date('Y')-1 .'-'.date('Y');?> Financial Report</h4>
                    <div class="col-md-12"><br></div>
                    <h4 class="text-center">
                    @if ($chDocuments->financial_report_received != '1')
                        Please complete the report below with finanacial information about your chapter.<br>
                        Reports are due by July 15th.
                    @else
                        <span style="color: #dc3545">Your chapter's Financial Report has been Submitted!<br>
                        Please save a copy of the PDF for your records.</span><br>
                        <br>
                        <button type="button" id="btn-download-pdf" class="btn bg-primary" onclick="openPdfViewer('{{ $chDocuments->financial_pdf_path }}')">View/Download PDF</button>
                        {{-- <button type="button" id="btn-download-pdf" class="btn btn-primary" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->financial_pdf_path }}'"><i class="fas fa-file-pdf"></i>&nbsp; Download PDF</button> --}}
                    @endif
                    </h4>
                        </div>
                    </div>
                </div>
            </div>

        <div class="container-fluid">
                @auth
                    <form id="financial_report" name="financial_report" role="form" data-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("board.updatefinancialreport", $chDetails->id) }}'>
                    @csrf

            <div class="row">

                @include('partials.financial_accordion', ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userType' => $userType,
                'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName, 'chIsActive' => $chIsActive
               ])

            </form>
            @else
                <p>Your session has expired. Please <a href="{{ url('/login') }}">log in</a> again.</p>
            @endif

            <div class="card-body text-center">
                @if($userType === 'board')
                    <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
                @endif
                @if($userType === 'coordinator')
                    <a href="{{ route('viewas.viewchapterpresident', $chFinancialReport['chapter_id']) }}" class="btn btn-primary" id="btn-back"><i class="fas fa-reply"></i>&nbsp; Back</a>
                @endif
                @if($chDocuments->financial_report_received !='1')
                    <button type="button" id="btn-save" class="btn btn-primary"><i class="fas fa-save"></i>&nbsp; Save</button>
                @endif
                {{-- @if($chDocuments->financial_report_received !='1' && $thisDate->month >= 3 && $thisDate->month <= 12)
                    <button type="button" id="final-submit" class="btn btn-success"><i class="fas fa-share-square" ></i>&nbsp; Submit</button>
                @endif --}}
                @if($chDocuments->financial_report_received =='1')
                    <button type="button" id="btn-download-pdf" class="btn btn-primary" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments['financial_pdf_path'] }}'"><i class="fas fa-file-pdf"></i>&nbsp; Download PDF</button>
                @endif
            </div>

        <!-- End Modal Popups -->
    </div>
</div>
</div>

@endsection
@section('customscript')
<script>

</script>
@stack('scripts')


