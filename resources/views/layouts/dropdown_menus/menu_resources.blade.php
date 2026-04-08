<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="{{ route('resources.toolkit') }}">Coordinator Toolkit</a>
    <a class="dropdown-item" href="{{ route('resources.resources') }}">Chapter Resources</a>
    <a class="dropdown-item" href="{{ route('resources.elearning') }}">eLearning Library</a>
    @if ($ITCondition)
        <a class="dropdown-item" href="{{ route('resources.awards') }}">Award Badges</a>
    @endif
</div>
