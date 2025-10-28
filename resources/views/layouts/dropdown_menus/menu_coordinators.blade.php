<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($supervisingCoordinatorCondition || $ITCondition)
        <a class="dropdown-item" href="{{ route('coordinators.coordlist') }}">Active Coordinator List</a>
    @endif
    @if (($supervisingCoordinatorCondition && $regionalCoordinatorCondition) || $ITCondition)
        <a class="dropdown-item" href="{{ route('coordinators.coordretired') }}">Retired Coordinator List</a>
    @endif
</div>
