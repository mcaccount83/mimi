<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  {{-- <link rel="stylesheet" href="{{ config('settings.base_url') }}coordinator_theme/dist/css/AdminLTE.min.css"> --}}

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

<!-- DataTables -->
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <!-- Custom CSS -->
  {{-- <link rel="stylesheet" href="{{ config('settings.base_url') }}coordinator_theme/dist/css/custom.css"> --}}
  <!-- Custom CSS for Financial Report -->
  {{-- <link rel="stylesheet" href="{{ config('settings.base_url') }}chapter_theme/css/custom_financial.css"> --}}

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{config('app.name')}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

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
  .fa-layers {
        display: inline-block;
        position: relative;
        width: 1.25em;
        height: 1em;
        vertical-align: middle;
    }
    .fa-layers .fas {
        position: absolute;
        top: 0;
        left: 0;
    }

    .sidebar {
        font-size: 0.90rem;
        line-height: 1;
    }

    .nav-icon {
        margin-right: 10px;
    }
    .nav-link {
        display: flex;
        align-items: center;
    }

    .custom-logo {
        width: 70px; /* Adjust width as needed */
        height: 70px; /* Adjust height as needed */
        display: block;
        margin: 10px auto; /* Centers horizontally and adds top and bottom margin */
    }

    .brand-link {
        display: flex;
        justify-content: center; /* Centers items horizontally */
        align-items: center; /* Centers items vertically */
        margin-top: 10px; /* Adds top margin */
    }

    /* Adjust spacing for SweetAlert2 buttons */
    .swal2-confirm {
        margin-right: 5px; /* Adjust the spacing between buttons */
    }

    .swal2-cancel {
        margin-left: 5px; /* Optionally adjust left margin if needed */
    }

    .swal2-checkbox-container {
        margin-top: 5px;
    }

    .swal2-checkbox {
        margin-right: 5px;
    }

