<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('app.name')}}</title>

    {{-- <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}

    <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ env('BASE_URL') }}coordinator_theme/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ env('BASE_URL') }}coordinator_theme/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ env('BASE_URL') }}coordinator_theme/dist/css/adminlte.min.css">

    <script>
        window.onload = function () {
            if (window.history && window.history.pushState) {
                window.history.pushState('preventBack', null, '');
                window.onpopstate = function () {
                    location.reload();
                };
            }
        };
    </script>

</head>
<body>
    <div id="app">

            @yield('content')

    </div>

<!-- jQuery -->
<script src="{{ env('BASE_URL') }}plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{ env('BASE_URL') }}plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="{{ env('BASE_URL') }}dist/js/adminlte.min.js"></script>
</body>
<!-- Scripts -->
{{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
@yield('customscript')
</html>
