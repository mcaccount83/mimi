<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="{{ route('admin.toolkit') }}">Coordinator Toolkit</a>
    {{-- <a class="dropdown-item" href="{{ url(config('forum.frontend.router.prefix') . '/c/2-vollist') }}" target="_blank">Vollist - NEW!</a> --}}
    <a class="dropdown-item" href="{{ route('admin.resources') }}">Chapter Resources</a>
    @if ($assistConferenceCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('admin.downloads') }}">Download Reports</a>
    @endif
    @if ($regionalCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('admin.bugs') }}">MIMI Bugs & Wishes</a>
    @endif
    <a class="dropdown-item" href="https://momsclub.org/elearning/" target="_blank">eLearning</a>
</div>