</style>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Site wrapper -->
    <div class="wrapper">
         <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Navbar Link -->
                <li class="nav-item">
                    <a href="https://momsclub.org/" target="_blank" class="hidden-xs">Return to Main Site</a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Menu for Logged In Users -->
        @auth

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="band-link">
                <img src="{{ config('settings.base_url') }}theme/dist/img/logo.png" alt="MC" class="brand-image img-circle elevation-3 custom-logo">
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">

                        <!-- Coordinator Dashboard Menu Item -->
                        <li class="nav-item">
                            <a href="{{ route('coordinators.viewprofile') }}" class="nav-link {{ Request::is('coordviewprofile') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Chapters Menu Item -->
                        @php
                            if ($coordinatorCondition) {
                                $chaptersRoute = route('chapters.chaplist');
                            } elseif ($inquiriesCondition) {
                                $chaptersRoute = route('chapters.chapinquiries');
                            } elseif ($einCondition || $adminReportCondition) {
                                $chaptersRoute = route('international.intchapter');
                            }
                            $activeChpaterRoutes = [
                                'chapter/chapterlist', 'chapterdetails/*', 'chapterdetailsedit/*', 'chapterboardedit/*', 'chapter/chapternew',
                                'chapter/zapped', 'chapter/inquiries', 'chapter/inquirieszapped', 'international/chapter', 'international/chapterzapped'
                            ];
                        @endphp
                        @if (isset($chaptersRoute))
                            <li class="nav-item">
                                <a href="{{ $chaptersRoute }}" class="nav-link {{ isActiveRoute($activeChpaterRoutes) }}">
                                    <i class="nav-icon fas fa-home"></i>
                                    <p>Chapters</p>
                                </a>
                            </li>
                        @endif

                        <!-- Coordinaros Menu Item -->
                        @php
                            if ($supervisingCoordinatorCondition) {
                                $coordinatorsRoute = route('coordinators.coordlist');
                            } elseif ($einCondition || $adminReportCondition) {
                                $coordinatorsRoute = route('international.intcoord');
                            }
                            $activeCoordinatorsRoutes = [
                                'coordinator/coordlist', 'coordinator/retired', 'coordnew', 'coorddetails/*',
                                'coorddetailsedit/*', 'coorddetailseditrole/*', 'coorddetailseditrecognition/*',
                                'international/coordinator', 'international/coordinatorretired'
                            ];
                        @endphp
                        @if (isset($coordinatorsRoute))
                            <li class="nav-item">
                                <a href="{{ $coordinatorsRoute }}" class="nav-link {{ isActiveRoute($activeCoordinatorsRoutes) }}">
                                    <i class="nav-icon fas fa-user-friends"></i>
                                    <p>Coordinators</p>
                                </a>
                            </li>
                        @endif

                        <!-- Payments/Donations Menu Item -->
                        @php
                            if ($regionalCoordinatorCondition) {
                                $paymentsRoute = route('chapters.chapreregistration');
                            } elseif ($m2mCondition || $adminReportCondition) {
                                $paymentsRoute = route('international.intdonation');
                            }
                            $activePaymentsRoutes = [
                                'chapter/reregistration', 'chapter/donations', 'international/donation','chapterpaymentedit/*'
                            ];
                        @endphp
                        @if (isset($paymentsRoute))
                            <li class="nav-item">
                                <a href="{{ $paymentsRoute }}" class="nav-link {{ isActiveRoute($activePaymentsRoutes) }}">
                                    <i class="nav-icon fas fa-dollar-sign"></i>
                                    <p>Payments/Donations</p>
                                </a>
                            </li>
                        @endif

                        <!-- Website Review Menu Item -->
                        @php
                            if ($webReviewCondition) {
                                $websiteRoute = route('chapters.chapwebsite');
                                $activeWebsiteRoutes = [
                                    'chapter/website', 'chapterdetails/*'
                                ];
                            } elseif ($regionalCoordinatorCondition) {
                                $websiteRoute = route('chapters.chapwebsite');
                                $activeWebsiteRoutes = [
                                    'chapter/website', 'chapter/socialmedia', 'chapterwebsiteedit/*'
                                ];
                            }
                        @endphp
                        @if (isset($websiteRoute))
                            <li class="nav-item">
                                <a href="{{ $websiteRoute }}" class="nav-link {{ isActiveRoute($activeWebsiteRoutes) }}">
                                    <i class="nav-icon fas fa-laptop"></i>
                                    <p>Website/Social Media</p>
                                </a>
                            </li>
                        @endif

                        <!-- BoardList Email Menu Item -->
                        @php
                            if ($listAdminCondition || $adminReportCondition) {
                                $boardlistRoute = route('chapters.chapboardlist');
                            }
                            $activeBoardlistRoutes = [
                                'chapter/boardlist'
                            ];
                        @endphp
                        @if (isset($boardlistRoute))
                            <li class="nav-item">
                                <a href="{{ $boardlistRoute }}" class="nav-link {{ isActiveRoute($activeBoardlistRoutes) }}">
                                    <i class="nav-icon fas fa-dollar-sign"></i>
                                    <p>BoardList Emails</p>
                                </a>
                            </li>
                        @endif

                        @if ($einCondition)
                        <li class="nav-item">
                            <a href="{{ route('international.inteinstatus') }}" class="nav-link {{ Request::is('chapterreports/inteinstatus') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-university"></i>
                                <p>International EIN Status</p>
                            </a>
                        </li>
                        @endif

                        <!-- Chapter Reports Menu Item -->
                        @php
                            if ($assistConferenceCoordinatorCondition) {
                                $chapterReportsRoute = route('chapreports.chaprptchapterstatus');
                            }
                            $activeChapterReportsRoutes = [
                                'chapterreports/*'
                            ];
                        @endphp
                        @if (isset($chapterReportsRoute))
                            <li class="nav-item">
                                <a href="{{ $chapterReportsRoute }}" class="nav-link {{ isActiveRoute($activeChapterReportsRoutes) }}">
                                    <i class="nav-icon fas fa-clipboard"></i>
                                    <p>Chapter Reports</p>
                                </a>
                            </li>
                        @endif

                        <!-- Coordinator Reports Menu Item -->
                        @php
                            if ($supervisingCoordinatorCondition) {
                                $coordReportsRoute = route('coordreports.coordrptvolutilization');
                            } elseif ($coordinatorCondition) {
                                $coordReportsRoute = route('coordreports.coordrptreportingtree');
                            }
                            $activeCoordReportsRoutes = [
                                'coordreports/*'
                            ];
                        @endphp
                        @if (isset($coordReportsRoute))
                            <li class="nav-item">
                                <a href="{{ $coordReportsRoute }}" class="nav-link {{ isActiveRoute($activeCoordReportsRoutes) }}">
                                    <i class="nav-icon fas fa-id-card-alt"></i>
                                    <p>Coordinator Reports</p>
                                </a>
                            </li>
                        @endif

                        <!-- End of Year Reports Menu Item -->
                        @php
                            if ($eoyReportConditionDISABLED || ($eoyReportCondition && $eoyTestCondition && $testers_yes) || ($eoyReportCondition && $coordinators_yes)) {
                                $eoyReportsRoute = route('eoyreports.eoystatus');
                            }
                            $activeEOYReportsRoutes = [
                                'eoy/*', 'eoydetails/*', 'eoydetailseditboundaries/*', 'eoydetailseditawards/*'
                            ];
                        @endphp
                        @if (isset($eoyReportsRoute))
                            <li class="nav-item">
                                <a href="{{ $eoyReportsRoute }}" class="nav-link {{ isActiveRoute($activeEOYReportsRoutes) }}">
                                    <i class="nav-icon fas fa-chart-line"></i>
                                    <p>EOY Reports</p>
                                </a>
                            </li>
                        @endif

                        <!-- Admin Reports Menu Item -->
                        @php
                            if ($adminReportCondition) {
                                $adminReportsRoute = route('admin.reregdate');
                            }
                            $activeAdminReportsRoutes = [
                                'admin/*', 'adminreports/*'
                            ];
                        @endphp
                        @if (isset($adminReportsRoute))
                            <li class="nav-item">
                                <a href="{{ $adminReportsRoute }}" class="nav-link {{ isActiveRoute($activeAdminReportsRoutes) }}">
                                    <i class="nav-icon fas fa-unlock"></i>
                                    <p>Admin Tasks/Reports</p>
                                </a>
                            </li>
                        @endif

                        <!-- Resources Reports Menu Item -->
                        @php
                            $resourcesRoute = route('admin.toolkit');
                            $activeResourcesRoutes = [
                                'resources/*'
                            ];
                        @endphp
                        @if (isset($resourcesRoute))
                            <li class="nav-item">
                                <a href="{{ $resourcesRoute }}" class="nav-link {{ isActiveRoute($activeResourcesRoutes) }}">
                                    <i class="nav-icon fas fa-briefcase"></i>
                                    <p>Resources</p>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a href="{{ route('coordinators.profile') }}" class="nav-link {{ Request::is('coordprofile') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-edit"></i>
                            <p>Update Profile</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('logout') }}" class="nav-link"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>

                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        @endauth
        <!-- /.menu -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Default box -->
                        <div class="content-wrapper">

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

                        <section class="content-header">
                            <div class="container-fluid">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <h1>@yield('page_title')</h1>
                                    </div>
                                    <div class="col-sm-6">
                                        <ol class="breadcrumb float-sm-right">
                                            <li class="breadcrumb-item">
                                                <a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Dashboard</a>
                                            </li>
                                            <li class="breadcrumb-item active">@yield('breadcrumb')</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </section>

                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->

    </div>
    <footer class="main-footer">
        <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="https://momsclub.org/" target="_blank">MOMS Club</a>.</strong> All rights reserved.
    </footer>
