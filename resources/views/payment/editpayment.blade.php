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
                <div class="card-body">
                     <div class="card-header text-center bg-transparent">
                    <h3 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                    <p class="mb-0">{{ $chDetails->confname }} Conference, {{ $chDetails->regname }} Region
                    <br>
                  EIN: {{$chDetails->ein}}
                  </p>
                </div>

                  <ul class="list-group list-group-flush mb-3">
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
                            <div class="col-auto fw-bold">Status:</div>
                            <div class="col text-end">
                                {{ $chapterStatus }}
                            </div>
                        </div>
                      </li>
                     <li class="list-group-item">
                          <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                            <div class="row mb-2">
                          <span id="display_corlist"></span>
                            </div>
                        </li>
                  <li class="list-group-item">
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
                <h3>Payment & Donation Information</h3>
                                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <h5>Re-Registration Payment
                                    <small>(Adding a re-reg payment will automatically move next renewal year forward)</small>
                                    </h5>
                                </div>
                                <label class="col-sm-2 col-form-label">Date Received:</label>
                                <div class="col-sm-2">
                                    <input type="date" name="PaymentDate" id="PaymentDate"class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask >
                                </div>
                                <label class="col-sm-2 col-form-label">Payment Amount:</label>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                            <span class="input-group-text">$</span>
                                        <input type="text" name="rereg" id="rereg" class="form-control"/>
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Members Paid For:</label>
                                <div class="col-sm-2">
                                    <input type="number" name="members" id="members" onKeyPress="if(this.value.length==9) return false;" class="form-control"  />
                                </div>
                            </div>
                            <div class="row mb-3 mb-1">
                                <label class="col-sm-2 col-form-label">Re-Registration Notes:</label>
                                <div class="col-sm-8">
                                  <input type="text" name="ch_regnotes" id="ch_regnotes" class="form-control"  value="{{ $chPayments->rereg_notes}}" >
                                </div>
                            </div>
                            <div class="row mb-3 ">
                                <div class="col-md-12 d-flex align-items-center">
                                    <label class="col-form-label me-2">Send Payment Received Notification to Chapter:</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="ch_notify" id="ch_notify" class="form-check-input" >
                                            <label class="form-check-label" for="ch_notify"></label>
                                        </div>
                                    </div>
                                <div class="col-md-12 d-flex align-items-center">
                                    <label class="col-form-label me-2">Waive Late Fee:</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="ch_waive_late" id="ch_waive_late" class="form-check-input" {{$chPayments->rereg_waivelate == 1 ? 'checked' : ''}}>
                                            <label class="form-check-label" for="ch_waive_late"></label>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            <!-- /.form group -->
                            <div class="row mb-3 mb-1">
                                <div class="col-sm-12">
                                    <h5>M2M Fund Donation Payment</h5>
                                </div>
                                <label class="col-sm-2 col-form-label">Date Received:</label>
                                <div class="col-sm-3">
                                    <input type="date" name="M2MPaymentDate" id="M2MPaymentDate"class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask />
                                </div>
                                <label class="col-sm-2 ms-1 col-form-label">Donation Amount:</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                            <span class="input-group-text">$</span>
                                        <input type="text" name="m2m" id="m2m" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3 ">
                                <div class="col-md-12 d-flex align-items-center">
                                    <label class="col-form-label me-2">Send M2M Donation Thank You to Chapter:</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="ch_thanks" id="ch_thanks" class="form-check-input" >
                                            <label class="form-check-label" for="ch_thanks"></label>
                                        </div>
                                    </div>
                                </div>
                            <hr>
                                <!-- /.form group -->
                            <div class="row mb-3 mb-1">
                                <div class="col-sm-12">
                                    <h5>Sustaining Chapter Donation Payment</h5>
                                </div>
                                <label class="col-sm-2 col-form-label">Date Received:</label>
                                <div class="col-sm-3">
                                    <input type="date" name="SustainingPaymentDate" id="SustainingPaymentDate"class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask />
                                </div>
                                <label class="col-sm-2 ms-1 col-form-label">Donation Amount:</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                            <span class="input-group-text">$</span>
                                        <input type="text" name="sustaining" id="sustaining" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3 ">
                                <div class="col-md-12 d-flex align-items-center">
                                    <label class="col-form-label me-2">Send Sustaining Chapter Donation Thank You to Chapter:</label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" name="ch_sustaining" id="ch_sustaining" class="form-check-input" >
                                            <label class="form-check-label" for="ch_sustaining"></label>
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
                @if ($coordinatorCondition)
                    <button type="submit" class="btn btn-primary bg-gradient mb-2" ><i class="bi bi-floppy-fill me-2"></i>Save Payment Information</button>
                @endif
                <button type="button" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.paymenthistory', ['id' => $chDetails->id]) }}'"><i class="bi bi-file-earmark-text me-2"></i>View Payment History</button>
                <br>
                @if ($confId == $chConfId)
                        <button type="button" id="back-rereg" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.chapreregistration') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-credit-card-fill me-2"></i>Back to Re-Registration Report</button>
                        <button type="button" id="back-donation" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.chapdonations') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-currency-dollar me-2"></i>Back to Donations Report</button>
                @elseif ($confId != $chConfId)
                    <button type="button" id="back-rereg" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.chapreregistration', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-credit-card-fill me-2"></i>Back to International Re-Registration Report</button>
                    <button type="button" id="back-donation" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.chapdonations', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-currency-dollar me-2"></i>Back to International Donations Report</button>
                @endif
                <button type="button" id="back-details" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Chapter Details</button>
                {{-- <button type="button" id="back-details" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.paymenthistory', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-file-earmark-text me-2"></i>Back to Payment History</button> --}}
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
    @include('layouts.scripts.disablefields')

<script>
document.addEventListener('DOMContentLoaded', function () {
    function setupPairValidation(dateId, amountId, extraId = null) {
        const dateField = document.getElementById(dateId);
        const amountField = document.getElementById(amountId);
        const extraField = extraId ? document.getElementById(extraId) : null;
        if (!dateField || !amountField) return;

        function validate() {
            const required = !!(dateField.value || amountField.value || (extraField && extraField.value));
            dateField.toggleAttribute('required', required);
            amountField.toggleAttribute('required', required);
            if (extraField) extraField.toggleAttribute('required', required);
        }

        dateField.addEventListener('input', validate);
        amountField.addEventListener('input', validate);
        if (extraField) extraField.addEventListener('input', validate);
    }

    setupPairValidation('PaymentDate', 'rereg', 'members');  // all 3 required together
    setupPairValidation('M2MPaymentDate', 'm2m');
    setupPairValidation('SustainingPaymentDate', 'sustaining');
});

</script>
@endsection
