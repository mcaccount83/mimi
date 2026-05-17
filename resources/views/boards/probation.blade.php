@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Quarterly Financial Submission')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

                    <form method="POST" action='{{ route("board.updateprobation", $chDetails->id) }}' autocomplete="off">
                        @csrf

                         <div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                             <h3>Quarterly Financial Submission</h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                    Since your chapter in on probation for Excess Member Benefit Expenses, you will need to complete a quarterly financial report submission.<br>
                                    This should be completed/submitted each quarter to satisfy the terms of your probationary status.
                               </div>
                    </div>
                    <br>

        {{-- Start of Submission Form --}}
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header"><strong>Financial Details</strong>
                                </div>
                                <div class="card-body">
                            Enter Dues Income and Member Benefit Expenses for each Quarter Below.<br>
                                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4 float-start me-2 mb-1">
                                    <label for="q1_dues">Q1 Dues Income</label>
                                        @currencyInput('q1_dues', $chDetails->probationSubmit?->q1_dues)
                            </div>
                            <div class="col-md-4 float-start me-2 mb-1">
                                    <label for="q1_benefit">Q1 Member Benefit Expenses</label>
                                        @currencyInput('q1_benefit', $chDetails->probationSubmit?->q1_benefit)
                            </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                            <div class="col-md-4 float-start me-2 mb-1">
                                        <label for="Q1Dues">Q2 Dues Income</label>
                                            @currencyInput('q2_dues', $chDetails->probationSubmit?->q2_dues)
                                </div>
                            <div class="col-md-4 float-start me-2 mb-1">
                                        <label for="Q1Benefit">Q2 Member Benefit Expenses</label>
                                            @currencyInput('q2_benefit', $chDetails->probationSubmit?->q2_benefit)
                                </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                            <div class="col-md-4 float-start me-2 mb-1">
                                            <label for="Q1Dues">Q3 Dues Income</label>
                                                @currencyInput('q3_dues', $chDetails->probationSubmit?->q3_dues)
                                    </div>
                            <div class="col-md-4 float-start me-2 mb-1">
                                            <label for="Q1Benefit">Q3 Member Benefit Expenses</label>
                                                @currencyInput('q3_benefit', $chDetails->probationSubmit?->q3_benefit)
                                    </div>
                            <div class="col-md-4 float-start me-2 mb-1">
                                    </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                            <div class="col-md-4 float-start me-2 mb-1">
                                                <label for="Q1Dues">Q4 Dues Income</label>
                                                    @currencyInput('q4_dues', $chDetails->probationSubmit?->q4_dues)
                                        </div>
                            <div class="col-md-4 float-start me-2 mb-1">
                                                <label for="Q1Benefit">Q4 Member Benefit Expenses</label>
                                                    @currencyInput('q4_benefit', $chDetails->probationSubmit?->q4_benefit)
                                        </div>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                            <div class="col-md-4 float-start me-2 mb-1">
                                                    <label for="Q1Dues">YTD Dues Income</label>
                                                    @currencyInput('TotalDues', '', true)
                                            </div>
                            <div class="col-md-4 float-start me-2 mb-1">
                                                    <label for="Q1Benefit">YTD Benefit Expenses</label>
                                                    @currencyInput('TotalBenefit', '', true)
                                            </div>

                                             </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                            <div class="col-md-4 float-start me-2 mb-1">
                                        <label for="Q1Percentage">YTD Benefit %</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="TotalPercentage" id="TotalPercentage" value="0%" readonly>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-center mt-3">
                <button type="submit" id="Save" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-chevron-double-right me-2"></i>Submit</button>
                    </div>

                    </div>
                </div>
                 <!-- /.form-container- -->

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
<script>

document.addEventListener("DOMContentLoaded", function() {
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

