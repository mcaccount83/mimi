@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Donation History')

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
                            <h3>Sustaining Chapter & Mother-to-Mother Fund Donation History</h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">

<div class="row">

                              <div class="col-md-6 mb-3">
                                     <h4 class="profile-username">Mother-to-Mother Fund Donations</h4>
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

                              <div class="col-md-6 mb-3">
                                <h4 class="profile-username">Sustaining Chapter Donations</h4>
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

                            </div>

                            <div class="col-md-12">
                            <div class="card-body text-center mt-3">
                                <button type="button" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('board-new.editdonate', ['id' => $chDetails->id]) }}'"><i class="bi bi-currency-dollar me-2"></i>Make a Donation</button>
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
@if($userTypeId == \App\Enums\UserTypeEnum::COORD)
    @php $disableMode = 'disable-all'; @endphp
    @include('layouts.scripts.disablefields')
@endif
@endsection
