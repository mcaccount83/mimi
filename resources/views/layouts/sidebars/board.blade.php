
    <!-- Board Dashboard Menu Item -->
    <li class="nav-item">
        <a href="{{ route('board.chapterprofile', ['id' => $chDetails->id]) }}" class="nav-link {{ Request::is('board/chapterprofile/*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house-fill"></i>
            <p>Chapter Profile</p>
        </a>
    </li>

    <!-- Board Menu Item -->
    @isset($chDetails)
    @php
        $boardRoute = route('board.editboard', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/board/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-people-fill"></i>
                <p>Executive Board</p>
            </a>
        </li>
    @endisset
    @endisset

    <!-- Online Menu Item -->
    @isset($chDetails)
    @php
        $boardRoute = route('board.editonline', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/online/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-laptop"></i>
                <p>Online Accounts</p>
            </a>
        </li>
    @endisset
    @endisset

    <!-- ReReg Menu Item -->
    @isset($chDetails)
    @php
        $boardRoute = route('board.viewrereghistory', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/rereghistory/*',
            'board/reregpayment/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-credit-card-fill"></i>
                <p>Re-Registration</p>
            </a>
        </li>
    @endisset
    @endisset

    <!-- Donations Menu Item -->
    @isset($chDetails)
    @php
        $boardRoute = route('board.viewdonationhistory', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/donationhistory/*',
            'board/donation/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-currency-dollar"></i>
                <p>Donations</p>
            </a>
        </li>
    @endisset
    @endisset

    <!-- Documents Menu Item -->
    @isset($chDetails)
    @php
        $boardRoute = route('board.viewdocuments', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/documents/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-files"></i>
                <p>Documents</p>
            </a>
        </li>
    @endisset
    @endisset

    <!-- Awards Menu Item -->
    @isset($chDetails)
    @php
        $boardRoute = route('board.viewawardhistory', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/awardhistory/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi-award-fill"></i>
                <p>Awards</p>
            </a>
        </li>
    @endisset
    @endisset

    @if ($probationParty)
     {{-- @if($chDetails->status_id == \App\Enums\OperatingStatusEnum::PROBATION ); --}}
    {{-- @if($chDetails->probation == '5') --}}
        <!-- Quarterly Submission Menu Item -->
        @isset($chDetails)
        @php
            $boardRoute = route('board.editprobation', ['id' => $chDetails->id]);

            $activeBoardRoutes = [
                'board/probation/*',
            ];
        @endphp
        @isset($boardRoute)
            <li class="nav-item">
                <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                    <i class="nav-icon bi bi-pie-chart-fill"></i>
                    <p>Quarterly Submision</p>
                </a>
            </li>
        @endisset
        @endisset
    @endif

    <!-- End of Year Menu Item -->
    @isset($chDetails)
    @php
        $boardRoute = route('board.viewendofyear', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/endofyear/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-file-earmark-bar-graph-fill"></i>
                <p>End of Year</p>
            </a>
        </li>
    @endisset
    @endisset

    <!-- Resources Menu Item -->
    @isset($chDetails)
    @php
        $boardRoute = route('board.viewresources', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/resources/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-briefcase-fill"></i>
                <p>Resources</p>
            </a>
        </li>
    @endisset
    @endisset

    <!-- eLearning Menu Item -->
    @isset($chDetails)
    @php
        $boardRoute = route('board.viewelearning', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/elearning/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-mortarboard-fill"></i>
                <p>eLearning</p>
            </a>
        </li>
    @endisset
    @endisset

    <!-- ForumList Menu Item -->
    @if ($boardList)
        @php
            $forumRoute = url(config('forum.frontend.router.prefix') . '/unread');

            $activeForumRoutes = [
                'forum/*',
            ];
        @endphp
        @isset($forumRoute)
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
        @endisset
    @endif

    <!-- Main MC Webstie -->
    <li class="nav-item">
        <a href="https://momsclub.org" target="_blank" rel="noopener noreferrer" class="nav-link">
        <i class="nav-icon bi bi-globe-americas"></i>
        <p>Main MC Website</p>
        </a>
    </li>

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
        <a href="{{ route('board.profile', ['id' => $chDetails->id]) }}" class="nav-link {{ Request::is('board/profile/*') ? 'active' : '' }}">
        <i class="nav-icon bi bi-person-circle"></i>
        <p>Update Profile<br>
            @if ($userTypeId == \App\Enums\UserTypeEnum::COORD && isset($bdTypeId) && $bdTypeId !== null)
            (President)</p>
            @else
            ({{$loggedIn}})</p>
            @endif
        </a>
    </li>

