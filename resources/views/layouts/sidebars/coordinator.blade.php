

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
        $activeChapterRoutes = [
            'chapter/*',
        ];
    @endphp
    @isset($chaptersRoute)
        <li class="nav-item">
            <a href="{{ $chaptersRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeChapterRoutes) }}">
                <i class="nav-icon bi bi-house-fill"></i>
                <p>Chapters</p>
            </a>
        </li>
    @endisset

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
    @isset($coordinatorsRoute)
        <li class="nav-item">
            <a href="{{ $coordinatorsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeCoordinatorsRoutes) }}">
                <i class="nav-icon bi bi-people-fill"></i>
                <p>Coordinators</p>
            </a>
        </li>
    @endisset

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
    @isset($paymentsRoute)
        <li class="nav-item">
            <a href="{{ $paymentsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activePaymentsRoutes) }}">
                <i class="nav-icon bi bi-credit-card-fill"></i>
                <p>Payments/Donations</p>
            </a>
        </li>
    @endisset

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
    @isset($websiteRoute)
        <li class="nav-item">
            <a href="{{ $websiteRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeWebsiteRoutes) }}">
                <i class="nav-icon bi bi-laptop"></i>
                <p>Website/Social Media</p>
            </a>
        </li>
    @endisset

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
    @isset($inquiriesRoute)
        <li class="nav-item">
            <a href="{{ $inquiriesRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeInquiriesRoutes) }}">
                <i class="nav-icon bi bi-pin-map-fill"></i>
                <p>Inquiries
                    @if($pendingInquiryCount > 0)
                        <span class="badge bg-danger badge-pill notification-badge">
                            PENDING
                        </span>
                    @endif
                </p>
            </a>
        </li>
    @endisset

        <!-- New Menu Item -->
        @php
        if ($coordinatorCondition && $conferenceCoordinatorCondition) {
            $newChaptersRoute = route('chapters.chaplistpending');
        } elseif ($ITCondition) {
            $newChaptersRoute = route('chapters.chaplistpending', ['check5' => 'yes']);
        }
        $activeNewChapterRoutes = [
            'application/*',
        ];
    @endphp
    @isset($newChaptersRoute)
        <li class="nav-item">
            <a href="{{ $newChaptersRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeNewChapterRoutes) }}">
                <i class="nav-icon bi bi-asterisk"></i>
                <p>New Chapters/Coordinators
                     @if($pendingNewChapterCount > 0 || $pendingNewCoordCount > 0)
                        <span class="badge bg-danger badge-pill notification-badge">
                            PENDING
                        </span>
                    @endif
                </p>
            </a>
        </li>
    @endisset

        <!-- ListAdmin Menu Item -->
        @php
            // if ($coordinatorCondition && $conferenceCoordinatorCondition) {
            if ($listAdminCondition || $ITCondition) {
                $listSubscriptionRoute = route('forum.chaptersubscriptionlist');
            } elseif ($listAdminCondition || $ITCondition) {
                $listSubscriptionRoute = route('forum.chaptersubscriptionlist', ['check5' => 'yes']);
            }
            $activeChapterRoutes = [
                'listadmin/*',
            ];
        @endphp
        @isset($listSubscriptionRoute)
            <li class="nav-item">
                <a href="{{ $listSubscriptionRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeChapterRoutes) }}">
                    <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em;">
                            <i class="bi bi-chat-quote-fill position-absolute"></i>
                            <i class="bi bi-gear-fill position-absolute" style="font-size: 0.5em; bottom: -0.1em; right: -0.1em; background-color: #343a40; border-radius: 90%;"></i>
                        </span>
                    <p>ListAdmin
                        @if( $pendingPostsCount > 0 || $pendingThreadsCount > 0 )
                        <span class="badge bg-danger badge-pill notification-badge">
                            PENDING
                        </span>
                    @endif
                    </p>
                </a>
            </li>
        @endisset

    <!-- BoardList Email Menu Item -->
    {{-- @php
        if ($listAdminCondition || $ITCondition) {
            $boardlistRoute = route('chapters.chapboardlist');
        }
        $activeBoardlistRoutes = [
            'listadmin/boardlist'
        ];
    @endphp
    @isset($boardlistRoute)
        <li class="nav-item">
            <a href="{{ $boardlistRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardlistRoutes) }}">
                <i class="nav-icon bi bi-card-list"></i>
                <p>BoardList Emails - OLD</p>
            </a>
        </li>
    @endisset --}}

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
    @isset($chapterReportsRoute)
        <li class="nav-item">
            <a href="{{ $chapterReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeChapterReportsRoutes) }}">
                <i class="nav-icon bi bi-house-gear-fill"></i>
                <p>Chapter Reports</p>
            </a>
        </li>
    @endisset

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
    @isset($coordReportsRoute)
        <li class="nav-item">
            <a href="{{ $coordReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeCoordReportsRoutes) }}">
                    <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em;">
                        <i class="bi bi-people-fill position-absolute"></i>
                        <i class="bi bi-gear-fill position-absolute" style="font-size: 0.5em; bottom: -0.1em; right: -0.1em; background-color: #343a40; border-radius: 90%;"></i>
                    </span>
                <p>Coordinator Reports</p>
            </a>
        </li>
    @endisset

    <!-- End of Year Reports Menu Item-->
        @php
            if (($coordinatorCondition && $displayEOYLIVE) || ($eoyReportCondition && $displayEOYLIVE) || ($eoyTestCondition && $displayEOYTESTING)) {
                $eoyReportsRoute = route('eoyreports.eoystatus');
            } elseif ($ITCondition) {
                $eoyReportsRoute = route('eoyreports.eoystatus', ['check5' => 'yes']);
            } elseif ($einCondition && $displayEOYLIVE) {
                $eoyReportsRoute = route('eoyreports.eoyirssubmission', ['check5' => 'yes']);
            }
            $activeEOYReportsRoutes = [
                'eoyreports/*',
            ];
        @endphp
        @isset($eoyReportsRoute)
            <li class="nav-item">
                <a href="{{ $eoyReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeEOYReportsRoutes) }}">
                    <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em;">
                        <i class=" bi bi-file-earmark-bar-graph-fill position-absolute"></i>
                        <i class="bi bi-gear-fill position-absolute" style="font-size: 0.5em; bottom: -0.1em; right: -0.1em; background-color: #343a40; border-radius: 90%;"></i>
                    </span>
                    <p>EOY Reports
                        @if ($ITCondition && !$displayEOYTESTING && !$displayEOYLIVE) *ADMIN*@endif
                        @if ($eoyTestCondition && $displayEOYTESTING) *TESTING*@endif
                    </p>
                </a>
            </li>
        @endisset

    <!-- Admin Reports Menu Item -->
    @php
        if ($coordinatorCondition && $conferenceCoordinatorCondition) {
            $adminReportsRoute =  url(config('sentemails.routepath'));
        }
        $activeAdminReportsRoutes = [
            'adminreports/*'
        ];
    @endphp
    @isset($adminReportsRoute)
        <li class="nav-item">
            <a href="{{ $adminReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeAdminReportsRoutes) }}">
            <span class="nav-icon d-inline-flex align-items-center justify-content-center position-relative" style="width: 1em; height: 1em;">
                        <i class="bi bi-shield-shaded position-absolute"></i>
                        <i class="bi bi-gear-fill position-absolute" style="font-size: 0.5em; bottom: -0.1em; right: -0.1em; background-color: #343a40; border-radius: 90%;"></i>
                    </span>
                <p>Admin Reports</p>
            </a>
        </li>
    @endisset

    <!-- User Reports Menu Item -->
    @php
        if ($ITCondition) {
            $userReportsRoute = route('userreports.useradmin');
        }
        $activeUserReportsRoutes = [
            'userreports/*'
        ];
    @endphp
    @isset($userReportsRoute)
        <li class="nav-item">
            <a href="{{ $userReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeUserReportsRoutes) }}">
                    <i class="nav-icon bi bi-person-fill-gear"></i>
                <p>User Reports</p>
            </a>
        </li>
    @endisset

        <!-- Tech Reports Menu Item -->
    @php
        if ($ITCondition) {
            $techReportsRoute = route('logs');
        }
        $activeTechReportsRoutes = [
            'techreports/*'
        ];
    @endphp
    @isset($techReportsRoute)
        <li class="nav-item">
            <a href="{{ $techReportsRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeTechReportsRoutes) }}">
                    <i class="nav-icon bi bi-database-fill-gear"></i>
                <p>IT Reports</p>
            </a>
        </li>
    @endisset

    <!-- Resources Reports Menu Item -->
    @php
        if ($coordinator) {
            $resourcesRoute = route('resources.toolkit');
        }
        $activeResourcesRoutes = [
            'resources/*'
        ];
    @endphp
    @isset($resourcesRoute)
        <li class="nav-item">
            <a href="{{ $resourcesRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeResourcesRoutes) }}">
                <i class="nav-icon bi bi-briefcase-fill"></i>
                <p>Resources</p>
            </a>
        </li>
    @endisset

        <!-- ForumList Menu Item -->
        @php
        $forumRoute = url(config('forum.frontend.router.prefix') . '/unread');

        $activeForumRoutes = [
            'forum/*',
        ];
        @endphp
        @isset($forumRoute)
            <li class="nav-item">
                <a href="{{ $forumRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeForumRoutes) }}">
                    <i class="nav-icon bi bi-chat-quote-fill"></i>
                    <p>ForumLists
                        @if( $unreadForumCount > 0)
                        <span class="badge bg-danger badge-pill notification-badge">
                            UNREAD
                        </span>
                    @endif
                    </p>
                </a>
            </li>
        @endisset

    <li class="nav-item">
        <a href="{{ route('coordinators.profile') }}" class="nav-link {{ Request::is('coordprofile') ? 'active' : '' }}">
        <i class="nav-icon bi bi-person-circle"></i>
        <p>Update Profile<br>
        ({{$loggedIn}})</p>
        </a>
    </li>

