<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="{{ route('chapters.chaplistpending') }}">
            Pending Chapter List
            @if($pendingNewChapterCount > 0)
                <span class="badge bg-danger ms-2">{{ $pendingNewChapterCount }} Pending</span>
            @endif
        </a>
        <a class="dropdown-item" href="{{ route('coordinators.coordpending') }}">
            Pending Coordinator List
            @if($pendingNewCoordCount > 0)
                <span class="badge bg-danger ms-2">{{ $pendingNewCoordCount }} Pending</span>
            @endif
        </a>
        <a class="dropdown-item" href="{{ route('chapters.chaplistdeclined') }}">Not Approved Chapter List</a>
        <a class="dropdown-item" href="{{ route('coordinators.coordrejected') }}">Not Approved Coordinator List</a>
</div>
