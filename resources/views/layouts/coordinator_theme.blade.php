<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/dist/css/skins/_all-skins.min.css') }}">
    <!-- Morris chart -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/morris.js/morris.css') }}">
    <!-- jvectormap -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/jvectormap/jquery-jvectormap.css') }}">
    <!-- Date Picker -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
	<!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/plugins/iCheck/all.css') }}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/dist/css/custom.css') }}">
    <!-- Custom CSS for Financial Report -->
    <link rel="stylesheet" href="{{ asset('chapter_theme/css/custom_financial.css') }}">
    <!-- Data Table -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
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

<body class="hold-transition skin-blue sidebar-mini fixed">
<div class="wrapper">
  <header class="main-header">
    <!-- Logo -->
    <a href="{{ route('home') }}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>M</b>C</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b><img src="{{ asset('coordinator_theme/dist/img/logo.png') }}" alt=""></b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
           <li class="dropdown user user-menu">
            <a href="https://momsclub.org/" target="_blank" class="hidden-xs">Return to Main Site</a>
          </li>
         </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <section class="sidebar">
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header"></li>

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

        <li class="{{ Request::is('coordinator/dashboard') ? 'active' : '' }}  ">
          <a href="{{ route('coordinator.showdashboard') }}">
            <i class="fa fa-tachometer"></i> <span>Dashboard</span>
          </a>
        </li>

        @if ($coordinatorCondition)
            <li class="{{ Request::is('chapter/list') ? 'active' : '' }} {{ Request::is('chapter/create') ? 'active' : '' }} {{ Request::is('chapter/edit/*') ? 'active' : ''}} {{ Request::is('chapterlist') ? 'active' : '' }}  ">
            <a href="{{ route('chapter.list') }}">
                <i class="fa fa-list"></i> <span>Chapter List</span>
            </a>
            </li>
        @endif

        @if ($regionalCoordinatorCondition)
            <li class="{{ Request::is('chapter/zapped') ? 'active' : '' }} {{ Request::is('chapter/zapped/view/*') ? 'active' : ''}}">
            <a href="{{ route('chapter.zapped') }}">
                <i class="fa fa-ban"></i>
                <span>Zapped Chapter List</span>
            </a>
            </li>
        @endif

        @if ($regionalCoordinatorCondition || $inquiriesCondition)
            <li class="{{ Request::is('chapter/re-registration') ? 'active' : '' }} {{ Request::is('chapter/re-registration/payment/*') ? 'active' : '' }}">
            <a href="{{ route('chapter.registration') }}">
                <i class="fa fa-credit-card"></i> <span>Re-Registrations</span>
            </a>
            </li>
        @endif

        @if ($regionalCoordinatorCondition || $inquiriesCondition)
            <li class="{{ Request::is('chapter/inquiries') ? 'active' : '' }} {{ Request::is('chapter/inquiriesview/*') ? 'active' : '' }}">
            <a href="{{ route('chapter.inquiries') }}">
                <i class="fa fa-map-marker"></i>
                <span>Inquiries</span>
            </a>
            </li>
        @endif

        @if ($inquiriesCondition)
            <li class="{{ Request::is('chapter/inquirieszapped') ? 'active' : '' }} ">
            <a href="{{ route('chapter.inquirieszapped') }}">
                <i class="fa fa-ban"></i>
                <span>Zapped Chapters</span>
            </a>
            </li>
        @endif

        @if ($regionalCoordinatorCondition || $webReviewCondition)
            <li class="{{ Request::is('chapter/website') ? 'active' : '' }} {{ Request::is('chapter/website/edit/*') ? 'active' : '' }}">
            <a href="{{ route('chapter.website') }}">
                <i class="fa fa-laptop"></i><span>Website Review </span>
                </a>
            </li>
        @endif

        @if ($adminReportCondition || $listAdminCondition)
            <li class="{{ Request::is('reports/boardlist') ? 'active' : '' }}">
            <a href="{{ route('report.boardlist') }}">
                <i class="fa fa-bars"></i><span>BoardList</span>
            </a>
            </li>
        @endif

        @if ($supervisingCoordinatorCondition)
            <li class="{{ Request::is('coordinatorlist') ? 'active' : '' }} {{ Request::is('coordinator/create') ? 'active' : '' }} {{ Request::is('coordinator/edit/*') ? 'active' : '' }} {{ Request::is('coordinator/role/*') ? 'active' : '' }}">
            <a href="{{ route('coordinator.list') }}">
                <i class="fa fa-user"></i>
                <span>Coordinator List</span>
            </a>
            </li>
        @endif

        @if ($assistConferenceCoordinatorCondition)
            <li class="{{ Request::is('coordinator/retired') ? 'active' : '' }} {{ Request::is('coordinator/retired/view/*') ? 'active' : '' }}">
            <a href="{{ route('coordinator.retired') }}">
                <i class="fa fa-user-times"></i>
                <span>Retired Coordinator List</span>
            </a>
            </li>
        @endif

         @if ($einCondition || $inquiriesCondition)
            <li class="<?php if (($positionid == 12)){ ?> {{ Request::is('home') ? 'active' : '' }}<?php }?> {{ Request::is('chapter/international') ? 'active' : '' }}">
            <a href="{{ route('chapter.inter') }}">
                <i class="fa fa-list"></i>
                <span>International Chapter List</span>
            </a>
            </li>
        @endif

        @if ($einCondition)
            <li class="{{ Request::is('chapter/international/zap') ? 'active' : '' }}  {{ Request::is('chapter/international/zapped/view/*') ? 'active' : ''}}">
            <a href="{{ route('chapter.interzap') }}">
                <i class="fa fa-ban"></i>
                <span>International Zapped Chapter List</span>
            </a>
            </li>
        @endif

        @if ($positionid == 0 )
            <li class="{{ Request::is('coordinator/international') ? 'active' : '' }}">
            <a href="{{ route('coordinator.inter') }}">
                <i class="fa fa-user"></i>
                <span>International Coordinator List</span></a>
            </li>
        @endif

        @if($positionid == 0)
            <li class="{{ Request::is('coordinator/retiredinternational') ? 'active' : '' }} {{ Request::is('coordinator/retired/view/*') ? 'active' : '' }}">
            <a href="{{ route('coordinator.retinter') }}">
                <i class="fa fa-user-times"></i>
                <span>International Retired Coordinator List</span></a>
            </li>
        @endif

        @if ($einCondition)
            <li class="{{ Request::is('reports/inteinstatus') ? 'active' : '' }}">
            <a href="{{ route('report.inteinstatus') }}">
                    <i class="fa fa-bank"></i> <span>Chapter EIN Status</span></a>
            </li>
        @endif

        @if ($m2mCondition)
            <li class="{{ Request::is('reports/intm2mdonation') ? 'active' : '' }}">
            <a href="{{ route('report.intm2mdonation') }}">
                    <i class="fa fa-money"></i> <span>M2M Donations</span></a>
            </li>
        @endif

        @if ($adminReportCondition)
            <li class="treeview {{ Request::is('chapter/international') ? 'active' : '' }} {{ Request::is('chapter/international/*') ? 'active' : '' }} {{ Request::is('coordinator/international') ? 'active' : '' }} {{ Request::is('coordinator/international/view/*') ? 'active' : '' }} {{ Request::is('coordinator/international/*') ? 'active' : '' }} {{ Request::is('coordinator/retiredinternational') ? 'active' : '' }}  {{ Request::is('coordinator/retiredinternational/view/*') ? 'active' : ''}} {{ Request::is('reports/inteinstatus') ? 'active' : '' }} {{ Request::is('reports/intm2mdonation') ? 'active' : '' }}">
                <a href="#"><i class="fa fa-globe"></i> <span>International Lists</span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                    <li class="{{ Request::is('chapter/international') ? 'active' : '' }} {{ Request::is('chapter/international/view/*') ? 'active' : '' }}">
            <a href="{{ route('chapter.inter') }}">
                <i class="fa fa-list"></i>
                <span>Chapter List</span>
            </a>
            </li>
                <li class="{{ Request::is('chapter/international/zap') ? 'active' : '' }}  {{ Request::is('chapter/international/zapped/view/*') ? 'active' : ''}}">
            <a href="{{ route('chapter.interzap') }}">
                <i class="fa fa-ban"></i>
                <span>Zapped Chapter List</span>
            </a>
            </li>
                <li class="{{ Request::is('coordinator/international') ? 'active' : '' }} {{ Request::is('coordinator/international/view/*') ? 'active' : '' }}">
            <a href="{{ route('coordinator.inter') }}">
                <i class="fa fa-user"></i>
                <span>Coordinator List</span>
            </a>
            </li>
                <li class="{{ Request::is('coordinator/retiredinternational') ? 'active' : '' }} {{ Request::is('coordinator/retiredinternational/view/*') ? 'active' : ''}}">
            <a href="{{ route('coordinator.retinter') }}">
                <i class="fa fa-user-times"></i>
                <span>Retired Coordinator List</span>
            </a>
                </li>
                <li class="{{ Request::is('reports/inteinstatus') ? 'active' : '' }}">
            <a href="{{ route('report.inteinstatus') }}">
                    <i class="fa fa-bank"></i> <span>Chapter EIN Status</span>
                    </a>
                    </li>
                    <li class="{{ Request::is('reports/intm2mdonation') ? 'active' : '' }}">
            <a href="{{ route('report.intm2mdonation') }}">
                    <i class="fa fa-money"></i> <span>M2M Donations</span>
                    </a>
            </li>
                </ul>
            </li>
        @endif

        @if ($regionalCoordinatorCondition)
            <li class="treeview {{ Request::is('reports/chapterstatus') ? 'active' : '' }} {{ Request::is('reports/chapternew') ? 'active' : '' }} {{ Request::is('reports/chapterlarge') ? 'active' : '' }} {{ Request::is('reports/chapterprobation') ? 'active' : '' }} {{ Request::is('reports/chaptercoordinators') ? 'active' : '' }} {{ Request::is('reports/m2mdonation') ? 'active' : '' }} {{ Request::is('chapter/m2mdonation/*') ? 'active' : '' }} {{ Request::is('reports/einstatus') ? 'active' : '' }} {{ Request::is('reports/socialmedia') ? 'active' : '' }}">
                <a href="#"><i class="fa fa-home "></i> <span>Chapter Reports</span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
                <li class="{{ Request::is('reports/chapterstatus') ? 'active' : '' }}"><a href="{{ route('report.chapterstatus') }}">
                    <i class="fa fa-check"></i> <span>Chapter Status</span>
                    </a>
                </li>
                <li class="{{ Request::is('reports/einstatus') ? 'active' : '' }}"><a href="{{ route('report.einstatus') }}">
                    <i class="fa fa-bank"></i> <span>Chapter EIN Status</span>
                    </a>
                </li>
                @if ($assistConferenceCoordinatorCondition)
                <li class="{{ Request::is('reports/chapternew') ? 'active' : '' }}"><a href="{{ route('report.chapternew') }}">
                    <i class="fa fa-plus-square"></i> <span>New Chapters</span>
                    </a>
                </li>
                @endif
                <li class="{{ Request::is('reports/chapterlarge') ? 'active' : '' }}"><a href="{{ route('report.chapterlarge') }}">
                    <i class="fa fa-expand"></i> <span>Large Chapters</span>
                    </a>
                </li>
                <li class="{{ Request::is('reports/chapterprobation') ? 'active' : '' }}"><a href="{{ route('report.chapterprobation') }}">
                    <i class="fa fa-times-circle"></i> <span>Probation Chapters</span>
                    </a>
                </li>
                <li class="{{ Request::is('reports/m2mdonation') ? 'active' : '' }} {{ Request::is('chapter/m2mdonation/*') ? 'active' : '' }}"><a href="{{ route('report.m2mdonation') }}">
                    <i class="fa fa-money"></i> <span>M2M Donations</span>
                    </a>
                </li>
                <li class="{{ Request::is('reports/socialmedia') ? 'active' : '' }}"><a href="{{ route('report.socialmedia') }}">
                    <i class="fa fa-share-alt"></i> <span>Social Media</span>
                    </a>
                </li>
                <li class="{{ Request::is('reports/chaptercoordinators') ? 'active' : '' }}"><a href="{{ route('report.chaptercoordinators') }}">
                    <i class="fa fa-users"></i> <span>Chapter Coordinator</span>
                    </a>
                </li>

                </ul>
            </li>
        @endif

        @if ($coordinatorCondition)
            <li class="treeview {{ Request::is('reports/chaptervolunteer') ? 'active' : '' }} {{ Request::is('reports/coordinatortodo') ? 'active' : '' }} {{ Request::is('reports/intcoordinatortodo') ? 'active' : '' }} {{ Request::is('reports/appreciation') ? 'active' : '' }} {{ Request::is('reports/birthday') ? 'active' : '' }} {{ Request::is('coordinator/appreciation/*') ? 'active' : '' }} {{ Request::is('reports/reportingtree') ? 'active' : '' }}">
                <a href="#"><i class="fa fa-child"></i> <span>Coordinator Reports</span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
                <ul class="treeview-menu">
                @if ($supervisingCoordinatorCondition)
                    <li class="{{ Request::is('reports/chaptervolunteer') ? 'active' : '' }}"><a href="{{ route('report.chaptervolunteer') }}">
                        <i class="fa fa-pie-chart"></i> <span>Volunteer Utilization</span>
                        </a>
                    </li>
                @endif
                @if (($positionid ==6 || $positionid ==25))
                    <li class="{{ Request::is('reports/coordinatortodo') ? 'active' : '' }}"><a href="{{ route('report.coordinatortodo') }}">
                        <i class="fa fa-check-square-o"></i> <span>Coordinator ToDo</span>
                        </a>
                    </li>
                @endif
                @if ($founderCondition)
                    <li class="{{ Request::is('reports/intcoordinatortodo') ? 'active' : '' }}"><a href="{{ route('report.intcoordinatortodo') }}">
                        <i class="fa fa-check-square-o"></i> <span>Coordinator ToDo</span>
                        </a>
                    </li>
                @endif
                @if ($assistConferenceCoordinatorCondition)
                    <li class="{{ Request::is('reports/appreciation') ? 'active' : '' }} {{ Request::is('coordinator/appreciation/*') ? 'active' : '' }}"><a href="{{ route('report.appreciation') }}">
                        <i class="fa fa-gift"></i> <span>Volunteer Appreciation</span>
                        </a>
                    </li>
                @endif
                @if ($regionalCoordinatorCondition)
                    <li class="{{ Request::is('reports/birthday') ? 'active' : '' }} {{ Request::is('coordinator/birthday/*') ? 'active' : '' }}"><a href="{{ route('report.birthday') }}">
                        <i class="fa fa-birthday-cake"></i> <span>Volunteer Birthdays</span>
                        </a>
                    </li>
                @endif
                <li class="{{ Request::is('reports/reportingtree') ? 'active' : '' }}"><a href="{{ route('report.reportingtree') }}">
                    <i class="fa fa-sitemap"></i> <span>Reporting Tree</span>
                    </a>
                </li>
                </ul>
            </li>
        @endif

        @if ($eoyReportConditionDISABLED || ($eoyReportCondition && $eoyTestCondition && $testers_yes) || ($eoyReportCondition && $coordinators_yes))
        <li class="treeview {{ Request::is('yearreports/*') ? 'active' : '' }} {{ Request::is('chapter/financial/*') ? 'active' : ''}} {{ Request::is('chapter/boardinfo/*') ? 'active' : ''}}  {{ Request::is('chapter/boundaryview/*') ? 'active' : '' }} {{ Request::is('chapter/statusview/*') ? 'active' : ''}} {{ Request::is('chapter/awardsview/*') ? 'active' : '' }} {{ Request::is('yearreports/addawards') ? 'active' : '' }}">
            <a href="#"><i class="fa fa-bar-chart"></i> <span>EOY Reports</span>
              <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
                <li class="{{ Request::is('yearreports/eoystatus') ? 'active' : '' }} {{ Request::is('chapter/statusview/*') ? 'active' : ''}}">
                    <a href="{{ route('report.eoystatus') }}">
                  <i class="fa fa-list-ol"></i> <span>End of Year Status</span>
                  </a>
              </li>
              <li class="{{ Request::is('yearreports/boardinfo') ? 'active' : '' }} {{ Request::is('chapter/boardinfo/*') ? 'active' : ''}}">
                <a href="{{ route('report.boardinfo') }}">
                  <i class="fa fa-users"></i> <span>Board Reports</span>
                  </a>
              </li>
              <li class="{{ Request::is('yearreports/review') ? 'active' : '' }} {{ Request::is('chapter/financial/*') ? 'active' : ''}}">
                <a href="{{ route('report.review') }}">
                  <i class="fa fa-usd"></i> <span>Financial Reports</span>
                  </a>
              </li>

              <li class="{{ Request::is('yearreports/boundaryissue') ? 'active' : '' }}  {{ Request::is('chapter/boundaryview/*') ? 'active' : '' }}">
                <a href="{{ route('report.issues') }} ">
                  <i class="fa fa-map-o"></i> <span>Boundary issues</span>
                  </a>
              </li>
              <li class="{{ Request::is('yearreports/chapteraward') ? 'active' : '' }} {{ Request::is('yearreports/addawards') ? 'active' : '' }} {{ Request::is('chapter/awardsview/*') ? 'active' : '' }}">
                <a href="{{ route('report.awards') }}">
                  <i class="fa fa-trophy"></i> <span>Chapter Awards</span>
                  </a>
              </li>
            </ul>
            </li>
        @endif

        @if ($adminReportCondition)
            <li class="treeview {{ Request::is('admin/eoy') ? 'active' : '' }}  {{ Request::is('admin/reregdate') ? 'active' : '' }} {{ Request::is('admin/reregdate/*') ? 'active' : '' }} {{ Request::is('adminreports/duplicateuser') ? 'active' : '' }} {{ Request::is('adminreports/duplicateboardid') ? 'active' : '' }} {{ Request::is('adminreports/multipleboard') ? 'active' : '' }} {{ Request::is('adminreports/nopresident') ? 'active' : '' }} {{ Request::is('adminreports/outgoingboard') ? 'active' : '' }}">
                <a href="#"><i class="fa fa-cogs"></i> <span>Admin Items</span>
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>
                <ul class="treeview-menu">
            <li class="{{ Request::is('admin/eoy') ? 'active' : '' }}">
                <a href="{{ route('admin.eoy') }}">
                    <i class="fa fa-bullseye"></i>
                    <span>EOY Procedures</span>
                </a>
                </li>
                <li class="{{ Request::is('admin/reregdate') ? 'active' : '' }} {{ Request::is('admin/reregdate/*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reregdate') }}">
                        <i class="fa fa-calendar-o"></i>
                        <span>Re-Reg Renewal Dates</span>
                    </a>
                    </li>
            <li class="{{ Request::is('adminreports/duplicateuser') ? 'active' : '' }}">
            <a href="{{ route('report.duplicateuser') }}">
                <i class="fa fa-user-plus"></i>
                <span>Duplicate Users</span>
            </a>
            </li>
            <li class="{{ Request::is('adminreports/duplicateboardid') ? 'active' : '' }}">
            <a href="{{ route('report.duplicateboardid') }}">
                <i class="fa fa-exchange"></i>
                <span>Duplicate Board Id</span>
            </a>
            </li>
            <li class="{{ Request::is('adminreports/multipleboard') ? 'active' : '' }}">
            <a href="{{ route('report.multipleboard') }}">
                <i class="fa fa-random"></i>
                <span>Multiple Boards</span>
            </a>
            </li>
            <li class="{{ Request::is('adminreports/nopresident') ? 'active' : '' }}">
            <a href="{{ route('report.nopresident') }}">
                <i class="fa fa-minus-circle"></i>
                <span>No President</span>
            </a>
            </li>
            <li class="{{ Request::is('adminreports/outgoingboard') ? 'active' : '' }}">
                <a href="{{ route('report.outgoingboard') }}">
                <i class="fa fa-share"></i>
                <span>Outgoing Board</span>
                </a>
            </li>
            <li class="{{ Request::is('logs') ? 'active' : '' }}">
                <a href="{{ route('logs') }}" target="_blank">
                <i class="fa fa-exclamation-triangle"></i>
                <span>Error Logs</span>
                </a>
            </li>
            <li class="{{ Request::is('admin/sentemails') ? 'active' : '' }}">
                <a href="{{ url(config('sentemails.routepath')) }}" target="_blank">
                <i class="fa fa-share-square-o"></i>
                <span>Sent Email</span>
                </a>
            </li>
                </ul>
            </li>
        @endif

        @if ($assistConferenceCoordinatorCondition)
        <li class="{{ Request::is('reports/downloads') ? 'active' : '' }}">
            <a href="{{ route('report.downloads') }}"><i class="fa fa-download"></i> <span>Download Reports</span></a>
        </li>
        @endif

        @if ($regionalCoordinatorCondition)
            <li class="{{ Request::is('admin/bugs') ? 'active' : '' }}">
            <a href="{{ route('admin.bugs') }}">
                <i class="fa fa-bug"></i>
                <span>MIMI Bugs & Wishes</span>
            </a>
            </li>
        @endif

        <li class="{{ Request::is('admin/resources') ? 'active' : '' }}">
            <a href="{{ route('admin.resources') }}">
                <i class="fa fa-briefcase"></i>
                <span>Chapter Resources</span>
            </a>
        </li>

        <li class="{{ Request::is('admin/toolkit') ? 'active' : '' }}">
            <a href="{{ route('admin.toolkit') }}">
                <i class="fa fa-briefcase"></i>
                <span>Coordinator Toolkit</span>
            </a>
        </li>

        {{-- <div class="too" style="padding-left:15px; padding-top:9px;"><a href="https://momsclub.org/coordinator-toolkit/" target="_blank"><i class="fa fa-briefcase"></i>&nbsp;&nbsp;&nbsp;Coordinator Toolkit</a></div> --}}

        <div class="too" style="padding-left:15px; padding-top:20px;"><a href="https://momsclub.org/elearning/" target="_blank"><i class="fa fa-graduation-cap"></i>&nbsp;&nbsp;eLearning Library</a></div>

        <div class="too" style="padding-left:15px; padding-top: 20px;">

        @if ($positionid >=1 && $positionid <=24)
            <li class="{{ Request::is('coordinator/profile') ? 'active' : '' }}">
                <a href="{{ route('coordinator.showprofile') }}">
                    <i class="fa fa-user-circle"></i><span>&nbsp; Update Profile </br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<sub>(<?php echo $loggedIn;?>)</sub></span></a>
            </li></div>
        @endif

        <li class="">
          <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out"></i> <span>        {{ __('Logout') }}</span>

           </a>
		    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
           </form>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
  <div class="content-wrapper">
   @yield('content')
   </div>
  <footer class="main-footer">
        <strong>Copyright &copy; <?php echo date('Y');?> <a href="https://momsclub.org/" target="_blank">MOMS Club</a>.</strong> All rights
    reserved.
  </footer>
