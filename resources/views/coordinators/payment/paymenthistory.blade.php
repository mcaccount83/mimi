@extends('layouts.mimi_theme')

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
                  </p>
                    </div>
                  EIN: {{$chDetails->ein}}
                  </p>
                   <ul class="list-group list-group-flush mb-3">
                      <li class="list-group-item">
                            @include('coordinators.partials.paymentinfo')
                            @include('coordinators.partials.donationinfo')
                            @include('coordinators.partials.founderhistory')
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
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#rereg" data-bs-toggle="tab">Re-Reg Payments</a></li>
                        <li class="nav-item"><a class="nav-link" href="#m2m" data-bs-toggle="tab">M2M Fund Donations</a></li>
                        <li class="nav-item"><a class="nav-link" href="#sustaining" data-bs-toggle="tab">Sustaining Chapter Donations</a></li>
                        <li class="nav-item"><a class="nav-link" href="#grants" data-bs-toggle="tab">Grant Requests</a></li>
                    </ul>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <div class="tab-content">

                        {{-- Re-Registration Tab --}}
                        <div class="active tab-pane" id="rereg">
                            <h3 class="profile-username">Re-Registration Payments</h3>
                            <div class="row mb-4">
                                <div class="col-12">
                                    {{-- Check if there are ANY payments (current OR historical) --}}
                                    @if($chPayments->rereg_date || $reregHistory->count() > 0)

                                        {{-- Current Payment --}}
                                        @if($chPayments->rereg_date)
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                Date: {{ date('m/d/Y', strtotime($chPayments->rereg_date)) }}<br>
                                                Amount: ${{ number_format($chPayments->rereg_payment, 2) }}<br>
                                                Members: {{ $chPayments->rereg_members }}<br>
                                                @if($coordinatorCondition && $conferenceCoordinatorCondition)
                                                    <button type="button" class="btn btn-primary bg-gradient btn-xs mt-1 mb-1" onclick="window.location.href='{{ route('adminreports.editrereg', ['id' => $chDetails->id]) }}'">Edit Payment Information</button>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Historical Payments --}}
                                        @if($reregHistory->count() > 0)
                                            @foreach($reregHistory as $payment)
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    Date: {{ date('m/d/Y', strtotime($payment->payment_date)) }}<br>
                                                    Amount: ${{ number_format($payment->payment_amount, 2) }}<br>
                                                    Members: {{ $payment->rereg_members }}<br>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif

                                    @else
                                        {{-- Only show this if BOTH current and history are empty --}}
                                        <p class="text-muted">No re-registration payments</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- M2M Tab --}}
                        <div class="tab-pane" id="m2m">
                            <h3 class="profile-username">Mother-to-Mother Fund Donations</h3>
                            <div class="row mb-4">
                                <div class="col-12">
                                    {{-- Check if there are ANY donations (current OR historical) --}}
                                    @if($chPayments->m2m_date || $m2mHistory->count() > 0)

                                        {{-- Current Donation --}}
                                        @if($chPayments->m2m_date)
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                Date: {{ date('m/d/Y', strtotime($chPayments->m2m_date)) }}<br>
                                                Amount: ${{ number_format($chPayments->m2m_donation, 2) }}<br>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Historical Donations --}}
                                        @if($m2mHistory->count() > 0)
                                            @foreach($m2mHistory as $payment)
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    Date: {{ date('m/d/Y', strtotime($payment->payment_date)) }}<br>
                                                    Amount: ${{ number_format($payment->payment_amount, 2) }}<br>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif

                                    @else
                                        {{-- Only show this if BOTH current and history are empty --}}
                                        <p class="text-muted">No M2M Fund donations</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Sustaining Tab --}}
                        <div class="tab-pane" id="sustaining">
                            <h3 class="profile-username">Sustaining Chapter Donations</h3>
                            <div class="row mb-4">
                                <div class="col-12">
                                    {{-- Check if there are ANY donations (current OR historical) --}}
                                    @if($chPayments->sustaining_date || $sustainingHistory->count() > 0)

                                        {{-- Current Donation --}}
                                        @if($chPayments->sustaining_date)
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                Date: {{ date('m/d/Y', strtotime($chPayments->sustaining_date)) }}<br>
                                                Amount: ${{ number_format($chPayments->sustaining_donation, 2) }}<br>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Historical Donations --}}
                                        @if($sustainingHistory->count() > 0)
                                            @foreach($sustainingHistory as $payment)
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    Date: {{ date('m/d/Y', strtotime($payment->payment_date)) }}<br>
                                                    Amount: ${{ number_format($payment->payment_amount, 2) }}<br>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif

                                    @else
                                        {{-- Only show this if BOTH current and history are empty --}}
                                        <p class="text-muted">No sustaining chapter donations</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Grant Requests Tab --}}
                        <div class="tab-pane" id="grants">
                            <h3 class="profile-username">Grant Requests Submitted</h3>
                            <div class="row mb-4">
                                <div class="col-12">
                                    @foreach($grantRequests as $request)
                                           @if($request->submitted_at)

                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    Date: {{ date('m/d/Y', strtotime($request->submitted_at)) }}<br>
                                                    Member in Need: {{ $request->first_name }} {{ $request->last_name }}<br>
                                                    Status:  @if($request->submitted == '1') Submitted @else Draft @endif<br>
                                                    Decision: @if($request->grant_approved == '1') Approved
                                                        @elseif($request->grant_approved == '0') Declined
                                                        @else No Decision Made @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-12">
            <div class="card-body text-center mt-3">
                <button type="button" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.editpayment', ['id' => $chDetails->id]) }}'"><i class="bi bi-plus-lg me-2"></i>Manually Add Payments/Donations</button>
                <br>
                @if ($confId == $chConfId)
                    <button type="button" id="back-rereg" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.chapreregistration') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-credit-card-fill me-2"></i>Back to Re-Registration Report</button>
                    <button type="button" id="back-donation" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.chapdonations') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-currency-dollar me-2"></i>Back to Donations Report</button>
                @elseif ($confId != $chConfId)
                    <button type="button" id="back-rereg" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.chapreregistration', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-credit-card-fill me-2"></i>Back to International Re-Registration Report</button>
                    <button type="button" id="back-donation" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.chapdonations', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-currency-dollar me-2">Back to International Donations Report</button>
                @endif
                <button type="button" id="back-details" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Chapter Details</button>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@section('customscript')
    @include('layouts.scripts.disablefields')
@endsection
