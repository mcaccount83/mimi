@extends('layouts.public_theme')

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" method="POST" action='{{ route("public.updatenewchapter") }}'>
                        @csrf

                        <div class="col-md-12">
                            <div class="card card-widget widget-user">
                                <div class="widget-user-header bg-primary">
                                    <div class="widget-user-image">
                                        <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12"><br><br></div>
                                    <h2 class="text-center">New Chapter Application</h2>
                                    </h2>
                                    <p class="description text-center">
                                        All chapters are in PENDING status until reviewed by our Coordintaor Team. After review, you will receive an email communication from your Coordinator.<br>
                                        Log in at at <a href="http://momsclub.org/mimi" target="_blank">http://momsclub.org/mimi</a> with the email address and password you registered with to check your application status.<br>
                                    </p>

                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>

                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                 <!-- /.card-header -->
                            <div class="row">
                                <div class="col-md-12">

                  <h3 class="profile-username ">Chapter Information</h3>
                    <!-- Toggle Switch - Default to NO (unchecked) -->
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Are you being sistered by another chapter?</label>
                        <div class="col-sm-8">
                            <input type="checkbox" name="SisteredBy" id="sisteredToggle"
                                data-bootstrap-switch
                                data-on-text="Yes"
                                data-off-text="No"
                                data-on-color="success"
                                data-off-color="danger">
                        </div>
                    </div>

                    <!-- Sistered By Field - Visible when YES sistered is checked -->
                    <div class="form-group row" id="sisteredByField" style="display: none;">
                        <label class="col-sm-4 col-form-label">If so, which chapter?</label>
                        <div class="col-sm-8">
                            <input type="text" name="ch_sisteredby" id="ch_sisteredby" class="form-control" placeholder="Chapter Name" required>
                        </div>

                    </div>

                    <!-- Hear About By Field - Visible when NO sistered is checked -->
                    <div class="form-group row" id="hearAboutByField" style="display: none;">
                        <label class="col-sm-4 col-form-label">If not, how did you hear about us?</label>
                        <div class="col-sm-8">
                            <input type="text" name="ch_hearabout" id="ch_hearabout" class="form-control" placeholder="Online Search, Friend, Word of Mouth, etc" required>
                        </div>
                    </div>

                  <div class="form-group row mt-1">
                    <label class="col-sm-4 col-form-label">State:</label>
                    <div class="col-sm-8">
                        <select id="ch_state" name="ch_state" class="form-control" required>
                            <option value="">Select State</option>
                            @foreach($allStates as $state)
                                <option value="{{$state->id}}">
                                    {{$state->state_long_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mt-1" id="country-container" style="display: none;">
                    <label class="col-sm-4 col-form-label">Country:</label>
                    <div class="col-sm-8">
                        <select id="ch_country" name="ch_country" class="form-control" required>
                            <option value="">Select Country</option>
                            @foreach($allCountries as $country)
                                <option value="{{$country->id}}" >
                                    {{$country->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Requested Name:</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">MOMS Club of</span>
                            </div>
                            <input type="text" name="ch_name" id="ch_name" class="form-control" placeholder="Chapter Name" required>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <p>All MOMS Club chapter names should start with "MOMS Club of" and be followed by the city/township/territory that best represents your area.</p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Requested Boundaries:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_boundariesterry" id="ch_boundariesterry" class="form-control" placeholder="Boundaries" required>
                    </div>
                    <div class="col-sm-12">
                        <p>Boundaries should define your chapter borders and/or describe your chapter's territory to potential members. Don't worry, if you are unsure what your boundaries
                            should be take your best guess and your Coordinator will help you finalize them.
                        </p>
                        </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Email for Members to Contact:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" placeholder="Inquiries Email" required>
                    </div>
                    <div class="col-sm-12">
                    <p>Don't worry if you don't have a separate email.  This can be your personal email for now. You can always update it. If your chapter is approved we will provide you
                        with a momsclub.org email address for your chapter to use.</p>
                    </div>
                </div>

                </div>
            </div>

            <hr>

                <h3 class="profile-username">Founder Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 mb-1 col-form-label">Name:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control"  required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label">Contact:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"   required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label">Address:</label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" placeholder="Address" required >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="col-sm-3 mb-1">
                                <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control" placeholder="City" required >
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_pre_state" id="ch_pre_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}">
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control" placeholder="Zip" required >
                                </div>
                            <div class="col-sm-2 mb-1" id="ch_pre_country-container" style="display: none;">
                                <select name="ch_pre_country" id="ch_pre_country" class="form-control" style="width: 100%;" required>
                                    <option value="">Select Country</option>
                                    @foreach($allCountries as $country)
                                        <option value="{{$country->id}}">
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Password:</label>
                                <div class="col-sm-5">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                </div>
                                <div class="col-sm-5">
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
                                    <small id="password-match-message" class="form-text"></small>
                                </div>
                                <div class="col-sm-12">
                                    <p>Select a password to create your MOMS Information Management Interface (MIMI) account where you can check on your application status, view contact
                                        information for your assigned Coordinator and much more.</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <hr>

                    <div id="payment-section">
                <h3 class="profile-username">Payment Information</h3>
                <!-- /.card-header -->
                <div class="row">
                    <div class="col-md-12">

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label>New Chapter Fee</label>
                                <input type="text" name="newchap" id="newchap" class="form-control" value="$30.00" readonly>
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
                             <div class="col-md-12" ><center>
                                Please note: your payment will not be processed until your chapter is approved.<br>
                                After it is approved, your payment will be processed and we will send you your MOMS Club Chapter Manual.<br>
                                There are no refunds after the payment has been processed.
                            </center></div>
                            <br>
                            <div class="col-md-12" style="color: red;"><center>Page will automatically re-direct after application submission with success or error message.<br>
                                DO NOT refresh page after clicking "Submit Payment" or you may be charged multiple times!</center></div>
                            <br>

                                <button type="submit" class="btn btn-primary"><i class="fas fa-share" ></i>&nbsp;{{ __('Submit Application') }}</button>
                        </div>
                    </form>
                </div>
            </div>

                    </div>
    <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </div>
    <!-- /.col -->
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
<!-- /.container- -->
@endsection
@section('customscript')

<script>
$(function () {
    // Initialize Bootstrap Switch
    $("input[data-bootstrap-switch]").bootstrapSwitch();

    // Handle switch change event
    $('#sisteredToggle').on('switchChange.bootstrapSwitch', function(event, state) {
        if (state) {
            // Switch is ON (Yes) - Show sistered field, hide hear about field
            $('#sisteredByField').slideDown(300);
            $('#hearAboutByField').slideUp(300);
            $('#ch_sisteredby').prop('required', true);
            $('#ch_hearabout').prop('required', false).val(''); // Clear hear about field
        } else {
            // Switch is OFF (No) - Hide sistered field, show hear about field
            $('#sisteredByField').slideUp(300);
            $('#hearAboutByField').slideDown(300);
            $('#ch_sisteredby').prop('required', false).val(''); // Clear sistered field
            $('#ch_hearabout').prop('required', true);
        }
    });

    // Initialize the field states on page load
    var initialState = $('#sisteredToggle').bootstrapSwitch('state');

    if (initialState) {
        // YES - Show sistered field
        $('#sisteredByField').show();
        $('#hearAboutByField').hide();
        $('#ch_sisteredby').prop('required', true);
        $('#ch_hearabout').prop('required', false);
    } else {
        // NO - Show hear about field
        $('#sisteredByField').hide();
        $('#hearAboutByField').show();
        $('#ch_sisteredby').prop('required', false);
        $('#ch_hearabout').prop('required', true);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Chapter state and country
    const stateDropdown = document.getElementById('ch_state');
    const countryContainer = document.getElementById('country-container');
    const countrySelect = document.getElementById('ch_country');

    // President state and country
    const statePreDropdown = document.getElementById('ch_pre_state');
    const countryPreContainer = document.getElementById('ch_pre_country-container');
    const countryPreSelect = document.getElementById('ch_pre_country');

    // Check if elements exist before adding listeners
    if (stateDropdown && countryContainer && countrySelect) {
        // Initially set country field requirement based on state selection
        toggleCountryField();

        // Add event listener to the state dropdown
        stateDropdown.addEventListener('change', toggleCountryField);

        function toggleCountryField() {
            const selectedStateId = parseInt(stateDropdown.value) || 0;
            const specialStates = [52, 53, 54, 55]; // States that should show the country field

            if (specialStates.includes(selectedStateId)) {
                // Show country field
                countryContainer.style.display = 'flex';
                countrySelect.setAttribute('required', 'required');

                // Hide payment section and remove required attributes
                hidePaymentSection();
            } else {
                // Hide country field
                countryContainer.style.display = 'none';
                countrySelect.removeAttribute('required');
                countrySelect.value = "";

                // Show payment section and restore required attributes
                showPaymentSection();
            }
        }
    }

    // President state logic (unchanged)
    if (statePreDropdown && countryPreContainer && countryPreSelect) {
        togglePreCountryField();
        statePreDropdown.addEventListener('change', togglePreCountryField);

        function togglePreCountryField() {
            const selectedPreStateId = parseInt(statePreDropdown.value) || 0;
            const specialPreStates = [52, 53, 54, 55];

            if (specialPreStates.includes(selectedPreStateId)) {
                countryPreContainer.style.display = 'flex';
                countryPreSelect.setAttribute('required', 'required');
            } else {
                countryPreContainer.style.display = 'none';
                countryPreSelect.removeAttribute('required');
                countryPreSelect.value = "";
            }
        }
    }

    function hidePaymentSection() {
        // Hide payment fields section (everything except submit button and authorize notice)
        const paymentHeading = Array.from(document.querySelectorAll('h3')).find(h3 =>
            h3.textContent.trim() == 'Payment Information'
        );

        if (paymentHeading) {
            // Hide the payment heading
            paymentHeading.style.display = 'none';

            // Hide all payment form groups
            const paymentFields = [
                'card_number', 'expiration_date', 'cvv', 'first_name',
                'last_name', 'email', 'address', 'city', 'state', 'zip',
                'newchap', 'fee', 'total'
            ];

            paymentFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    // Hide the entire form group
                    let formGroup = field.closest('.form-group');
                    if (formGroup) {
                        formGroup.style.display = 'none';
                    }

                    // Remove required attribute and clear value
                    field.removeAttribute('required');
                    field.value = '';
                }
            });

            // Hide the payment warning message (but keep the submit section warning)
            const paymentWarningDiv = document.querySelector('.col-md-12[style*="color: red"]:not(.card-body .col-md-12)');
            if (paymentWarningDiv && !paymentWarningDiv.closest('.card-body.text-center')) {
                paymentWarningDiv.style.display = 'none';
            }
        }

        // Hide the authorize.net notice section
        const authorizeNotice = document.querySelector('img[alt="authorize-net-seal"]');
        if (authorizeNotice) {
            // Hide the entire authorize notice section
            let noticeSection = authorizeNotice.closest('.col-md-12');
            if (noticeSection) {
                noticeSection.style.display = 'none';
            }
            // Also hide the preceding empty div
            if (noticeSection && noticeSection.previousElementSibling) {
                noticeSection.previousElementSibling.style.display = 'none';
            }
        }

        // Update submit button text and warning message
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.innerHTML = '<i class="fas fa-share"></i>&nbsp;Submit Application';
        }

        // Update the submit section warning message for non-payment submissions
        const submitWarningDiv = document.querySelector('.card-body.text-center .col-md-12[style*="color: red"]');
        if (submitWarningDiv) {
            submitWarningDiv.innerHTML = '<center>Page will automatically re-direct after application submission with success or error message.<br>Please do not refresh the page after clicking "Submit Application"!</center>';
        }
    }

    function showPaymentSection() {
        // Show payment fields section
        const paymentHeading = Array.from(document.querySelectorAll('h3')).find(h3 =>
            h3.textContent.trim() == 'Payment Information'
        );

        if (paymentHeading) {
            // Show the payment heading
            paymentHeading.style.display = 'block';

            // Show all payment form groups and restore required attributes
            const paymentFields = [
                'card_number', 'expiration_date', 'cvv', 'first_name',
                'last_name', 'email', 'address', 'city', 'state', 'zip',
                'newchap', 'fee', 'total'
            ];

            paymentFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    // Show the entire form group
                    let formGroup = field.closest('.form-group');
                    if (formGroup) {
                        formGroup.style.display = 'flex';
                    }

                    // Restore required attribute (except for readonly fields)
                    if (fieldId != 'newchap' && fieldId != 'fee' && fieldId != 'total') {
                        field.setAttribute('required', 'required');
                    }
                }
            });

            // Show the payment warning message (if there was one outside submit section)
            const paymentWarningDiv = document.querySelector('.col-md-12[style*="color: red"]:not(.card-body .col-md-12)');
            if (paymentWarningDiv && !paymentWarningDiv.closest('.card-body.text-center')) {
                paymentWarningDiv.style.display = 'block';
            }
        }

        // Show the authorize.net notice section
        const authorizeNotice = document.querySelector('img[alt="authorize-net-seal"]');
        if (authorizeNotice) {
            // Show the entire authorize notice section
            let noticeSection = authorizeNotice.closest('.col-md-12');
            if (noticeSection) {
                noticeSection.style.display = 'block';
            }
            // Also show the preceding empty div
            if (noticeSection && noticeSection.previousElementSibling) {
                noticeSection.previousElementSibling.style.display = 'block';
            }
        }

        // Update submit button text and warning message
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.innerHTML = '<i class="fas fa-share"></i>&nbsp;Submit Payment';
        }

        // Update the submit section warning message for payment submissions
        const submitWarningDiv = document.querySelector('.card-body.text-center .col-md-12[style*="color: red"]');
        if (submitWarningDiv) {
            submitWarningDiv.innerHTML = '<center>Page will automatically re-direct after application submission with success or error message.<br>DO NOT refresh page after clicking "Submit Payment" or you may be charged multiple times!</center>';
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const matchMessage = document.getElementById('password-match-message');

    function checkPasswordMatch() {
        if (confirmPassword.value == '') {
            matchMessage.textContent = '';
            matchMessage.className = 'form-text';
            return;
        }

        if (password.value == confirmPassword.value) {
            matchMessage.textContent = 'Passwords match!';
            matchMessage.className = 'form-text text-success';
            confirmPassword.setCustomValidity('');
        } else {
            matchMessage.textContent = 'Passwords do not match!';
            matchMessage.className = 'form-text text-danger';
            confirmPassword.setCustomValidity('Passwords do not match');
        }
    }

    // Check on input in either field
    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);
});

    // Function to calculate total due
    function calculateTotal() {
        var newchap = parseFloat(document.getElementById('newchap').value.replace('$', ''));
        var fee = parseFloat(document.getElementById('fee').value.replace('$', ''));
        var total = newchap + fee;

        document.getElementById('total').value = '$' + total.toFixed(2);
    }

    // Call the function when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
    });

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
