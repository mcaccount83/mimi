@extends('layouts.public_theme')

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" method="POST" action='{{ route("public.updatenewinquiry") }}'>
                        @csrf
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                        <div class="col-md-12">
                            <div class="card card-widget widget-user">
                                <div class="widget-user-header bg-primary">
                                    <div class="widget-user-image">
                                        <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12"><br><br></div>
                                    <h2 class="text-center">New Chapter Inquiry</h2>
                                    </h2>
                                    <p class="description text-center">
                                        If you wish to join the chapter near you, please fill out the form below. The information will help us determine if there is a chapter in your area.<br>
                                        Providing accurate and complete information will help to ensure our coordinators are able to connect you with the correct chapter. Please provide as much information as possible and spell out city/town names instead of using abbreviations.<br>
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


                <h3 class="profile-username">Inquiry Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 mb-1 col-form-label">Name:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="inquiryFirstName" id="inquiryFirstName" class="form-control"  required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="inquiryLastName" id="inquiryLastName" class="form-control" required placeholder="Last Name" >
                                </div>
                            </div>

                                <div class="form-group row mt-1">
                                    <label class="col-sm-4 col-form-label">Where are you looking for a chapter:</label>
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
                                    <label class="col-sm-4 col-form-label">Which Country:</label>
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

                                <label class="col-sm-2 mb-1 col-form-label">Contact:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="inquiryEmail" id="inquiryEmail" class="form-control"  required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="inquiryPhone" id="inquiryPhone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label">Address:</label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="inquiryAddress" id="inquiryAddress" class="form-control" placeholder="Address" required >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="col-sm-3 mb-1">
                                <input type="text" name="inquiryCity" id="inquiryCity" class="form-control" placeholder="City" required >
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="inquiryState" id="inquiryState" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}">
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="inquiryZip" id="inquiryZip" class="form-control" placeholder="Zip" required >
                                </div>
                            <div class="col-sm-2 mb-1" id="inquiryCountry-container" style="display: none;">
                                <select name="inquiryCountry" id="inquiryCountry" class="form-control" style="width: 100%;" required>
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

                         <label class="col-sm-2 mb-1 col-form-label">County:</label>
                        <div class="col-sm-10 mb-1">
                        <input type="text" name="inquiryCounty" id="inquiryCounty" class="form-control" required placeholder="County" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 mb-1 col-form-label">Township:</label>
                        <div class="col-sm-10 mb-1">
                        <input type="text" name="inquiryTownship" id="inquiryTownship" class="form-control"  placeholder="Township" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 mb-1 col-form-label">Area/Neighborhood:</label>
                        <div class="col-sm-10 mb-1">
                        <input type="text" name="inquiryArea" id="inquiryArea" class="form-control"  placeholder="Area/Neighborhood" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 mb-1 col-form-label">School District:</label>
                        <div class="col-sm-10 mb-1">
                        <input type="text" name="inquirySchool" id="inquirySchool" class="form-control"  placeholder="School District" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 mb-1 col-form-label">Additional Comments</label>
                        <div class="col-sm-10 mb-1">
                            <textarea name="inquiryComments" id="inquiryComments" class="form-control" rows="4" maxlength="520"></textarea>
                            <small class="form-text text-muted">
                        <span id="char-count-comments">0</span>/520 characters
                            </small>
                        </div>
                    </div>

                        </div>
                    </div>

                    <hr>

                    <div class="card-body text-center">
                             <div class="col-md-12" ><center>
                                   The information provided below is only used to try to find you the closest MOMS Club chapter in your area, never for advertising or promotional purposes.
                                </center></div>
                            <br>

                                <button type="submit" class="btn btn-primary"><i class="fas fa-share" ></i>&nbsp;{{ __('Submit Inquiry') }}</button>
                        </div>
  </form>
    <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </div>
    <!-- /.col -->
    </div>

<div class="col-md-12" style="font-size: 0.8em"></div>


</div>
<!-- /.container- -->
@endsection
@section('customscript')

<script>
document.addEventListener('DOMContentLoaded', function() {
     // Define all textareas and their corresponding counter IDs with their max lengths
    const textareaConfigs = [
        { name: 'inquiryComments', counterId: 'char-count-comments', maxLength: 520 },
    ];

    // Loop through each textarea configuration
    textareaConfigs.forEach(config => {
        // Select either input or textarea based on config
        const selector = config.isInput ? `input[name="${config.name}"]` : `textarea[name="${config.name}"]`;
        const field = document.querySelector(selector);
        const charCount = document.getElementById(config.counterId);

        if (field && charCount) {
            field.addEventListener('input', function() {
                const currentLength = this.value.length;
                charCount.textContent = currentLength;

                // Change color based on usage
                const parent = charCount.parentElement;
                if (currentLength >= config.maxLength * 0.9) { // 90% full
                    parent.className = 'form-text text-danger';
                } else if (currentLength >= config.maxLength * 0.8) { // 80% full
                    parent.className = 'form-text text-warning';
                } else {
                    parent.className = 'form-text text-muted';
                }
            });
        }
    });

    // Chapter state and country
    const stateDropdown = document.getElementById('ch_state');
    const countryContainer = document.getElementById('country-container');
    const countrySelect = document.getElementById('ch_country');

    // President state and country
    const statePreDropdown = document.getElementById('inquiryState');
    const countryPreContainer = document.getElementById('inquiryCountry-container');
    const countryPreSelect = document.getElementById('inquiryCountry');

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

            } else {
                // Hide country field
                countryContainer.style.display = 'none';
                countrySelect.removeAttribute('required');
                countrySelect.value = "";
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

});

</script>
@endsection
