<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
     @if ($coordinatorCondition && $conferenceCoordinatorCondition || $ITCondition)
        <a class="dropdown-item" href="{{ route('chapreports.chaprptchapterstatus') }}">Chapter Status Report</a>
        <a class="dropdown-item" href="{{ route('chapreports.chaprpteinstatus') }}">IRS Status Report</a>
        <a class="dropdown-item" href="{{ route('chapreports.chaprptnewchapters') }}">New Chapter Report</a>
        <a class="dropdown-item" href="{{ route('chapreports.chaprptlargechapters') }}">Large Chapter Report</a>
        <a class="dropdown-item" href="{{ route('chapreports.chaprptprobation') }}">Chapter Probation Report</a>
        <a class="dropdown-item" href="{{ route('chapreports.chaprptcoordinators') }}">Chapter Coordinators Report</a>
    @elseif ($einCondition)
        <a class="dropdown-item" href="{{ route('chapreports.chaprpteinstatus', ['check5' => 'yes']) }}">International IRS Status Report</a>
    @endif
</div>
