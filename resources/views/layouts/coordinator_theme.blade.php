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
                        <li class="nav-item">
                            <a href="{{ route('coordinators.coorddashboard') }}" class="nav-link {{ Request::is('coordinator/dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        @if ($coordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapters.chaplist') }}" class="nav-link {{ Request::is('chapter/chapterlist') ? 'active' : '' }} {{ Request::is('chapter/chapterview/*') ? 'active' : '' }}
                                     {{ Request::is('chapter/chapternew') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Chapter List</p>
                            </a>
                        </li>
                        @endif
                        @if ($regionalCoordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapters.chapzapped') }}" class="nav-link {{ Request::is('chapter/zapped') ? 'active' : '' }}  {{ Request::is('chapter/zappedview/*') ? 'active' : '' }}">
                                <span class="nav-icon fa-layers fa-fw">
                                    <i class="fas fa-list"></i>
                                    <i class="fas fa-slash"></i>
                                </span>
                                <p>Zapped Chapter List</p>
                            </a>
                        </li>
                        @endif
                        @if ($regionalCoordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapters.chapreregistration') }}" class="nav-link {{ Request::is('chapter/reregistration') ? 'active' : '' }} {{ Request::is('chapter/reregistrationpayment/*') ? 'active' : '' }}
                                    {{ Request::is('chapter/reregistrationnotes/*') ? 'active' : '' }}">
                                <i class="nav-icon far fa-credit-card"></i>
                                <p>Re-Registration</p>
                            </a>
                        </li>
                        @endif
                        @if ($inquiriesCondition || $regionalCoordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapters.chapinquiries') }}" class="nav-link {{ Request::is('chapter/inquiries') ? 'active' : '' }} {{ Request::is('chapter/inquiriesview/*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-map-marker-alt"></i>
                                <p>Inquiries</p>
                            </a>
                        </li>
                        @endif
                        @if ($inquiriesCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapters.chapinquirieszapped') }}" class="nav-link {{ Request::is('chapter/inquirieszapped') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-ban"></i>
                                <p>Zapped Chapters</p>
                            </a>
                        </li>
                        @endif
                        @if ($webReviewCondition || $regionalCoordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapters.chapwebsite') }}" class="nav-link {{ Request::is('chapter/website') ? 'active' : '' }} {{ Request::is('chapter/websiteview/*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-laptop"></i>
                                <p>Website Review</p>
                            </a>
                        </li>
                        @endif
                        @if ($listAdminCondition || $ITCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapters.chapboardlist') }}" class="nav-link {{ Request::is('chapter/updatewebsite') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-paste"></i>
                                <p>BoardList</p>
                            </a>
                        </li>
                        @endif
                        @if ($regionalCoordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('coordinators.coordlist') }}" class="nav-link {{ Request::is('coordinator/coordlist') ? 'active' : '' }} {{ Request::is('coordinator/coordinatornew') ? 'active' : '' }}
                                {{ Request::is('coordinator/coordinatorview/*') ? 'active' : '' }} {{ Request::is('coordinator/roleview/*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Coordinator List</p>
                            </a>
                        </li>
                        @endif
                        @if ($regionalCoordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('coordinators.coordretired') }}" class="nav-link {{ Request::is('coordinator/retired') ? 'active' : '' }} {{ Request::is('coordinator/retiredview/*') ? 'active' : '' }}">
                                <span class="nav-icon fa-layers fa-fw">
                                    <i class="fas fa-users"></i>
                                    <i class="fas fa-slash"></i>
                                </span>
                                <p>Retired Coordinator List</p>
                            </a>
                        </li>
                        @endif
                        @if ($einCondition)
                        <li class="nav-item">
                            <a href="{{ route('international.intchapter') }}" class="nav-link {{ Request::is('international/chapter') ? 'active' : '' }} {{ Request::is('international/chapterview/*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>International Chapter List</p>
                            </a>
                        </li>
                        @endif
                        @if ($einCondition)
                        <li class="nav-item">
                            <a href="{{ route('international.intchapterzapped') }}" class="nav-link {{ Request::is('international/chapterzapped') ? 'active' : '' }} {{ Request::is('international/chapterzappedview/*') ? 'active' : '' }}">
                                <span class="nav-icon fa-layers fa-fw">
                                    <i class="fas fa-list"></i>
                                    <i class="fas fa-slash"></i>
                                </span>
                                <p>International Zapped Chapter List</p>
                            </a>
                        </li>
                        @endif
                        @if ($einCondition)
                        <li class="nav-item">
                            <a href="{{ route('international.inteinstatus') }}" class="nav-link {{ Request::is('international/einstatus') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-university"></i>
                                <p>International EIN Status</p>
                            </a>
                        </li>
                        @endif
                        @if ($m2mCondition)
                        <li class="nav-item">
                            <a href="{{ route('international.intdonation') }}" class="nav-link {{ Request::is('nternational/donation') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-money-bill-alt"></i>
                                <p>International Donations List</p>
                            </a>
                        </li>
                        @endif

                        @if ($adminReportCondition)
                        <li class="nav-item {{ Request::is('international/*') ? 'menu-open' : '' }} "> <a href="#" class="nav-link {{ Request::is('internatinal/*') ? 'active' : '' }} ">
                                <i class="nav-icon fa fa-globe"></i>
                                <p>International Lists<i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                <a href="{{ route('international.intchapter') }}" class="nav-link {{ Request::is('international/chapter') ? 'active' : '' }} {{ Request::is('international/chapterview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-list"></i>
                                    <p>Chapter List</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('international.intchapterzapped') }}" class="nav-link {{ Request::is('international/chapterzapped') ? 'active' : '' }} {{ Request::is('international/chapterzappedview/*') ? 'active' : '' }}">
                                    <span class="nav-icon fa-layers fa-fw">
                                        <i class="fas fa-list"></i>
                                        <i class="fas fa-slash"></i>
                                    </span>
                                    <p>Zapped Chapter List</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('international.intcoord') }}" class="nav-link {{ Request::is('international/coordinator') ? 'active' : '' }} {{ Request::is('international/coordinatorview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Coordinator List</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('international.intcoordretired') }}" class="nav-link {{ Request::is('international/coordinatorretired') ? 'active' : '' }} {{ Request::is('iternational/coordinatorretiredview/*') ? 'active' : '' }}">
                                    <span class="nav-icon fa-layers fa-fw">
                                        <i class="fas fa-users"></i>
                                        <i class="fas fa-slash"></i>
                                    </span>
                                    <p>Retired Coordinator List</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('international.inteinstatus') }}" class="nav-link {{ Request::is('international/einstatus') ? 'active' : '' }} {{ Request::is('iternational/einstatusview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-university"></i>
                                    <p>Chapter EIN Status</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('international.intdonation') }}" class="nav-link {{ Request::is('international/donation') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-money-bill-alt"></i>
                                    <p>Donations List</p>
                                </a>
                            </li>
                            </ul>
                        </li>
                        @endif

                        @if ($regionalCoordinatorCondition)
                        <li class="nav-item {{ Request::is('chapterreports/*') ? 'menu-open' : '' }} "> <a href="#" class="nav-link {{ Request::is('chapterreports/*') ? 'active' : '' }} ">
                             <i class="nav-icon fas fa-tasks"></i>
                                <p>Chapter Reports<i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                <a href="{{ route('chapreports.chaprptchapterstatus') }}" class="nav-link {{ Request::is('chapterreports/chapterstatus') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-check-square"></i>
                                    <p>Chapter Status</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('chapreports.chaprpteinstatus') }}" class="nav-link {{ Request::is('chapterreports/einstatus') ? 'active' : '' }} {{ Request::is('chapterreports/einstatusview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-university"></i>
                                    <p>Chapter EIN Status</p>
                                </a>
                            </li>
                            @if ($assistConferenceCoordinatorCondition)
                                <li class="nav-item">
                                <a href="{{ route('chapreports.chaprptnewchapters') }}" class="nav-link {{ Request::is('chapterreports/newchapters') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-plus-square"></i>
                                    <p>New Chapters</p>
                                </a>
                            </li>
                            @endif
                                <li class="nav-item">
                                <a href="{{ route('chapreports.chaprptlargechapters') }}" class="nav-link {{ Request::is('chapterreports/largechapters') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-expand-alt"></i>
                                    <p>Large Chapters</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('chapreports.chaprptprobation') }}" class="nav-link {{ Request::is('chapterreports/probation') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-times-circle"></i>
                                    <p>Probation Chapters</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('chapreports.chaprptdonations') }}" class="nav-link {{ Request::is('chapterreports/donations') ? 'active' : '' }} {{ Request::is('chapterreports/donationsview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-money-bill-alt"></i>
                                    <p>Donations List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('chapreports.chaprptsocialmedia') }}" class="nav-link {{ Request::is('chapterreports/socialmedia') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-share-alt"></i>
                                    <p>Social Media</p>
                                </a>
                                </li>
                            <li class="nav-item">
                                <a href="{{ route('chapreports.chaprptcoordinators') }}" class="nav-link {{ Request::is('chapterreports/coordinators') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-house-user"></i>
                                    <p>Chapter Coordinators</p>
                                </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if ($coordinatorCondition)
                        <li class="nav-item {{ Request::is('coordreports/*') ? 'menu-open' : '' }} "> <a href="#" class="nav-link {{ Request::is('coordreports/*') ? 'active' : '' }} ">
                                <i class="nav-icon fas fa-id-card"></i>
                                <p>Coordinator Reports<i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @if ($supervisingCoordinatorCondition)
                                <li class="nav-item">
                                <a href="{{ route('coordreports.coordrptvolutilization') }}" class="nav-link {{ Request::is('coordreports/volunteerutilization') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-check-square"></i>
                                    <p>Volunteer Utilization</p>
                                </a>
                            </li>
                            @endif
                            @if ($conferenceCoordinatorCondition && !$founderCondition)
                            <li class="nav-item">
                                <a href="{{ route('coordreports.coordrpttodo') }}" class="nav-link {{ Request::is('coordreports/coordinatortodo') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-university"></i>
                                    <p>Cooordinator ToDo</p>
                                </a>
                            </li>
                            @endif
                            {{-- @if ($founderCondition)
                            <li class="nav-item">
                                <a href="{{ route('report.intcoordinatortodo') }}" class="nav-link {{ Request::is('coordreports/intcoordinatortodo') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-plus-square"></i>
                                    <p>Coordinator ToDo</p>
                                </a>
                            </li>
                            @endif --}}
                            @if ($assistConferenceCoordinatorCondition)
                                <li class="nav-item">
                                <a href="{{ route('coordreports.coordrptappreciation') }}" class="nav-link {{ Request::is('coordreports/appreciation') ? 'active' : '' }} {{ Request::is('coordreports/appreciationview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-expand-alt"></i>
                                    <p>Volunteer Appreciation</p>
                                </a>
                                </li>
                                @endif
                                @if ($regionalCoordinatorCondition)
                                <li class="nav-item">
                                <a href="{{ route('coordreports.coordrptbirthdays') }}" class="nav-link {{ Request::is('coordreports/birthdays') ? 'active' : '' }} {{ Request::is('coordreports/birthdaysview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-times-circle"></i>
                                    <p>Volunteer Birthdays</p>
                                </a>
                                </li>
                                @endif
                                <li class="nav-item">
                                <a href="{{ route('coordreports.coordrptreportingtree') }}" class="nav-link {{ Request::is('coordreports/reportingtree') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-money-bill-alt"></i>
                                    <p>Reporting Tree</p>
                                </a>
                            </li>
                            </ul>
                        </li>
                        @endif

                        @if ($eoyReportConditionDISABLED || ($eoyReportCondition && $eoyTestCondition && $testers_yes) || ($eoyReportCondition && $coordinators_yes))
                        <li class="nav-item {{ Request::is('eoy/*') ? 'menu-open' : '' }} "> <a href="#" class="nav-link {{ Request::is('eoy/*') ? 'active' : '' }} ">
                                <i class="nav-icon far fa-chart-bar"></i>
                                <p>EOY Reports<i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                <a href="{{ route('eoyreports.eoystatus') }}" class="nav-link {{ Request::is('eoy/status') ? 'active' : '' }} {{ Request::is('eoy/statusview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tasks"></i>
                                    <p>End of Year Status</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('eoyreports.eoyboardreport') }}" class="nav-link {{ Request::is('eoy/boardreport') ? 'active' : '' }} {{ Request::is('eoy/boardreportview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Board Reports</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('eoyreports.eoyfinancialreport') }}" class="nav-link {{ Request::is('eoy/financialreport') ? 'active' : '' }} {{ Request::is('eoy/financialreportview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                    <p>Financial Reports</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('eoyreports.eoyattachments') }}" class="nav-link {{ Request::is('eoy/attachments') ? 'active' : '' }} {{ Request::is('eoy/attachmentsview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-paperclip"></i>
                                    <p>Report Attachments</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('eoyreports.eoyboundaries') }}" class="nav-link {{ Request::is('eoy/boundaries') ? 'active' : '' }} {{ Request::is('eoy/boundariesview/*') ? 'active' : '' }}">
                                    <i class="nav-icon far fa-map"></i>
                                    <p>Boundary Issues</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('eoyreports.eoyawards') }}" class="nav-link {{ Request::is('eoy/awards') ? 'active' : '' }} {{ Request::is('eoy/awardsview') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-award"></i>
                                    <p>Chapter Awards</p>
                                </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if ($adminReportCondition)
                        <li class="nav-item {{ Request::is('admin/eoy') ? 'menu-open' : '' }} {{ Request::is('admin/reregdate') ? 'menu-open' : '' }}
                                            {{ Request::is('admin/reregdate/*') ? 'menu-open' : '' }} {{ Request::is('adminreports/duplicateuser') ? 'menu-open' : '' }}
                                            {{ Request::is('adminreports/duplicateboardid') ? 'menu-open' : '' }} {{ Request::is('adminreports/multipleboard') ? 'menu-open' : '' }}
                                            {{ Request::is('adminreports/nopresident') ? 'menu-open' : '' }} {{ Request::is('adminreports/outgoingboard') ? 'menu-open' : '' }}
                                             {{ Request::is('admin/jobs') ? 'menu-open' : '' }}  {{ Request::is('admin/googledrive') ? 'menu-open' : '' }}
                                              {{ Request::is('admin/logs') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('admin/eoy') ? 'active' : '' }} {{ Request::is('admin/reregdate') ? 'active' : '' }}
                                                        {{ Request::is('admin/reregdate/*') ? 'active' : '' }} {{ Request::is('adminreports/duplicateuser') ? 'active' : '' }}
                                                        {{ Request::is('adminreports/duplicateboardid') ? 'active' : '' }} {{ Request::is('adminreports/multipleboard') ? 'active' : '' }}
                                                        {{ Request::is('adminreports/nopresident') ? 'active' : '' }} {{ Request::is('adminreports/outgoingboard') ? 'active' : '' }}
                                                         {{ Request::is('admin/jobs') ? 'active' : '' }} {{ Request::is('admin/googledrive') ? 'active' : '' }}
                                                          {{ Request::is('admin/logs') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-unlock-alt"></i>
                                <p>Admin Items<i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                <a href="{{ route('admin.eoy') }}" class="nav-link {{ Request::is('admin/eoy') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-bullseye"></i>
                                    <p>EOY Procedures</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.googledrive') }}" class="nav-link {{ Request::is('admin/googledrive') ? 'active' : '' }}">
                                    <i class="nav-icon fab fa-google"></i>
                                    <p>Google Drive</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('admin.reregdate') }}" class="nav-link {{ Request::is('admin/reregdate') ? 'active' : '' }} {{ Request::is('admin/reregdate/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-calendar-alt"></i>
                                    <p>Re-Reg Renewal Dates</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('admin.duplicateuser') }}" class="nav-link {{ Request::is('adminreports/duplicateuser') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-friends"></i>
                                    <p>Duplicate Users</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.duplicateboardid') }}" class="nav-link {{ Request::is('adminreports/duplicateboardid') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-exchange-alt"></i>
                                    <p>Duplicate Board Details</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('admin.nopresident') }}" class="nav-link {{ Request::is('adminreports/nopresident') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-minus-circle"></i>
                                    <p>No President</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.outgoingboard') }}" class="nav-link {{ Request::is('adminreports/outgoingboard') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-share"></i>
                                    <p>Outgoing Board</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                    <a href="{{ route('queue-monitor::index') }}" target="_blank" class="nav-link {{ Request::is('admin/jobs') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-envelope"></i>
                                    <p>Mail Queue</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ url(config('sentemails.routepath')) }}" target="_blank" class="nav-link {{ Request::is('admin/sentemails') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-share-square"></i>
                                    <p>Sent Email</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logs') }}" target="_blank" class="nav-link {{ Request::is('admin/logs') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-exclamation-triangle"></i>
                                        <p>Error Logs</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        <li class="nav-item {{ Request::is('admin/downloads') ? 'menu-open' : '' }} {{ Request::is('admin/bugs') ? 'menu-open' : '' }}
                                            {{ Request::is('admin/resources') ? 'menu-open' : '' }} {{ Request::is('admin/toolkit') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('admin/downloads') ? 'active' : '' }} {{ Request::is('admin/bugs') ? 'active' : '' }}
                                                        {{ Request::is('admin/resources') ? 'active' : '' }} {{ Request::is('admin/toolkit') ? 'active' : '' }} ">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>Resources<i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                        @if ($assistConferenceCoordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('admin.downloads') }}" class="nav-link {{ Request::is('admin/downloads') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-download"></i>
                            <p>Download Reports</p>
                            </a>
                        </li>
                        @endif
                        @if ($regionalCoordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('admin.bugs') }}" class="nav-link {{ Request::is('admin/bugs') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-bug"></i>
                            <p>MIMI Bugs & Wishes</p>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('admin.resources') }}" class="nav-link {{ Request::is('admin/resources') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-folder-open"></i>
                            <p>Chapter Resources</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.toolkit') }}" class="nav-link {{ Request::is('admin/toolkit') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-toolbox"></i>
                            <p>Coordinator Toolkit</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="https://momsclub.org/elearning/" target="_blank" class="nav-link">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>eLearning</p>
                            </a>
                        </li>
                    </li>
                </ul>

                        <li class="nav-item">
                            <a href="{{ route('coordinators.coordprofile') }}" class="nav-link {{ Request::is('coordinator/profile') ? 'active' : '' }}">
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

    });

 </script>
 @yield('customscript')
 </html>

