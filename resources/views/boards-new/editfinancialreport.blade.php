@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Chapter Profile')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

            <form id="financial_report" name="financial_report" role="form" data-bs-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("board-new.updatefinancialreport", $chDetails->id) }}'>
                    @csrf

            <div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                             <h3>{{ $financialReportName }}
                            @if ($ITCondition && !$displayTESTING && !$displayLIVE) *ADMIN*@endif
                            @if ($eoyTestCondition && $displayTESTING) *TESTING*@endif
                            </h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                         <div class="row">
                            <div class="col-md-12 mb-3">

                            @if ($chEOYDocuments->financial_report_received != '1')
                                <label class="me-2">Report Status:</label><span class="badge bg-danger fs-7">Due July 15th</span><br><br>
                                Please complete the report below with finanacial information about your chapter.<br>
                                Reports are due by July 15th.<br>
                            @elseif ($chEOYDocuments->financial_report_received == '1' && $chEOYDocuments->financial_review_complete != '1')
                                <label class="me-2">Report Status:</label><span class="badge bg-warning text-dark fs-7">Submitted</span><br><br>
                                    Your chapter's Financial Report has been Submitted. A coordinator will review your report and let you know if there are any questions.<br>
                                    Please save a copy of the PDF for your records.</span><br>
                                <br>
                                <button type="button" id="btn-download-pdf" class="btn btn-primary bg-gradient" onclick="openPdfViewer('{{ $chEOYDocuments->$yearColumnName }}')">{{$financialPDFName}}</button>
                            @elseif ($chEOYDocuments->financial_report_received == '1' && $chEOYDocuments->financial_review_complete == '1')
                                <label class="me-2">Report Status:</label><span class="badge bg-success fs-7">Reviewed</span><br><br>
                                    Your chapter's Financial Report Review has been completed.<br>
                                    Please save a copy of the PDF for your records.</span><br>
                                <br>
                                <button type="button" id="btn-download-pdf" class="btn btn-primary bg-gradient" onclick="openPdfViewer('{{ $chEOYDocuments->$yearColumnName }}')">{{$financialPDFName}}</button>
                            @endif
                        </div>
                </div>
                <br>

                {{-- Start of Financial Report --}}
            <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header"><strong>Report Details</strong>
                                </div>
                                <div class="card-body">
                    When you have filled in all the answers, submit the report and save a copy in your chapter's permanent files.
                    The International MOMS Club does not keep copies of your reports long term. You need to be sure your chapter has a copy and keeps it for the life of your chapter,
                    as this would be the information you would need if the IRS were to do an audit. The Financial Report and all required additional documents are due no later than July 15th.
                    <br><br>

                    @include('boards-new.partials.financial_accordion', ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userTypeId' => $userTypeId,
                        'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chEOYDocuments' => $chEOYDocuments, 'stateShortName' => $stateShortName, 'chActiveId' => $chActiveId,
                        'lastyear' => $lastYear, 'currentYear' => $currentYear, 'irsFilingName' => $irsFilingName
                    ])

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


