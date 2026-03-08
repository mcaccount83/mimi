
    <!-- Board Dashboard Menu Item -->
    <li class="nav-item">
        <a href="{{ route('board-new.chapterprofile', ['id' => $chDetails->id]) }}" class="nav-link {{ Request::is('board-new/chapterprofile/*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house-fill"></i>
            <p>Chapter Profile</p>
        </a>
    </li>

    <!-- Board Menu Item -->
    @if(isset($chDetails))
    @php
        $boardRoute = route('board-new.editboard', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/board/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-people-fill"></i>
                <p>Executive Board</p>
            </a>
        </li>
    @endif
    @endif

    <!-- Online Menu Item -->
    @if(isset($chDetails))
    @php
        $boardRoute = route('board-new.editonline', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/online/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-laptop"></i>
                <p>Online Accounts</p>
            </a>
        </li>
    @endif
    @endif

    <!-- ReReg Menu Item -->
    @if(isset($chDetails))
    @php
        $boardRoute = route('board-new.viewrereghistory', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/rereghistory/*',
            'board-new/reregpayment/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-credit-card-fill"></i>
                <p>Re-Registration</p>
            </a>
        </li>
    @endif
    @endif

    <!-- Donations Menu Item -->
    @if(isset($chDetails))
    @php
        $boardRoute = route('board-new.viewdonationhistory', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/donationhistory/*',
            'board-new/donation/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-currency-dollar"></i>
                <p>Donations</p>
            </a>
        </li>
    @endif
    @endif

    <!-- Documents Menu Item -->
    @if(isset($chDetails))
    @php
        $boardRoute = route('board-new.viewdocuments', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/documents/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-files"></i>
                <p>Documents</p>
            </a>
        </li>
    @endif
    @endif

    {{-- @if($chDetails->probation_id == '3') --}}
        <!-- Quarterly Submission Menu Item -->
        @if(isset($chDetails))
        @php
            $boardRoute = route('board-new.editprobation', ['id' => $chDetails->id]);

            $activeBoardRoutes = [
                'board-new/probation/*',
            ];
        @endphp
        @if (isset($boardRoute))
            <li class="nav-item">
                <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                    <i class="nav-icon bi bi-pie-chart-fill"></i>
                    <p>Quarterly Submision</p>
                </a>
            </li>
        @endif
        @endif
    {{-- @endif --}}

    <!-- Resources Menu Item -->
    @if(isset($chDetails))
    @php
        $boardRoute = route('board-new.viewresources', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/resources/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-briefcase-fill"></i>
                <p>Resources</p>
            </a>
        </li>
    @endif
    @endif

    <!-- End of Year Menu Item -->
    @if(isset($chDetails))
    @php
        $boardRoute = route('board-new.viewendofyear', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/endofyear/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-file-earmark-bar-graph-fill"></i>
                <p>End of Year</p>
            </a>
        </li>
    @endif
    @endif

    <!-- eLearning Menu Item -->
    @if(isset($chDetails))
    @php
        $boardRoute = route('board-new.viewelearning', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/elearning/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-mortarboard-fill"></i>
                <p>eLearning</p>
            </a>
        </li>
    @endif
    @endif

    <!-- ForumList Menu Item -->
    @php
        $forumRoute = url(config('forum.frontend.router.prefix') . '/unread');

        $activeForumRoutes = [
            'forum/*',
        ];
    @endphp
    @if (isset($forumRoute))
        <li class="nav-item">
             @if ($userTypeId == \App\Enums\UserTypeEnum::COORD && isset($bdTypeId) && $bdTypeId !== null)
        <a href="#" target="_blank" class="nav-link" style="cursor: default; pointer-events: none; background-color: transparent !important; color: #c2c7d0 !important;">
            @else
            <a href="{{ $forumRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeForumRoutes) }}">
                @endif
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
    @endif

{{--

        <li class="nav-item position-relative">
            @if ($userTypeId == \App\Enums\UserTypeEnum::COORD && isset($bdTypeId) && $bdTypeId !== null)
        <a href="#" target="_blank" class="nav-link" style="cursor: default; pointer-events: none; background-color: transparent !important; color: #c2c7d0 !important;">
            @else
        <a href="{{ url(config('forum.frontend.router.prefix') . '/unread') }}" target="_blank" class="nav-link">
            @endif
            <i class="nav-icon bi bi-chat-quote-fill"></i>
            <p>
                ForumLists
                @if( $unreadForumCount > 0)
                    <span class="badge badge-danger badge-pill notification-badge">
                        UNREAD
                    </span>
                @endif
            </p>
        </a>
    </li> --}}

    <li class="nav-item">
        <a href="{{ route('board-new.profile', ['id' => $chDetails->id]) }}" class="nav-link {{ Request::is('board-new/profile/*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-person-circle"></i>
        <p>Update Profile</p>
        </a>
    </li>

