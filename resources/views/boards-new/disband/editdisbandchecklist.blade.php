@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Disband Checklist')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

            <form id="checklist" name="checklist" role="form" data-bs-toggle="validator" method="POST" action='{{ route("board-new.updatedisbandchecklist", $chDetails->id) }}'>
                @csrf

                 <div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                             <h3>Disbanding Checklist</h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">

                        <label class="me-2">Chapter Status:</label>
                         @if ($chDetails->active_status == 1 )
                            <span class="badge bg-success fs-7">Chapter is ACTIVE</span>
                            {{ $chapterStatus }}
                        @elseif ($chDetails->active_status == 2)
                            <span class="badge bg-warning text-dark fs-7">Chapter is PENDING</span>
                        @elseif ($chDetails->active_status == 3)
                            <span class="badge bg-warning text-dark fs-7">Chapter was NOT APPROVED</span><br>
                        @elseif ($chDetails->active_status == 0)
                            <span class="badge bg-danger fs-7">Chapter is NOT ACTIVE</span><br>
                        @endif
                        <br>
                        Our records indicate that your chapter has disbanded.<br>
                        If you believe this information is incorrect, then please contact your Primary Coordinator immediately<br>
                        <br>
                        <div class="col-md-12 mb-1">
                                @include('boards-new.partials.coordinatorlist')
                        </div>
                        <br>
                        <div class="col-md-12 mb-3">
                                    <div class="col-md-12 float-start">
                                        @if ($chDocuments->disband_letter_path == null)
                                            <button type="button" id="btn-download-pdf" class="btn btn-primary bg-gradient btn-sm mb-2" disabled><i class="bi bi-file-earmark-pdf-fill me-2" disabled></i>No Disband Letter on File</button>
                                        @else
                                            <button type="button" id="btn-download-pdf" class="btn btn-primary bg-gradient btn-sm mb-2 keep-enabled" onclick="openPdfViewer('{{ $chDocuments->disband_letter_path }}')">
                                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>View/Download Disband Letter</button>
                                        @endif
                                        <br>
                                         @if ($chEOYDocuments->final_financial_pdf_path == null)
                                                <button type="button" class="btn btn-primary bg-gradient btn-sm mb-2" disabled><i class="bi bi-file-earmark-pdf-fill me-2"></i>Financial Report PDF Not Available</button>
                                            @else
                                            <button type="button" id="btn-download-pdf" class="btn btn-primary bg-gradient btn-sm mb-2 keep-enabled" onclick="openPdfViewer('{{ $chEOYDocuments->final_financial_pdf_path }}')">
                                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>View/Download Financial Report
                                            </button>
                                        @endif
                                    </div>
                                </div>
                    </div>
                    </div>
                <br>

                {{-- Start of Disband Checklist --}}
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header"><strong>Checklist Details</strong>
                                </div>
                                <div class="card-body">

                                <div class="col-md-12 mb-3">
                                    <div class="col-md-12 float-start mb-2">
                                        <h4>Please acknowledge the following items have been completed.</h4>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="col-md-12 float-start d-flex mb-2">
                                        <label style="margin-right: 20px;">Our final re-registration payment has been sent to International.</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="FinalPayment" name="FinalPayment" class="form-check-input" {{$chDisbanded?->final_payment == '1' ? 'checked' : ''}} >
                                            <label class="form-check-label" for="FinalPayment">YES</label>
                                        </div>
                                        {{-- @if ($chDisbanded?->final_payment != '1')
                                                <button type="button" class="btn btn-primary bg-gradient btn-xs ms-3 mb-1" onclick="window.location.href='{{ route('board-new.editreregpayment', ['id' => $chDetails->id]) }}'">PAY HERE</button>
                                        @endif --}}
                                        <br>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="col-md-12 float-start d-flex mb-2">
                                        <label style="margin-right: 20px;">Our remaining treasury balance has been donated to a registered 501(c)(3) charity.</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="DonateFunds" name="DonateFunds" class="form-check-input" {{$chDisbanded?->donate_funds == '1' ? 'checked' : ''}}>
                                            <label class="form-check-label" for="DonateFunds">YES</label>
                                        </div>
                                        {{-- @if ($chDisbanded?->donate_funds != '1')
                                                <button type="button" class="btn btn-primary bg-gradient btn-xs ms-3 mb-1" onclick="window.location.href='{{ route('board-new.editdonate', ['id' => $chDetails->id]) }}'">DONATE HERE</button>
                                        @endif --}}
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="col-md-12 float-start d-flex mb-2">
                                        <label style="margin-right: 20px;">Our MOMS Club manual has been returned or destroyed.</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="DestroyManual" name="DestroyManual" class="form-check-input" {{$chDisbanded?->destroy_manual == '1' ? 'checked' : ''}}>
                                            <label class="form-check-label" for="DestroyManual">YES</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="col-md-12 float-start d-flex mb-2">
                                        <label style="margin-right: 20px;">All references to our chapter and it's affiliation to MOMS Club have been removed from the internet.</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="RemoveOnline" name="RemoveOnline" class="form-check-input" {{$chDisbanded?->remove_online == '1' ? 'checked' : ''}}>
                                            <label class="form-check-label" for="RemoveOnline">YES</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="col-md-12 float-start d-flex mb-2">
                                        <label style="margin-right: 20px;">Our final 990N has been filed with the IRS, being sure to check the box that the chapter has terminated.</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="FileIRS" name="FileIRS" class="form-check-input" {{$chDisbanded?->file_irs == '1' ? 'checked' : ''}}>
                                            <label class="form-check-label" for="FileIRS">YES</label>
                                        </div>
                                        {{-- @if ($chDisbanded?->file_financial != '1')
                                            @if($displayEINInstructionsLIVE == true)
                                                <a href="https://sa.www4.irs.gov/sso/ial1?resumePath=%2Fas%2F5Ad0mGlkzW%2Fresume%2Fas%2Fauthorization.ping&allowInteraction=true&reauth=false&connectionId=SADIPACLIENT&REF=3C53421849B7D5B806E50960DF0AC7530889D9ADE9238D5D3B8B00000069&vnd_pi_requested_resource=https%3A%2F%2Fsa.www4.irs.gov%2Fepostcard%2F&vnd_pi_application_name=EPOSTCARD"
                                                    class="btn btn-primary bg-gradient btn-xs ms-3 mb-1" target="_blank" >FILE HERE</a>
                                            @endif
                                        @endif --}}
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="col-md-12 float-start d-flex mb-2">
                                        <label style="margin-right: 20px;">Our final Financial Report has been submitted.</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                id="FileFinancial"
                                                name="FileFinancial"
                                                class="form-check-input"
                                                {{ $chDisbanded?->file_financial == '1' ? 'checked' : '' }}
                                                {{ $chDisbanded?->file_financial == '1' ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="FileFinancial">YES</label>
                                        </div>
                                          {{-- @if ($chDisbanded?->file_financial != '1')
                                                <button type="button" class="btn btn-primary bg-gradient btn-xs ms-3 mb-1" onclick="window.location.href='{{ route('board-new.editfinancialreportfinal', ['id' => $chDetails->id]) }}'">FILE HERE</button>
                                        @endif --}}
                                    </div>
                                </div>
                                </div>

                                <div class="card-body text-center mt-3">
                                       @if ($chDisbanded?->final_payment != '1')
                                                <button type="button" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('board-new.editreregpayment', ['id' => $chDetails->id]) }}'"><i class="bi bi-credit-card-fill me-2"></i>Pay ReRegistration</button>
                                        @endif
                                          @if ($chDisbanded?->donate_funds != '1')
                                                <button type="button" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('board-new.editdonate', ['id' => $chDetails->id]) }}'"><i class="bi bi-currency-dollar me-2"></i>Donate Chapter Funds</button>
                                        @endif
                                        @if ($chDisbanded?->file_financial != '1')
                                                <button type="button" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('board-new.editfinancialreportfinal', ['id' => $chDetails->id]) }}'"><i class="bi bi-file-earmark-bar-graph me-2"></i>Submit Financial Report</button>
                                        @endif
                                        @if ($chDisbanded?->file_financial != '1')
                                            @if($displayEINInstructionsLIVE == true)
                                                <a href="https://sa.www4.irs.gov/sso/ial1?resumePath=%2Fas%2F5Ad0mGlkzW%2Fresume%2Fas%2Fauthorization.ping&allowInteraction=true&reauth=false&connectionId=SADIPACLIENT&REF=3C53421849B7D5B806E50960DF0AC7530889D9ADE9238D5D3B8B00000069&vnd_pi_requested_resource=https%3A%2F%2Fsa.www4.irs.gov%2Fepostcard%2F&vnd_pi_application_name=EPOSTCARD"
                                                    class="btn btn-primary bg-gradient mb-2" target="_blank" >File IRS 990N</a>
                                            @endif
                                        @endif
                                <br>
                                <button type="submit" id="btn-save" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-floppy-fill me-2"></i>Save Checklist</button>
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
@if($userTypeId == \App\Enums\UserTypeEnum::COORD)
    @php $disableMode = 'disable-all'; @endphp
    @include('layouts.scripts.disablefields')
@endif
@endsection
