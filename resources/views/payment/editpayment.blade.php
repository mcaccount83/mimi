@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Payments & Donations')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

.custom-span {
    border: none !important;
    background-color: transparent !important;
    padding: 0.375rem 0 !important; /* Match the vertical padding of form-control */
    box-shadow: none !important;
}


</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("payment.updatepayment", $chDetails->id) }}'>
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
                          <b>Re-Registration Dues:</b><span class="float-right">
                              @if ($chPayments->rereg_members	)
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
                          </span>
                          <br>
                      </li>
                      <li class="list-group-item">
                        <b>Founded:</b><span class="float-right">{{ $startMonthName }} {{ $chDetails->start_year }}</span>
                           <br>
                          <b>Status:</b><span class="float-right ">{{ $chapterStatus }}</span>
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
                <h3 class="profile-username">Payment & Donation Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <h5>Re-Registration Payment</h5>
                                </div>
                                <label class="col-sm-2 col-form-label">Date Received:</label>
                                <div class="col-sm-3">
                                    <input type="date" name="PaymentDate" id="PaymentDate"class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask >
                                </div>
                                <label class="col-sm-2 ml-1 col-form-label">Members Paid For:</label>
                                <div class="col-sm-3">
                                    <input type="number" name="members" id="members" onKeyPress="if(this.value.length==9) return false;" class="form-control"  />
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label class="col-sm-2 col-form-label">Re-Registration Notes:</label>
                                <div class="col-sm-8">
                                  <input type="text" name="ch_regnotes" id="ch_regnotes" class="form-control"  value="{{ $chPayments->rereg_notes}}" >
                                </div>
                            </div>
                            <div class="form-group row ">
                                <div class="col-md-12 d-flex align-items-center">
                                    <label class="col-form-label mr-2">Send Payment Received Notification to Chapter:</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="ch_notify" id="ch_notify" class="custom-control-input" >
                                            <label class="custom-control-label" for="ch_notify"></label>
                                        </div>
                                    </div>
                                <div class="col-md-12 d-flex align-items-center">
                                    <label class="col-form-label mr-2">Waive Late Fee:</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="ch_waive_late" id="ch_waive_late" class="custom-control-input" {{$chPayments->rereg_waivelate == 1 ? 'checked' : ''}}>
                                            <label class="custom-control-label" for="ch_waive_late"></label>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            <!-- /.form group -->
                            <div class="form-group row mb-1">
                                <div class="col-sm-12">
                                    <h5>M2M Fund Donation Payment</h5>
                                </div>
                                <label class="col-sm-2 col-form-label">Date Received:</label>
                                <div class="col-sm-3">
                                    <input type="date" name="M2MPaymentDate" id="M2MPaymentDate"class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask />
                                </div>
                                <label class="col-sm-2 ml-1 col-form-label">Donation Amount:</label>
                                <div class="col-sm-3">
                                    <div class="input-group row">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="text" name="m2m" id="m2m" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <div class="col-md-12 d-flex align-items-center">
                                    <label class="col-form-label mr-2">Send M2M Donation Thank You to Chapter:</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="ch_thanks" id="ch_thanks" class="custom-control-input" >
                                            <label class="custom-control-label" for="ch_thanks"></label>
                                        </div>
                                    </div>
                                </div>
                            <hr>
                                <!-- /.form group -->
                            <div class="form-group row mb-1">
                                <div class="col-sm-12">
                                    <h5>Sustaining Chapter Donation Payment</h5>
                                </div>
                                <label class="col-sm-2 col-form-label">Date Received:</label>
                                <div class="col-sm-3">
                                    <input type="date" name="SustainingPaymentDate" id="SustainingPaymentDate"class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask />
                                </div>
                                <label class="col-sm-2 ml-1 col-form-label">Donation Amount:</label>
                                <div class="col-sm-3">
                                    <div class="input-group row">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="text" name="sustaining" id="sustaining" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <div class="col-md-12 d-flex align-items-center">
                                    <label class="col-form-label mr-2">Send Sustaining Chapter Donation Thank You to Chapter:</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="ch_sustaining" id="ch_sustaining" class="custom-control-input" >
                                            <label class="custom-control-label" for="ch_sustaining"></label>
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
            <div class="card-body text-center">
                @if ($coordinatorCondition)
                    <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save Payment Information</button>
                @endif
                @if ($confId == $chConfId)
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('payment.chapreregistration') }}'"><i class="fas fa-reply mr-2"></i>Back to Re-Registration Report</button>
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('payment.chapdonations') }}'"><i class="fas fa-reply mr-2"></i>Back to Donations Report</button>
                @elseif ($confId != $chConfId)
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('payment.chapreregistration', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Re-Registration Report</button>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('payment.chapdonations', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Donations Report</button>
                @endif
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter Details</button>
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
// Disable fields, links and buttons
var $chActiveId = @json($chActiveId);
$(document).ready(function () {
    // Disable fields for chapters that are not active
    if (($chActiveId != 1)) {
        $('input, select, textarea, button').prop('disabled', true);

        $('a[href^="mailto:"]').each(function() {
            $(this).addClass('disabled-link'); // Add disabled class for styling
            $(this).attr('href', 'javascript:void(0);'); // Prevent navigation
            $(this).on('click', function(e) {
                e.preventDefault(); // Prevent link click
            });
        });
    }
});


$(document).ready(function() {
    // Function to load the coordinator list based on the selected value
    function loadCoordinatorList(corId) {
        if(corId != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list") }}' + '/' + corId,
                type: "GET",
                success: function(result) {
                $("#display_corlist").html(result);
                },
                error: function (jqXHR, exception) {
                console.log("Error: ", jqXHR, exception);
                }
            });
        }
    }

    // Get the selected coordinator ID on page load
    var selectedCorId = $("#ch_primarycor").val();
        loadCoordinatorList(selectedCorId);

        // Update the coordinator list when the dropdown changes
        $("#ch_primarycor").change(function() {
            var selectedValue = $(this).val();
            loadCoordinatorList(selectedValue);
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const paymentDate = document.getElementById('PaymentDate');
    const membersPaidFor = document.getElementById('MembersPaidFor');

    function validateFields() {
        if (paymentDate.value || membersPaidFor.value) {
            // If either field has a value, make both required
            paymentDate.setAttribute('required', 'required');
            membersPaidFor.setAttribute('required', 'required');
        } else {
            // If neither field has a value, remove the required attribute
            paymentDate.removeAttribute('required');
            membersPaidFor.removeAttribute('required');
        }
    }

    // Add event listeners for input change
    paymentDate.addEventListener('input', validateFields);
    membersPaidFor.addEventListener('input', validateFields);
});

document.addEventListener('DOMContentLoaded', function () {
    const M2MPaymentDate = document.getElementById('M2MPaymentDate');
    const M2MPayment = document.getElementById('M2MPayment');

    function validateFields() {
        if (M2MPaymentDate.value || M2MPayment.value) {
            // If either field has a value, make both required
            M2MPaymentDate.setAttribute('required', 'required');
            M2MPayment.setAttribute('required', 'required');
        } else {
            // If neither field has a value, remove the required attribute
            M2MPaymentDate.removeAttribute('required');
            M2MPayment.removeAttribute('required');
        }
    }

    // Add event listeners for input change
    M2MPaymentDate.addEventListener('input', validateFields);
    M2MPayment.addEventListener('input', validateFields);
});

document.addEventListener('DOMContentLoaded', function () {
    const SustainingPaymentDate = document.getElementById('SustainingPaymentDate');
    const SustainingPayment = document.getElementById('SustainingPayment');

    function validateFields() {
        if (SustainingPaymentDate.value || SustainingPayment.value) {
            // If either field has a value, make both required
            SustainingPaymentDate.setAttribute('required', 'required');
            SustainingPayment.setAttribute('required', 'required');
        } else {
            // If neither field has a value, remove the required attribute
            SustainingPaymentDate.removeAttribute('required');
            SustainingPayment.removeAttribute('required');
        }
    }

    // Add event listeners for input change
    SustainingPaymentDate.addEventListener('input', validateFields);
    SustainingPayment.addEventListener('input', validateFields);
});


</script>
@endsection