</div>
</body>

<!-- jQuery 3 -->
<script src="{{ asset('coordinator_theme/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('coordinator_theme/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- DataTables -->
<script src="{{ asset('coordinator_theme/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('coordinator_theme/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('coordinator_theme/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- Morris.js charts -->
<script src="{{ asset('coordinator_theme/bower_components/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('coordinator_theme/bower_components/morris.js/morris.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('coordinator_theme/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap -->
<script src="{{ asset('coordinator_theme/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('coordinator_theme/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('coordinator_theme/bower_components/jquery-knob/dist/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('coordinator_theme/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('coordinator_theme/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<!-- datepicker -->
<script src="{{ asset('coordinator_theme/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{ asset('coordinator_theme/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
<!-- Slimscroll -->
<script src="{{ asset('coordinator_theme/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- iCheck 1.0.1 -->
<script src="{{ asset('coordinator_theme/plugins/iCheck/icheck.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('coordinator_theme/bower_components/fastclick/lib/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('coordinator_theme/dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('coordinator_theme/dist/js/pages/dashboard.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('coordinator_theme/dist/js/demo.js') }}"></script>

<script>
   //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    })
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass   : 'iradio_minimal-red'
    })
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    //Timepicker
    $('.timepicker').timepicker({
      showInputs: false
    })
