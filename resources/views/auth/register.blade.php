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
                    <h5 class="mb-3">Registration Not Available</h5>
                    <p class="text-muted mb-4">
                        Self-registration is not available for this application.
                        Please contact your coordinator if you need access.
                    </p>
                    <a href="{{ route('login') }}" class="btn btn-primary d-grid">
                        Return to Login
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
