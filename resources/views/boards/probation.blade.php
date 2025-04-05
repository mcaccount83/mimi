@extends('layouts.board_theme')


@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" action='{{ route("board.updatepresident", $chDetails->id) }}' autocomplete="off">
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
                         <p class="description text-center">
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
                                        <input type="text" class="form-control " name="q1_dues" id="q1_dues" value="{{ $chDetails->probation?->q1_dues }}"
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
                                        <input type="text" class="form-control " name="q1_benefit" id="q1_benefit" value="{{ $chDetails->probation?->q1_benefit }}"
                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 float-left nopadding">
                                <div class="form-group">
                                    <label for="Q1Percentage">Q1 Party %</label>
                                    <div class="form-group">
                                        <div class="input-group-prepend">
                                           <input type="text" class="form-control" name="q1_percentage" id="q1_percentage" value="0%" readonly>
                                        </div>
                                    </div>
                                </div>
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
                                            <input type="text" class="form-control " name="q1_dues" id="q1_dues" value="{{ $chDetails->probation?->q1_dues }}"
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
                                            <input type="text" class="form-control " name="q1_benefit" id="q1_benefit" value="{{ $chDetails->probation?->q1_benefit }}"
                                                data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 float-left nopadding">
                                    <div class="form-group">
                                        <label for="Q1Percentage">Q2 Party %</label>
                                        <div class="form-group">
                                            <div class="input-group-prepend">
                                               <input type="text" class="form-control" name="q1_percentage" id="q1_percentage" value="0%" readonly>
                                            </div>
                                        </div>
                                    </div>
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
                                                <input type="text" class="form-control " name="q1_dues" id="q1_dues" value="{{ $chDetails->probation?->q1_dues }}"
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
                                                <input type="text" class="form-control " name="q1_benefit" id="q1_benefit" value="{{ $chDetails->probation?->q1_benefit }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 float-left nopadding">
                                        <div class="form-group">
                                            <label for="Q1Percentage">Q3 Party %</label>
                                            <div class="form-group">
                                                <div class="input-group-prepend">
                                                   <input type="text" class="form-control" name="q1_percentage" id="q1_percentage" value="0%" readonly>
                                                </div>
                                            </div>
                                        </div>
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
                                                    <input type="text" class="form-control " name="q1_dues" id="q1_dues" value="{{ $chDetails->probation?->q1_dues }}"
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
                                                    <input type="text" class="form-control " name="q1_benefit" id="q1_benefit" value="{{ $chDetails->probation?->q1_benefit }}"
                                                        data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 float-left nopadding">
                                            <div class="form-group">
                                                <label for="Q1Percentage">Q4 Party %</label>
                                                <div class="form-group">
                                                    <div class="input-group-prepend">
                                                       <input type="text" class="form-control" name="q1_percentage" id="q1_percentage" value="0%" readonly>
                                                    </div>
                                                </div>
                                            </div>
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
                                                        <input type="text" class="form-control " name="q1_dues" id="q1_dues" readonly
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
                                                        <input type="text" class="form-control " name="q1_benefit" id="q1_benefit" readonly
                                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 float-left nopadding">
                                                <div class="form-group">
                                                    <label for="Q1Percentage">YTD Party %</label>
                                                    <div class="form-group">
                                                        <div class="input-group-prepend">
                                                           <input type="text" class="form-control" name="q1_percentage" id="q1_percentage" value="0%" readonly>
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
                <button id="Save" type="submit" class="btn btn-primary" onclick="return validateEmailsBeforeSubmit()"><i class="fas fa-save" ></i>&nbsp; Save</button>

            </form>
                <button id="Password" type="button" class="btn btn-primary" onclick="showChangePasswordAlert()"><i class="fas fa-lock" ></i>&nbsp; Change Password</button>
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
/* Curency Mask */
document.addEventListener("DOMContentLoaded", function() {
    Inputmask().mask(document.querySelectorAll('[data-inputmask]'));

    var q1DuesInput = document.getElementById("q1_dues");
    var q1BenefitInput = document.getElementById("q1_benefit");
    var q1DuesInput = document.getElementById("q2_dues");
    var q1BenefitInput = document.getElementById("q2_benefit");
    var q1DuesInput = document.getElementById("q3_dues");
    var q1BenefitInput = document.getElementById("q3_benefit");
    var q1DuesInput = document.getElementById("q4_dues");
    var q1BenefitInput = document.getElementById("q4_benefit");

    q1DuesInput.addEventListener("input", ChangeQ1Fees);
    q1DuesInput.addEventListener("blur", ChangeQ1Fees);
    q1DuesInput.addEventListener("change", ChangeQ1Fees);
    q1BenefitInput.addEventListener("input", ChangeQ1Fees);
    q1BenefitInput.addEventListener("blur", ChangeQ1Fees);
    q1BenefitInput.addEventListener("change", ChangeQ1Fees);

    q2DuesInput.addEventListener("input", ChangeQ2Fees);
    q2DuesInput.addEventListener("blur", ChangeQ2Fees);
    q2DuesInput.addEventListener("change", ChangeQ2Fees);
    q2BenefitInput.addEventListener("input", ChangeQ2Fees);
    q2BenefitInput.addEventListener("blur", ChangeQ2Fees);
    q2BenefitInput.addEventListener("change", ChangeQ2Fees);

    q3DuesInput.addEventListener("input", ChangeQ3Fees);
    q3DuesInput.addEventListener("blur", ChangeQ3Fees);
    q3DuesInput.addEventListener("change", ChangeQ3Fees);
    q3BenefitInput.addEventListener("input", ChangeQ3Fees);
    q3BenefitInput.addEventListener("blur", ChangeQ3Fees);
    q3BenefitInput.addEventListener("change", ChangeQ3Fees);

    q4DuesInput.addEventListener("input", ChangeQ4Fees);
    q4DuesInput.addEventListener("blur", ChangeQ4Fees);
    q4DuesInput.addEventListener("change", ChangeQ4Fees);
    q4BenefitInput.addEventListener("input", ChangeQ4Fees);
    q4BenefitInput.addEventListener("blur", ChangeQ4Fees);
    q4BenefitInput.addEventListener("change", ChangeQ4Fees);

    setTimeout(ChangeQ1Fees, 500);
    setTimeout(ChangeQ2Fees, 500);
    setTimeout(ChangeQ3Fees, 500);
    setTimeout(ChangeQ4Fees, 500);
});

