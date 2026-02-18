@extends('layouts.public_theme')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">

            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="band-link">
                    <img src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" width="120">
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4 text-center">
                    <h5 class="mb-3">Email Verification Not Required</h5>
                    <p class="text-muted mb-4">
                        This application does not require email verification.
                        Please contact your coordinator if you are having trouble accessing your account.
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">
                        Return to Login
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
