@extends('layouts.coordinator_theme')

@section('page_title', 'Board Details')
@section('breadcrumb', 'Edit Board')
<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("userreports.updatenewboard", $chDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

        <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region
                <br>
                EIN: {{$chDetails->ein}}
                </p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">

                <b>Boundaries:</b> {{$chDetails->territory}}
                    </li>
                    <li class="list-group-item">
                        <b>Re-Registration Dues:</b><span class="float-right">
                            @if ($chPayments->rereg_members)
                                <b>{{ $chPayments->rereg_members }} Members</b> on <b><span class="date-mask">{{ $chPayments->rereg_date }}</span></b>
                            @else
                                No Payment Recorded
                            @endif
                        </span><br>
                        <b>M2M Donation:</b><span class="float-right">
                            @if ($chPayments->m2m_donation)
                                <b>${{ $chPayments->m2m_donation }}</b> on <b><span class="date-mask">{{ $chPayments->m2m_date }}</span></b>
                            @else
                                No Donation Recorded
                            @endif
                        </span><br>
                        <b>Sustaining Chapter Donation: </b><span class="float-right">
                            @if ($chPayments->sustaining_donation)
                                <b>${{ $chPayments->sustaining_donation }}</b> on <b><span class="date-mask">{{ $chPayments->sustaining_date }}</span></b>
                            @else
                                No Donation Recorded
                            @endif
                        </span><br>
                    </li>
                    <li class="list-group-item">
                        <b>Founded:</b> <span class="float-right">{{ $startMonthName }} {{ $chDetails->start_year }}</span>
                        <br>
                        <b>Formerly Known As:</b> <span class="float-right">{{ $chDetails->former_name }}</span>
                        <br>
                        <b>Sistered By:</b> <span class="float-right">{{ $chDetails->sistered_by }}</span>
                    </li>
                    <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                    <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
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
                <h3 class="profile-username">Board Member Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                         <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-2 mb-3 col-form-label">President:</label>
                            <div class="col-sm-5 mb-3">
                            <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control" required placeholder="First Name" >
                            </div>
                            <div class="col-sm-5 mb-3">
                            <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" required placeholder="Last Name">
                            </div>
                            <label class="col-sm-2 mb-3 col-form-label"></label>
                            <div class="col-sm-5 mb-3">
                            <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  required placeholder="Email Address" >
                            </div>
                            <div class="col-sm-5 mb-3">
                            <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required placeholder="Phone Number" >
                            </div>
                            <label class="col-sm-2 mb-3 col-form-label"></label>
                            <div class="col-sm-10 mb-3">
                            <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" placeholder="Address"  required >
                            </div>
                            <label class="col-sm-2 mb-3 col-form-label"><br></label>
                            <div class="col-sm-3 mb-3">
                             <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <select name="ch_pre_state" id="ch_pre_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}">
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-3">
                                    <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control"   required placeholder="Zip">
                                </div>
                                <div class="col-sm-2 mb-3" id="ch_pre_country-container" style="display: none;">
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
                <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return validateEmailsBeforeSubmit();"><i class="fas fa-save mr-2"></i>Save Board Member</button>
                @if($chDetails->active_status == 1)
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('userreports.nopresident') }}'"><i class="fas fa-reply mr-2"></i>Back to No President List</button>
                @else
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('userreports.nopresidentinactive') }}'"><i class="fas fa-reply mr-2"></i>Back to No President List</button>
                @endif
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