function ChangeQ1Fees() {
    var q1DuesInput = document.getElementById("q1_dues");
    var q1BenefitInput = document.getElementById("q1_benefit");
    var q1PercentageInput = document.getElementById("q1_percentage");

    var q1Dues = parseNumericValue(q1DuesInput.value);
    var q1Benefit = parseNumericValue(q1BenefitInput.value);

    var percentage = 0;
    if (q1Dues > 0) {
        percentage = ( q1Benefit / q1Dues) * 100;
    }

    q1PercentageInput.value = percentage.toFixed(2) + "%";

    ReCalculateTotal();
}

function ChangeQ2Fees() {
    var q2DuesInput = document.getElementById("q2_dues");
    var q2BenefitInput = document.getElementById("q2_benefit");
    var q2PercentageInput = document.getElementById("q2_percentage");

    var q2Dues = parseNumericValue(q2DuesInput.value);
    var q2Benefit = parseNumericValue(q2BenefitInput.value);

    var percentage = 0;
    if (q2Dues > 0) {
        percentage = ( q2Benefit/ q2Dues) * 100;
    }

    q2PercentageInput.value = percentage.toFixed(2) + "%";

    ReCalculateTotal();
}

function ChangeQ3Fees() {
    var q3DuesInput = document.getElementById("q3_dues");
    var q3BenefitInput = document.getElementById("q3_benefit");
    var q3PercentageInput = document.getElementById("q3_percentage");

    var q3Dues = parseNumericValue(q3DuesInput.value);
    var q3Benefit = parseNumericValue(q3BenefitInput.value);

    var percentage = 0;
    if (q3Dues > 0) {
        percentage = ( q3Benefit/ q3Dues) * 100;
    }

    q3PercentageInput.value = percentage.toFixed(2) + "%";

    ReCalculateTotal();
}

function ChangeQ4Fees() {
    var q4DuesInput = document.getElementById("q4_dues");
    var q4BenefitInput = document.getElementById("q4_benefit");
    var q4PercentageInput = document.getElementById("q4_percentage");

    var q3Dues = parseNumericValue(q4DuesInput.value);
    var q3Benefit = parseNumericValue(q4BenefitInput.value);

    var percentage = 0;
    if (q4Dues > 0) {
        percentage = ( q4Benefit/ q4Dues) * 100;
    }

    q4PercentageInput.value = percentage.toFixed(2) + "%";

    ReCalculateTotal();
}
function ReCalculateTotal() {
    var totalQ1Dues = parseNumericValue(document.getElementById("q1_dues").value);
    var totalQ1Benefit = parseNumericValue(document.getElementById("q1_benefit").value);
    var totalQ2Dues = parseNumericValue(document.getElementById("q2_dues").value);
    var totalQ2Benefit = parseNumericValue(document.getElementById("q2_benefit").value);
    var totalQ3Dues = parseNumericValue(document.getElementById("q3_dues").value);
    var totalQ3Benefit = parseNumericValue(document.getElementById("q3_benefit").value);
    var totalQ4Dues = parseNumericValue(document.getElementById("q4_dues").value);
    var totalQ4Benefit = parseNumericValue(document.getElementById("q4_benefit").value);

    var totalDues = totalQ1Dues + totalQ2Dues + totalQ3Dues + totalQ4Dues;
    var totalBenefit = totalQ1Benefit + totalQ2Benefit + totalQ3Benefit + totalQ4Benefit;

    var totalPercentage = 0;
    if (totalDues > 0) {
        totalPercentage = (totalBenefit / totalDues) * 100;
    }

    if (document.getElementById("TotalDues")) {
        document.getElementById("TotalDues").value = formatCurrency(totalDues);
    }
    if (document.getElementById("TotalBenefit")) {
        document.getElementById("TotalBenefit").value = formatCurrency(totalBenefit);
    }
    if (document.getElementById("TotalPercentage")) {
        document.getElementById("TotalPercentage").value = totalPercentage.toFixed(2) + "%";
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

