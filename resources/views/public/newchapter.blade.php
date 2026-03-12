@extends('layouts.public_theme')

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" method="POST" action='{{ route("public.updatenewchapter") }}'>
                        @csrf
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card bg-primary">
                                    <div class="card-body text-center">
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
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Are you being sistered by another chapter?</label>
                        <div class="col-sm-8">
                            <div class="form-check form-switch">
    <input type="checkbox" name="SisteredBy" id="sisteredToggle" class="form-check-input" role="switch">
    <label class="form-check-label" for="sisteredToggle"></label>
</div>
                        </div>
                    </div>

                    <!-- Sistered By Field - Visible when YES sistered is checked -->
                    <div class="row mb-3" id="sisteredByField" style="display: none;">
                        <label class="col-sm-4 col-form-label">If so, which chapter?</label>
                        <div class="col-sm-8">
                            <input type="text" name="ch_sisteredby" id="ch_sisteredby" class="form-control" placeholder="Chapter Name" required>
                        </div>

                    </div>

                    <!-- Hear About By Field - Visible when NO sistered is checked -->
                    <div class="row mb-3" id="hearAboutByField" style="display: none;">
                        <label class="col-sm-4 col-form-label">If not, how did you hear about us?</label>
                        <div class="col-sm-8">
                            <input type="text" name="ch_hearabout" id="ch_hearabout" class="form-control" placeholder="Online Search, Friend, Word of Mouth, etc" required>
                        </div>
                    </div>

                  <div class="row mb-3 mt-1">
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

                <div class="row mb-3 mt-1" id="country-container" style="display: none;">
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

                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Requested Name:</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                                <span class="input-group-text">MOMS Club of</span>
                            <input type="text" name="ch_name" id="ch_name" class="form-control" placeholder="Chapter Name" required>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <p>All MOMS Club chapter names should start with "MOMS Club of" and be followed by the city/township/territory that best represents your area.</p>
                    </div>
                </div>

                <div class="row mb-3">
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

                <div class="row mb-3">
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
                            <div class="row mb-3">
                                <label class="col-sm-2 mb-1 col-form-label">Name:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control"  required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label">Contact:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" required placeholder="Email Address" >
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
                                    <input type="text" id="ch_pre_state_display" class="form-control bg-light" placeholder="Same as Chapter State" readonly>
                                    <input type="hidden" name="ch_pre_state" id="ch_pre_state">
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control" placeholder="Zip" required>
                                </div>
                                <div class="col-sm-2 mb-1" id="ch_pre_country-container" style="display: none;">
                                    <input type="text" id="ch_pre_country_display" class="form-control bg-light" placeholder="Same as Chapter Country" readonly>
                                    <input type="hidden" name="ch_pre_country" id="ch_pre_country">
                                </div>
                            </div>

                            <div class="row mb-3">
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

                   <div id="payment-section">
                     <hr>
                        <h3 class="profile-username">Payment Information</h3>
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

                        <div class="row mb-3">
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

                      </div>
                        </div>
                    </div> {{-- END payment-section --}}

            {{-- Authorize.net notice --}}
                    {{-- <div class="col-md-12 mt-3" id="authorize-notice" style="font-size: 0.8em">
                        <img src="{{ config('settings.base_url') }}images/authorize-net-seal.jpg" alt="authorize-net-seal" style="float: left; margin-right: 20px; width: 115px; height: 115px;">
                        <p>You can pay with confidence! ...</p>
                    </div> --}}

                    {{-- Submit section - always visible --}}
                    <div class="card-body text-center mt-3">
                        <div class="col-md-12 mb-3" id="payment-notice"><center>
                            Please note: your payment will not be processed until your chapter is approved.<br>
                            After it is approved, your payment will be processed and we will send you your MOMS Club Chapter Manual.<br>
                            There are no refunds after the payment has been processed.
                        </center></div>
                        <div class="col-md-12 mb-3" style="color: #dc3545;" id="submit-warning"><center>
                            Page will automatically re-direct after application submission with success or error message.<br>
                            DO NOT refresh page after clicking "Submit Payment" or you may be charged multiple times!
                        </center></div>
                        <button type="submit" class="btn btn-primary bg-gradient mb-2" id="submit-btn">
                            <i class="bi bi-chevron-double-right me-2"></i>{{ __('Submit Application') }}
                        </button>
                    </div>

                </form>
                 <div class="col-md-12 mt-3" id="authorize-notice" style="font-size: 0.8em">
                    <img src="{{ config('settings.base_url') }}images/authorize-net-seal.jpg" alt="authorize-net-seal" style="float: left; margin-right: 20px; width: 115px; height: 115px;">
                    <p>You can pay with confidence! We have partnered with <a href="http://www.authorize.net" target="blank">Authorize.Net</a>, a leading payment gateway since 1996,
                    to accept credit cards and electronic check payments safely and securely for our chapters.<br>
                    <br>
                    The Authorize.Net Payment Gateway manages the complex routing of sensitive customer information through the electronic check and credit card processing networks.
                    See an <a href="http://www.authorize.net/resources/howitworksdiagram/" target="blank">online payments diagram</a> to see how it works.</p>
                </div>

            </div>
      </div>
    <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </div>
    <!-- /.col -->
    </div>

