@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Chapter Profile')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

              <div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                            <h3>Re-Registration Payment History</h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">

 <div class="row">
                              <div class="col-12 mb-3">
                                    <label class="me-2">Last Re-Registration Payment:</label>
                                        @if ($chPayments->rereg_members)
                                            ${{ number_format($chPayments->rereg_payment, 2) }} for {{ $chPayments->rereg_members }} Members on <span class="date-mask">{{ $chPayments->rereg_date }}</span>
                                        @else
                                            No Payment Recorded
                                        @endif
                                        <br>
                                    <label class="me-2">Next Re-Registration Payment:</label>
                                    @if ($currentDate->gte($dueDate))
                                        @if ($chDetails->start_month_id == $currentMonth)
                                            <span class="badge bg-success fs-7">Due Now (<span class="date-mask">{{ $renewalDate }})</span></span>
                                        @else
                                            <span class="badge bg-danger fs-7">Overdue (<span class="date-mask">{{ $renewalDate }})</span></span>
                                        @endif
                                    @else
                                        Due on <span class="date-mask">{{ $renewalDate }}</span>
                                    @endif
                                    <br>
                                </div>

                              <div class="col-12 mb-3">
                                    <label>All Re-Registration Payments:</label><br>
                                    {{-- Check if there are ANY payments (current OR historical) --}}
                                    @if($chPayments->rereg_date || $reregHistory->count() > 0)

                                        {{-- Current Payment --}}
                                        @if($chPayments->rereg_date)
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                Date: {{ date('m/d/Y', strtotime($chPayments->rereg_date)) }}<br>
                                                Amount: ${{ number_format($chPayments->rereg_payment, 2) }}<br>
                                                Members: {{ $chPayments->rereg_members }}<br>
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

                            <div class="col-md-12">
                            <div class="card-body text-center mt-3">
                                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board-new.editreregpayment', ['id' => $chDetails->id]) }}'"><i class="bi bi-credit-card-fill me-2"></i>Make a Payment</button>
                            </div>
                        </div>

                        </div>
            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->

            </div>
            <!-- /.col -->
       </div>
    </div>
    <!-- /.container- -->
@endsection
@section('customscript')
@php $disableMode = 'disable-all'; @endphp
@include('layouts.scripts.disablefields')
@endsection
