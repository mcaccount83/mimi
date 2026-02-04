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
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#rereg" data-toggle="tab">Re-Reg Payments</a></li>
                        <li class="nav-item"><a class="nav-link" href="#m2m" data-toggle="tab">M2M Fund Donations</a></li>
                        <li class="nav-item"><a class="nav-link" href="#sustaining" data-toggle="tab">Sustaining Chapter Donations</a></li>
                        <li class="nav-item"><a class="nav-link" href="#manual" data-toggle="tab">Manual Orders</a></li>
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
                                                    <button class="btn bg-gradient-primary btn-xs mt-1 mb-1" onclick="window.location.href='{{ route('adminreports.editreregdate', ['id' => $chDetails->id]) }}'">Edit Payment Information</button>
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

                        {{-- Manual Tab --}}
                        <div class="tab-pane" id="manual">
                            <h3 class="profile-username">Manual Orders</h3>
                            <div class="row mb-4">
                                <div class="col-12">
                                    {{-- Check if there are ANY orders (current OR historical) --}}
                                    @if($chPayments->manual_date || $manualHistory->count() > 0)

                                        {{-- Current Order --}}
                                        @if($chPayments->manual_date)
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                Date: {{ date('m/d/Y', strtotime($chPayments->manual_date)) }}<br>
                                                Amount: ${{ number_format($chPayments->manual_order, 2) }}<br>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Historical Orders --}}
                                        @if($manualHistory->count() > 0)
                                            @foreach($manualHistory as $payment)
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
                                        <p class="text-muted">No manual orders</p>
                                    @endif
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
            <div class="card-body text-center">
                <button type="button" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('payment.editpayment', ['id' => $chDetails->id]) }}'"><i class="fas fa-credit-card mr-2"></i>Manually Add Payments/Donations</button>
                <br>
                @if ($confId == $chConfId)
                    <button type="button" id="back-rereg" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('payment.chapreregistration') }}'"><i class="fas fa-reply mr-2"></i>Back to Re-Registration Report</button>
                    <button type="button" id="back-donation" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('payment.chapdonations') }}'"><i class="fas fa-reply mr-2"></i>Back to Donations Report</button>
                @elseif ($confId != $chConfId)
                    <button type="button" id="back-rereg" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('payment.chapreregistration', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Re-Registration Report</button>
                    <button type="button" id="back-donation" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('payment.chapdonations', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Donations Report</button>
                @endif
                <button type="button" id="back-details" class="btn bg-gradient-primary mb-3 keep-enabled" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter Details</button>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@section('customscript')
    @include('layouts.scripts.disablefields')
@endsection
