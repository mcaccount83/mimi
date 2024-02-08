@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment Confirmation</div>

                <div class="card-body">
                    <h5 class="card-title">Thank you for your payment!</h5>
                    <p class="card-text">Your payment of ${{ $amount / 100 }} was successful.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
