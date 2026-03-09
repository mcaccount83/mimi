<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if(($listAdminCondition) || ($ITCondition))
    <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ url(config('forum.frontend.router.prefix') . '/pending-approval/threads') }}">
        Pending Threads
        @if($pendingThreadsCount > 0)
            <span class="badge bg-danger ms-2">{{ $pendingThreadsCount }} Pending</span>
        @endif
    </a>
    <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{ url(config('forum.frontend.router.prefix') . '/pending-approval/posts') }}">
        Pending Posts
        @if($pendingPostsCount > 0)
            <span class="badge bg-danger ms-2">{{ $pendingPostsCount }} Pending</span>
        @endif
    </a>
    @endif
    @if (($coordinatorCondition) || ($ITCondition))
        <a class="dropdown-item" href="{{ route('forum.chaptersubscriptionlist') }}">Chapter Subscription List</a>
    @elseif (($listAdminCondition) || ($ITCondition))
        <a class="dropdown-item" href="{{ route('forum.chaptersubscriptionlist', ['check5' => 'yes']) }}">International Chapter Subscription List</a>
    @endif
    @if ($supervisingCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('forum.coordinatorsubscriptionlist') }}">Coordinator Subscription List</a>
    @elseif (($listAdminCondition) || ($ITCondition))
        <a class="dropdown-item" href="{{ route('forum.coordinatorsubscriptionlist', ['check5' => 'yes']) }}">International Coordinator Subscription List</a>
    @endif
</div>


