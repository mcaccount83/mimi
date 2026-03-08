<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if($board || $coordinator)
        <a class="dropdown-item" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.boardlistLink')) }}">{{ $fiscalYear }} BoardList</a>
    @endif
    @if($coordinator)
        <a class="dropdown-item" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.coordinatorlistLink')) }}">CoordinatorList</a>
    @endif
    <a class="dropdown-item" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.publicannouncementslink')) }}">Public Announcements</a>
</div>
