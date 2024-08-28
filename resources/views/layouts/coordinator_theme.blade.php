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
</style>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Site wrapper -->
    <div class="wrapper">

        @php
        $coordinatorCondition = ($positionid >= 1 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25);  //*BS-Founder & ACC
        $founderCondition = $positionid == 7;  //*Founder
        $conferenceCoordinatorCondition = ($positionid >= 6 && $positionid <= 7);  //*CC-Founder
        $assistConferenceCoordinatorCondition = ($positionid >= 6 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25);  //CC-Founder & ACC
        $regionalCoordinatorCondition = ($positionid >= 5 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25);  //*RC-Founder & ACC
        $assistRegionalCoordinatorCondition = ($positionid >= 4 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25);  //*ARC-Founder & ACC
        $supervisingCoordinatorCondition =  ($positionid >= 3 && $positionid <= 7 || $positionid == 25);  //*SC-Founder & ACC
        $areaCoordinatorCondition =  ($positionid >= 2 && $positionid <= 7 || $positionid == 25);  //*AC-Founder & ACC
        $bigSisterCondition = ($positionid >= 1 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25);  //*BS-Founder & ACC

        $eoyTestCondition = ($positionid >= 6 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25) ||
                ($positionid == 29 || $secpositionid == 29);  //CC-Founder & ACC, AR Tester
        $eoyReportCondition = ($positionid >= 1 && $positionid <= 7) || ($positionid == 25 || $secpositionid == 25) ||
                ($positionid == 19 || $secpositionid == 19) || ($positionid == 29 || $secpositionid == 29);  //*BS-Founder & ACC, AR Reviewer, AR Tester
        $eoyReportConditionDISABLED = ($positionid == 13 || $secpositionid == 13);  //*IT Coordinator
        $inquiriesCondition = ($positionid == 8 || $secpositionid == 8);  //*Inquiries Coordinator
        $webReviewCondition = ($positionid == 9 || $secpositionid == 9);  //*Website Reviewer
        $einCondition = ($positionid == 12 || $secpositionid == 12);  //*EIN Coordinator
        $adminReportCondition = ($positionid == 13 || $secpositionid == 13);  //*IT Coordinator
        $m2mCondition = ($positionid == 21 || $secpositionid == 21);  //*M2M Committee
        $listAdminCondition = ($positionid == 23 || $secpositionid == 23);  //*ListAdmin
    @endphp

    @php
        $admin = DB::table('admin')
            ->select('admin.*',
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'),)
            ->leftJoin('coordinator_details as cd', 'admin.updated_id', '=', 'cd.coordinator_id')
            ->orderBy('admin.id', 'desc') // Assuming 'id' represents the order of insertion
            ->first();

        $eoy_testers = $admin->eoy_testers;
        $eoy_coordinators = $admin->eoy_coordinators;

        $testers_yes = ($eoy_testers == 1);
        $coordinators_yes = ($eoy_coordinators == 1);
    @endphp

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
                            <a href="{{ route('coordinator.showdashboard') }}" class="nav-link {{ Request::is('coordinator/dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        @if ($coordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapter.list') }}" class="nav-link {{ Request::is('chapter/list') ? 'active' : '' }} {{ Request::is('chapter/edit/*') ? 'active' : '' }}
                                     {{ Request::is('chapter/create') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Chapter List</p>
                            </a>
                        </li>
                        @endif
                        @if ($regionalCoordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapter.zapped') }}" class="nav-link {{ Request::is('chapter/zapped') ? 'active' : '' }}  {{ Request::is('chapter/zapped/view/*') ? 'active' : '' }}">
                                <span class="nav-icon fa-layers fa-fw">
                                    <i class="fas fa-list"></i>
                                    <i class="fas fa-slash"></i>
                                </span>
                                <p>Zapped Chapter List</p>
                            </a>
                        </li>
                        @endif
                        @if ($regionalCoordinatorCondition || $inquiriesCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapter.registration') }}" class="nav-link {{ Request::is('chapter/re-registration') ? 'active' : '' }} {{ Request::is('chapter/re-registration/payment/*') ? 'active' : '' }}
                                    {{ Request::is('chapter/re-registration/notes/*') ? 'active' : '' }}">
                                <i class="nav-icon far fa-credit-card"></i>
                                <p>Re-Registration</p>
                            </a>
                        </li>
                        @endif
                        @if ($regionalCoordinatorCondition || $inquiriesCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapter.inquiries') }}" class="nav-link {{ Request::is('chapter/inquiries') ? 'active' : '' }} {{ Request::is('chapter/inquiriesview/*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-map-marker-alt"></i>
                                <p>Inquiries</p>
                            </a>
                        </li>
                        @endif
                        @if ($inquiriesCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapter.inquirieszapped') }}" class="nav-link {{ Request::is('chapter/inquirieszapped') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-ban"></i>
                                <p>Zapped Chapters</p>
                            </a>
                        </li>
                        @endif
                        @if ($regionalCoordinatorCondition || $webReviewCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapter.website') }}" class="nav-link {{ Request::is('chapter/website') ? 'active' : '' }} {{ Request::is('chapter/website/edit/*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-laptop"></i>
                                <p>Website Review</p>
                            </a>
                        </li>
                        @endif
                        @if ($adminReportCondition || $listAdminCondition)
                        <li class="nav-item">
                            <a href="{{ route('report.boardlist') }}" class="nav-link {{ Request::is('reports/boardlist') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-bars"></i>
                                <p>BoardList</p>
                            </a>
                        </li>
                        @endif
                        @if ($adminReportCondition || $listAdminCondition)
                        <li class="nav-item">
                            <a href="{{ route('coordinator.list') }}" class="nav-link {{ Request::is('coordinatorlist') ? 'active' : '' }} {{ Request::is('coordinator/create') ? 'active' : '' }}
                                {{ Request::is('coordinator/edit/*') ? 'active' : '' }} {{ Request::is('coordinator/role/*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-friends"></i>
                                <p>Coordinator List</p>
                            </a>
                        </li>
                        @endif
                        @if ($regionalCoordinatorCondition || $inquiriesCondition)
                        <li class="nav-item">
                            <a href="{{ route('coordinator.retired') }}" class="nav-link {{ Request::is('coordinator/retired') ? 'active' : '' }} {{ Request::is('coordinator/retired/view/*') ? 'active' : '' }}">
                                <span class="nav-icon fa-layers fa-fw">
                                    <i class="fas fa-user-friends"></i>
                                    <i class="fas fa-slash"></i>
                                </span>
                                <p>Retired Coordinator List</p>
                            </a>
                        </li>
                        @endif
                        @if ($einCondition || $inquiriesCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapter.inter') }}" class="nav-link <?php if (($positionid == 12)){ ?> {{ Request::is('home') ? 'active' : '' }}<?php }?>  {{ Request::is('chapter/international') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>International Chapter List</p>
                            </a>
                        </li>
                        @endif
                        @if ($einCondition)
                        <li class="nav-item">
                            <a href="{{ route('chapter.interzap') }}" class="nav-link {{ Request::is('chapter/international/zap') ? 'active' : '' }} {{ Request::is('chapter/international/zapped/view/*') ? 'active' : '' }}">
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
                            <a href="{{ route('report.inteinstatus') }}" class="nav-link {{ Request::is('reports/inteinstatus') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-university"></i>
                                <p>Chapter EIN Status</p>
                            </a>
                        </li>
                        @endif
                        @if ($m2mCondition)
                        <li class="nav-item">
                            <a href="{{ route('report.intm2mdonation') }}" class="nav-link {{ Request::is('reports/intm2mdonation') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-money-bill-alt"></i>
                                <p>M2M Donations</p>
                            </a>
                        </li>
                        @endif

                        @if ($adminReportCondition)
                        <li class="nav-item {{ Request::is('chapter/international') ? 'menu-open' : '' }} {{ Request::is('chapter/international/view/*') ? 'menu-open' : '' }}
                                            {{ Request::is('chapter/international/zap') ? 'menu-open' : '' }} {{ Request::is('chapter/international/zapped/view/*') ? 'menu-open' : '' }}
                                            {{ Request::is('chapter/international/zap') ? 'menu-open' : '' }} {{ Request::is('chapter/international/zapped/view/*') ? 'menu-open' : '' }}
                                            {{ Request::is('coordinator/international') ? 'menu-open' : '' }} {{ Request::is('coordinator/international/view/*') ? 'menu-open' : '' }}
                                            {{ Request::is('coordinator/retiredinternational') ? 'menu-open' : '' }} {{ Request::is('coordinator/retiredinternational/view/*') ? 'menu-open' : '' }}
                                            {{ Request::is('reports/inteinstatus') ? 'menu-open' : '' }} {{ Request::is('reports/intm2mdonation') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('chapter/international') ? 'active' : '' }} {{ Request::is('chapter/international/view/*') ? 'active' : '' }}
                                                        {{ Request::is('chapter/international/zap') ? 'active' : '' }} {{ Request::is('chapter/international/zapped/view/*') ? 'active' : '' }}
                                                        {{ Request::is('chapter/international/zap') ? 'active' : '' }} {{ Request::is('chapter/international/zapped/view/*') ? 'active' : '' }}
                                                        {{ Request::is('coordinator/international') ? 'active' : '' }} {{ Request::is('coordinator/international/view/*') ? 'active' : '' }}
                                                        {{ Request::is('coordinator/retiredinternational') ? 'active' : '' }} {{ Request::is('coordinator/retiredinternational/view/*') ? 'active' : '' }}
                                                        {{ Request::is('reports/inteinstatus') ? 'active' : '' }} {{ Request::is('reports/intm2mdonation') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-globe"></i>
                                <p>International Lists<i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                <a href="{{ route('chapter.inter') }}" class="nav-link {{ Request::is('chapter/international') ? 'active' : '' }} {{ Request::is('chapter/international/view/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-list"></i>
                                    <p>Chapter List</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('chapter.interzap') }}" class="nav-link {{ Request::is('chapter/international/zap') ? 'active' : '' }} {{ Request::is('chapter/international/zapped/view/*') ? 'active' : '' }}">
                                    <span class="nav-icon fa-layers fa-fw">
                                        <i class="fas fa-list"></i>
                                        <i class="fas fa-slash"></i>
                                    </span>
                                    <p>Zapped Chapter List</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('coordinator.inter') }}" class="nav-link {{ Request::is('coordinator/international') ? 'active' : '' }} {{ Request::is('coordinator/international/view/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-friends"></i>
                                    <p>Coordinator List</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('coordinator.retinter') }}" class="nav-link {{ Request::is('coordinator/retiredinternational') ? 'active' : '' }} {{ Request::is('coordinator/retiredinternational/view/*') ? 'active' : '' }}">
                                    <span class="nav-icon fa-layers fa-fw">
                                        <i class="fas fa-user-friends"></i>
                                        <i class="fas fa-slash"></i>
                                    </span>
                                    <p>Retired Coordinator List</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('report.inteinstatus') }}" class="nav-link {{ Request::is('reports/inteinstatus') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-university"></i>
                                    <p>Chapter EIN Status</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('report.intm2mdonation') }}" class="nav-link {{ Request::is('reports/intm2mdonation') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-money-bill-alt"></i>
                                    <p>M2M Donations</p>
                                </a>
                            </li>
                            </ul>
                        </li>
                        @endif

                        @if ($regionalCoordinatorCondition)
                        <li class="nav-item {{ Request::is('reports/chapterstatus') ? 'menu-open' : '' }} {{ Request::is('reports/chapternew') ? 'menu-open' : '' }}
                                            {{ Request::is('reports/chapterlarge') ? 'menu-open' : '' }} {{ Request::is('reports/chapterprobation') ? 'menu-open' : '' }}
                                            {{ Request::is('reports/chaptercoordinators') ? 'menu-open' : '' }} {{ Request::is('reports/m2mdonation') ? 'menu-open' : '' }}
                                            {{ Request::is('chapter/m2mdonation/*') ? 'menu-open' : '' }} {{ Request::is('reports/einstatus') ? 'menu-open' : '' }}
                                            {{ Request::is('reports/socialmedia') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('reports/chapterstatus') ? 'active' : '' }} {{ Request::is('reports/chapternew') ? 'active' : '' }}
                                                        {{ Request::is('reports/chapterlarge') ? 'active' : '' }} {{ Request::is('reports/chapterprobation') ? 'active' : '' }}
                                                        {{ Request::is('reports/chaptercoordinators') ? 'active' : '' }} {{ Request::is('reports/m2mdonation') ? 'active' : '' }}
                                                        {{ Request::is('chapter/m2mdonation/*') ? 'active' : '' }} {{ Request::is('reports/einstatus') ? 'active' : '' }}
                                                        {{ Request::is('reports/socialmedia') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-laptop-house"></i>
                                <p>Chapter Reports<i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                <a href="{{ route('report.chapterstatus') }}" class="nav-link {{ Request::is('reports/chapterstatus') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-check-square"></i>
                                    <p>Chapter Status</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.einstatus') }}" class="nav-link {{ Request::is('reports/einstatus') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-university"></i>
                                    <p>Chapter EIN Status</p>
                                </a>
                            </li>
                            @if ($assistConferenceCoordinatorCondition)
                                <li class="nav-item">
                                <a href="{{ route('report.chapternew') }}" class="nav-link {{ Request::is('reports/chapternew') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-plus-square"></i>
                                    <p>New Chapters</p>
                                </a>
                            </li>
                            @endif
                                <li class="nav-item">
                                <a href="{{ route('report.chapterlarge') }}" class="nav-link {{ Request::is('reports/chapterlarge') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-expand-alt"></i>
                                    <p>Large Chapters</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('report.chapterprobation') }}" class="nav-link {{ Request::is('reports/chapterprobation') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-times-circle"></i>
                                    <p>Probation Chapters</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('report.m2mdonation') }}" class="nav-link {{ Request::is('reports/m2mdonation') ? 'active' : '' }} {{ Request::is('chapter/m2mdonation/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-money-bill-alt"></i>
                                    <p>M2M Donations</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('report.socialmedia') }}" class="nav-link {{ Request::is('reports/socialmedia') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-share-alt"></i>
                                    <p>Social Media</p>
                                </a>
                                </li>
                            <li class="nav-item">
                                <a href="{{ route('report.chaptercoordinators') }}" class="nav-link {{ Request::is('reports/chaptercoordinators') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-house-user"></i>
                                    <p>Chapter Coordinators</p>
                                </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if ($coordinatorCondition)
                        <li class="nav-item {{ Request::is('reports/chaptervolunteer') ? 'menu-open' : '' }} {{ Request::is('reports/coordinatortodo') ? 'menu-open' : '' }}
                                            {{ Request::is('reports/intcoordinatortodo') ? 'menu-open' : '' }} {{ Request::is('reports/appreciation') ? 'menu-open' : '' }}
                                            {{ Request::is('reports/birthday') ? 'menu-open' : '' }} {{ Request::is('coordinator/appreciation/*') ? 'menu-open' : '' }}
                                            {{ Request::is('reports/reportingtree') ? 'menu-open' : '' }} {{ Request::is('coordinator/birthday/*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('reports/chaptervolunteer') ? 'active' : '' }} {{ Request::is('reports/coordinatortodo') ? 'active' : '' }}
                                                        {{ Request::is('reports/intcoordinatortodo') ? 'active' : '' }} {{ Request::is('reports/appreciation') ? 'active' : '' }}
                                                        {{ Request::is('reports/birthday') ? 'active' : '' }} {{ Request::is('coordinator/appreciation/*') ? 'active' : '' }}
                                                        {{ Request::is('reports/reportingtree') ? 'active' : '' }} {{ Request::is('coordinator/birthday/*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-laptop-house"></i>
                                <p>Coordinator Reports<i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                @if ($supervisingCoordinatorCondition)
                                <li class="nav-item">
                                <a href="{{ route('report.chaptervolunteer') }}" class="nav-link {{ Request::is('reports/chaptervolunteer') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-check-square"></i>
                                    <p>Volunteer Utilization</p>
                                </a>
                            </li>
                            @endif
                            @if (($positionid ==6 || $positionid ==25))
                            <li class="nav-item">
                                <a href="{{ route('report.coordinatortodo') }}" class="nav-link {{ Request::is('reports/coordinatortodo') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-university"></i>
                                    <p>Cooordinator ToDo</p>
                                </a>
                            </li>
                            @endif
                            @if ($founderCondition)
                            <li class="nav-item">
                                <a href="{{ route('report.intcoordinatortodo') }}" class="nav-link {{ Request::is('reports/intcoordinatortodo') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-plus-square"></i>
                                    <p>Coordinator ToDo</p>
                                </a>
                            </li>
                            @endif
                            @if ($assistConferenceCoordinatorCondition)
                                <li class="nav-item">
                                <a href="{{ route('report.appreciation') }}" class="nav-link {{ Request::is('reports/appreciation') ? 'active' : '' }} {{ Request::is('coordinator/appreciation/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-expand-alt"></i>
                                    <p>Volunteer Appreciation</p>
                                </a>
                                </li>
                                @endif
                                @if ($regionalCoordinatorCondition)
                                <li class="nav-item">
                                <a href="{{ route('report.birthday') }}" class="nav-link {{ Request::is('reports/birthday') ? 'active' : '' }} {{ Request::is('coordinator/birthday/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-times-circle"></i>
                                    <p>Volunteer Birthdays</p>
                                </a>
                                </li>
                                @endif
                                <li class="nav-item">
                                <a href="{{ route('report.reportingtree') }}" class="nav-link {{ Request::is('reports/reportingtree') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-money-bill-alt"></i>
                                    <p>Reporting Tree</p>
                                </a>
                            </li>
                            </ul>
                        </li>
                        @endif

                        @if ($eoyReportConditionDISABLED || ($eoyReportCondition && $eoyTestCondition && $testers_yes) || ($eoyReportCondition && $coordinators_yes))
                        <li class="nav-item {{ Request::is('yearreports/*') ? 'menu-open' : '' }} {{ Request::is('chapter/financial/*') ? 'menu-open' : '' }}
                                            {{ Request::is('chapter/boardinfo/*') ? 'menu-open' : '' }} {{ Request::is('chapter/boundaryview/*') ? 'menu-open' : '' }}
                                            {{ Request::is('chapter/statusview/*') ? 'menu-open' : '' }} {{ Request::is('chapter/awardsview/*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('yearreports/*') ? 'active' : '' }} {{ Request::is('chapter/financial/*') ? 'active' : '' }}
                                                        {{ Request::is('chapter/boardinfo/*') ? 'active' : '' }} {{ Request::is('chapter/boundaryview/*') ? 'active' : '' }}
                                                        {{ Request::is('chapter/statusview/*') ? 'active' : '' }} {{ Request::is('chapter/awardsview/*') ? 'active' : '' }}">
                                <i class="nav-icon far fa-chart-bar"></i>
                                <p>EOY Reports<i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                <a href="{{ route('report.eoystatus') }}" class="nav-link {{ Request::is('yearreports/eoystatus') ? 'active' : '' }} {{ Request::is('chapter/statusview/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tasks"></i>
                                    <p>End of Year Status</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('report.boardinfo') }}" class="nav-link {{ Request::is('yearreports/boardinfo') ? 'active' : '' }} {{ Request::is('chapter/boardinfo/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Board Reports</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('report.review') }}" class="nav-link {{ Request::is('yearreports/review') ? 'active' : '' }} {{ Request::is('chapter/financial/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                    <p>Financial Reports</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('report.issues') }}" class="nav-link {{ Request::is('yearreports/boundaryissue') ? 'active' : '' }} {{ Request::is('chapter/boundaryview/*') ? 'active' : '' }}">
                                    <i class="nav-icon far fa-map"></i>
                                    <p>Boundary Issues</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('report.awards') }}" class="nav-link {{ Request::is('yearreports/chapterawards') ? 'active' : '' }} {{ Request::is('yearreports/addawards') ? 'active' : '' }}
                                        {{ Request::is('chapter/awardsview/*') ? 'active' : '' }}">
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
                                             {{ Request::is('adminreports/mailqueue') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('admin/eoy') ? 'active' : '' }} {{ Request::is('admin/reregdate') ? 'active' : '' }}
                                                        {{ Request::is('admin/reregdate/*') ? 'active' : '' }} {{ Request::is('adminreports/duplicateuser') ? 'active' : '' }}
                                                        {{ Request::is('adminreports/duplicateboardid') ? 'active' : '' }} {{ Request::is('adminreports/multipleboard') ? 'active' : '' }}
                                                        {{ Request::is('adminreports/nopresident') ? 'active' : '' }} {{ Request::is('adminreports/outgoingboard') ? 'active' : '' }}
                                                         {{ Request::is('adminreports/mailqueue') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cogs"></i>
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
                                <a href="{{ route('admin.reregdate') }}" class="nav-link {{ Request::is('admin/reregdate') ? 'active' : '' }} {{ Request::is('admin/reregdate/*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-calendar-alt"></i>
                                    <p>Re-Reg Renewal Dates</p>
                                </a>
                            </li>
                                <li class="nav-item">
                                <a href="{{ route('admin.duplicateuser') }}" class="nav-link {{ Request::is('adminreports/duplicateuser') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-plus"></i>
                                    <p>Duplicate Users</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.duplicateboardid') }}" class="nav-link {{ Request::is('adminreports/duplicateboardid') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-exchange-alt"></i>
                                    <p>Duplicate Board IDs</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                <a href="{{ route('admin.multipleboard') }}" class="nav-link {{ Request::is('adminreports/multipleboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-random"></i>
                                    <p>Multiple Boards</p>
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
                                <a href="{{ route('logs') }}" target="_blank" class="nav-link {{ Request::is('logs') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-exclamation-triangle"></i>
                                    <p>Error Logs</p>
                                </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.mailqueue') }}" class="nav-link {{ Request::is('adminreports/mailqueue') ? 'active' : '' }}">
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
                            </ul>
                        </li>
                        @endif

                        <li class="nav-item {{ Request::is('reports/downloads') ? 'menu-open' : '' }} {{ Request::is('admin/bugs') ? 'menu-open' : '' }}
                                            {{ Request::is('admin/resources') ? 'menu-open' : '' }} {{ Request::is('admin/toolkit') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ Request::is('reports/downloads') ? 'active' : '' }} {{ Request::is('admin/bugs') ? 'active' : '' }}
                                                        {{ Request::is('admin/resources') ? 'active' : '' }} {{ Request::is('admin/toolkit') ? 'active' : '' }} ">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>Resources<i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                        @if ($assistConferenceCoordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('report.downloads') }}" class="nav-link {{ Request::is('reports/downloads') ? 'active' : '' }}">
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
                            <a href="{{ route('coordinator.showprofile') }}" class="nav-link {{ Request::is('coordinator/profile') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-circle"></i>
                            <p>Update Profile</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                                    document.getElementById('logout-form').submit();">
                                                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp;&nbsp;{{ __('Logout') }}</span>
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
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
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

