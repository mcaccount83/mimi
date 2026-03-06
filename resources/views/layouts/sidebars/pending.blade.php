 <!-- Chapter Status Menu Item -->
<li class="nav-item">
        <a href="{{ route('board-new.newchapterstatus', ['id' => $chDetails->id]) }}" class="nav-link {{ Request::is('board-new/newchapterstatus/*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-house-fill"></i>
            <p>Chapter Status</p>
        </a>
    </li>
{{--
 @if(isset($chDetails))
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
@endif --}}
