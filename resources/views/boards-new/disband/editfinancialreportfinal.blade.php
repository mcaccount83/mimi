@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Chapter Profile')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

            <form id="financial_report" name="financial_report" role="form" data-bs-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("board.updatedisbandreport", $chDetails->id) }}'>
        @csrf

<div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                             @if ($chDisbanded?->file_financial == null)
                                <h3>Final Financial Report</h3>
                            @else
                                <h3>Financial Report has been submitted.</h3>
                                    @if ($chEOYDocuments->final_financial_pdf_path == null)
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm mb-2" disabled>
                                    <i class="bi bi-file-earmark-pdf-fill btn-smme-2"></i>Financial Report PDF Not Available
                                </button>
                                @else
                                <button type="button" id="btn-download-pdf" class="btn btn-primary bg-gradient btn-sm mb-2" onclick="openPdfViewer('{{ $chEOYDocuments->final_financial_pdf_path }}')">
                                    <i class="bi bi-file-earmark-pdf-fill me-2"></i>View/Download Financial Report
                                </button>
                            @endif
                            @endif
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                         <div class="row">
                            <div class="col-md-12 mb-3">

                            @if ($chEOYDocuments->final_report_received != '1')
                                <label class="me-2">Report Status:</label><span class="badge bg-danger fs-7">Due Now</span><br><br>
                                Please complete the report below with final finanacial information about your chapter.<br>
                                Your final report is due upon chapter termination.<br>
                            @elseif ($chEOYDocuments->final_report_received == '1')
                                <label class="me-2">Report Status:</label><span class="badge bg-success fs-7">Submitted</span><br><br>
                                    Your chapter's final Financial Report Review has been submitted.<br>
                                    Please save a copy of the PDF for your records.</span><br>
                                <br>
                                <button type="button" id="btn-download-pdf" class="btn btn-primary bg-gradient" onclick="openPdfViewer('{{ $chEOYDocuments->final_financial_pdf_path }}')">Financial Report PDF</button>
                            @endif
                        </div>
                </div>
                <br>

                {{-- Financial Report Form --}}
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header"><strong>Report Details</strong>
                                </div>
                                <div class="card-body">

                                @if ($chDisbanded?->file_financial == null)
                                    @if ($chFinancialReport)
                                        @include('boards-new.partials.financial_accordion', [
                                            'chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userTypeId' => $userTypeId, 'userName' => $userName,
                                            'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName, 'chActiveId' => $chActiveId
                                        ])
                                    @else
                                        <div class="col-md-12 float-start">
                                            <h4>No Financial Report Available.</h4>
                                        </div>
                                    @endif
                                @endif
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
