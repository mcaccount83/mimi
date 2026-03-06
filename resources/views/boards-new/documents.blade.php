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
                             <h3>Documents</h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">

                        @if($chDetails->active_status == \App\Enums\ChapterStatusEnum::ZAPPED)
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Disband Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    @if($chDocuments->disband_letter_path != null)
                                        <button class="btn btn-primary bg-gradient btn-sm keep-enabled" type="button" id="disband-letter" onclick="openPdfViewer('{{ $chDocuments->disband_letter_path }}')">Disband Letter</button>
                                    @else
                                        <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No Disband Letter on File</button>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Final Financial Report:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    @if($chDisbanded?->file_financial == 1 && $chEOYDocuments->final_financial_pdf_path != null)
                                        <button class="btn btn-primary bg-gradient btn-sm keep-enabled" type="button" id="final-pdf" onclick="openPdfViewer('{{ $chEOYDocuments->final_financial_pdf_path }}')">Final Financial PDF</button>
                                    @else
                                        <button class="btn btn-primary bg-gradient btn-sm disabled" type="button" disabled>Final PDF Not Available</button>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <label>EIN Letter:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                @if($chDocuments->ein_letter_path != null)
                                    <button class="btn btn-primary bg-gradient btn-sm keep-enabled" type="button" id="ein-letter" onclick="openPdfViewer('{{ $chDocuments->ein_letter_path }}')">EIN Letter from IRS</button>
                                @else
                                    <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No EIN Letter on File</button>
                                @endif
                            </div>
                        </div>

                        @if($chDetails->active_status == \App\Enums\ChapterStatusEnum::ACTIVE)
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Chapter in Good Standing Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button id="GoodStanding" type="button" class="btn btn-primary bg-gradient btn-sm" onclick="window.open('{{ route('pdf.chapteringoodstanding', ['id' => $chDetails->id]) }}', '_blank')">Good Standing Chapter Letter</button><br>
                                </div>
                            </div>

                            @if($chDetails->active_status != \App\Enums\OperatingStatusEnum::OK)
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <label>Probation Letter:</label>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        @if($chDocuments->probation_path != null)
                                            <button class="btn btn-primary bg-gradient btn-sm" type="button" id="probation-file" onclick="openPdfViewer('{{ $chDocuments->probation_path }}')">Probation Letter</button>
                                        @else
                                            <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No Probation Letter on File</button>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($chDetails->active_status == \App\Enums\OperatingStatusEnum::OK && $chDocuments->probation_release_path != null)
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation Release Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button class="btn btn-primary bg-gradient btn-sm" type="button" id="probaton-release-file" onclick="openPdfViewer('{{ $chDocuments->probation_release_path }}')">Probation Release Letter</button>
                                </div>
                            </div>
                            @endif

                            @if($chDocuments->name_change_letter_path != null)
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label>Name Change Letter:</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <button class="btn btn-primary bg-gradient btn-sm" type="button" id="name-change-file" onclick="openPdfViewer('{{ $chDocuments->name_change_letter_path }}')">Name Change Letter</button>
                                    </div>
                                </div>
                            @endif

                            @endif

                            @if (!empty($financialReportPdfs))
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label>Financial Reports:</label>
                                    </div>
                                    <div class="col-sm-6">
                                        @foreach ($financialReportPdfs as $year => $path)
                                            <button type="button" class="btn btn-primary bg-gradient btn-sm me-1 mb-1"
                                                onclick="openPdfViewer('{{ $path }}')">
                                                {{ $year - 1 }}-{{ $year }} Financial Report
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif


                        </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
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
