@extends('layouts.chapter_theme')

@section('content')

<div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-user">
                    <div class="card-image color_header">
                    </div>
                    <div class="card-body">
                        <div class="author">
                                <div class="border-gray avatar">
                                    <img src="{{ config('settings.base_url') }}theme/dist/img/logo.png" alt="MC">
                                </div>
                               <h2 class="moms-c"> MOMS Club of {{ $chapterList[0]->name }}, {{$chapterState}} </h2>
                        </div>
                        <div class="col-md-12"><center><h4>Thank you for donating to the Mother-to-Mother Fund!</h4></center></div>
                        </div>

                    <div class="card-body">
                        <div class="col-md-12"><strong>Mother-to-Mother Fund Information</strong></div>
                        <div class="col-md-12">The Mother-To-Mother Fund is our ONLY official MOMS Club charity and is supported only by donations from the local chapters.
                            Because of donations from chapters and volunteers in the past, we have been able to offer grants for emergency expenses to our MOMS Club mothers
                            suffering from devastating financial and natural disasters.</div>
                        <div class="col-md-12"><br></div>


{{-- Start of Payment Form --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><strong>Mother-to-Mother Fund Donation</strong></div>

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

                    <form method="POST" action="{{ route('process.donation') }}">
                        @csrf
                        <?php
                        ?>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>Mother-to-Mother Fund Donation</label>
                                <input type="text" name="donation" id="donation" class="form-control" value="$0.00" oninput="formatCurrency(this)">
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
                            <div class="col-md-6">
                            <label for="card_number" >{{ __('Card Number') }}</label> <span class="field-required">*</span>
                                <input id="card_number" type="text" class="form-control @error('card_number') is-invalid @enderror" name="card_number" required autocomplete="off" >
                                @error('card_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                            <label for="expiration_date" ">{{ __('Expiration Date (MM/YY)') }}</label> <span class="field-required">*</span>
                                <input id="expiration_date" type="text" class="form-control @error('expiration_date') is-invalid @enderror" name="expiration_date" required autocomplete="off" >
                                @error('expiration_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                            <label for="cvv" >{{ __('CVV') }}</label> <span class="field-required">*</span>
                                <input id="cvv" type="text" class="form-control @error('cvv') is-invalid @enderror" name="cvv" required autocomplete="off" >
                                @error('cvv')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
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

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>Cardholder Address</label> <span class="field-required">*</span>
                                <input type="text" name="address" id="address" class="form-control"  required >
                            </div>
                        </div>
                        <div class="form-group row">
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

                        <div class="form-group text-center">
                            <div class="col-md-12" style="color: red;"><center>Page will automatically re-direct after payment submission with success or error message.<br>
                                DO NOT refresh page after clicking "Submit Payment" or you may be charged multiple times!</center></div>
                            <button type="submit" class="btn btn-info btn-fill"><i class="fa fa-share fa-fw" aria-hidden="true" ></i>&nbsp;{{ __('Submit Payment') }}</button>
                            <a href="{{ route('home') }}" class="btn btn-info btn-fill"><i class="fa fa-home fa-fw" aria-hidden="true" ></i>&nbsp; Back to HOME</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- End of Payment Form --}}
@if(app()->environment('local'))
    <img src="/uploads/authorize-net-seal.png" alt="authorizze-net-seal" style="float: left; margin-right: 20px;">
@else
    <img src="/mimi/uploads/authorize-net-seal.png" alt="authorizze-net-seal" style="float: left; margin-right: 20px;">
@endif
<div class="col-md-12" style="font-size: 0.8em"></div>
<div class="col-md-12" style="font-size: 0.8em">
    You can pay with confidence! We have partnered with <a href="http://www.authorize.net" target="blank">Authorize.Net</a>, a leading payment gateway since 1996,
    to accept credit cards and electronic check payments safely and securely for our chapters.</div>
    <div class="col-md-12" style="font-size: 0.8em"><br></div>
<div class="col-md-12" style="font-size: 0.8em">The Authorize.Net Payment Gateway manages the complex routing of sensitive customer information through the electronic check and credit card processing networks.
    See an <a href="http://www.authorize.net/resources/howitworksdiagram/" target="blank">online payments diagram</a> to see how it works.</div>
</div>
<div class="col-md-12"><br></div>

</div>
</div>
</div>
@endsection
@section('customscript')

<script>
    document.querySelector('form').addEventListener('submit', function(){
        document.querySelector('button[type="submit"]').setAttribute('disabled', 'disabled');
    });

    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        value = (value / 100).toFixed(2);
        input.value = '$' + value;
    }


    // Function to calculate total due
    function calculateTotal() {
        var donationInput = document.getElementById('donation');
        var donationValue = donationInput.value.trim();
        var donationFee = parseFloat(donationValue.replace('$', ''));

        // Check if donation fee is a valid number
        if (isNaN(donationFee)) {
            donationFee = 0; // Set to 0 if input is not a valid number
        }

        var fee = parseFloat(document.getElementById('fee').value.replace('$', ''));
        var total = donationFee + fee;

        document.getElementById('total').value = '$' + total.toFixed(2);
    }

    // Call calculateTotal function when the donation donation input changes
    document.getElementById('donation').addEventListener('input', calculateTotal);
    // Call calculateTotal function initially to calculate total based on default values
    calculateTotal();

    // Additional email validation
    document.getElementById('email').addEventListener('blur', function() {
        let emailInput = this.value.trim();
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(emailInput)) {
            document.getElementById('emailHelp').innerHTML = 'Please enter a valid email address.';
        } else {
            document.getElementById('emailHelp').innerHTML = '';
        }
    });

</script>
@endsection

