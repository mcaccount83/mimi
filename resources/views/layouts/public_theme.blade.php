<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    {{-- <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free/css/all.min.css"> --}}
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/solid.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/brands.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/css/v5-font-face.css" rel="stylesheet" />
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Switch -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/bootstrap-switch/css/bootstrap-switch.min.css">
    <!-- BS Stepper -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/bs-stepper/css/bs-stepper.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/daterangepicker/daterangepicker.css">

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

<style>
    .disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #6c757d; /* Muted color */
    }
</style>

</head>

<body style="background-color: #f0f0f0 !important;" class="hold-transition layout-top-nav">
    <div class="wrapper">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <div class="content">
        @include('layouts.scripts.messages')

        @yield('content')
    </div>

  <!-- Main Footer -->

    <!-- Default to the left -->

  <!-- </footer> -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ config('settings.base_url') }}theme/plugins/jquery/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{ config('settings.base_url') }}theme/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- FontAwesome -->
<script defer src="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/js/all.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- Bootstrap Switch -->
<script src="{{ config('settings.base_url') }}theme/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- BS-Stepper -->
<script src="{{ config('settings.base_url') }}theme/plugins/bs-stepper/js/bs-stepper.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- InputMask -->
<script src="{{ config('settings.base_url') }}theme/plugins/moment/moment.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="{{ config('settings.base_url') }}theme/plugins/daterangepicker/daterangepicker.js"></script>

<!-- Sript Functions -->
@include('layouts.scripts.alert')
@include('layouts.scripts.bootstrapswitch')
@include('layouts.scripts.datetime')
@include('layouts.scripts.masks')
@include('layouts.scripts.pdfviewer')

@yield('customscript')
@stack('scripts')

</body>
</html>
