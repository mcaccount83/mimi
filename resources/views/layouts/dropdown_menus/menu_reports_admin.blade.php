<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="{{ route('adminreports.downloads') }}">Export Reports</a>
    @if ($ITCondition)
        <a class="dropdown-item" href="{{ route('adminreports.conferencelist') }}">Conference List</a>
        <a class="dropdown-item" href="{{ route('adminreports.regionlist') }}">Region List</a>
        <a class="dropdown-item" href="{{ route('adminreports.statelist') }}">State List</a>
    @endif
</div>
