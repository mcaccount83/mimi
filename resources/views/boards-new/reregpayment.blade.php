@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Re-Registration Payment')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

            <form method="POST" action="{{ route('process.payment') }}">
            @csrf

             <div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                            <h3>Re-Registration Payment</h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                    @if ($chDetails->active_status == \App\Enums\ChapterStatusEnum::ZAPPED)
                        @if ($chDisbanded?->final_payment == '1')
                            <div class="row">
                                <div class="col-md-12">
                                    Thank You for submitting your Final Re-Registration payment!  If you need to submit another payment, change your Disbanding Checklist answer to NO and the payment
                                    form will be available.
                                </div>
                            </div>
                            <br>
                        @else
                            <div class="row">
                                <div class="col-md-12">
                                    As part of officially closing your chapter, you'll need to pay your chapter's final re-registration payment.<br><br>
                                </div>
                                <div class="col-12 mb-3">
                                        <label class="me-2">RE-REGISTRATION DUES LAST PAID:</label><span class="date-mask">{{$chDetails->payments->rereg_date}}</span>
                                    <br>
                                        <label class="me-2">LAST NUMBER OF MEMBERS REGISTERED:</label>{{ $chDetails->payments->rereg_members}}
                                </div>
                                <hr>
                                <div class="col-12 mb-3">
                                    <b>Payment Calculation:</b>
                                    <ul style="padding-left: 1.5rem;" class="mb-0">
                                        <li>Determine how many people paid dues since your last payment</li>
                                        <li>Add in any people who paid reduced dues or had their dues waived due to financial hardship</li>
                                        <li>If the total number of members is less than 10, your total amount due is $50</li>
                                        <li>If the total number of members is 10 or more, multiply the number by $5.00 to get your total amount due</li>
                                    </ul><br>
                                    <b>Late Fee:</b><br>
                                    A late fee of $10.00 will be added if your payment is submitted after the last day of <b>{{ $startMonthName }}</b>.<br>
                                    <br>
                                    <b>Sustaining Chapter Donation:</b><br>
                                    Sustaining chapter donations are voluntary and in addition to your chapter’s re-registration dues.
                                    The minimum recommended sustaining chapter donation is $100. The donation benefits the International MOMS Club, which is a 501 (c)(3) public charity.
                                    Your support to the MOMS Club is a service project for your chapter and should be included in its own line on your chapter’s Annual and Financial Reports.
                                    Your donation will help us keep dues low and help new and existing chapters in the U.S. and around the world.<br>
                                    <br>
                                    NOTE -- You may also make sustaining chapter donations and/or Mother-to-Mother fund donations separate from your Re-Registration payment through the donations tab in MIMI.
                                </div>
                            </div>
                            <br>
                        @endif
                    @else
                    <div class="row">
                        <div class="col-md-12">
                            Your chapter's anniversary month is <b>{{ $startMonthName }}</b>.<br>
                            Re-registration payments are due each year by the last day of your anniversary month.<br>
                            Your next payment:
                            @if ($currentDate->gte($dueDate))
                                @if ($chDetails->start_month_id == $currentMonth)
                                    <span class="badge bg-success fs-7">Due Now (<span class="date-mask">{{ $renewalDate }})</span></span>
                                @else
                                    <span class="badge bg-danger fs-7">Overdue (<span class="date-mask">{{ $renewalDate }})</span></span>
                                @endif
                            @else
                                Due on <span class="date-mask">{{ $renewalDate }}</span>
                            @endif
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-12 mb-3">
                                <label class="me-2">RE-REGISTRATION DUES LAST PAID:</label><span class="date-mask">{{$chDetails->payments->rereg_date}}</span>
                            <br>
                                <label class="me-2">LAST NUMBER OF MEMBERS REGISTERED:</label>{{ $chDetails->payments->rereg_members}}
                        </div>
                    <hr>
                    <div class="col-12 mb-3">
                        <b>Payment Calculation:</b>
                        <ul style="padding-left: 1.5rem;" class="mb-0">
                            <li>Determine how many people paid dues to your chapter from <b>{{ $startRange }}</b> of the Previous year through <b>{{ $endRange }}</b> of the current year</li>
                            <li>Add in any people who paid reduced dues or had their dues waived due to financial hardship</li>
                            <li>If the total number of members is less than 10, your total amount due is $50</li>
                            <li>If the total number of members is 10 or more, multiply the number by $5.00 to get your total amount due</li>
                        </ul><br>
                        <b>Late Fee:</b><br>
                        A late fee of $10.00 will be added if your payment is submitted after the last day of <b>{{ $startMonthName }}</b>.<br>
                        <br>
                        <b>Sustaining Chapter Donation:</b><br>
                        Sustaining chapter donations are voluntary and in addition to your chapter’s re-registration dues.
                        The minimum recommended sustaining chapter donation is $100. The donation benefits the International MOMS Club, which is a 501 (c)(3) public charity.
                        Your support to the MOMS Club is a service project for your chapter and should be included in its own line on your chapter’s Annual and Financial Reports.
                        Your donation will help us keep dues low and help new and existing chapters in the U.S. and around the world.<br>
                    </div>
                </div>
                <br>
                @endif

                @if ($chDisbanded?->final_payment != '1')
                {{-- Start of Payment Form --}}
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header"><strong>Re-Registration Payment Submission</strong>
                                </div>
                                <div class="card-body">
                                    @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif


                                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label>Number of Members</label> <span class="field-required">*</span>
                                                <input type="text" name="members" id="members" class="form-control"  required >
                                            </div>
                                            <div class="col-md-4" disabled >
                                                <label>Late Fee</label>
                                                <input type="text" name="late" id="late" class="form-control" value="{{ (($currentDate->gte($dueDate) && $dueDate->month != $currentDate->month) && $chDetails->payments->rereg_waivelate != '1') ? '$10.00' : '$0.00' }}" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Total Re-Registration Fees</label>
                                                <input type="text" name="rereg" id="rereg" class="form-control"  readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label>Optional Sustaining Chapter Donation</label>
                                                <input type="text" name="sustaining" id="sustaining" class="form-control" value="$0.00" oninput="formatCurrency(this)">
                                            </div>
                                            <div class="col-md-4">
                                                <label>Online Processing Fee</label>
                                                <input type="text" name="fee" id="fee" class="form-control" value="$5.00" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Total Due</label>
                                                <input type="text" name="total" id="total" class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                            <label for="card_number" >{{ __('Card Number') }}</label> <span class="field-required">*</span>
                                                <input id="card_number" type="text" class="form-control @error('card_number') is-invalid @enderror" name="card_number" required autocomplete="off" maxlength="16">
                                                @error('card_number')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                            <div class="col-md-3">
                                            <label for="expiration_date" >{{ __('Expiration Date (MMYY)') }}</label> <span class="field-required">*</span>
                                                <input id="expiration_date" type="text" class="form-control @error('expiration_date') is-invalid @enderror" name="expiration_date" required autocomplete="off" maxlength="5">
                                                @error('expiration_date')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>

                                            <div class="col-md-3">
                                            <label for="cvv" >{{ __('CVV') }}</label> <span class="field-required">*</span>
                                                <input id="cvv" type="text" class="form-control @error('cvv') is-invalid @enderror" name="cvv" required autocomplete="off" maxlength="4">
                                                @error('cvv')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label>Cardholder First Name</label> <span class="field-required">*</span>
                                                <input type="text" name="first_name" id="first_name" class="form-control"  required >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Cardholder Last Name</label> <span class="field-required">*</span>
                                                <input type="text" name="last_name" id="last_name" class="form-control"  required >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Cardholder Email</label> <span class="field-required">*</span>
                                                <input type="text" name="email" id="email" class="form-control"  required >
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label>Cardholder Address</label> <span class="field-required">*</span>
                                                <input type="text" name="address" id="address" class="form-control"  required >
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label>City</label> <span class="field-required">*</span>
                                                <input type="text" name="city" id="city" class="form-control"  required >
                                            </div>
                                            <div class="col-md-4">
                                                <label>State</label> <span class="field-required">*</span>
                                                <input type="text" name="state" id="state" class="form-control"  required >
                                            </div>
                                            <div class="col-md-4">
                                                <label>Zip</label> <span class="field-required">*</span>
                                                <input type="text" name="zip" id="zip" class="form-control"  required >
                                            </div>
                                        </div>

                                        <div class="card-body text-center mt-3">
                                            <div class="col-md-12" style="color: #dc3545;"><center>Page will automatically re-direct after payment submission with success or error message.<br>
                                                DO NOT refresh page after clicking "Submit Payment" or you may be charged multiple times!</center></div>
                                            <br>
                                                <button type="submit" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-chevron-double-right me-2"></i>{{ __('Submit Payment') }}</button>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <!-- /.payment-container- -->

                <div class="col-md-12 mt-3" style="font-size: 0.8em">
                    <img src="{{ config('settings.base_url') }}images/authorize-net-seal.jpg" alt="authorize-net-seal" style="float: left; margin-right: 20px; width: 115px; height: 115px;">
                    <p>
                    <br>
                    You can pay with confidence! We have partnered with <a href="http://www.authorize.net" target="blank">Authorize.Net</a>, a leading payment gateway since 1996,
                    to accept credit cards and electronic check payments safely and securely for our chapters.<br>
                    <br>
                    The Authorize.Net Payment Gateway manages the complex routing of sensitive customer information through the electronic check and credit card processing networks.
                    See an <a href="http://www.authorize.net/resources/howitworksdiagram/" target="blank">online payments diagram</a> to see how it works.</p>
                </div>

            @endif

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

    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        value = (value / 100).toFixed(2);
        input.value = '$' + value;
    }

    // Function to calculate total re-registration fees
    function calculateTotalRR() {
        var membersInput = document.getElementById('members');
        var members = membersInput.value.trim() == '' ? 0 : parseFloat(membersInput.value);
        var lateFee = parseFloat(document.getElementById('late').value.replace('$', ''));
        var totalRR = 0;

        if (members < 10) {
            totalRR = 50 + lateFee;
        } else {
            totalRR = (members * 5) + lateFee;
        }

        document.getElementById('rereg').value = '$' + totalRR.toFixed(2);

        calculateTotal();
    }

    // Call calculateTotalRR function when the number of members input changes
    document.getElementById('members').addEventListener('input', calculateTotalRR);
    // Call calculateTotal function initially to calculate total based on default values
    calculateTotalRR();

    // Function to calculate total due
    function calculateTotal() {
        var sustainingInput = document.getElementById('sustaining');
        var sustainingValue = sustainingInput.value.trim();
        var sustainingFee = parseFloat(sustainingValue.replace('$', ''));

        // Check if sustaining fee is a valid number
        if (isNaN(sustainingFee)) {
            sustainingFee = 0; // Set to 0 if input is not a valid number
        }

        var fee = parseFloat(document.getElementById('fee').value.replace('$', ''));
        var totalRR = parseFloat(document.getElementById('rereg').value.replace('$', ''));
        var total = sustainingFee + fee + totalRR;

        document.getElementById('total').value = '$' + total.toFixed(2);
    }

    // Call calculateTotal function when the sustaining donation input changes
    document.getElementById('sustaining').addEventListener('input', calculateTotal);
    // Call calculateTotal function initially to calculate total based on default values
    calculateTotal();

// Additional email validation
const emailField = document.getElementById('email');
if (emailField) {
    emailField.addEventListener('blur', function() {
        let emailInput = this.value.trim();
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(emailInput)) {
            this.setCustomValidity('Please enter a valid email address.');
        } else {
            this.setCustomValidity('');
        }
    });
}

</script>
@endsection

