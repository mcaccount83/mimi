<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="{{ route('forum.chaptersubscriptionlist') }}">Chapter Subscription List</a>
    @if ($supervisingCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('forum.coordinatorsubscriptionlist') }}">Coordinator Subscription List</a>
    @endif
    @if (($listAdminCondition) || ($adminReportCondition))
        <a class="dropdown-item" href="{{ route('forum.internationalchaptersubscriptionlist') }}">International Chapter Subscription List</a>
        <a class="dropdown-item" href="{{ route('forum.internationalcoordinatorsubscriptionlist') }}">International Coordinator Subscription List</a>
    @endif
</div>
