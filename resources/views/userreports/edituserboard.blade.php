@extends('layouts.coordinator_theme')

@section('page_title', 'User Details')
@section('breadcrumb', 'Edit User')
<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("userreports.updateuserboard", $userDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

        <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                    <h3 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                    <p class="mb-0">{{ $chDetails->confname }} Conference, {{ $chDetails->regname }} Region
                  </p>
                    </div>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item mt-2">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <label class="col-form-label mb-0 me-2">Position:</label>
                            <div >
                                        {{$bdPosition}}
                                <label ></label>
                            </div>
                          </div>

                          <div class="row mb-3">
                            <label class="col-form-label col-sm-6">Active Status:</label>
                            <div class="col-sm-6">
                                <select id="status" name="status" class="form-control float-end text-end"required>
                                    @foreach($AllUserStatus as $status)
                                        <option value="{{$status->id}}"
                                            @if($userDetails->is_active == $status->id) selected @endif>
                                            {{$status->user_status}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                           <div class="row mb-3">
                            <label class="col-form-label col-sm-6">User Type:</label>
                            <div class="col-sm-6">
                                <select id="type" name="type" class="form-control float-end text-end"required>
                                    @foreach($AllUserType as $type)
                                        <option value="{{$type->id}}"
                                            @if($userDetails->type_id == $type->id) selected @endif>
                                            {{$type->user_type}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                           <div class="row mb-3">
                            <label class="col-form-label col-sm-6">Admin Role:</label>
                            <div class="col-sm-6">
                                <select id="role" name="role" class="form-control float-end text-end"required>
                                    @foreach($AllAdminRole as $role)
                                        <option value="{{$role->id}}"
                                            @if($userDetails->is_admin == $role->id) selected @endif>
                                            {{$role->admin_role}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </li>
                    <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                    <li class="list-group-item mt-2" id="display_corlist" ></li>
               </ul>

               <div class="text-center">
                      @if ($chDetails->active_status == 1 )
                          <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                      @elseif ($chDetails->active_status == 2)
                        <b><span style="color: #ff851b;">Chapter is PENDING</span></b>
                      @elseif ($chDetails->active_status == 3)
                        <b><span style="color: #dc3545;">Chapter was NOT APPROVED</span></b><br>
                          Declined Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
                      @elseif ($chDetails->active_status == 0)
                          <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                          Disband Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
                      @endif
                  </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                <h3 class="profile-username">User Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                         <!-- /.form group -->
                        <div class="row mb-3">

                            <label class="col-sm-2 mb-3 col-form-label">Name:</label>
                            <div class="col-sm-5 mb-3">
                                <input type="text" name="fname" id="fname" class="form-control" required placeholder="First Name" value="{{ $bdDetails->first_name }}">
                            </div>
                            <div class="col-sm-5 mb-3">
                                <input type="text" name="lname" id="lname" class="form-control" required placeholder="Last Name" value="{{ $bdDetails->last_name }}">
                            </div>
                            <label class="col-sm-2 mb-3 col-form-label">Email/Phone:</label>
                            <div class="col-sm-5 mb-3">
                        <input type="text" name="email" id="email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" required placeholder="Email Address" value="{{ $bdDetails->email }}">

                        </div>
                            <div class="col-sm-5 mb-3">
                                <input type="text" name="phone" id="phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required placeholder="Phone Number" value="{{ $bdDetails->phone }}">
                           </div>
                            <label class="col-sm-2 mb-3 col-form-label">Address:</label>
                            <div class="col-sm-10 mb-3">
                                <input type="text" name="street" id="street" class="form-control" placeholder="Address" required value="{{ $bdDetails->street_address }}">

                            </div>
                            <label class="col-sm-2 mb-3 col-form-label"><br></label>
                            <div class="col-sm-3 mb-3">
                                    <input type="text" name="city" id="city" class="form-control" required placeholder="City" value="{{ $bdDetails->city }}">
                                </div>
                                <div class="col-sm-3 mb-32">
                                    <select name="state" id="state" class="form-control" style="width: 100%;" required>
                                @foreach($allStates as $state)
                                    <option value="{{$state->id}}" {{ $bdDetails->state_id == $state->id ? 'selected' : '' }}>
                                        {{$state->state_long_name}}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                             <div class="col-sm-2 mb-3">
                                    <input type="text" name="zip" id="zip" class="form-control" value="{{ $bdDetails->zip }}"  required placeholder="Zip">
                                </div>
                            <div class="col-sm-2 mb-3" id="country-container" style="display: {{ $bdDetails->country_id ? 'block' : 'none' }};">
                                <select name="country" id="country" class="form-control" style="width: 100%;" required>
                                    @foreach($allCountries as $country)
                                        <option value="{{$country->id}}" {{ $bdDetails->country_id == $country->id ? 'selected' : '' }} read only>
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
            <div class="card-body text-center mt-3">
                <button type="submit" class="btn btn-primary bg-gradient mb-2" onclick="return validateEmailsBeforeSubmit();"><i class="bi bi-floppy-fill me-2"></i>Save User Information</button>
                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ request('return') }}'">
                        <i class="bi bi-chevron-double-left me-2"></i>Back to List
</button>
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
    const stateDropdown = document.getElementById('state');
    const countryContainer = document.getElementById('country-container');
    const countrySelect = document.getElementById('country');

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

</script>
@endsection
