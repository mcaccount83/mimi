<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition || $eoyReportCondition || $eoyTestCondition ||$ITCondition)
        <a class="dropdown-item" href="{{ route('eoyreports.eoystatus') }}">EOY Status Report</a>
        <a class="dropdown-item" href="{{ route('eoyreports.eoyboardreport') }}">Board Election Reports</a>
        <a class="dropdown-item" href="{{ route('eoyreports.eoyfinancialreport') }}">Financial Reports</a>
        <a class="dropdown-item" href="{{ route('eoyreports.eoyattachments') }}">Financial Report Attachments</a>
        <a class="dropdown-item" href="{{ route('eoyreports.eoyirssubmission') }}">990N Filing Report</a>
        <a class="dropdown-item" href="{{ route('eoyreports.eoyboundaries') }}">Boundary Issues Report</a>
        <a class="dropdown-item" href="{{ route('eoyreports.eoyawards') }}">Chapter Awards Report</a>
     @elseif ($einCoordinator)
        <a class="dropdown-item" href="{{ route('chapters.eoyirssubmission', ['check5' => 'yes']) }}">International 990N Filing Report</a>
    @endif
</div>
