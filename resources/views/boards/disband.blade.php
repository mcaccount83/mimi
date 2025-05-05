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
                            @php
                                $thisDate = \Illuminate\Support\Carbon::now();
                            @endphp
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
                            <form id="checklist" name="checklist" role="form" data-toggle="validator" method="POST" action='{{ route("board.updatedisbandchecklist", $chDetails->id) }}'>
                                @csrf

                            <div class="row">

                                <div class="col-12 form-row form-group">
                                    <div class="col-md-12 float-left">
                                        @if ($chDocuments->disband_letter_path === null)
                                            <button type="button" id="btn-download-pdf" class="btn bg-primary" disabled><i class="fas fa-file-pdf mr-2"></i>No Disband Letter on File</button>
                                        @else
                                            <button type="button" id="btn-download-pdf" class="btn bg-primary" onclick="openPdfViewer('{{ $chDocuments->disband_letter_path }}')">
                                                <i class="fas fa-file-pdf mr-2"></i>View/Download Disband Letter</button>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 form-row form-group">
                                    <div class="col-md-12 float-left">
                                        <h4>Please acknowledge the following items have been completed.</h4>
                                    </div>
                                </div>

                                <div class="col-12 form-row form-group">
                                    <div class="col-md-12 float-left d-flex">
                                        <label style="margin-right: 20px;">Our final re-registration payment has been sent to International.</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" id="FinalPayment" name="FinalPayment" class="custom-control-input" {{$chDisbanded?->final_payment == '1' ? 'checked' : ''}} >
                                            <label class="custom-control-label" for="FinalPayment">YES</label>
                                        </div>
                                        @if ($chDisbanded?->final_payment != '1')
                                            @if ($userAdmin == '1')
                                                <button type="button" class="btn btn-primary btn-xs ml-3 mb-1" onclick="window.location.href='{{ route('admin.board.editreregpayment', ['chapter_id' => $chDetails->id]) }}'">PAY HERE</button>
                                            @elseif($userType === 'coordinator' && $userAdmin != '1')
                                                <button type="button" class="btn btn-primary btn-xs ml-3 mb-1" onclick="window.location.href='{{ route('viewas.viewchapterreregistration', ['id' => $chDetails->id]) }}'">PAY HERE</button>
                                            @else
                                                <button type="button" class="btn btn-primary btn-xs ml-3 mb-1" onclick="window.location.href='{{ route('board.editreregpayment') }}'">PAY HERE</button>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 form-row form-group">
                                    <div class="col-md-12 float-left d-flex">
                                        <label style="margin-right: 20px;">Our remaining treasury balance has been donated to a registered 501(c)(3) charity.</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" id="DonateFunds" name="DonateFunds" class="custom-control-input" {{$chDisbanded?->donate_funds == '1' ? 'checked' : ''}}>
                                            <label class="custom-control-label" for="DonateFunds">YES</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 form-row form-group">
                                    <div class="col-md-12 float-left d-flex">
                                        <label style="margin-right: 20px;">Our MOMS Club manual has been returned or destroyed.</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" id="DestroyManual" name="DestroyManual" class="custom-control-input" {{$chDisbanded?->destroy_manual == '1' ? 'checked' : ''}}>
                                            <label class="custom-control-label" for="DestroyManual">YES</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 form-row form-group">
                                    <div class="col-md-12 float-left d-flex">
                                        <label style="margin-right: 20px;">All references to our chapter and it's affiliation to MOMS Club have been removed from the internet.</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" id="RemoveOnline" name="RemoveOnline" class="custom-control-input" {{$chDisbanded?->remove_online == '1' ? 'checked' : ''}}>
                                            <label class="custom-control-label" for="RemoveOnline">YES</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 form-row form-group">
                                    <div class="col-md-12 float-left d-flex">
                                        <label style="margin-right: 20px;">Our final 990N has been filed with the IRS, being sure to check the box that the chapter has terminated.</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" id="FileIRS" name="FileIRS" class="custom-control-input" {{$chDisbanded?->file_irs == '1' ? 'checked' : ''}}>
                                            <label class="custom-control-label" for="FileIRS">YES</label>
                                        </div>
                                        @if ($chDisbanded?->file_financial != '1')
                                            @if($thisDate->month >= 7 && $thisDate->month <= 12)
                                                <a href="https://sa.www4.irs.gov/sso/ial1?resumePath=%2Fas%2F5Ad0mGlkzW%2Fresume%2Fas%2Fauthorization.ping&allowInteraction=true&reauth=false&connectionId=SADIPACLIENT&REF=3C53421849B7D5B806E50960DF0AC7530889D9ADE9238D5D3B8B00000069&vnd_pi_requested_resource=https%3A%2F%2Fsa.www4.irs.gov%2Fepostcard%2F&vnd_pi_application_name=EPOSTCARD"
                                                    class="btn btn-primary btn-xs ml-3 mb-1" target="_blank" >FILE HERE</a>
                                            @else
                                                <button id="990NLink" class="btn btn-primary btn-xs ml-3 mb-1 disabled">Not Available Until July 1st</button>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 form-row form-group">
                                    <div class="col-md-12 float-left d-flex">
                                        <label style="margin-right: 20px;">Our final Financial Report has been submitted (below).</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" id="FileFinancial" name="FileFinancial" class="custom-control-input" {{$chDisbanded?->file_financial == '1' ? 'checked' : ''}}>
                                            <label class="custom-control-label" for="FileFinancial">YES</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body text-center">
                                    <button type="submit" id="btn-save" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>Save Checklist
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
     {{-- <form id="financial_report" name="financial_report" role="form" data-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("board.updatedisbandreport", $chDetails->id) }}'> --}}
        <form id="financial_report" name="financial_report" role="form" data-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("board.updatefinancialreport", $chDetails->id) }}'>
        @csrf
                    <div class="row">

                                <div class="col-12 form-row form-group">
                                    <div class="col-md-12 float-left">
                                        @if ($chDocuments->final_financial_pdf_path === null)
                                            <h4>Financial Report</h4>
                                        @else
                                            <h4>Financial Report has been submitted.</h4>
                                            <button type="button" id="btn-download-pdf" class="btn bg-primary" onclick="openPdfViewer('{{ $chDocuments->final_financial_pdf_path }}')">
                                                <i class="fas fa-file-pdf mr-2"></i>View/Download Financial Report
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                @if ($chDocuments->final_financial_pdf_path === null)
                                    @if ($chFinancialReport)
                                        @include('boards.financial_accordion', [
                                            'chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userType' => $userType, 'userName' => $userName,
                                            'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName, 'chIsActive' => $chIsActive
                                        ])
                                    @else
                                        <div class="col-md-12 float-left">
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

@stack('scripts')
