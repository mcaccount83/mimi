<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>
    {{-- <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"> --}}

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    {{-- <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free/css/all.min.css"> --}}
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/solid.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/brands.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/css/v5-font-face.css" rel="stylesheet" />
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Switch -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/bootstrap-switch/css/bootstrap-switch.min.css">
    <!-- BS Stepper -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/bs-stepper/css/bs-stepper.min.css">
    <!-- Summernote CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/daterangepicker/daterangepicker.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">



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
    .email-column a {
        display: inline-block;
        text-decoration: none;
        color: inherit;
    }

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


    .notification-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        font-size: 12px;
        line-height: 18px;
        /* background-color: #dc3545; */
        background-color: #28a745;
        color: #ffffff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-item.position-relative {
        position: relative;
    }

    /* Optional: Add hover effect */
    .notification-badge:hover {
        /* background-color: #c82333; */
        background-color: #28a745;
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
                <img src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" class="brand-image img-circle elevation-3 custom-logo">
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">

                    <!-- Menu for Logged In Users -->
                    @auth

                        <!-- Coordinator Dashboard Menu Item -->
                        <li class="nav-item">
                            <a href="{{ route('coordinators.viewprofile') }}" class="nav-link {{ Request::is('viewprofile') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Chapters Menu Item -->
                        @php
                            if ($coordinatorCondition) {
                                $chaptersRoute = route('chapters.chaplist');
                            } elseif ($einCondition || $ITCondition) {
                                $chaptersRoute = route('chapters.chaplist', ['check5' => 'yes']);
                            } elseif ($inquiriesCondition) {
                                $chaptersRoute = route('chapters.chapinquiries', ['check3' => 'yes']);
                            } elseif ($inquiriesInternationalCondition) {
                                $chaptersRoute = route('chapters.chapinquiries', ['check5' => 'yes']);
                            }
                            $activeChpaterRoutes = [
                                'chapter/*',
                            ];
                        @endphp
                        @if (isset($chaptersRoute))
                            <li class="nav-item">
                                <a href="{{ $chaptersRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeChpaterRoutes) }}">
                                    <i class="nav-icon fas fa-home"></i>
                                    <p>Chapters</p>
                                </a>
                            </li>
                        @endif

                        <!-- Coordinaros Menu Item -->
                        @php
                            if ($supervisingCoordinatorCondition) {
                                $coordinatorsRoute = route('coordinators.coordlist');
                            } elseif ($ITCondition) {
                                $coordinatorsRoute = route('coordinators.coordlist', ['check5' => 'yes']);
                            }
                            $activeCoordinatorsRoutes = [
                                'coordinator/*',
                            ];
                        @endphp
                        @if (isset($coordinatorsRoute))
                            <li class="nav-item">
                                <a href="{{ $coordinatorsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeCoordinatorsRoutes) }}">
                                    <i class="nav-icon fas fa-user-friends"></i>
                                    <p>Coordinators</p>
                                </a>
                            </li>
                        @endif

                        <!-- Payments/Donations Menu Item -->
                        @php
                            if ($coordinatorCondition && $regionalCoordinatorCondition) {
                                $paymentsRoute = route('payment.chapreregistration');
                            } elseif ($m2mCondition || $ITCondition) {
                                $paymentsRoute = route('payment.chapreregistration', ['check5' => 'yes']);
                            }
                            $activePaymentsRoutes = [
                                'payment/*'
                            ];
                        @endphp
                        @if (isset($paymentsRoute))
                            <li class="nav-item">
                                <a href="{{ $paymentsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activePaymentsRoutes) }}">
                                    <i class="nav-icon fas fa-dollar-sign"></i>
                                    <p>Payments/Donations</p>
                                </a>
                            </li>
                        @endif

                        <!-- Website Review Menu Item -->
                        @php
                            if ($coordinatorCondition && !$webReviewCondition) {
                                $websiteRoute = route('chapters.chapwebsite');
                            } elseif ($webReviewCondition) {
                                $websiteRoute = route('chapters.chapwebsite', ['check3' => 'yes']);
                            } elseif ($ITCondition) {
                                $websiteRoute = route('chapters.chapwebsite', ['check5' => 'yes']);
                            }
                            $activeWebsiteRoutes = [
                                'online/*'
                            ];
                        @endphp
                        @if (isset($websiteRoute))
                            <li class="nav-item">
                                <a href="{{ $websiteRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeWebsiteRoutes) }}">
                                    <i class="nav-icon fas fa-laptop"></i>
                                    <p>Website/Social Media</p>
                                </a>
                            </li>
                        @endif

                          <!-- New Menu Item -->
                          @php
                            if ($coordinatorCondition && $conferenceCoordinatorCondition) {
                                $newChaptersRoute = route('chapters.chaplistpending');
                            } elseif ($ITCondition) {
                                $newChaptersRoute = route('chapters.chaplistpending', ['check5' => 'yes']);
                            }
                            $activeNewChpaterRoutes = [
                                'application/*',
                            ];
                        @endphp
                        @if (isset($newChaptersRoute))
                            <li class="nav-item">
                                <a href="{{ $newChaptersRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeNewChpaterRoutes) }}">
                                    <i class="nav-icon fas fa-star-of-life"></i>
                                    <p>New Chapters/Coordinators</p>
                                </a>
                            </li>
                        @endif

                        <!-- List Subscription Menu Item -->
                        @php
                            if ($listAdminCondition || $ITCondition) {
                                $listSubscriptionRoute = route('forum.chaptersubscriptionlist');
                            } elseif ($listAdminCondition || $ITCondition) {
                                $listSubscriptionRoute = route('forum.chaptersubscriptionlist', ['check5' => 'yes']);
                            }
                            $activeChpaterRoutes = [
                                'forum/chaptersubscriptionlist', 'forum/coordinatorsubscriptionlist',
                            ];
                        @endphp
                        @if (isset($listSubscriptionRoute))
                            <li class="nav-item">
                                <a href="{{ $listSubscriptionRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeChpaterRoutes) }}">
                                    <i class="nav-icon fas fa-rectangle-list"></i>
                                    <p>List Subscriptions</p>
                                </a>
                            </li>
                        @endif

                        <!-- BoardList Email Menu Item -->
                        @php
                            if ($listAdminCondition || $ITCondition) {
                                $boardlistRoute = route('chapters.chapboardlist');
                            }
                            $activeBoardlistRoutes = [
                                'forum/boardlist'
                            ];
                        @endphp
                        @if (isset($boardlistRoute))
                            <li class="nav-item">
                                <a href="{{ $boardlistRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardlistRoutes) }}">
                                    <i class="nav-icon fas fa-rectangle-list"></i>
                                    <p>BoardList Emails - OLD</p>
                                </a>
                            </li>
                        @endif

                        <!-- Chapter Reports Menu Item -->
                        @php
                            if ($coordinatorCondition && $conferenceCoordinatorCondition || $ITCondition) {
                                $chapterReportsRoute = route('chapreports.chaprptchapterstatus');
                            } elseif ($einCoondition) {
                                $coordReportsRoute = route('chapreports.chaprpteinstatus', ['check5' => 'yes']);
                            }
                            $activeChapterReportsRoutes = [
                                'chapterreports/*'
                            ];
                        @endphp
                        @if (isset($chapterReportsRoute))
                            <li class="nav-item">
                                <a href="{{ $chapterReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeChapterReportsRoutes) }}">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>Chapter Reports</p>
                                </a>
                            </li>
                        @endif

                        <!-- Coordinator Reports Menu Item -->
                        @php
                            if ($supervisingCoordinatorCondition || $ITCondition) {
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
                                <a href="{{ $coordReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeCoordReportsRoutes) }}">
                                    <i class="nav-icon fas fa-clipboard-user"></i>
                                    <p>Coordinator Reports</p>
                                </a>
                            </li>
                        @endif

                        <!-- End of Year Reports Menu Item-->
                            @php
                                if (($coordinatorCondition && $displayLIVE) || ($eoyTestCondition && $displayTESTING) || ($eoyReportCondition && $displayLIVE || $ITCondition)) {
                                    $eoyReportsRoute = route('eoyreports.eoystatus');
                                } elseif ($einCondition) {
                                    $eoyReportsRoute = route('eoyreports.eoyirssubmission', ['check5' => 'yes']);
                                }
                                $activeEOYReportsRoutes = [
                                    'eoy/*',
                                ];
                            @endphp
                            @if (isset($eoyReportsRoute))
                                <li class="nav-item">
                                    <a href="{{ $eoyReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeEOYReportsRoutes) }}">
                                        <i class="nav-icon fas fa-clipboard-check"></i>
                                        <p>EOY Reports
                                            @if ($ITCondition && !$displayTESTING && !$displayLIVE) *ADMIN*@endif
                                            @if ($eoyTestCondition && $displayTESTING) *TESTING*@endif
                                        </p>
                                    </a>
                                </li>
                            @endif

                        <!-- Admin Reports Menu Item -->
                        @php
                            if ($coordinatorCondition && $conferenceCoordinatorCondition || $ITCondition) {
                                $adminReportsRoute = route('adminreports.paymentlist');
                            }
                            $activeAdminReportsRoutes = [
                                'adminreports/*'
                            ];
                        @endphp
                        @if (isset($adminReportsRoute))
                            <li class="nav-item">
                                <a href="{{ $adminReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeAdminReportsRoutes) }}">
                                    <i class="nav-icon fas fa-clipboard"></i>
                                    <p>Admin Reports</p>
                                </a>
                            </li>
                        @endif

                         <!-- User Reports Menu Item -->
                        @php
                            if ($ITCondition) {
                                $userReportsRoute = route('userreports.useradmin');
                            }
                            $activeUserReportsRoutes = [
                                'userreports/*'
                            ];
                        @endphp
                        @if (isset($userReportsRoute))
                            <li class="nav-item">
                                <a href="{{ $userReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeUserReportsRoutes) }}">
                                    <i class="nav-icon fas fa-paste"></i>
                                    <p>User Reports</p>
                                </a>
                            </li>
                        @endif

                         <!-- Tech Reports Menu Item -->
                        @php
                            if ($ITCondition) {
                                $techReportsRoute = route('logs');
                            }
                            $activeTechReportsRoutes = [
                                'techreports/*'
                            ];
                        @endphp
                        @if (isset($techReportsRoute))
                            <li class="nav-item">
                                <a href="{{ $techReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeTechReportsRoutes) }}">
                                    <i class="nav-icon fas fa-clipboard-question"></i>
                                    <p>IT Reports</p>
                                </a>
                            </li>
                        @endif

                        <!-- Resources Reports Menu Item -->
                        @php
                            $resourcesRoute = route('resources.toolkit');
                            $activeResourcesRoutes = [
                                'resources/*'
                            ];
                        @endphp
                        @if (isset($resourcesRoute))
                            <li class="nav-item">
                                <a href="{{ $resourcesRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeResourcesRoutes) }}">
                                    <i class="nav-icon fas fa-toolbox"></i>
                                    <p>Resources</p>
                                </a>
                            </li>
                        @endif

                         <!-- CoordinatorList Menu Item -->
                         <li class="nav-item position-relative">
                            <a href="{{ url(config('forum.frontend.router.prefix') . '/unread') }}" target="_blank" class="nav-link">
                                <i class="nav-icon fas fa-comments"></i>
                                <p>
                                    CoordinatorList
                                    @if( $unreadForumCount > 0)
                                        <span class="badge badge-danger badge-pill notification-badge">
                                            UNREAD
                                        </span>
                                    @endif
                                </p>
                            </a>
                        </li>

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

                        @else
                        <!-- Menu for Non-Logged In Users -->

                        <br>
                       <li style="padding: 0 20px;">
                    @isset($url)
                    <form method="POST" action='{{ url("login/$url") }}' aria-label="{{ __('Login') }}">
                    @else
                    <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                    @endisset

                    @csrf

                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="Email">
                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="Password">
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-secondary btn-block" onClick="this.form.submit(); this.disabled=true;">{{ __('Login') }}</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                </li>
                 <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link">
                                <i class="nav-icon fas fa-sign-in-alt"></i>
                                <p>Main Login Page</p>
                            </a>
                        </li>
                    </form>

                        </li>
                    @endauth
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <!-- /.menu -->

        <!-- Main content -->
        <section class="content">
            @include('layouts.scripts.messages')

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- Default box -->
                        <div class="content-wrapper">
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
<!-- FontAwesome -->
<script defer src="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/js/all.js"></script>
<!-- overlayScrollbars -->
<script src="{{ config('settings.base_url') }}theme/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- Bootstrap Switch -->
<script src="{{ config('settings.base_url') }}theme/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- BS-Stepper -->
<script src="{{ config('settings.base_url') }}theme/plugins/bs-stepper/js/bs-stepper.min.js"></script>
<!-- Summernote -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<!-- Sript Functions -->
@include('layouts.scripts.email')
@include('layouts.scripts.alert')
@include('layouts.scripts.datatable')
@include('layouts.scripts.datetime')
@include('layouts.scripts.masks')
@include('layouts.scripts.export')
@include('layouts.scripts.pdfviewer')
@include('layouts.scripts.bootstrapswitch')

@yield('customscript')
@stack('scripts')

</body>
</html>

