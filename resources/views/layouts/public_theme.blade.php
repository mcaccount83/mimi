<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--begin::Head-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />

     <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- jQuery as classic sync script - MUST be before Vite modules -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Vite Compiled Assets -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/flash.js'])

    <!-- Google Recaptcha -->
    <script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.recaptcha.site_key') }}"></script>

    {{-- Flash Messages as meta tags --}}
    @if ($message = Session::get('success'))
        <meta name="flash-success" content="{{ $message }}">
    @endif
    @if ($message = Session::get('info'))
        <meta name="flash-info" content="{{ $message }}">
    @endif
    @if ($message = Session::get('warning'))
        <meta name="flash-warning" content="{{ $message }}">
    @endif
    @if ($message = Session::get('fail'))
        <meta name="flash-fail" content="{{ $message }}">
    @endif
    @if(View::shared('errors', false) != false && $errors->any())
        <meta name="flash-errors" content="<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>">
    @endif

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

    h1, h2, h3, h4, h5, h6 {
    font-weight: normal !important;
}

h1 { font-size: 2rem !important; }
h2 { font-size: 1.75rem !important; }
h3 { font-size: 1.5rem !important; }
h4 { font-size: 1.25rem !important; }
h5 { font-size: 1rem !important; }
h6 { font-size: 0.875rem !important; }
</style>

</head>
  <!--end::Head-->
  <!--begin::Body-->
<body style="background-color: #f0f0f0 !important;" class="hold-transition layout-top-nav">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->

           @yield('content')
     </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->

  <!--begin::Footer-->
      <!--end::Footer-->
 </div>
    <!--end::App Wrapper-->

<!-- Sript Functions -->
@include('layouts.scripts.alert')
@include('layouts.scripts.datetime')
@include('layouts.scripts.masks')
@include('layouts.scripts.pdfviewer')
@include('layouts.scripts.recaptcha')

@yield('customscript')
@stack('scripts')

</body>
</html>