</div>
<!-- /.container- -->
@endsection
@section('customscript')

<script>
// ─── UTILITY FUNCTIONS (defined first so they're available everywhere) ───────

function calculateTotal() {
    var newchap = parseFloat(document.getElementById('newchap').value.replace('$', ''));
    var fee = parseFloat(document.getElementById('fee').value.replace('$', ''));
    var total = newchap + fee;
    document.getElementById('total').value = '$' + total.toFixed(2);
}

function hidePaymentSection() {
    document.getElementById('payment-section').style.display = 'none';
    document.getElementById('payment-notice').style.display = 'none';
    document.getElementById('authorize-notice').style.display = 'none';
    document.getElementById('submit-btn').innerHTML = '<i class="bi bi-chevron-double-right me-2"></i>Submit Application';
    document.getElementById('submit-warning').innerHTML = '<center>Page will automatically re-direct after application submission with success or error message.<br>Please do not refresh the page after clicking "Submit Application"!</center>';

    // Remove required from payment fields
    ['card_number','expiration_date','cvv','first_name','last_name','email','address','city','state','zip'].forEach(id => {
        const f = document.getElementById(id);
        if (f) { f.removeAttribute('required'); f.value = ''; }
    });
}

function showPaymentSection() {
    document.getElementById('payment-section').style.display = 'block';
    document.getElementById('payment-notice').style.display = 'block';
    document.getElementById('authorize-notice').style.display = 'block';
    document.getElementById('submit-btn').innerHTML = '<i class="bi bi-chevron-double-right me-2"></i>Submit Payment';
    document.getElementById('submit-warning').innerHTML = '<center>Page will automatically re-direct after application submission with success or error message.<br>DO NOT refresh page after clicking "Submit Payment" or you may be charged multiple times!</center>';

    // Restore required on payment fields
    ['card_number','expiration_date','cvv','first_name','last_name','email','address','city','state','zip'].forEach(id => {
        const f = document.getElementById(id);
        if (f) f.setAttribute('required', 'required');
    });

    // Restore fee display fields
    document.getElementById('newchap').value = '$30.00';
    document.getElementById('fee').value = '$5.00';
    calculateTotal();
}

// ─── DOM READY ────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function() {

    // Calculate total on page load
    calculateTotal();

   // ── Sistered toggle ──
    const sisteredToggle = document.getElementById('sisteredToggle');

    function handleSisteredToggle() {
        if (sisteredToggle.checked) {
            $('#sisteredByField').slideDown(300);
            $('#hearAboutByField').slideUp(300);
            $('#ch_sisteredby').prop('required', true);
            $('#ch_hearabout').prop('required', false).val('');
        } else {
            $('#sisteredByField').slideUp(300);
            $('#hearAboutByField').slideDown(300);
            $('#ch_sisteredby').prop('required', false).val('');
            $('#ch_hearabout').prop('required', true);
        }
    }

    // Set initial state
    handleSisteredToggle();

    // Listen for changes
    sisteredToggle.addEventListener('change', handleSisteredToggle);

    // // ── Chapter state / country dropdown ──
    // const stateDropdown = document.getElementById('ch_state');
    // const countryContainer = document.getElementById('country-container');
    // const countrySelect = document.getElementById('ch_country');

    // if (stateDropdown && countryContainer && countrySelect) {
    //     toggleCountryField();
    //     stateDropdown.addEventListener('change', toggleCountryField);

    //     function toggleCountryField() {
    //         const selectedStateId = parseInt(stateDropdown.value) || 0;
    //         const specialStates = [52, 53, 54, 55];

    //         if (specialStates.includes(selectedStateId)) {
    //             countryContainer.style.display = 'flex';
    //             countrySelect.setAttribute('required', 'required');
    //             hidePaymentSection();
    //         } else {
    //             countryContainer.style.display = 'none';
    //             countrySelect.removeAttribute('required');
    //             countrySelect.value = "";
    //             showPaymentSection();
    //         }
    //     }
    // }

    // // ── Founder/president state / country dropdown ──
    // const statePreDropdown = document.getElementById('ch_pre_state');
    // const countryPreContainer = document.getElementById('ch_pre_country-container');
    // const countryPreSelect = document.getElementById('ch_pre_country');

    // if (statePreDropdown && countryPreContainer && countryPreSelect) {
    //     togglePreCountryField();
    //     statePreDropdown.addEventListener('change', togglePreCountryField);

    //     function togglePreCountryField() {
    //         const selectedPreStateId = parseInt(statePreDropdown.value) || 0;
    //         const specialPreStates = [52, 53, 54, 55];

    //         if (specialPreStates.includes(selectedPreStateId)) {
    //             countryPreContainer.style.display = 'flex';
    //             countryPreSelect.setAttribute('required', 'required');
    //         } else {
    //             countryPreContainer.style.display = 'none';
    //             countryPreSelect.removeAttribute('required');
    //             countryPreSelect.value = "";
    //         }
    //     }
    // }

    // ── Chapter state / country dropdown (also mirrors to president fields) ──
