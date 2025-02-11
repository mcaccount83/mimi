<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition)
        <a class="dropdown-item" href="{{ route('coordinators.coordlist') }}">Active Coordinator List</a>
        <a class="dropdown-item" href="{{ route('coordinators.coordretired') }}">Retired Coordinator List</a>
    @endif
    @if ($adminReportCondition)
        <a class="dropdown-item" href="{{ route('international.intcoord') }}">International Active Coordinator List</a>
        <a class="dropdown-item" href="{{ route('international.intcoordretired') }}">International Retired Coordinator List</a>
    @endif
</div>
