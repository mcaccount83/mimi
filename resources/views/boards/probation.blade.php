@extends('layouts.board_theme')

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" action='{{ route("board.updateprobation", $chDetails->id) }}' autocomplete="off">
                        @csrf

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
                                            <h4 class="text-center">Quarterly Financial Submission</h4>

                                    <p class="description text-center">
                                        Since your chapter in on probation for Excess Member Benefit Expenses, you will need to complete a quarterly financial report submission.
                                        <br>This should be completed/submitted each quarter to satisfy the terms of your probationary status.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>

    <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                         <!-- /.card-header -->
                         <p >
                            Enter Dues Income and Member Benefit Expenses for each Quarter Below.
                        </p>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4 float-left nopadding">
                                <div class="form-group">
                                    <label for="Q1Dues">Q1 Dues Income</label>
                                    <div class="form-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        <input type="text" class="form-control " name="q1_dues" id="q1_dues" value="{{ $chDetails->probationSubmit?->q1_dues }}"
                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 float-left nopadding">
                                <div class="form-group">
                                    <label for="Q1Benefit">Q1 Member Benefit Expenses</label>
                                    <div class="form-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        <input type="text" class="form-control " name="q1_benefit" id="q1_benefit" value="{{ $chDetails->probationSubmit?->q1_benefit }}"
                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 float-left nopadding">
                            </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4 float-left nopadding">
                                    <div class="form-group">
                                        <label for="Q1Dues">Q2 Dues Income</label>
                                        <div class="form-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            <input type="text" class="form-control " name="q2_dues" id="q2_dues" value="{{ $chDetails->probationSubmit?->q2_dues }}"
                                                data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 float-left nopadding">
                                    <div class="form-group">
                                        <label for="Q1Benefit">Q2 Member Benefit Expenses</label>
                                        <div class="form-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            <input type="text" class="form-control " name="q2_benefit" id="q2_benefit" value="{{ $chDetails->probationSubmit?->q2_benefit }}"
                                                data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 float-left nopadding">
                                </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4 float-left nopadding">
                                        <div class="form-group">
                                            <label for="Q1Dues">Q3 Dues Income</label>
                                            <div class="form-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                <input type="text" class="form-control " name="q3_dues" id="q3_dues" value="{{ $chDetails->probationSubmit?->q3_dues }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 float-left nopadding">
                                        <div class="form-group">
                                            <label for="Q1Benefit">Q3 Member Benefit Expenses</label>
                                            <div class="form-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                <input type="text" class="form-control " name="q3_benefit" id="q3_benefit" value="{{ $chDetails->probationSubmit?->q3_benefit }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 float-left nopadding">
                                    </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-4 float-left nopadding">
                                            <div class="form-group">
                                                <label for="Q1Dues">Q4 Dues Income</label>
                                                <div class="form-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    <input type="text" class="form-control " name="q4_dues" id="q4_dues" value="{{ $chDetails->probationSubmit?->q4_dues }}"
                                                        data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 float-left nopadding">
                                            <div class="form-group">
                                                <label for="Q1Benefit">Q4 Member Benefit Expenses</label>
                                                <div class="form-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    <input type="text" class="form-control " name="q4_benefit" id="q4_benefit" value="{{ $chDetails->probationSubmit?->q4_benefit }}"
                                                        data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 float-left nopadding">
                                        </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-4 float-left nopadding">
                                                <div class="form-group">
                                                    <label for="Q1Dues">YTD Dues Income</label>
                                                    <div class="form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        <input type="text" class="form-control " name="TotalDues" id="TotalDues" readonly
                                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 float-left nopadding">
                                                <div class="form-group">
                                                    <label for="Q1Benefit">YTD Benefit Expenses</label>
                                                    <div class="form-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        <input type="text" class="form-control " name="TotalBenefit" id="TotalBenefit" readonly
                                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 float-left nopadding">
                                                <div class="form-group">
                                                    <label for="Q1Percentage">YTD Benefit %</label>
                                                    <div class="form-group">
                                                        <div class="input-group-prepend">
                                                           <input type="text" class="form-control" name="TotalPercentage" id="TotalPercentage" value="0%" readonly>
                                                        </div>
                                                    </div>
                                                </div>
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

            <div class="card-body text-center">
                <button id="Save" type="submit" class="btn btn-primary"><i class="fas fa-share" ></i>&nbsp; Submit</button>

            @if ($userType == 'coordinator')
                <button type="button" id="btn-back" class="btn btn-primary" onclick="window.location.href='{{ route('board.editprofile', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2" ></i>Back to Profile</button>
            @else
                <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-reply" ></i>&nbsp; Back to Profile</a>
            @endif
                <button id="logout-btn" class="btn btn-primary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-undo" ></i>&nbsp; Logout</button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>

            </div>
            <!-- /.container- -->
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

    document.querySelector('form').addEventListener('submit', function(){
        document.querySelector('button[type="submit"]').setAttribute('disabled', 'disabled');
    });

    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        value = (value / 100).toFixed(2);
        input.value = '$' + value;
    }


document.addEventListener("DOMContentLoaded", function() {
    // Set up Inputmask
    Inputmask().mask(document.querySelectorAll('[data-inputmask]'));

    // Correctly get the input elements for all quarters
    var q1DuesInput = document.getElementById("q1_dues");
    var q1BenefitInput = document.getElementById("q1_benefit");
    var q2DuesInput = document.getElementById("q2_dues");
    var q2BenefitInput = document.getElementById("q2_benefit");
    var q3DuesInput = document.getElementById("q3_dues");
    var q3BenefitInput = document.getElementById("q3_benefit");
    var q4DuesInput = document.getElementById("q4_dues");
    var q4BenefitInput = document.getElementById("q4_benefit");

    // Add event listeners for Q1
    if (q1DuesInput) {
        q1DuesInput.addEventListener("input", ChangeQ1Fees);
        q1DuesInput.addEventListener("blur", ChangeQ1Fees);
        q1DuesInput.addEventListener("change", ChangeQ1Fees);
    }
    if (q1BenefitInput) {
        q1BenefitInput.addEventListener("input", ChangeQ1Fees);
        q1BenefitInput.addEventListener("blur", ChangeQ1Fees);
        q1BenefitInput.addEventListener("change", ChangeQ1Fees);
    }

    // Add event listeners for Q2
    if (q2DuesInput) {
        q2DuesInput.addEventListener("input", ChangeQ2Fees);
        q2DuesInput.addEventListener("blur", ChangeQ2Fees);
        q2DuesInput.addEventListener("change", ChangeQ2Fees);
    }
    if (q2BenefitInput) {
        q2BenefitInput.addEventListener("input", ChangeQ2Fees);
        q2BenefitInput.addEventListener("blur", ChangeQ2Fees);
        q2BenefitInput.addEventListener("change", ChangeQ2Fees);
    }

    // Add event listeners for Q3
    if (q3DuesInput) {
        q3DuesInput.addEventListener("input", ChangeQ3Fees);
        q3DuesInput.addEventListener("blur", ChangeQ3Fees);
        q3DuesInput.addEventListener("change", ChangeQ3Fees);
    }
    if (q3BenefitInput) {
        q3BenefitInput.addEventListener("input", ChangeQ3Fees);
        q3BenefitInput.addEventListener("blur", ChangeQ3Fees);
        q3BenefitInput.addEventListener("change", ChangeQ3Fees);
    }

    // Add event listeners for Q4
    if (q4DuesInput) {
        q4DuesInput.addEventListener("input", ChangeQ4Fees);
        q4DuesInput.addEventListener("blur", ChangeQ4Fees);
        q4DuesInput.addEventListener("change", ChangeQ4Fees);
    }
    if (q4BenefitInput) {
        q4BenefitInput.addEventListener("input", ChangeQ4Fees);
        q4BenefitInput.addEventListener("blur", ChangeQ4Fees);
        q4BenefitInput.addEventListener("change", ChangeQ4Fees);
    }

    // Run initial calculations with delay to ensure DOM is fully loaded
    setTimeout(function() {
        if (q1DuesInput) ChangeQ1Fees();
        if (q2DuesInput) ChangeQ2Fees();
        if (q3DuesInput) ChangeQ3Fees();
        if (q4DuesInput) ChangeQ4Fees();
    }, 500);
});

function ChangeQ1Fees() {
    var q1DuesInput = document.getElementById("q1_dues");
    var q1BenefitInput = document.getElementById("q1_benefit");
    var q1Dues = parseNumericValue(q1DuesInput.value);
    var q1Benefit = parseNumericValue(q1BenefitInput.value);

    ReCalculateTotal();
}

function ChangeQ2Fees() {
    var q2DuesInput = document.getElementById("q2_dues");
    var q2BenefitInput = document.getElementById("q2_benefit");
    var q2Dues = parseNumericValue(q2DuesInput.value);
    var q2Benefit = parseNumericValue(q2BenefitInput.value);

    ReCalculateTotal();
}

function ChangeQ3Fees() {
    var q3DuesInput = document.getElementById("q3_dues");
    var q3BenefitInput = document.getElementById("q3_benefit");
    var q3Dues = parseNumericValue(q3DuesInput.value);
    var q3Benefit = parseNumericValue(q3BenefitInput.value);

    ReCalculateTotal();
}

function ChangeQ4Fees() {
    var q4DuesInput = document.getElementById("q4_dues");
    var q4BenefitInput = document.getElementById("q4_benefit");
    var q4Dues = parseNumericValue(q4DuesInput.value);
    var q4Benefit = parseNumericValue(q4BenefitInput.value);

    ReCalculateTotal();
}

function ReCalculateTotal() {
    var totalQ1Dues = parseNumericValue(document.getElementById("q1_dues")?.value || "0");
    var totalQ1Benefit = parseNumericValue(document.getElementById("q1_benefit")?.value || "0");
    var totalQ2Dues = parseNumericValue(document.getElementById("q2_dues")?.value || "0");
    var totalQ2Benefit = parseNumericValue(document.getElementById("q2_benefit")?.value || "0");
    var totalQ3Dues = parseNumericValue(document.getElementById("q3_dues")?.value || "0");
    var totalQ3Benefit = parseNumericValue(document.getElementById("q3_benefit")?.value || "0");
    var totalQ4Dues = parseNumericValue(document.getElementById("q4_dues")?.value || "0");
    var totalQ4Benefit = parseNumericValue(document.getElementById("q4_benefit")?.value || "0");

    var totalDues = totalQ1Dues + totalQ2Dues + totalQ3Dues + totalQ4Dues;
    var totalBenefit = totalQ1Benefit + totalQ2Benefit + totalQ3Benefit + totalQ4Benefit;

    var totalPercentage = 0;
    if (totalDues > 0) {
        totalPercentage = (totalBenefit / totalDues) * 100;
    }

    var totalDuesElement = document.getElementById("TotalDues");
    var totalBenefitElement = document.getElementById("TotalBenefit");
    var totalPercentageElement = document.getElementById("TotalPercentage");

    if (totalDuesElement) {
        totalDuesElement.value = formatCurrency(totalDues);
    }
    if (totalBenefitElement) {
        totalBenefitElement.value = formatCurrency(totalBenefit);
    }
    if (totalPercentageElement) {
        totalPercentageElement.value = totalPercentage.toFixed(2) + "%";
    }
}

// Helper function to parse numeric values from formatted strings
function parseNumericValue(value) {
    if (!value) return 0;
    // Remove currency symbol, commas, spaces, and percent signs
    return parseFloat(value.replace(/[$,%\s]/g, '')) || 0;
}

// Helper function to format currency
function formatCurrency(value) {
    return value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}


</script>
@endsection

