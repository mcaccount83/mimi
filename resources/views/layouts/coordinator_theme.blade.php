<!doctype html>
<html lang="en">
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
    /* Sidebar font size */
    .app-sidebar .sidebar-wrapper {
        font-size: 0.95rem !important;
    }

    .app-sidebar .nav-link {
        font-size: 0.95rem !important;
        line-height: 1.2 !important;
    }

    .sidebar-menu .nav-icon {
    font-size: 1.3rem !important;
}

    /* Logo size and position in sidebar */
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

     .email-column a {
        display: inline-block;
        text-decoration: none;
        color: inherit;
    }

    label, .col-form-label {
    font-weight: bold;
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

h1, h2, h3, h4, h5, h6,
.card-title {
    font-weight: normal !important;
}

h1 { font-size: 2rem !important; }
h2 { font-size: 1.75rem !important; }
h3 { font-size: 1.25rem !important; }
h4 { font-size: 1.rem !important; }
h5 { font-size: 0.87rem !important; }
h6 { font-size: 0.75rem !important; }

.btn-xs {
    --bs-btn-padding-y: 0.25rem;
    --bs-btn-padding-x: 0.5rem;
    --bs-btn-font-size: 0.75rem;
    --bs-btn-border-radius: 0.25rem;
}

.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

</style>

  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
          </ul>
          <!--end::Start Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!-- Navbar Link -->
            <li class="nav-item">
                <a class="nav-link"  href="https://momsclub.org/" target="_blank" >Return to Main Site</a>
            </li>
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
          <!--begin::Brand Link-->
           <a href="{{ route('home') }}" class="brand-link d-flex justify-content-center align-items-center">
                <img src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" class="custom-logo">
            </a>
          <!--end::Brand Link-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="navigation"
              aria-label="Main navigation"
              data-accordion="false"
              id="navigation"
            >
              <!-- Menu for Logged In Users -->
                    @auth

     <!-- Coordinator Dashboard Menu Item -->
                        <li class="nav-item">
                            <a href="{{ route('coordinators.viewprofile') }}" class="nav-link {{ Request::is('viewprofile') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-speedometer2"></i>
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
                                    <i class="nav-icon bi bi-house-fill"></i>
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
                                    <i class="nav-icon bi bi-people-fill"></i>
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
                                    <i class="nav-icon bi bi-credit-card-2-back-fill"></i>
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
                                    <i class="nav-icon bi bi-laptop"></i>
                                    <p>Website/Social Media</p>
                                </a>
                            </li>
                        @endif

                        <!-- New Menu Item -->
                        @php
                        if (($coordinatorCondition && $conferenceCoordinatorCondition) || $inquiriesCondition) {
                                $inquiriesRoute = route('inquiries.inquiryapplication');
                            } elseif ($inquiriesInternationalCondition || $ITCondition) {
                                $inquiriesRoute = route('inquiries.inquiryapplication', ['check5' => 'yes']);
                            }
                            $activeInquiriesRoutes = [
                                'inquiries/*',
                            ];
                        @endphp
                        @if (isset($inquiriesRoute))
                            <li class="nav-item">
                                <a href="{{ $inquiriesRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeInquiriesRoutes) }}">
                                    <i class="nav-icon bi bi-pin-map-fill"></i>
                                    <p>Inquiries</p>
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
                                    <i class="nav-icon bi bi-asterisk"></i>
                                    <p>New Chapters/Coordinators</p>
                                </a>
                            </li>
                        @endif

                        <!-- List Subscription Menu Item -->
                        @php
                            // if ($coordinatorCondition && $conferenceCoordinatorCondition) {
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
                                    <i class="nav-icon bi bi-card-list"></i>
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
                                    <i class="nav-icon bi bi-card-list"></i>
                                    <p>BoardList Emails - OLD</p>
                                </a>
                            </li>
                        @endif

                        <!-- Chapter Reports Menu Item -->
                        @php
                            if ($coordinatorCondition && $regionalCoordinatorCondition) {
                                $chapterReportsRoute = route('chapreports.chaprptchapterstatus');
                            } elseif ($ITCondition) {
                                $coordReportsRoute = route('chapreports.chaprptchapterstatus', ['check5' => 'yes']);
                            } elseif ($einCondition) {
                                $coordReportsRoute = route('chapreports.chaprpteinstatus', ['check5' => 'yes']);
                            }
                            $activeChapterReportsRoutes = [
                                'chapterreports/*'
                            ];
                        @endphp
                        @if (isset($chapterReportsRoute))
                            <li class="nav-item">
                                <a href="{{ $chapterReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeChapterReportsRoutes) }}">
                                    {{-- <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em; font-size: 1.2em;">
                                            <i class="fa-solid fa-clipboard position-absolute"></i>
                                            <span style="ptop: 0.50em">▬</span>
                                            <i class="fa-solid fa-house position-absolute" style="font-size: 0.6em; color: rgba(0, 0, 0, 0.6); top: 0.50em;"></i>
                                        </span> --}}
                                    <i class="nav-icon bi bi-house-gear-fill"></i>
                                    <p>Chapter Reports</p>
                                </a>
                            </li>
                        @endif

                        <!-- Coordinator Reports Menu Item -->
                        @php
                            if ($supervisingCoordinatorCondition && $assistConferenceCoordinatorCondition) {
                                $coordReportsRoute = route('coordreports.coordrptvolutilization');
                            } elseif ($ITCondition) {
                                $coordReportsRoute = route('coordreports.coordrptvolutilization', ['check5' => 'yes']);
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
                                     {{-- <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em; font-size: 1.2em;">
                                            <i class="fa-solid fa-clipboard position-absolute"></i>
                                            <span style="ptop: 0.50em">▬</span>
                                            <i class="fa-solid fa-user-friends position-absolute" style="font-size: 0.5em; color: rgba(0, 0, 0, 0.6); top: 0.50em;"></i>
                                        </span> --}}
                                        <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em;">
                                            <i class="bi bi-people-fill position-absolute"></i>
                                            <i class="bi bi-gear-fill position-absolute" style="font-size: 0.5em; bottom: -0.1em; right: -0.1em; background-color: #343a40; border-radius: 90%;"></i>
                                        </span>
                                    <p>Coordinator Reports</p>
                                </a>
                            </li>
                        @endif

                        <!-- End of Year Reports Menu Item-->
                            @php
                                if (($coordinatorCondition && $displayLIVE) || ($eoyReportCondition && $displayLIVE) || ($eoyTestCondition && $displayTESTING)) {
                                    $eoyReportsRoute = route('eoyreports.eoystatus');
                                } elseif ($ITCondition) {
                                    $eoyReportsRoute = route('eoyreports.eoystatus', ['check5' => 'yes']);
                                } elseif ($einCondition && $displayLIVE) {
                                    $eoyReportsRoute = route('eoyreports.eoyirssubmission', ['check5' => 'yes']);
                                }
                                $activeEOYReportsRoutes = [
                                    'eoyreports/*',
                                ];
                            @endphp
                            @if (isset($eoyReportsRoute))
                                <li class="nav-item">
                                    <a href="{{ $eoyReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeEOYReportsRoutes) }}">
                                         {{-- <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em; font-size: 1.2em;">
                                            <i class="fa-solid fa-clipboard position-absolute"></i>
                                            <span style="ptop: 0.50em">▬</span>
                                            <i class="fa-solid fa-dollar-sign position-absolute" style="font-size: 0.6em; color: rgba(0, 0, 0, 0.8); top: 0.50em;"></i>
                                        </span> --}}
                                        <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em;">
                                            <i class=" bi bi-calculator-fill position-absolute"></i>
                                            <i class="bi bi-gear-fill position-absolute" style="font-size: 0.5em; bottom: -0.1em; right: -0.1em; background-color: #343a40; border-radius: 90%;"></i>
                                        </span>
                                        <p>EOY Reports
                                            @if ($ITCondition && !$displayTESTING && !$displayLIVE) *ADMIN*@endif
                                            @if ($eoyTestCondition && $displayTESTING) *TESTING*@endif
                                        </p>
                                    </a>
                                </li>
                            @endif

                        <!-- Admin Reports Menu Item -->
                        @php
                            if ($coordinatorCondition && $conferenceCoordinatorCondition) {
                                // $adminReportsRoute = route('adminreports.paymentlog');
                                $adminReportsRoute = route('adminreports.maillog');
                            }
                            $activeAdminReportsRoutes = [
                                'adminreports/*'
                            ];
                        @endphp
                        @if (isset($adminReportsRoute))
                           <li class="nav-item">
                                <a href="{{ $adminReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeAdminReportsRoutes) }}">
                            {{-- <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em; font-size: 1.2em;">
                                <i class="fa-solid fa-clipboard position-absolute"></i>
                                <span style="ptop: 0.50em">▬</span>
                                <i class="fa-solid fa-shield-halved position-absolute" style="font-size: 0.6em; color: rgba(0, 0, 0, 0.6); top: 0.50em;"></i>
                            </span> --}}
                             <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em;">
                                            <i class="bi bi-shield-shaded position-absolute"></i>
                                            <i class="bi bi-gear-fill position-absolute" style="font-size: 0.5em; bottom: -0.1em; right: -0.1em; background-color: #343a40; border-radius: 90%;"></i>
                                        </span>
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
                                {{-- <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em; font-size: 1.25em;">
                                    <i class="nav-icon fas fa-clipboard-user"></i>
                                </span> --}}
                                       <i class="nav-icon bi bi-person-fill-gear"></i>
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
                                     {{-- <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em; font-size: 1.2em;">
                                            <i class="fa-solid fa-clipboard position-absolute"></i>
                                            <span style="ptop: 0.50em">▬</span>
                                            <i class="fa-solid fa-screwdriver-wrench position-absolute" style="font-size: 0.5em; color: rgba(0, 0, 0, 0.6); top: 0.65em;"></i>
                                        </span> --}}
                                        <i class="nav-icon bi bi-database-fill-gear"></i>
                                    <p>IT Reports</p>
                                </a>
                            </li>
                        @endif

                        <!-- Resources Reports Menu Item -->
                        @php
                            if ($coordinator) {
                                $resourcesRoute = route('resources.toolkit');
                            }
                            $activeResourcesRoutes = [
                                'resources/*'
                            ];
                        @endphp
                        @if (isset($resourcesRoute))
                            <li class="nav-item">
                                <a href="{{ $resourcesRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeResourcesRoutes) }}">
                                    <i class="nav-icon bi bi-briefcase-fill"></i>
                                    <p>Resources</p>
                                </a>
                            </li>
                        @endif

                         <!-- CoordinatorList Menu Item -->
                         <li class="nav-item position-relative">
                            <a href="{{ url(config('forum.frontend.router.prefix') . '/unread') }}" target="_blank" class="nav-link">
                                <i class="nav-icon bi bi-chat-quote-fill"></i>
                                <p>
                                    CoordinatorList Forum
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
                            <i class="nav-icon bi bi-person-circle"></i>
                            <p>Update Profile</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('logout') }}" class="nav-link"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="nav-icon bi bi-arrow-counterclockwise"></i>
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
                            <div class="input-group-text">
                                <span class="bi bi-envelope"></span>
                            </div>
                    </div>

                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="Password">
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                            <div class="input-group-text">
                                <span class="bi bi-lock-fill"></span>
                            </div>
                    </div>

                    <div class="row">
                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-secondary bg-gradient" onClick="this.form.submit(); this.disabled=true;">{{ __('Login') }}</button>
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
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6"><h2 class="mb-0">@yield('page_title')</h2></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-dashboard me-2"></i>Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page">@yield('breadcrumb')</li>
                </ol></li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">

            @yield('content')

        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
       <footer class="app-footer">
        <!--begin::Copyright-->
            <strong>Copyright &copy;{{$currentYear}} <a href="https://momsclub.org/" target="_blank">MOMS Club</a>.</strong> All rights reserved.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

@include('layouts.scripts.alert')
@include('layouts.scripts.boards')
@include('layouts.scripts.boardreport')
@include('layouts.scripts.boardactivate')
@include('layouts.scripts.checkboxes')
@include('layouts.scripts.coordinatorlist')
@include('layouts.scripts.datatable')
@include('layouts.scripts.datetime')
@include('layouts.scripts.email')
@include('layouts.scripts.export')
@include('layouts.scripts.information')
@include('layouts.scripts.irs')
@include('layouts.scripts.masks')
@include('layouts.scripts.menu')
@include('layouts.scripts.password')
@include('layouts.scripts.pdfviewer')
@include('layouts.scripts.probation')
@include('layouts.scripts.resources')
@include('layouts.scripts.sendemail')
@include('layouts.scripts.uploads')
@include('layouts.scripts.useractions')
@include('layouts.scripts.website')

@yield('customscript')
@stack('scripts')

  </body>
  <!--end::Body-->
</html>

  </body>
  <!--end::Body-->
</html>
