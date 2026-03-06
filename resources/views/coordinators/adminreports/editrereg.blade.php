@extends('layouts.mimi_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Re-Registration Date')

@section('content')
    <!-- Main content -->
<section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
            <form method="POST" name="admin-rereg-date" action='{{ route("adminreports.updaterereg",$chDetails->id) }}'>
                @csrf

          <!-- Profile Image -->
          <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="card-header text-center bg-transparent">
                <h3 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                <p class="mb-0">{{ $conferenceDescription }} Conference, {{ $conferenceDescription }} Region
                      </p>
              </div>

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
        <div class="card-body">
            <div class="card-header bg-transparent border-0">
                <h3">Re-Registration Information</h3>
        </div>
           <!-- /.card-header -->
                            <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                <!-- /.form group -->
                <div class="row mb-3">
                    <label class="col-sm-4 mb-1 col-form-label">Founded/Renewal Month:</label>
                    <div class="col-sm-3 mb-1">
                        <select name="ch_founddate" class="form-control" style="width: 100%;">
                            <option value="">Select Month</option>
                            @foreach($allMonths as $month)
                                <option value="{{$month->id }}"
                                    @if($chDetails->start_month_id == $month->id) selected @endif>
                                    {{$month->month_long_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- /.form group -->
                <div class="row mb-3">
                    <label class="col-sm-4 mb-1 col-form-label">Next Renwal Year:</label>
                    <div class="col-sm-3 mb-1">
                        <input type="text" name="ch_renewyear" class="form-control" value="{{ $chDetails->next_renewal_year}}">
                    </div>
                </div>
                <!-- /.form group -->
                <div class="row mb-3">
                    <label class="col-sm-4 mb-1 col-form-label">Dues Last Paid:</label>
                    <div class="col-sm-3 mb-1">
                        <input type="date" name="ch_duespaid" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chPayments->rereg_date }}">
                    </div>
                </div>
                <!-- /.form group -->
                <div class="row mb-3">
                    <label class="col-sm-4 mb-1 col-form-label">Payment Amount:</label>
                    <div class="col-sm-3 mb-1">
                        <div class="input-group">
                                <span class="input-group-text">$</span>
                            <input type="text" name="ch_payment" class="form-control" value="{{ $chPayments->rereg_payment }}">
                        </div>
                    </div>
                </div>

                <!-- /.form group -->
                <div class="row mb-3">
                    <label class="col-sm-4 mb-1 col-form-label">Number of Members:</label>
                    <div class="col-sm-3 mb-1">
                        <input type="text" name="ch_members" class="form-control" value="{{ $chPayments->rereg_members }}">
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
                @if ($coordinatorCondition)
                    <button type="submit" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-floppy-fill me-2"></i>Save</button>
                    <br>
                @endif
                 @if ($confId == $chConfId)
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('adminreports.rereg') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-credit-card-fill me-2"></i>Back to Re-Reg Admin Report</button>
                @elseif ($confId != $chConfId)
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('adminreports.rereg', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-credit-card-fill me-2"></i>Back to International Re-Reg Admin Report</button>
                @endif
                <button type="button" id="back-details" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('payment.paymenthistory', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-file-earmark-text me-2"></i>Back to Payment History</button>
             </div>
          </div>
        </div>
        <!-- /.row -->
      </div
      ><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection


