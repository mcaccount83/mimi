 <!-- Chapter Status Menu Item -->
<li class="nav-item">
        <a href="{{ route('board.newchapterstatus', ['id' => $chDetails->id]) }}" class="nav-link {{ Request::is('board/newchapterstatus/*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house-fill"></i>
            <p>Chapter Status</p>
        </a>
    </li>
{{--
 @isset($chDetails)
     @php
        $boardRoute = route('board.newchapterstatus', ['id' => $chDetails->id]);

        $activeBoardRoutes = [
            'board/newchapterstatus/*',
        ];
    @endphp
    @isset($boardRoute)
        <li class="nav-item">
            <a href="{{ $boardRoute }}" class="nav-link {{ $positionService->isActiveRoute($activeBoardRoutes) }}">
                <i class="nav-icon bi bi-house-fill"></i>
                <p>Chapter Status</p>
            </a>
        </li>
    @endisset
@endisset --}}
