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

                        @isset($url)
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>

                        @endisset

                        @csrf

                    </form>


                    </div>
                    <!-- /.login-card-body -->
                </div>
            </div>
        </div>
    </body>
@endsection
@section('customscript')
<script>

function forceLogout() {
    fetch('{{ route('logout') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin'
    }).then(response => {
        if (response.ok) {
            window.location.href = '/'; // Redirect after logout
        }
    });
}


</script>
@endsection
