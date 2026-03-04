 <!-- Board Dashboard Menu Item -->
     @php
        $boardRoute = route('board-new.newchapterstatus', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board-new/newchapterstatus/*',
        ];
    @endphp
    @if (isset($boardRoute))
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-house-fill"></i>
                <p>Chapter Status</p>
            </a>
        </li>
    @endif
