@extends('layouts.chapter_theme')

@section('content')

<div class="container">
    <div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">×</button>
             <p>{{ $message }}</p>
            </div>
        @endif
        @if ($message = Session::get('fail'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">×</button>
             <p>{{ $message }}</p>
            </div>
        @endif
    </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-user">
                    <div class="card-image color_header">
                    </div>
                    <div class="card-body">
                        <div class="author">
                            <a href="#">
                                <div class="border-gray avatar">
                                    <img src="{{ asset('chapter_theme/img/logo.png') }}" alt="...">
                                </div>
                               <h2 class="moms-c"> MOMS Club of {{ $chapterList[0]->name }}, {{$chapterState}} </h2>
                            </a>
                        </div>
                        <div class="col-md-12"><br></div>
                        <div class="col-md-12"><center>Your chapter's anniversary month is <strong>{{ $startMonth }}</strong>.</center></div>
                        <div class="col-md-12"><center>Re-registration payments are due each year by the last day of your anniversary month.</center></div>
                        </div>

                    <div class="card-body">
                        <div class="col-md-12"><strong>Last Year's Re-Registration Information</strong></div>

                        <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>RE-REGISTRATION DUES LAST PAID</label>
                                    <p>{{\Carbon\Carbon::parse($chapterList[0]->dues_last_paid)->format('m-d-Y')}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>LAST NUMBER OF MEMBERS REGISTERED</label>
                                    <p>{{ $chapterList[0]->members_paid_for}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12"><br></div>
                        <div class="col-md-12"><strong>Payment Calculation:</strong></div>
                        <div class="col-md-12">
                        <ul><li>Determine how many people paid dues to your chapter from <b>{{ $startRange }}</b> of the Previous year through <b>{{ $endRange }}</b> of the current year</li>
                            <li>Add in any people who paid reduced dues or had their dues waived due to financial hardship</li>
                            <li>If the total number of members is less than 10, your total amount due is $50</li>
                            <li>If the total number of members is 10 or more, multiply the number by $5.00 to get your total amount due</li>
                        </ul>
                        </div>
                        <div class="col-md-12"><br></div>
                        <div class="col-md-12"><strong>Late Fee:</strong></div>
                        <div class="col-md-12">A late fee of $10.00 will be added if your payment is submitted after the last day of <strong>{{ $startMonth }}</strong>.</div>
                        <div class="col-md-12"><br></div>
                        <div class="col-md-12"><strong>Sustaining Chapter Donation:</strong></div>
                        <div class="col-md-12">Sustaining chapter donations are voluntary and in addition to your chapter’s re-registration dues.
                        The minimum recommended sustaining chapter donation is $100. The donation benefits the International MOMS Club, which is a 501 (c)(3) public charity.
                        Your support to the MOMS Club is a service project for your chapter and should be included in its own line on your chapter’s Annual and Financial Reports.
                        Your donation will help us keep dues low and help new and existing chapters in the U.S. and around the world.</div>
                        <div class="col-md-12"><br></div>


{{-- Start of Payment Form --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><strong>Re-Registration Payment Submission</strong></div>

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

                    <form method="POST" action="{{ route('process.payment') }}">
                        @csrf
                        <?php
                        ?>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>Number of Members</label> <span class="field-required">*</span>
                                <input type="text" name="members" id="members" class="form-control"  required >
                            </div>
                            @php
                                $thisDate = \Carbon\Carbon::now();
                            @endphp
                            <div class="col-md-4" disabled >
                                <label>Late Fee</label>
                                <input type="text" name="late" id="late" class="form-control" value="{{ ($thisDate->gte($due_date) && $due_date->month != $thisDate->month) ? '$10.00' : '$0.00' }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Total Re-Registration Fees</label>
                                <input type="text" name="rereg" id="rereg" class="form-control"  readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>Optional Sustaining Chapter Donation</label>
                                <input type="text" name="sustaining" id="sustaining" class="form-control" >
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

                        <div class="form-group row">
                            <label for="card_number" class="col-md-4 col-form-label text-md-right">{{ __('Card Number') }}</label> <span class="field-required">*</span>

                            <div class="col-md-6">
                                <input id="card_number" type="text" class="form-control @error('card_number') is-invalid @enderror" name="card_number" required autocomplete="off" maxlength="16">

                                @error('card_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="expiration_date" class="col-md-4 col-form-label text-md-right">{{ __('Expiration Date (MM/YY)') }}</label> <span class="field-required">*</span>

                            <div class="col-md-6">
                                <input id="expiration_date" type="text" class="form-control @error('expiration_date') is-invalid @enderror" name="expiration_date" required autocomplete="off" maxlength="5">

                                @error('expiration_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="cvv" class="col-md-4 col-form-label text-md-right">{{ __('CVV') }}</label> <span class="field-required">*</span>

                            <div class="col-md-6">
                                <input id="cvv" type="text" class="form-control @error('cvv') is-invalid @enderror" name="cvv" required autocomplete="off" maxlength="4">

                                @error('cvv')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-12 ">
                                <center><button type="submit" class="btn btn-info btn-fill"><i class="fa fa-share fa-fw" aria-hidden="true" ></i>&nbsp;
                                    {{ __('Submit Payment') }}
                                </button></center>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- End of Payment Form --}}
<div class="col-md-12">You can pay with confidence! We have partnered with <a href="http://www.authorize.net" target="blank">Authorize.Net</a>, a leading payment gateway since 1996,
    to accept credit cards and electronic check payments safely and securely for our chapters.</div>
    <div class="col-md-12 "><br></div>
<div class="col-md-12">The Authorize.Net Payment Gateway manages the complex routing of sensitive customer information through the electronic check and credit card processing networks.
    See an <a href="http://www.authorize.net/resources/howitworksdiagram/" target="blank">online payments diagram</a> to see how it works.</div>
</div>
<div class="col-md-12"><br></div>

<div class="col-md-12 text-center">
    <a href="{{ route('home') }}" class="btn btn-info btn-fill"><i class="fa fa-home fa-fw" aria-hidden="true" ></i>&nbsp; Back to HOME</a>
</div>
<div class="col-md-12"><br></div>

</div>
</div>
</div>
@endsection
@section('customscript')

<script>
    // Function to calculate total re-registration fees
    function calculateTotalRR() {
        var membersInput = document.getElementById('members');
        var members = membersInput.value.trim() === '' ? 0 : parseFloat(membersInput.value);
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

</script>
@endsection

