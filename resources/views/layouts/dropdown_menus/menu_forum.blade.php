<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
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
