@extends('layouts.mimi_theme')

@section('page_title', 'User Reports')
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
                <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                    <h3 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                    <p class="mb-0">{{ $conferenceDescription }} Conference, {{ $conferenceDescription }} Region
                    <br>
                  EIN: {{$chDetails->ein}}
                  </p>
                </div>

                  <ul class="list-group list-group-flush mb-2">
                      <li class="list-group-item">
                       <div class="row">
                            <div class="col-auto fw-bold">Re-Registration Dues:</div>
                            <div class="col text-end">
                                @if ($chPayments->rereg_members)
                                    <b>{{ $chPayments->rereg_members }} Members</b> on <b><span class="date-mask">{{ $chPayments->rereg_date }}</span></b>
                                @else
                                    No Payment Recorded
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">M2M Donation:</div>
                            <div class="col text-end">
                            @if ($chPayments->m2m_donation)
                                <b>${{ $chPayments->m2m_donation }}</b> on <b><span class="date-mask">{{ $chPayments->m2m_date }}</span></b>
                            @else
                                No Donation Recorded
                            @endif
                         </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Sustaining Chapter Donation:</div>
                            <div class="col text-end">
                            @if ($chPayments->sustaining_donation)
                                <b>${{ $chPayments->sustaining_donation }}</b> on <b><span class="date-mask">{{ $chPayments->sustaining_date }}</span></b>
                            @else
                                No Donation Recorded
                            @endif
                       </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-auto fw-bold">Founded:</div>
                            <div class="col text-end">
                                {{ $startMonthName }} {{ $chDetails->start_year }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Formerly Known As:</div>
                            <div class="col text-end">
                                {{ $chDetails->former_name }}
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Sistered By:</div>
                            <div class="col text-end">
                                {{ $chDetails->sistered_by }}
                                </div>
                        </div>
                    </li>
                   <li class="list-group-item">
                            @include('coordinators.partials.coordinatorlist')
                        </li>
                        <li class="list-group-item mt-3">
                            @include('coordinators.partials.chapterstatus')
                        </li>
                  </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body">
                                        <div class="card-header bg-transparent border-0">

                <h3 class="profile-username">Board Member Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                         <!-- /.form group -->
                        <div class="row mb-3">
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
                </div>
              <!-- /.card-body -->
                        </div>
            <!-- /.card -->
                      </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center mt-3">
                <button type="submit" class="btn btn-primary bg-gradient mb-2" onclick="return validateEmailsBeforeSubmit();"><i class="bi bi-floppy-fill me-2"></i>Save Board Member</button>
                @if($chDetails->active_status == 1)
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('userreports.nopresident') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-fill-gear me-2"></i>Back to Active No President List</button>
                @else
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('userreports.nopresidentinactive') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-fill-gear me-2"></i>Back to Non-Active No President List</button>
                @endif
   </div>
            </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
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
