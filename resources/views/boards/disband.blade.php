@extends('layouts.board_theme')

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" action='{{ route("board.updatedisbandchecklist", $chDetails->id) }}' autocomplete="off">
                        @csrf

                        <div class="col-md-12">
                            <div class="card card-widget widget-user">
                                <div class="widget-user-header bg-primary">
                                    <div class="widget-user-image">
                                        <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}theme/dist/img/logo.png" alt="MC" style="width: 115px; height: 115px;">
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
                                <div class="col-md-12"><br></div>
                                <div class="col-md-12" style="color: red;"><center>Our records indicate that your chpater has disbanded.</center></div>
                                <div class="col-md-12" style="color: red;"><center>If you believe this information is incorrect, then please contact your Primary Coordinator immediately</center></div>

                                <div class="col-md-12"><br></div>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="card card-primary card-outline">
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-12 form-row form-group">
                                        <div class="col-md-12 float-left">
                                        <h4>Please acknowledge the following items have been completed.</h4>
                                    </div>
                                </div>

                                <div class="col-12 form-row form-group">
                                    <div class="col-md-12 float-left d-flex">
                                        <label style="margin-right: 20px;">Our final re-registration payment has been sent International.</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" id="FinalPayment" name="FinalPayment" class="custom-control-input" {{$chDisbanded?->final_payment == '1' ? 'checked' : ''}} >
                                            <label class="custom-control-label" for="FinalPayment">YES</label>
                                        </div>
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
                                        <label style="margin-right: 20px;">All references to our chapter and it's affiliation to MOMS Club has been removed from the internet.</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" id="RemoveOnline" name="RemoveOnline" class="custom-control-input" {{$chDisbanded?->remove_online == '1' ? 'checked' : ''}}>
                                            <label class="custom-control-label" for="RemoveOnline">YES</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 form-row form-group">
                                    <div class="col-md-12 float-left d-flex">
                                        <label style="margin-right: 20px;">Our final 990N has been filed with the IRS.</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" id="FileIRS" name="FileIRS" class="custom-control-input" {{$chDisbanded?->file_irs == '1' ? 'checked' : ''}}>
                                            <label class="custom-control-label" for="FileIRS">YES</label>
                                        </div>
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



                    <div class="col-12"><br></div>
                        <hr>
                    <div class="col-12 form-row form-group">
                        <div class="col-md-12 float-left">
                                @if ($chDocuments->financial_report_received != '1')
                                    <h4>
                                    Financial Report
                                @else
                                    <h4>
                                        Financial Report has been submitted.
                                    </h4>
                                    <button type="button" id="btn-download-pdf" class="btn bg-primary" onclick="openPdfViewer('{{ $chDocuments->financial_pdf_path }}')">
                                        <i class="fas fa-file-pdf mr-2"></i>View/Download Financial Report</button>
                                @endif
                        </div>
                    </div>

                    @if ($chDocuments->financial_report_received != '1')
                        @include('partials.financial_accordion', ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userType' => $userType,
                            'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
                            ])
                    @endif

                </form>


                <div class="card-body text-center">
                    <button type="submit" id="btn-save" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Save Checklist</button>
                </div>



                    </div>
                </div>

            </div>
        </div>


        </div>
    </div>
</div>

@endsection
@section('customscript')
<script>

</script>

@stack('scripts')