</script>

<script type="text/javascript">
//format dates in tables to display as MM-DD-YYYY but stil sort correctly
function initializeDataTable(selector, options, columnDefs) {
    $(selector).DataTable({
        ...options,
        columnDefs: columnDefs.map(def => {
            if (def.type === 'date') {
                return {
                    ...def,
                    render: function (data, type, row) {
                        if (type === 'sort') {
                            return row[def.targets]; // Use original date for sorting
                        }
                        return def.format ? moment(data).format(def.format) : data || def.noPayment || ''; // Format date for display if specified
                    }
                };
            }
            return def;
        })
    });
}

$(document).ready(function() {
    initializeDataTable('#chapterlist', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false
    }, []);

    initializeDataTable('#chapterlist_reReg', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        { targets: 6, type: 'date', format: null },
        {
            targets: 7, render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }
    ]);

    initializeDataTable('#chapterlist_reRegDate', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        { targets: 3, type: 'date', format: null },
        {
            targets: 4, render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }
    ]);

    initializeDataTable('#chapterlist_einStatus', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        { targets: 2, type: 'date', format: null },
    ]);

    initializeDataTable('#chapterlist_inteinStatus', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        { targets: 3, type: 'date', format: null },
    ]);

    initializeDataTable('#chapterlist_large', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        {
            targets: 4, render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }
    ]);

    initializeDataTable('#chapterlist_donation', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        {
            targets: 4,
            render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }, // Note the comma here
        {
            targets: 6,
            render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }
    ]);

    initializeDataTable('#chapterlist_review', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        {
            targets: 9, render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }
    ]);

    initializeDataTable('#coordinatorlist', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false
    }, []);

    initializeDataTable('#coordinatorlist_birthday', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        { targets: 5, type: 'date', format: null },
        {
            targets: 6, render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }
    ]);
});
</script>
@yield('customscript')
</html>
