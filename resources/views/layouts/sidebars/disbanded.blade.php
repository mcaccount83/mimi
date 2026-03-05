    <!-- Board Dashboard Menu Item -->
    @if(isset($chDetails))
     @php
        $boardRoute = route('board-new.editdisbandchecklist', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/disbandchecklist/*',
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
    @endif

    <!-- ReReg Menu Item -->
    @if(isset($chDetails))
    @php
        $boardRoute = route('board-new.editfinancialreportfinal', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/financialreportfinal/*',
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
    @endif

    <!-- Donations Menu Item -->
    @if(isset($chDetails))
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
                <p>Donation</p>
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