const stateDropdown = document.getElementById('ch_state');
const countryContainer = document.getElementById('country-container');
const countrySelect = document.getElementById('ch_country');
const specialStates = [52, 53, 54, 55];

if (stateDropdown && countryContainer && countrySelect) {
    toggleCountryField();
    stateDropdown.addEventListener('change', toggleCountryField);

    function toggleCountryField() {
        const selectedStateId = parseInt(stateDropdown.value) || 0;
        const selectedStateText = stateDropdown.options[stateDropdown.selectedIndex].text;

        // Mirror state to president display + hidden input
        const preStateDisplay = document.getElementById('ch_pre_state_display');
        const preStateHidden = document.getElementById('ch_pre_state');
        if (preStateDisplay) preStateDisplay.value = stateDropdown.value ? selectedStateText : '';
        if (preStateHidden) preStateHidden.value = stateDropdown.value || '';

        if (specialStates.includes(selectedStateId)) {
            // Show chapter country field
            countryContainer.style.display = 'flex';
            countrySelect.setAttribute('required', 'required');

            // Show president country display field
            const preCountryContainer = document.getElementById('ch_pre_country-container');
            if (preCountryContainer) preCountryContainer.style.display = 'flex';

            // Hide payment section
            hidePaymentSection();
        } else {
            // Hide chapter country field
            countryContainer.style.display = 'none';
            countrySelect.removeAttribute('required');
            countrySelect.value = '';

            // Hide president country display field and clear it
            const preCountryContainer = document.getElementById('ch_pre_country-container');
            if (preCountryContainer) preCountryContainer.style.display = 'none';
            const preCountryDisplay = document.getElementById('ch_pre_country_display');
            const preCountryHidden = document.getElementById('ch_pre_country');
            if (preCountryDisplay) preCountryDisplay.value = '';
            if (preCountryHidden) preCountryHidden.value = '';

            // Show payment section
            showPaymentSection();
        }
    }

    // Also mirror country selection to president fields when country changes
    countrySelect.addEventListener('change', function() {
        const selectedCountryText = countrySelect.options[countrySelect.selectedIndex].text;
        const preCountryDisplay = document.getElementById('ch_pre_country_display');
        const preCountryHidden = document.getElementById('ch_pre_country');
        if (preCountryDisplay) preCountryDisplay.value = countrySelect.value ? selectedCountryText : '';
        if (preCountryHidden) preCountryHidden.value = countrySelect.value || '';
    });
}

function checkDuplicateEmail(email, id) {
        $.ajax({
            url: '{{ url("/checkemail/") }}' + '/' + email,
            type: "GET",
            success: function(result) {
                if (result.exists) {
                    alert('This Email already used in the system. Please try with new one.');
                    $("#" + id).val('');
                    $("#" + id).focus();
                }
            },
            error: function(jqXHR, exception) {
                console.error("Error checking email: ", exception);
            }
        });
    }

    // ── Password match check ──
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

    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);

    // ── Cardholder email validation ──
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

}); // end DOMContentLoaded
</script>
@endsection
