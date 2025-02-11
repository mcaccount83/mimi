<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition)
        <a class="dropdown-item" href="{{ route('chapters.chaplist') }}">Active Chapter List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapzapped') }}">Zapped Chapter List</a>
    @endif
    @if (($inquiriesCondition) || ($regionalCoordinatorCondition) || ($adminReportCondition))
        <a class="dropdown-item" href="{{ route('chapters.chapinquiries') }}">Inquiries Active Chapter List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapinquirieszapped') }}">Inquiries Zapped Chapter List</a>
    @endif
    @if (($einCondition) || ($adminReportCondition))
        <a class="dropdown-item" href="{{ route('international.intchapter') }}">International Active Chapter List</a>
        <a class="dropdown-item" href="{{ route('international.intchapterzapped') }}">International Zapped Chapter List</a>
    @endif
</div>
