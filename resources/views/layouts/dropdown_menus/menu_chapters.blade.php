<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition || $ITCondition)
        <a class="dropdown-item" href="{{ route('chapters.chaplist') }}">Active Chapter List</a>
    @endif
    @if (($coordinatorCondition && $regionalCoordinatorCondition) || $ITCondition)
        <a class="dropdown-item" href="{{ route('chapters.chapzapped') }}">Zapped Chapter List</a>
    @endif
</div>
