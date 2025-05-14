@extends('layouts.public_theme')

<style>

</style>

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
                                    @php
                                        $thisDate = \Illuminate\Support\Carbon::now();
                                    @endphp
                                    <div class="col-md-12"><br><br></div>
                                    <h2 class="text-center">New Chapter Application</h2>
                                    </h2>
                                    {{-- <p class="description text-center">
                                        All chapters are in PENDING status until reviewed by our Coordintaor Team. After review, you will receive an email communication from your Coordinator.<br>
                                        Log in at at <a href="http://momsclub.org/mimi" target="_blank">http://momsclub.org/mimi</a> with the email address and password you registered with to check your application status and view contact information for your assinged Coordinator.<br>
                                    </p> --}}

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
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control" placeholder="City" required >
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_pre_state" class="form-control" style="width: 100%;" required>
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
                            <div class="col-md-12" style="color: red;"><center>Page will automatically re-direct after application submission with success or error message.<br>
                                DO NOT refresh page after clicking "Submit Payment" or you may be charged multiple times!</center></div>
                            <br>

                                <button type="submit" class="btn btn-primary"><i class="fas fa-share" ></i>&nbsp;{{ __('Submit Application') }}</button>
                        </div>
                    </form>
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
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const matchMessage = document.getElementById('password-match-message');

    function checkPasswordMatch() {
        if (confirmPassword.value === '') {
            matchMessage.textContent = '';
            matchMessage.className = 'form-text';
            return;
        }

        if (password.value === confirmPassword.value) {
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

</script>
@endsection
