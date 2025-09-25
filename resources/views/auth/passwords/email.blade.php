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
        <p class="login-box-msg">{{ __('You forgot your password? Here you can easily retrieve a new password.') }}</p>

            <form method="POST" id="forgot-pswd" action="{{ route('password.email') }}">
                @csrf

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="input-group mb-3">
            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required placeholder="Email">
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
            <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block" id="email-btn" onclick="return validate();">{{ __('Send Password Reset Link') }}</button>
            </div>
            <!-- /.col -->
            </div>
        </form>

        <p class="mt-3 mb-1">
            <a href="{{ route('home') }}">Return to Login</a>
        </p>
        </div>
        <!-- /.login-card-body -->
    </div>
    </div>
    <!-- /.login-box -->
</div>
    </body>

@endsection
@section('customscript')
<script>
function validate(){
	var email = $("#email").val();
	if(email != ""){
		$("#email-btn").attr("disabled", "disabled");
		$("#forgot-pswd").submit();
	}
	return true;
}

</script>
@endsection

