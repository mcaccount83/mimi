<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="{{ route('chapters.chaplistpending') }}">Pending Chapter List</a>
        <a class="dropdown-item" href="{{ route('chapters.chaplistdeclined') }}">Not Approved Chapter List</a>
        <a class="dropdown-item" href="{{ route('coordinators.coordpending') }}">Pending Coordinator List</a>
        <a class="dropdown-item" href="{{ route('coordinators.coordrejected') }}">Not Approved Coordinator List</a>
    @if (($userAdmin))
        <a class="dropdown-item" href="{{ route('international.intchaplistpending') }}">International Pending Chapter List</a>
        <a class="dropdown-item" href="{{ route('international.intchaplistdeclined') }}">International Not Approved Chapter List</a>
        <a class="dropdown-item" href="{{ route('international.intcoordpending') }}">International Pending Coordinator List</a>
        <a class="dropdown-item" href="{{ route('international.intcoordrejected') }}">International Not Approved Coordinator List</a>
    @endif
</div>
