@extends('layouts.public_theme')

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" method="POST" action='{{ route("public.updatenewcoordinator") }}'>
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
                                    <h2 class="text-center">New Coordinator Application</h2>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>

                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                 <!-- /.card-header -->

                     <h3 class="profile-username">Personal Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 mb-1 col-form-label">Name:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="cd_fname" id="cd_fname" class="form-control"  required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="cd_lname" id="cd_lname" class="form-control" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label">Contact:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="cd_email" id="cd_email" class="form-control" required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="cd_phone" id="cd_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label">Address:</label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="cd_street" id="cd_street" class="form-control" placeholder="Address" required >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="col-sm-3 mb-1">
                                <input type="text" name="cd_city" id="cd_city" class="form-control" placeholder="City" required >
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="cd_state" id="cd_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}">
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="cd_zip" id="cd_zip" class="form-control" placeholder="Zip" required >
                                </div>
                            <div class="col-sm-2 mb-1" id="cd_country-container" style="display: none;">
                                <select name="cd_country" id="cd_country" class="form-control" style="width: 100%;" required>
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
                        <label class="col-sm-2 mb-1 col-form-label">Birthday:</label>
                                <div class="col-sm-3 mb-1">
                                    <select name="cd_bmonth" id="cd_bmonth" class="form-control" style="width: 100%;" required>
                                        <option value="">Select Month</option>
                                        @foreach($allMonths as $month)
                                        <option value="{{$month->id}}">
                                            {{$month->month_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <input type="text" name="cd_bday" id="cd_bday" class="form-control" required placeholder="Day" >
                                </div>
                            </div>

                        </div>
                    </div>

                         <hr>

                           <h3 class="profile-username">Volunteer Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                              <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Home Chapter:</label>
                                <div class="col-sm-8">
                                    <input type="text" name="home_chapter" id="home_chapter" class="form-control" required>
                                </div>
                            </div>
                             <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Chapter State:</label>
                                <div class="col-sm-8">
                                    <input type="text" name="home_state" id="home_state" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">How long have you been a MOMS Club Member?</label>
                                <div class="col-sm-8">
                                    <input type="text" name="start_date" id="start_date" class="form-control" maxlength="25" required>
                                    <small class="form-text text-muted">
<span id="char-count-date">0</span>/25 characters
        </small>
                                </div>
                            </div>

                            <div class="form-group row">
    <label class="col-sm-4 col-form-label">What jobs/offices have you held with the chapter? What programs/activities have you started or led?</label>
    <div class="col-sm-8">
        <textarea name="jobs_programs" class="form-control" rows="4" maxlength="520" required></textarea>
        <small class="form-text text-muted">
<span id="char-count-jobs">0</span>/520 characters
        </small>
    </div>
</div>
                             <div class="form-group row">
                                <label class="col-sm-4 col-form-label">How has the MOMS Club helped you?</label>
                                <div class="col-sm-8">
                                    <textarea name="helped_me" class="form-control" rows="4" required></textarea>
                                    <small class="form-text text-muted">
<span id="char-count-helped">0</span>/520 characters
        </small>
                                </div>
                            </div>

                             <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Did you experience any problems during your time in the MOMS Club? If so, how were those problems resolved or what did you learn from them?</label>
                                <div class="col-sm-8">
                                    <textarea name="problems" class="form-control" rows="4" required></textarea>
                                    <small class="form-text text-muted">
<span id="char-count-problems">0</span>/520 characters
        </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Why do you want to be an International MOMS Club Volunteer?</label>
                                <div class="col-sm-8">
                                    <textarea name="why_volunteer" class="form-control" rows="4" required></textarea>
                                    <small class="form-text text-muted">
<span id="char-count-volunteer">0</span>/520 characters
        </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Do you volunteer for anyone else? Please list all your volunteer positions and when you did them?</label>
                                <div class="col-sm-8">
                                    <textarea name="other_volunteer" class="form-control" rows="4" required></textarea>
                                    <small class="form-text text-muted">
<span id="char-count-other">0</span>/520 characters
        </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Do you have any special skills/talents/Hobbies (ie: other languages, proficient in any computer programs)?</label>
                                <div class="col-sm-8">
                                    <textarea name="special_skills" class="form-control" rows="4" required></textarea>
                                    <small class="form-text text-muted">
<span id="char-count-skills">0</span>/520 characters
        </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">What have you enjoyed most in previous volunteer experiences? Least?</label>
                                <div class="col-sm-8">
                                    <textarea name="enjoy_volunteering" class="form-control" rows="4" required></textarea>
                                    <small class="form-text text-muted">
<span id="char-count-enjoy">0</span>/520 characters
        </small>
                                </div>
                            </div>

                             <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Referred by (if applicable):</label>
                                <div class="col-sm-8">
                                    <input type="text" name="referred_by" id="referred_by" class="form-control"  required>
                                </div>
                            </div>


                        <div class="card-body text-center">
                            <div class="col-md-12" style="color: red;"><center>Page will automatically re-direct after application submission.</div>
                            <br>
                                <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-share mr-2"></i>Submit Application</button>
                        </div>

                    </form>

                        </div>
                    </div>
          <!-- /.card body -->
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
        { name: 'start_date', counterId: 'char-count-date', maxLength: 25, isInput: true },
        { name: 'jobs_programs', counterId: 'char-count-jobs', maxLength: 520 },
        { name: 'helped_me', counterId: 'char-count-helped', maxLength: 520 },
        { name: 'problems', counterId: 'char-count-problems', maxLength: 520 },
        { name: 'why_volunteer', counterId: 'char-count-volunteer', maxLength: 520 },
        { name: 'other_volunteer', counterId: 'char-count-other', maxLength: 520 },
        { name: 'special_skills', counterId: 'char-count-skills', maxLength: 520 },
        { name: 'enjoy_volunteering', counterId: 'char-count-enjoy', maxLength: 520 }
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
    const stateDropdown = document.getElementById('cd_state');
    const countryContainer = document.getElementById('cd_country-container');
    const countrySelect = document.getElementById('cd_country');

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
                countryContainer.style.display = 'flex'; // or 'block' depending on your layout
                countrySelect.setAttribute('required', 'required');
            } else {
                countryContainer.style.display = 'none';
                countrySelect.removeAttribute('required');
                // Optionally clear the country selection when hidden
                countrySelect.value = "";
            }
        }
    }
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
