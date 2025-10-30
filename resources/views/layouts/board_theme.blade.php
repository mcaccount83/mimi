<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    {{-- <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free/css/all.min.css"> --}}
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/solid.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/brands.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/css/v5-font-face.css" rel="stylesheet" />
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/daterangepicker/daterangepicker.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

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
    .ml-2 {
        margin-left: 0.5rem !important; /* Adjust the margin to control spacing for Vacant Buttons */
    }

    .custom-control-input:checked ~ .custom-control-label {
        color: black; /* Label color when toggle is ON for Vacant Buttons */
    }

    .custom-control-input:not(:checked) ~ .custom-control-label {
        color: #b0b0b0; /* Subdued label color when toggle is OFF for Vacant Buttons */
        opacity: 0.6;   /* Optional: Adds a subdued effectfor Vacant Buttons */
    }

    .disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #6c757d; /* Muted color */
    }

    .board-info {
        display: table;
        width: 100%;
        margin-bottom: 15px;
    }
    .info-row {
        display: table-row;
    }
    .info-label, .info-label-empty {
        display: table-cell;
        width: 150px;
        padding: 2px 10px 2px 0;
        vertical-align: top;
    }
    .info-label {
        font-weight: bold;
    }
    .info-data {
        display: table-cell;
        padding: 2px 0;
    }
</style>


</head>

<body style="background-color: #f0f0f0 !important;" class="hold-transition layout-top-nav">
    <div class="wrapper">
        @php
            $user = Auth::user();
            $userName = $user->first_name.' '.$user->last_name;
            $userEmail = $user->email;
            $userType = $user->user_type;
        @endphp

    <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">

    @if($userType == 'board')

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('home')}}" >
                <span class="no-icon">MIMI Profile</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('board.viewresources', ['id' => $chDetails->id]) }}'">
                <span class="no-icon">Chapter Resources</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="https://momsclub.org/elearning/" target="_blank">
                <span class="no-icon">eLearning Library</span>
            </a>
          </li>
        </ul>
      </div>
      @endif

    @if($userType != 'coordinator')
      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">

        <li class="nav-item">
            <a href="{{ route('logout') }}" class="nav-link"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
               <span class="no-icon">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>

      </ul>
      @endif

      @if($userType == 'coordinator')
      @php
          // Assuming you're already on a chapter edit page and the 'id' is available in the route.
          $id = request()->route('id'); // Get the current chapter ID from the route
      @endphp

      @if ($id) <!-- Check if $id is not null -->
          <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
              <li class="nav-item">
                  <a class="nav-link" href="{{ route('chapters.view', ['id' => $id]) }}">
                      <span class="no-icon">Back to Coordinator View / Chapter Details</span>
                  </a>
              </li>
          </ul>
      @endif
  @endif

    </div>
  </nav>
  <!-- /.navbar -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        @if($ITCondition == 1 )
            <p class="description text-center"><span style="color: red;">You are Viewing Chapter Pages as an Admin Coordinator -- All Information is Editable just as it is for Chapter Members.</p>
        @elseif($userType == 'coordinator' && $ITCondition != 1)
            <p class="description text-center"><span style="color: red;">You are Viewing Chapter Pages as a Coordinator -- All Information is READ ONLY.</p>
        @endif
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">

        @include('layouts.scripts.messages')

        @yield('content')
    </div>

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
        Copyright &copy;
        <script>
            document.write(new Date().getFullYear())
        </script>
        <a href="https://momsclub.org/" target="_blank">MOMS Club</a>. &nbsp;All rights reserved.
    </div>
    <!-- Default to the left -->

  </footer>
</div>
<!-- ./wrapper -->


<!-- jQuery -->
<script src="{{ config('settings.base_url') }}theme/plugins/jquery/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{ config('settings.base_url') }}theme/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- FontAwesome -->
<script defer src="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/js/all.js"></script>
<!-- overlayScrollbars -->
<script src="{{ config('settings.base_url') }}theme/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- InputMask -->
<script src="{{ config('settings.base_url') }}theme/plugins/moment/moment.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="{{ config('settings.base_url') }}theme/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ config('settings.base_url') }}theme/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Sript Functions -->
@include('layouts.scripts.alert')
@include('layouts.scripts.bdcoordinatorlist')
@include('layouts.scripts.boards')
@include('layouts.scripts.boardreport')
@include('layouts.scripts.datetime')
@include('layouts.scripts.email')
@include('layouts.scripts.masks')
@include('layouts.scripts.password')
@include('layouts.scripts.pdfviewer')
@include('layouts.scripts.website')

@yield('customscript')
@stack('scripts')

</body>
</html>
