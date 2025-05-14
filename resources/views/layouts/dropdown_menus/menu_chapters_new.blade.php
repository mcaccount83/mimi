<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="{{ route('chapters.chaplistpending') }}">Pending Chapter List</a>
        <a class="dropdown-item" href="{{ route('chapters.chaplistdeclined') }}">Not Approved Chapter List</a>
    @if (($userAdmin))
        <a class="dropdown-item" href="{{ route('international.intchaplistpending') }}">International Pending Chapter List</a>
        <a class="dropdown-item" href="{{ route('international.intchaplistdeclined') }}">International Not Approved Chapter List</a>
    @endif
</div>
