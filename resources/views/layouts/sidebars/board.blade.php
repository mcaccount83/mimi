

    <!-- Board Dashboard Menu Item -->
    <li class="nav-item">
        <a href="{{ route('board-new.chapterprofile', ['id' => $chDetails->id]) }}" class="nav-link {{ Request::is('chapterprofile') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house-fill"></i>
            <p>Chapter Profile</p>
        </a>
    </li>

    @php
        $boardRoute = route('board-new.chapterprofile', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/chapterprofile/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-house-fill"></i>
                <p>Chapter Profile</p>
            </a>
        </li>
    @endif

    <!-- Board Menu Item -->
    @php
        $boardRoute = route('board-new.editprofile', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/profile/*',
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

    <!-- ReReg Menu Item -->
    @php
        $boardRoute = route('board-new.editreregpayment', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
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

    <!-- Donations Menu Item -->
    @php
        $boardRoute = route('board-new.editdonate', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
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

    <!-- Documents Menu Item -->
    @php
        $boardRoute = route('board-new.editprofile', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/profile/*',
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

    <!-- Resources Menu Item -->
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

    <!-- End of Year Menu Item -->
    @php
        $boardRoute = route('board-new.editprofile', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/profile/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-people-fill"></i>
                <p>End of Year</p>
            </a>
        </li>
    @endif

    <!-- eLearning Menu Item -->
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

    <!-- BoardList Forum Menu Item -->
        <li class="nav-item position-relative">
        <a href="{{ url(config('forum.frontend.router.prefix') . '/unread') }}" target="_blank" class="nav-link">
            <i class="nav-icon bi bi-chat-quote-fill"></i>
            <p>
                BoardList Forum
                @if( $unreadForumCount > 0)
                    <span class="badge badge-danger badge-pill notification-badge">
                        UNREAD
                    </span>
                @endif
            </p>
        </a>
    </li>

