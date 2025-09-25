@extends('layouts.public_theme')

@section('content')
<div class="row justify-content-center">

    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href="{{ route('home') }}" class="band-link">
                    <img src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" width="120">
                </a>
                    </div>
                      <!-- /.login-logo -->

                     <div class="card">
                        <div class="card-body login-card-body">

                    <p class="login-box-msg">All Chapters & Coordinators should {{ isset($url) ? ucwords($url) : ""}} {{ __('Login') }} to Access your Account</p>

                    @isset($url)
                        <form method="POST" action='{{ url("login/$url") }}' aria-label="{{ __('Login') }}">
                        @else
                        <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                        @endisset

                        @csrf

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="Email">
                        @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        </div>
                        <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="Password">
                        @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label class="form-check-label" for="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                {{ __('Remember Me') }}
                            </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block" onClick="this.form.submit(); this.disabled=true;">{{ __('Login') }}</button>
                        </div>
                        <!-- /.col -->
                        </div>
                    </form>

                    {{-- <div class="social-auth-links text-center mb-3">
                        <p>- OR -</p>
                        <a href="#" class="btn btn-block btn-danger">
                        <i class="fab fa-google mr-2"></i> Sign in using Google
                        </a>
                    </div>
                    <!-- /.social-auth-links --> --}}
                    <p class="mb-1">
                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('I forgot my password') }}
                            </a>
                        @endif
                    </p>

                    <hr>
                      <p>If you not affiliated with a local MOMS Club chapter or would like to make an individual donation to support our 501(c)(3) organization,
                        you may <a href="{{ config('settings.base_url') }}donation" target="_blank">Donate Here</a><br>
                       </p>


                    </div>
                    <!-- /.login-card-body -->
                </div>
            </div>
        </div>
    </body>
@endsection
@section('customscript')
<script>
window.onload = function () {
    if (typeof history.pushState == "function") {
        history.pushState("jibberish", null, null);
        window.onpopstate = function () {
            history.pushState('newjibberish', null, null);
        };
    }
    else {
        var ignoreHashChange = true;
        window.onhashchange = function () {
            if (!ignoreHashChange) {
                ignoreHashChange = true;
                window.location.hash = Math.random();
            }
            else {
                ignoreHashChange = false;
            }
        };
    }
};
</script>
@endsection
