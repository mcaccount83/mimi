@extends('layouts.public_theme')

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                        <div class="col-md-12">
                            <div class="card card-widget widget-user">
                                <div class="widget-user-header bg-primary">
                                    <div class="widget-user-image">
                                        <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12"><br><br></div>
                                    <h2 class="text-center">Mother-to-Mother Fund<br>
                                                Grants Given</h2>
                                    <p class="description text-center">
                                    </p>

                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>

                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                 <!-- /.card-header -->
                            <div class="row">
                                <div class="col-md-12">

                  {{-- <h3 class="profile-username ">Grant Information</h3> --}}

                <div class="row mb-4">
                    <div class="col-md-12 text-center">
                        <h3><strong>Total Lifetime Grants: ${{ number_format($totalLifetimeGrants, 2) }}</strong></h3>
                    </div>
                </div>

                  @foreach($grantsByFiscalYear as $fiscalYear => $grants)
                <h3 class="mt-4">Fiscal Year {{ $fiscalYear }}</h3>

                @if($grants->count() > 0)
                    <table width="100%" style="border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #333; font-weight: bold;">
                                <td width="15%">Date</td>
                                <td width="10%">State</td>
                                <td width="55%">Description</td>
                                <td width="20%"><span class="ms-5">Amount</span></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grants as $list)
                                <tr style="border-bottom: 1px solid #555;">
                                    {{-- <td><span class="date-mask">{{ $list->submitted_at}}</span></td> --}}
                                    <td>{{ \Carbon\Carbon::parse($list->submitted_at)->format('M Y') }}</td>
                                    <td>{{$list->chapterstate->state_short_name}}</td>
                                    <td>{{ $list->review_description}}</td>
                                    <td><span class="ms-5">${{ number_format($list->amount_awarded, 2) }}</span></td>
                                </tr>
                            @endforeach
                            <tr style="border-top: 2px solid #333; font-weight: bold;">
                                <td colspan="3" style="text-align: right; padding-top: 10px;">Total for Fiscal Year {{ $fiscalYear }}:</td>
                                <td style="padding-top: 10px;"><span class="ms-5">${{ number_format($grants->sum('amount_awarded'), 2) }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <p>No grants for fiscal year {{ $fiscalYear }}</p>
                @endif

                <br><br>
            @endforeach



                </div>
            </div>




    <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </div>
    <!-- /.col -->
    </div>

<div class="col-md-12" style="font-size: 0.8em"></div>


</div>
<!-- /.container- -->
@endsection
@section('customscript')


@endsection
