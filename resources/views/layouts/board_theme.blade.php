<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />

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
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

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
            <a class="nav-link" href="{{ route('board.viewresources')}}" >
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

    @if($userType !== 'coordinator')
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
        @if($userType === 'coordinator')
        <p class="description text-center"><span style="color: red;">You are Viewing Chapter Pages as a Coordinator -- All Information is READ ONLY.</p>
        @endif
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        @if ($message = Session::get('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: @json($message),
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>
    @endif

    @if ($message = Session::get('info'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'info',
                    title: @json($message),
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>
    @endif

    @if ($message = Session::get('warning'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'warning',
                    title: @json($message),
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>
    @endif

    @if ($message = Session::get('fail'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: @json($message),
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>
    @endif

        @if ($errors->any())
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'There were some errors!',
                        html: '<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                        showConfirmButton: true,
                    });
                });
            </script>
        @endif

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

<!-- REQUIRED SCRIPTS -->

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

<!-- Select2 -->
<script src="{{ config('settings.base_url') }}theme/plugins/select2/js/select2.full.min.js"></script>
<!-- InputMask -->
<script src="{{ config('settings.base_url') }}theme/plugins/moment/moment.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="{{ config('settings.base_url') }}theme/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ config('settings.base_url') }}theme/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

<script>
    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
    //databask
    $('[data-mask]').inputmask()

</script>

<script>
    function openPdfViewer(filePath) {
        var base_url = '{{ url("/pdf-viewer") }}';
        window.open(base_url + '?id=' + encodeURIComponent(filePath), '_blank');
    }
</script>

<script>
    function applyPhoneMask() {
            Inputmask({"mask": "(999) 999-9999"}).mask(".phone-mask");
        }

        // Currency mask
        function applyCurrencyMask() {
        Inputmask({
            alias: 'currency',
            rightAlign: false,
            groupSeparator: ',',
            digits: 2,
            digitsOptional: false,
            placeholder: '0'
        }).mask(".currency-mask");
        }

        //Cusotmize AJAX Popups to Match Theme
    function customSuccessAlert(message) {
            Swal.fire({
                title: 'Success',
                html: message,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn-sm btn-success', // Match your theme button class
                },
                buttonsStyling: false // Disable default button styling
            });
        }

    function customWarningAlert(message) {
        Swal.fire({
            title: 'Warning',
            html: message,
            icon: 'warning',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success', // Match your theme button class
            },
            buttonsStyling: false // Disable default button styling
        });
    }

    function customInfoAlert(message) {
        Swal.fire({
            title: 'Info',
            html: message,
            icon: 'info',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success', // Match your theme button class
            },
            buttonsStyling: false // Disable default button styling
        });
    }

    function customErrorAlert(message) {
        Swal.fire({
            title: 'Error',
            html: message,
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success', // Match your theme button class
            },
            buttonsStyling: false // Disable default button styling
        });
    }
</script>

@yield('customscript')

@stack('scripts')

</body>
</html>
