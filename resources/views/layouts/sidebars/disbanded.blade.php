    <!-- Disband Checklist Dashboard Menu Item -->
    <li class="nav-item">
        <a href="{{ route('board.editdisbandchecklist', ['id' => $chDetails->id]) }}" class="nav-link {{ Request::is('board/disbandchecklist/*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house-fill"></i>
            <p>Disband Checklist</p>
        </a>
    </li>
    {{-- @if(isset($chDetails))
     @php
        $boardRoute = route('board.editdisbandchecklist', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/disbandchecklist/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-house-fill"></i>
                <p>Disband Checklist</p>
            </a>
        </li>
    @endif
    @endif --}}

    <!-- ReReg Menu Item -->
    @if(isset($chDetails))
    @php
        $boardRoute = route('board.editfinancialreportfinal', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/financialreportfinal/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-file-earmark-bar-graph"></i>
                <p>Financial Report</p>
            </a>
        </li>
    @endif
    @endif

     <!-- ReReg Menu Item -->
     @if(isset($chDetails))
    @php
        $boardRoute = route('board.editreregpayment', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/reregpayment/*',
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
        $boardRoute = route('board.editdonate', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/donation/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-currency-dollar"></i>
                <p>Donation</p>
            </a>
        </li>
    @endif
    @endif

    <!-- Documents Menu Item -->
    @if(isset($chDetails))
    @php
        $boardRoute = route('board.viewdocuments', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/documents/*',
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
