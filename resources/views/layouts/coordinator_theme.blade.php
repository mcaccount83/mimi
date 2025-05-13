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
{{-- <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free/css/all.min.css"> --}}
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/solid.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/brands.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/css/v5-font-face.css" rel="stylesheet" />
<!-- overlayScrollbars -->
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

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
<!-- Select2 -->
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<!-- DataTables -->
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

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


<script>
    function openPdfViewer(filePath) {
        var base_url = '{{ url("/pdf-viewer") }}';
        window.open(base_url + '?id=' + encodeURIComponent(filePath), '_blank');
    }
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

        <!-- Menu for Logged In Users -->
        @auth

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
                            } elseif ($einCondition || $userAdmin) {
                                $chaptersRoute = route('international.intchapter');
                            }
                            $activeChpaterRoutes = [
                                'chapter/chapterlist', 'chapterdetails/*', 'chapterdetailsedit/*', 'chapterboardedit/*', 'chapter/chapternew',
                                'chapter/zapped', 'chapter/inquiries', 'chapter/inquirieszapped', 'international/chapter', 'international/chapterzapped'
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
                            } elseif ($einCondition || $userAdmin) {
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
                                <a href="{{ $coordinatorsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeCoordinatorsRoutes) }}">
                                    <i class="nav-icon fas fa-user-friends"></i>
                                    <p>Coordinators</p>
                                </a>
                            </li>
                        @endif

                        <!-- Payments/Donations Menu Item -->
                        @php
                            if ($regionalCoordinatorCondition) {
                                $paymentsRoute = route('chapters.chapreregistration');
                            } elseif ($m2mCondition || $userAdmin) {
                                $paymentsRoute = route('international.intdonation');
                            }
                            $activePaymentsRoutes = [
                                'chapter/reregistration', 'international/reregistration', 'chapter/donations', 'international/donation','chapterpaymentedit/*'
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
                            if ($webReviewCondition) {
                                $websiteRoute = route('chapters.chapwebsite');
                                $activeWebsiteRoutes = [
                                    'chapter/website', 'chapterdetails/*'
                                ];
                            } elseif ($regionalCoordinatorCondition) {
                                $websiteRoute = route('chapters.chapwebsite');
                                $activeWebsiteRoutes = [
                                    'chapter/website', 'international/website', 'chapter/socialmedia', 'international/socialmedia', 'chapterwebsiteedit/*'
                                ];
                            }
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
                            if ($conferenceCoordinatorCondition) {
                                $newChaptersRoute = route('chapters.chaplistpending');
                            } elseif ($userAdmin) {
                                $newChaptersRoute = route('international.intchapterpending');
                            }
                            $activeNewChpaterRoutes = [
                                'chapter/pendingchapterlist', 'chapter/declinedchapterlist',
                                'international/pendingchapterlist', 'international/declinedchapterlist'
                            ];
                        @endphp
                        @if (isset($newChaptersRoute))
                            <li class="nav-item">
                                <a href="{{ $newChaptersRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeNewChpaterRoutes) }}">
                                    <i class="nav-icon fas fa-home"></i>
                                    <p>New Chapters</p>
                                </a>
                            </li>
                        @endif

                        <!-- List Subscription Menu Item -->
                        @php
                            // if ($coordinatorCondition) {
                            if ($listAdminCondition || $userAdmin) {
                                $listSubscriptionRoute = route('forum.chaptersubscriptionlist');
                            } elseif ($listAdminCondition || $userAdmin) {
                                $listSubscriptionRoute = route('forum.internationalchaptersubscriptionlist');
                            }
                            $activeChpaterRoutes = [
                                'forum/chaptersubscriptionlist', 'forum/coordinatorsubscriptionlist',
                                'forum/internationalchaptersubscriptionlist', 'forum/internationalcoordinatorsubscriptionlist'
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
                            if ($listAdminCondition || $userAdmin) {
                                $boardlistRoute = route('chapters.chapboardlist');
                            }
                            $activeBoardlistRoutes = [
                                'chapter/boardlist'
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

                        @if ($einCondition && !$coordinatorCondition)
                        <li class="nav-item">
                            <a href="{{ route('international.inteinstatus') }}" class="nav-link {{ Request::is('chapterreports/inteinstatus') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-university"></i>
                                <p>International EIN Status</p>
                            </a>
                        </li>
                        @endif

                        <!-- Chapter Reports Menu Item -->
                        @php
                            if ($coordinatorCondition) {
                                $chapterReportsRoute = route('chapreports.chaprptchapterstatus');
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
                                <a href="{{ $coordReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeCoordReportsRoutes) }}">
                                    <i class="nav-icon fas fa-clipboard-user"></i>
                                    <p>Coordinator Reports</p>
                                </a>
                            </li>
                        @endif

                        <!-- End of Year Reports Menu Item-->
                            @php
                                if ($userAdmin || ($eoyTestCondition && $displayTESTING) || ($eoyReportCondition && $displayLIVE)) {
                                    $eoyReportsRoute = route('eoyreports.eoystatus');
                                }
                                $activeEOYReportsRoutes = [
                                    'eoy/*', 'eoydetails/*', 'eoydetailseditboundaries/*', 'eoydetailseditawards/*'
                                ];
                            @endphp
                            @if (isset($eoyReportsRoute))
                                <li class="nav-item">
                                    <a href="{{ $eoyReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeEOYReportsRoutes) }}">
                                        <i class="nav-icon fas fa-chart-line"></i>
                                        <p>EOY Reports
                                            @if ($userAdmin && !$displayTESTING && !$displayLIVE) *ADMIN*@endif
                                            @if ($eoyTestCondition && $displayTESTING) *TESTING*@endif
                                        </p>
                                    </a>
                                </li>
                            @endif

                        <!-- Admin Menu Item -->
                        @php
                            if ($userAdmin) {
                                $adminReportsRoute = route('admin.reregdate');
                            }
                            $activeAdminReportsRoutes = [
                                'admin/*'
                            ];
                        @endphp
                        @if (isset($adminReportsRoute))
                            <li class="nav-item">
                                <a href="{{ $adminReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeAdminReportsRoutes) }}">
                                    <i class="nav-icon fas fa-unlock-keyhole"></i>
                                    <p>Admin</p>
                                </a>
                            </li>
                        @endif

                        <!-- Admin Reports Menu Item -->
                        @php
                            if ($userAdmin) {
                                $adminReportsRoute = route('adminreports.useradmin');
                            }
                            $activeAdminReportsRoutes = [
                                'adminreports/*'
                            ];
                        @endphp
                        @if (isset($adminReportsRoute))
                            <li class="nav-item">
                                <a href="{{ $adminReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeAdminReportsRoutes) }}">
                                    <i class="nav-icon fas fa-clipboard-check"></i>
                                    <p>Admin Reports</p>
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
                            {{-- <a href="{{ url(config('forum.frontend.router.prefix') . '/c/2-coordinatorlist') }}" target="_blank" class="nav-link"> --}}
                            <a href="{{ url(config('forum.frontend.router.prefix') . '/unread') }}" target="_blank" class="nav-link">
                                <i class="nav-icon fas fa-comments"></i>
                                <p>
                                    CoordinatorList
                                    {{-- @if( $forumCount->getUnreadForumCount() > 0) --}}
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

                        @if(View::shared('errors', false) !== false && $errors->any())
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
<!-- FontAwesome -->
<script defer src="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/js/all.js"></script>
<!-- overlayScrollbars -->
<script src="{{ config('settings.base_url') }}theme/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<!-- Summernote -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

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
        Inputmask({"mask": "http://*{1,250}"}).mask(".http-mask");
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

    function startExport(exportType, exportName) {
    // Define routes for each export type
    const routes = {
        'chapter': '{{ route("export.chapter") }}',
        'zapchapter': '{{ route("export.zapchapter") }}',
        'coordinator': '{{ route("export.coordinator", 0) }}',
        'retiredcoordinator': '{{ route("export.retiredcoordinator") }}',
        'appreciation': '{{ route("export.appreciation") }}',
        'chaptercoordinator': '{{ route("export.chaptercoordinator") }}',
        'reregoverdue': '{{ route("export.rereg") }}',
        'einstatus': '{{ route("export.einstatus") }}',
        'eoystatus': '{{ route("export.eoystatus") }}',

        'intchapter': '{{ route("export.intchapter") }}',
        'intzapchapter': '{{ route("export.intzapchapter") }}',
        'intcoordinator': '{{ route("export.intcoordinator", 0) }}',
        'intretiredcoordinator': '{{ route("export.intretcoordinator") }}',
        'intreregoverdue': '{{ route("export.intrereg") }}',
        'inteinstatus': '{{ route("export.inteinstatus") }}',
        'intirsfiling': '{{ route("export.intirsfiling") }}',
        'inteoystatus': '{{ route("export.inteoystatus") }}',
    };

    // Get the route for this export type
    const route = routes[exportType];

    // Check if we need to use AJAX (recommended approach that gives better control)
    const useAjax = true;

    if (useAjax) {
        // Show processing dialog
        Swal.fire({
            title: `Exporting ${exportName}`,
            text: 'Please wait while your download is being prepared...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();

                // Use AJAX to request the file and track its progress
                $.ajax({
                    url: route,
                    type: 'GET',
                    xhrFields: {
                        responseType: 'blob' // Important for handling file downloads
                    },
                    success: function(blob) {
                        // Create a URL for the blob
                        const url = window.URL.createObjectURL(blob);

                        // Create a temporary link and trigger the download
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;

                        // Set filename from Content-Disposition header if available, otherwise use a default
                        const filename = exportType + '-export.csv'; // Default filename
                        a.download = filename;

                        document.body.appendChild(a);
                        a.click();

                        // Clean up
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        // Show success message
                        Swal.fire({
                            title: 'Download Complete!',
                            text: `Your ${exportName} has been downloaded successfully.`,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn-sm btn-success'
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        // Show error message if download fails
                        Swal.fire({
                            title: 'Download Failed',
                            text: 'There was a problem generating your export. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn-sm btn-danger'
                            }
                        });
                    }
                });
            }
        });
    } else {
        // Fallback approach using iframe if AJAX doesn't work with your server setup
        // Show processing dialog
        Swal.fire({
            title: `Exporting ${exportName}`,
            text: 'Please wait while your download begins...',
            allowOutsideClick: false,
            showConfirmButton: true,
            confirmButtonText: 'Download Complete',
            customClass: {
                confirmButton: 'btn-sm btn-success'
            },
            didOpen: () => {
                // Create a hidden iframe to handle the download
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                document.body.appendChild(iframe);

                // Start the download
                iframe.src = route;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // User confirmed download is complete
                Swal.fire({
                    title: 'Download Complete!',
                    text: `Your ${exportName} has been downloaded successfully.`,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }
}

// function showChapterEmailModal(chapterName, chapterId) {
//     Swal.fire({
//         title: 'Chapter Email Message',
//         html: `
//             <p>This will send your message to the full board and full coordinator list for <b>${chapterName}</b>.</p>
//             <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
//                 <input type="text" id="email_subject" name="email_subjet" class="swal2-input" placeholder ="Enter Subject" required style="width: 100%; margin: 0 !important; ">
//             </div>
//             <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
//                 <textarea id="email_message" name="email_message" class="swal2-textarea" placeholder="Email Message" required style="width: 100%; height: 80px; margin: 0 !important; box-sizing: border-box;"></textarea>
//             </div>
//             <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
//         `,
//         showCancelButton: true,
//         confirmButtonText: 'OK',
//         cancelButtonText: 'Close',
//         customClass: {
//             confirmButton: 'btn-sm btn-success',
//             cancelButton: 'btn-sm btn-danger'
//         },
//         preConfirm: () => {
//             const subject = Swal.getPopup().querySelector('#email_subject').value;
//             const message = Swal.getPopup().querySelector('#email_message').value;
//             const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

//             if (!subject) {
//                 Swal.showValidationMessage('Please enter subject.');
//                 return false;
//             }

//             if (!message) {
//                 Swal.showValidationMessage('Please enter message.');
//                 return false;
//             }

//             return {
//                 email_subject: subject,
//                 email_message: message,
//                 chapter_id: chapterId,
//             };
//         }
//     }).then((result) => {
//         if (result.isConfirmed) {
//             const data = result.value;

//             Swal.fire({
//                 title: 'Processing...',
//                 text: 'Please wait while we process your request.',
//                 allowOutsideClick: false,
//                 customClass: {
//                     confirmButton: 'btn-sm btn-success',
//                     cancelButton: 'btn-sm btn-danger'
//                 },
//                 didOpen: () => {
//                     Swal.showLoading();

//                     // Perform the AJAX request
//                     $.ajax({
//                         url: '{{ route('chapters.sendchapter') }}',
//                         type: 'POST',
//                         data: {
//                             subject: data.email_subject,
//                             message: data.email_message,
//                             chapterId: data.chapter_id,
//                             _token: '{{ csrf_token() }}'
//                         },
//                         success: function(response) {
//                             Swal.fire({
//                                 title: 'Success!',
//                                 text: response.message,
//                                 icon: 'success',
//                                 showConfirmButton: false,  // Automatically close without "OK" button
//                                 timer: 1500,
//                                 customClass: {
//                                     confirmButton: 'btn-sm btn-success'
//                                 }
//                             }).then(() => {
//                                 location.reload(); // Reload the page to reflect changes
//                             });
//                         },
//                         error: function(jqXHR, exception) {
//                             Swal.fire({
//                                 title: 'Error!',
//                                 text: 'Something went wrong, Please try again.',
//                                 icon: 'error',
//                                 confirmButtonText: 'OK',
//                                 customClass: {
//                                     confirmButton: 'btn-sm btn-success'
//                                 }
//                             });
//                         }
//                     });
//                 }
//             });
//         }
//     });
// }

function showChapterEmailModal(chapterName, chapterId) {
    Swal.fire({
        title: 'Chapter Email Message',
        html: `
            <p>This will send your message to the full board and full coordinator list for <b>${chapterName}</b>.</p>
            <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <input type="text" id="email_subject" name="email_subject" class="swal2-input" placeholder ="Enter Subject" required style="width: 100%; margin: 0 !important; ">
            </div>
            <div style="width: 100%; margin-bottom: 10px;">
                <textarea id="email_message" name="email_message" class="rich-editor" placeholder="Email Message" required style="width: 100%; height: 150px; margin: 0 !important; box-sizing: border-box;"></textarea>
            </div>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger',
            popup: 'swal-wide-popup' // Add this class for wider popup
        },
        didOpen: () => {
            // Initialize Summernote on the email message textarea
            $('#email_message').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link']]
                ],
                callbacks: {
                    onChange: function(contents) {
                        // Update the hidden textarea with the HTML content
                        $(this).val(contents);
                    }
                }
            });

            // Add some styling for the wider popup
            if (!document.getElementById('swal-wide-popup-style')) {
                const style = document.createElement('style');
                style.id = 'swal-wide-popup-style';
                style.innerHTML = `
                    .swal-wide-popup {
                        width: 80% !important;
                        max-width: 800px !important;
                    }
                    .note-editor {
                        margin-bottom: 10px !important;
                        width: 100% !important;
                    }
                    .note-editable {
                        text-align: left !important;
                    }
                    .note-editing-area {
                        width: 100% !important;
                    }
                `;
                document.head.appendChild(style);
            }
        },
        preConfirm: () => {
            const subject = Swal.getPopup().querySelector('#email_subject').value;
            // Get the HTML content from Summernote
            const message = $('#email_message').summernote('code');
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            if (!subject) {
                Swal.showValidationMessage('Please enter subject.');
                return false;
            }

            if (!message) {
                Swal.showValidationMessage('Please enter message.');
                return false;
            }

            return {
                email_subject: subject,
                email_message: message,
                chapter_id: chapterId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                didOpen: () => {
                    Swal.showLoading();

                    // Perform the AJAX request
                    $.ajax({
                        url: '{{ route('chapters.sendchapter') }}',
                        type: 'POST',
                        data: {
                            subject: data.email_subject,
                            message: data.email_message,
                            chapterId: data.chapter_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,  // Automatically close without "OK" button
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            });
                        }
                    });
                }
            });
        }
    });
}

 </script>
 @yield('customscript')
 </html>

