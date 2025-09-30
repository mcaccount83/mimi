<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="{{ route('resources.toolkit') }}">Coordinator Toolkit</a>
    <a class="dropdown-item" href="{{ url(config('forum.frontend.router.prefix') . '/c/2-coordinatorlist') }}" target="_blank">CoordinatorList</a>
    <a class="dropdown-item" href="{{ route('resources.resources') }}">Chapter Resources</a>
    @if ($assistConferenceCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('resources.downloads') }}">Download Reports</a>
    @endif
    @if ($regionalCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('resources.bugs') }}">MIMI Bugs & Wishes</a>
    @endif
    <a class="dropdown-item" href="{{ route('resources.elearning') }}">eLearning Library</a>
    {{-- <a class="dropdown-item" href="https://momsclub.org/elearning/" target="_blank">eLearning Library</a> --}}
</div>
