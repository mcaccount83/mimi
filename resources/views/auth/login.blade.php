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
                <div class="card-body p-4">

                    <p class="text-center mb-3">
                        All Chapters &amp; Coordinators should {{ isset($url) ? ucwords($url) : "" }} {{ __('Login') }} to Access your Account
                    </p>

                    @isset($url)
                        <form method="POST" action='{{ url("login/$url") }}' aria-label="{{ __('Login') }}">
                    @else
                        <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                    @endisset

                        @csrf

                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="input-group mb-3">
                            <input id="email" type="email"
                                class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                name="email" value="{{ old('email') }}"
                                required autofocus placeholder="Email">
                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="input-group mb-3">
                            <input id="password" type="password"
                                class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                name="password" required placeholder="Password">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="row align-items-center mb-3">
                            <div class="col-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                                </div>
                            </div>
                            <div class="col-4 d-grid">
                                <button type="submit" class="btn btn-primary"
                                    onclick="this.form.submit(); this.disabled=true;">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>

                    </form>

                    @if (Route::has('password.request'))
                        <p class="mb-2 text-center">
                            <a href="{{ route('password.request') }}">{{ __('I forgot my password') }}</a>
                        </p>
                    @endif

                    <hr>
                    <p class="small text-center mb-0">
                        If you are not affiliated with a local MOMS Club chapter or would like to make an individual donation to support our 501(c)(3) organization,
                        you may <a href="{{ config('settings.base_url') }}donation" target="_blank">Donate Here</a>.
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('customscript')
<script>
window.onload = function () {
    if (typeof history.pushState === "function") {
        history.pushState("jibberish", null, null);
        window.onpopstate = function () {
            history.pushState('newjibberish', null, null);
        };
    } else {
        var ignoreHashChange = true;
        window.onhashchange = function () {
            if (!ignoreHashChange) {
                ignoreHashChange = true;
                window.location.hash = Math.random();
            } else {
                ignoreHashChange = false;
            }
        };
    }
};
</script>
@endsection
