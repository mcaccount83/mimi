    <!-- Disband Checklist Dashboard Menu Item -->
    <li class="nav-item">
        <a href="{{ route('board.editdisbandchecklist', ['id' => $chDetails->id]) }}" class="nav-link {{ Request::is('board/disbandchecklist/*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house-fill"></i>
            <p>Disband Checklist</p>
        </a>
    </li>
    {{-- @isset($chDetails)
     @php
        $boardRoute = route('board.editdisbandchecklist', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/disbandchecklist/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-house-fill"></i>
                <p>Disband Checklist</p>
            </a>
        </li>
    @endisset
    @endisset --}}

    <!-- ReReg Menu Item -->
    @isset($chDetails)
    @php
        $boardRoute = route('board.editfinancialreportfinal', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/financialreportfinal/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-file-earmark-bar-graph"></i>
                <p>Financial Report</p>
            </a>
        </li>
    @endisset
    @endisset

     <!-- ReReg Menu Item -->
     @isset($chDetails)
    @php
        $boardRoute = route('board.editreregpayment', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
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
        $boardRoute = route('board.editdonate', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/donation/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-currency-dollar"></i>
                <p>Donation</p>
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
