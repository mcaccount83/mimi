@extends('layouts.coordinator_theme')

@section('page_title', 'New Chapter Details')
@section('breadcrumb', 'New Chapter')
<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("chapters.updatenew") }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username ">Chapter Information</h3>
                  <div class="form-group row">
                    <label class="col-sm-4 col-form-label">MOMS Club of:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_name" id="ch_name" class="form-control" placeholder="Chapter Name" required>
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

                 <div class="form-group row mb-1" id="country-container" style="display: none;">
                    <label class="col-sm-4 col-form-label">Country:</label>
                    <div class="col-sm-8">
                        <select id="ch_country" name="ch_country" class="form-control" required>
                            <option value="">Select Country</option>
                            @foreach($allCountries as $country)
                                <option value="{{$country->id}}">
                                    {{$country->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mt-1">
                    <label class="col-sm-4 col-form-label">Region:</label>
                    <div class="col-sm-8">
                        <select id="ch_region" name="ch_region" class="form-control" required>
                            <option value="">Select Region</option>
                            @foreach($allRegions as $region)
                                <option value="{{$region->id}}">
                                    {{$region->long_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">EIN:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_ein" id="ch_ein" class="form-control" placeholder="EIN Number">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Boundaries:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_boundariesterry" id="ch_boundariesterry" class="form-control" placeholder="Boundaries" required>
                    </div>
                </div>

                <div class="form-group row mt-1">
                    <label class="col-sm-4 col-form-label">Status:</label>
                    <div class="col-sm-8">
                        <select id="ch_status" name="ch_status" class="form-control" required>
                            <option value="">Select Status</option>
                            @foreach($allStatuses as $status)
                                <option value="{{$status->id}}">
                                    {{$status->chapter_status}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Inquiries Email:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" placeholder="Inquiries Email" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Primary Coordinator</label>
                    <div class="col-sm-8">
                        <select name="ch_primarycor" id="ch_primarycor" class="form-control select2-bs4" style="width: 100%;" required>
                            <option value="">Select Primary Coordinator</option>
                            @foreach($pcDetails as $coordinator)
                            <option value="{{ $coordinator['cid'] }}" data-region-id="{{ $coordinator['regid'] }}">
                                {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                  </div>
                  <div id="display_corlist"> </div>

                </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                <h3 class="profile-username">Board Member Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                         <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-2 mb-1 col-form-label">Founder:</label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control" required placeholder="First Name" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" required placeholder="Last Name">
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  required placeholder="Email Address" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required placeholder="Phone Number" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-10 mb-1">
                            <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" placeholder="Address"  required >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"><br></label>
                            <div class="col-sm-3 mb-1">
                             <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control"  required placeholder="City">
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
                                    <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control"   required placeholder="Zip">
                                </div>
                                <div class="col-sm-2" id="ch_pre_country-container" style="display: none;">
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

                        </div>
                    </div>
                </div>
              <!-- /.card-body -->
                        </div>
            <!-- /.card -->
                      </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center">
                {{-- <button type="button" class="btn bg-gradient-primary mb-3" onclick="showChapterSetupModalBlank()"><i class="fas fa-envelope mr-2"></i>Send Chapter Startup Email</button> --}}
                <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return validateEmailsBeforeSubmit();"><i class="fas fa-save mr-2"></i>Save New Chapter</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chaplistpending') }}'"><i class="fas fa-reply mr-2"></i>Back to Pending Chapter List</button>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>

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
});

    // Call the function when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
    });

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
