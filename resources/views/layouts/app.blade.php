<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>

    <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ config('settings.base_url') }}coordinator_theme/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ config('settings.base_url') }}coordinator_theme/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ config('settings.base_url') }}coordinator_theme/dist/css/adminlte.min.css">
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
<script src="{{ config('settings.base_url') }}plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{ config('settings.base_url') }}plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="{{ config('settings.base_url') }}dist/js/adminlte.min.js"></script>
</body>

@yield('customscript')
</html>
