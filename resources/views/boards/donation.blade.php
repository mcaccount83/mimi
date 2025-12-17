@extends('layouts.board_theme')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
         <!-- Widget: user widget style 1 -->
         <div class="card card-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-primary">
                <div class="widget-user-image">
                    <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                  </div>
                        </div>
                        <div class="card-body">

                            <div class="col-md-12"><br><br></div>
                        <h2 class="text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                        <h4 class="text-center">Sustaining Chapter & Mother-to-Mother Fund Donations</h4>

                    </div>
                    <div class="col-md-12">
                        <p class="description">
                                         Sustaining chapter donations benefits the International MOMS Club, which is a 501 (c)(3) public charity.
                                         Your donation will help us keep dues low and help new and existing chapters in the U.S. and around the world.
                                    </p>

                                    <p class="description">
                                         The Mother-To-Mother Fund is our ONLY official MOMS Club charity and is supported only by donations from the local chapters.
                        Because of donations from chapters and volunteers in the past, we have been able to offer grants for emergency expenses to our MOMS Club mothers
                        suffering from devastating financial and natural disasters.
                                    </p>
                    </div>
                </div>


{{-- Start of Payment Form --}}
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
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
                            <div class="col-md-6">
                                <label>Sustaining Chapter Donation</label>
                                <input type="text" name="sustaining" id="sustaining" class="form-control" value="$0.00" oninput="formatCurrency(this)">
                            </div>
                            <div class="col-md-6">
                                <label>Mother-to-Mother Fund Donation</label>
                                <input type="text" name="m2m" id="m2m" class="form-control" value="$0.00" oninput="formatCurrency(this)">
                            </div>
                            <div class="col-md-6">
                                <label>Online Processing Fee</label>
                                <input type="text" name="fee" id="fee" class="form-control" value="$5.00" readonly>
                            </div>
                            <div class="col-md-6">
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
                            <label for="expiration_date" >{{ __('Expiration Date (MMYY)') }}</label> <span class="field-required">*</span>
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

                        <div class="card-body text-center">
                            <div class="col-md-12" style="color: red;"><center>Page will automatically re-direct after payment submission with success or error message.<br>
                                DO NOT refresh page after clicking "Submit Payment" or you may be charged multiple times!</center></div>
                            <br>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-share" ></i>&nbsp;{{ __('Submit Payment') }}</button>

                            @if($chActiveId != '1')
                                <a href="{{ route('board.editdisbandchecklist', $chDetails->id) }}" class="btn btn-primary" id="btn-back"><i class="fas fa-reply"></i>&nbsp; Back to Checklist</a>
                            @else
                                @if ($userTypeId == \App\Enums\UserTypeEnum::COORD)
                                    <button type="button" id="btn-back" class="btn btn-primary" onclick="window.location.href='{{ route('board.editprofile', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2" ></i>Back to Profile</button>
                                @else
                                    <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-reply" ></i>&nbsp; Back to Profile</a>
                                @endif
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12" style="font-size: 0.8em"></div>
<div class="col-md-12" style="font-size: 0.8em">
    <img src="{{ config('settings.base_url') }}images/authorize-net-seal.jpg" alt="authorize-net-seal" style="float: left; margin-right: 20px; width: 115px; height: 115px;">
    <p>You can pay with confidence! We have partnered with <a href="http://www.authorize.net" target="blank">Authorize.Net</a>, a leading payment gateway since 1996,
    to accept credit cards and electronic check payments safely and securely for our chapters.<br>
    <br>
    The Authorize.Net Payment Gateway manages the complex routing of sensitive customer information through the electronic check and credit card processing networks.
    See an <a href="http://www.authorize.net/resources/howitworksdiagram/" target="blank">online payments diagram</a> to see how it works.</p>
</div>

</div>
</div>
</div>
@endsection
@section('customscript')

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Define the sections we need to handle
    const sections = ['pres'];

    // Special state IDs that should show the country field
    const specialStates = [52, 53, 54, 55];

    // Process each section
    sections.forEach(section => {
        const stateDropdown = document.getElementById(`ch_${section}_state`);
        const countryContainer = document.getElementById(`ch_${section}_country-container`);
        const countrySelect = document.getElementById(`ch_${section}_country`);

        // Only proceed if all elements exist
        if (stateDropdown && countryContainer && countrySelect) {
            // Function to toggle country field visibility
            function toggleCountryField() {
                const selectedStateId = parseInt(stateDropdown.value) || 0;

                if (specialStates.includes(selectedStateId)) {
                    countryContainer.style.display = 'flex';
                    countrySelect.setAttribute('required', 'required');
                } else {
                    countryContainer.style.display = 'none';
                    countrySelect.removeAttribute('required');
                    countrySelect.value = "";
                }
            }

            // Set initial state
            toggleCountryField();

            // Add event listener
            stateDropdown.addEventListener('change', toggleCountryField);
        }
    });
});

    /* Disable fields and buttons  */
    $(document).ready(function () {
        var userTypeId = @json($userTypeId);
        var userAdmin = @json($userAdmin);

        $('select[name="pres_state"]').prop('disabled', true);

         if (if (userTypeId == '1' && userAdmin != 1) {
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

    // Function to calculate total due
    function calculateTotal() {
        var sustainingInput = document.getElementById('sustaining');
        var sustainingValue = sustainingInput.value.trim();
        var sustainingFee = parseFloat(sustainingValue.replace('$', ''));

        // Check if sustaining fee is a valid number
        if (isNaN(sustainingFee)) {
            sustainingFee = 0; // Set to 0 if input is not a valid number
        }

        var donationInput = document.getElementById('m2m');
        var donationValue = donationInput.value.trim();
        var donationFee = parseFloat(donationValue.replace('$', ''));

        // Check if donation fee is a valid number
        if (isNaN(donationFee)) {
            donationFee = 0; // Set to 0 if input is not a valid number
        }

        var feeInput = document.getElementById('fee');
        var feeValue = feeInput.value.trim();
        var fee = parseFloat(feeValue.replace('$', ''));

        // Check if fee is a valid number
        if (isNaN(fee)) {
            fee = 0; // Set to 0 if input is not a valid number
        }

        var total = sustainingFee + donationFee + fee;

        document.getElementById('total').value = '$' + total.toFixed(2);
    }

// Add event listeners to all input fields that affect the calculation
document.getElementById('sustaining').addEventListener('input', calculateTotal);
document.getElementById('m2m').addEventListener('input', calculateTotal);
document.getElementById('fee').addEventListener('input', calculateTotal);

// Call calculateTotal function initially to calculate total based on default values
document.addEventListener('DOMContentLoaded', calculateTotal);

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