</div>

<!-- jQuery -->
<script src="{{ config('settings.base_url') }}theme/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{ config('settings.base_url') }}theme/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="{{ config('settings.base_url') }}theme/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
{{-- <script src="{{ config('settings.base_url') }}coordinator_theme/dist/js/adminlte.min.js"></script> --}}

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

<!-- DataTables  & Plugins -->
<script src="{{ config('settings.base_url') }}theme/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/jszip/jszip.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/pdfmake/pdfmake.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/pdfmake/vfs_fonts.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script>
   $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy hh:mm:ss
    $('#datemask2').inputmask('mm/dd/yyyy hh:mm:ss', { 'placeholder': 'mm/dd/yyyy hh:mm:ss' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date picker
    $('#datepicker').datetimepicker({
        format: 'L'
    });
     //Date picker
     $('#datepicker1').datetimepicker({
        format: 'L'
    });

    //Date and time picker
    $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY hh:mm A'
      }
    })

    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })


  })

  //Cusotmize AJAX Popups to Match Theme
  function customSuccessAlert(message) {
        Swal.fire({
            title: 'Success!',
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
        title: 'Oops!',
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
        title: 'Did You Know?',
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
        title: 'Oops!',
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

 <script>

 $(function () {
    $('#chapterlist').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });

    $('#coordinatorlist').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });

  });

  function convertDateFormat(dateString) {
        var parts = dateString.split('-');
        return parts[1] + '/' + parts[2] + '/' + parts[0];
    }

    function applyDateMask() {
        $('.date-mask').each(function() {
            var originalDate = $(this).text();
            var formattedDate = convertDateFormat(originalDate);
            $(this).text(formattedDate);
        });
        Inputmask({"mask": "99/99/9999"}).mask(".date-mask");
    }

    $(document).ready(function() {
        var table = $('#coordinatorlist').DataTable();

        applyDateMask();

        table.on('draw', function() {
            applyDateMask();
        });
    });


  function applyPhoneMask() {
        Inputmask({"mask": "(999) 999-9999"}).mask(".phone-mask");
    }

    function applyHttpMask() {
        Inputmask({"mask": "http://*{1,250}.*{2,25}"}).mask(".http-mask");
    }

    $(document).ready(function() {
        var table = $('#chapterlist').DataTable();

        applyPhoneMask();

        table.on('draw', function() {
            applyPhoneMask();
        });

        applyDateMask();

        table.on('draw', function() {
            applyDateMask();
        });

        applyHttpMask();

        table.on('draw', function() {
            applyHttpMask();
        });

    });


 </script>
 @yield('customscript')
 </html>

