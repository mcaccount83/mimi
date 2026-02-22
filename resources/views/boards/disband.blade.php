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

                            <div class="col-md-12"><br><br></div>
                            <h2 class="text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                            <h4 class="text-center">Disbanding Checklist</h4>
                        </div>
                        <div class="col-md-12" style="color: red;"><center>Our records indicate that your chpater has disbanded.</center></div>
                        <div class="col-md-12" style="color: red;"><center>If you believe this information is incorrect, then please contact your Primary Coordinator immediately</center></div>
                        <div class="col-md-12"><br></div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-body">

                            {{-- Checklist Form --}}
                            <form id="checklist" name="checklist" role="form" data-bs-toggle="validator" method="POST" action='{{ route("board.updatedisbandchecklist", $chDetails->id) }}'>
                                @csrf

                            <div class="row">

                                <div class="col-12 form-row mb-3">
                                    <div class="col-md-12 float-start">
                                        @if ($chDocuments->disband_letter_path == null)
                                            <button type="button" id="btn-download-pdf disabled" class="btn bg-primary"><i class="bi bi-file-earmark-pdf-fill me-2" disabled></i>No Disband Letter on File</button>
                                        @else
                                            <button type="button" id="btn-download-pdf" class="btn bg-primary" onclick="openPdfViewer('{{ $chDocuments->disband_letter_path }}')">
                                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>View/Download Disband Letter</button>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 form-row mb-3">
                                    <div class="col-md-12 float-start">
                                        <h4>Please acknowledge the following items have been completed.</h4>
                                    </div>
                                </div>

                                <div class="col-12 form-row mb-3">
                                    <div class="col-md-12 float-start d-flex">
                                        <label style="margin-right: 20px;">Our final re-registration payment has been sent to International.</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="FinalPayment" name="FinalPayment" class="form-check-input" {{$chDisbanded?->final_payment == '1' ? 'checked' : ''}} >
                                            <label class="form-check-label" for="FinalPayment">YES</label>
                                        </div>
                                        @if ($chDisbanded?->final_payment != '1')
                                                <button type="button" class="btn btn-primary bg-gradient btn-xs ms-3 mb-1" onclick="window.location.href='{{ route('board.editreregpayment', ['id' => $chDetails->id]) }}'">PAY HERE</button>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 form-row mb-3">
                                    <div class="col-md-12 float-start d-flex">
                                        <label style="margin-right: 20px;">Our remaining treasury balance has been donated to a registered 501(c)(3) charity.</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="DonateFunds" name="DonateFunds" class="form-check-input" {{$chDisbanded?->donate_funds == '1' ? 'checked' : ''}}>
                                            <label class="form-check-label" for="DonateFunds">YES</label>
                                        </div>
                                        @if ($chDisbanded?->donate_funds != '1')
                                                <button type="button" class="btn btn-primary bg-gradient btn-xs ms-3 mb-1" onclick="window.location.href='{{ route('board.editdonate', ['id' => $chDetails->id]) }}'">DONATE HERE</button>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 form-row mb-3">
                                    <div class="col-md-12 float-start d-flex">
                                        <label style="margin-right: 20px;">Our MOMS Club manual has been returned or destroyed.</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="DestroyManual" name="DestroyManual" class="form-check-input" {{$chDisbanded?->destroy_manual == '1' ? 'checked' : ''}}>
                                            <label class="form-check-label" for="DestroyManual">YES</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 form-row mb-3">
                                    <div class="col-md-12 float-start d-flex">
                                        <label style="margin-right: 20px;">All references to our chapter and it's affiliation to MOMS Club have been removed from the internet.</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="RemoveOnline" name="RemoveOnline" class="form-check-input" {{$chDisbanded?->remove_online == '1' ? 'checked' : ''}}>
                                            <label class="form-check-label" for="RemoveOnline">YES</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 form-row mb-3">
                                    <div class="col-md-12 float-start d-flex">
                                        <label style="margin-right: 20px;">Our final 990N has been filed with the IRS, being sure to check the box that the chapter has terminated.</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="FileIRS" name="FileIRS" class="form-check-input" {{$chDisbanded?->file_irs == '1' ? 'checked' : ''}}>
                                            <label class="form-check-label" for="FileIRS">YES</label>
                                        </div>
                                        @if ($chDisbanded?->file_financial != '1')
                                            @if($displayEINInstructionsLIVE == true)
                                                <a href="https://sa.www4.irs.gov/sso/ial1?resumePath=%2Fas%2F5Ad0mGlkzW%2Fresume%2Fas%2Fauthorization.ping&allowInteraction=true&reauth=false&connectionId=SADIPACLIENT&REF=3C53421849B7D5B806E50960DF0AC7530889D9ADE9238D5D3B8B00000069&vnd_pi_requested_resource=https%3A%2F%2Fsa.www4.irs.gov%2Fepostcard%2F&vnd_pi_application_name=EPOSTCARD"
                                                    class="btn btn-primary bg-gradient btn-xs ms-3 mb-1" target="_blank" >FILE HERE</a>
                                            @else
                                                <button id="990NLink" class="btn btn-primary bg-gradient btn-xs ms-3 mb-1 disabled" disabled>Not Available Until July 1st</button>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                               <div class="col-12 form-row mb-3">
                                    <div class="col-md-12 float-start d-flex">
                                        <label style="margin-right: 20px;">Our final Financial Report has been submitted (below).</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                id="FileFinancial"
                                                name="FileFinancial"
                                                class="form-check-input"
                                                {{ $chDisbanded?->file_financial == '1' ? 'checked' : '' }}
                                                {{ $chDisbanded?->file_financial == '1' ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="FileFinancial">YES</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body text-center mt-3">
                                    <button type="submit" id="btn-save" class="btn btn-primary bg-gradient mb-2">
                                        <i class="bi bi-floppy-fill me-2"></i>Save Checklist
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                {{-- Separate div and card for Financial Report --}}
<div class="col-md-12">
    <div class="card card-primary card-outline">
        <div class="card-body">


     {{-- Financial Report Form --}}
        <form id="financial_report" name="financial_report" role="form" data-bs-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("board.updatedisbandreport", $chDetails->id) }}'>
        @csrf
                    <div class="row">

                                <div class="col-12 form-row mb-3">
                                    <div class="col-md-12 float-start">
                                        @if ($chDisbanded?->file_financial == null)
                                            <h4>Financial Report</h4>
                                        @else
                                            <h4>Financial Report has been submitted.</h4>
                                             @if ($chEOYDocuments->final_financial_pdf_path == null)
                                             <button type="button" class="btn bg-primary" disabled>
                                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>Financial PDF Not Available
                                            </button>
                                            @else
                                            <button type="button" id="btn-download-pdf" class="btn bg-primary" onclick="openPdfViewer('{{ $chEOYDocuments->final_financial_pdf_path }}')">
                                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>View/Download Financial Report
                                            </button>
                                        @endif
                                        @endif
                                    </div>
                                </div>

                                @if ($chDisbanded?->file_financial == null)
                                    @if ($chFinancialReport)
                                        @include('boards.financial_accordion', [
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
            </form>

        </div>
    </div>
</div>
@endsection
@section('customscript')
<script>

</script>
@endsection
