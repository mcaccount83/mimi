<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($supervisingCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('coordreports.coordrptvolutilization') }}">Coordinator Utilization Report</a>
    @endif
    @if ($assistConferenceCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('coordreports.coordrptappreciation') }}">Coordinator Appreciation Report</a>
        <a class="dropdown-item" href="{{ route('coordreports.coordrptbirthdays') }}">Coordinator Birthday Report</a>
    @endif
    <a class="dropdown-item" href="{{ route('coordreports.coordrptreportingtree') }}">Coordinator Reporting Tree</a>
    @if ($userAdmin)
        <a class="dropdown-item" href="{{ route('coordreports.intcoordrptreportingtree') }}">International Coordinator Reporting Tree</a>
    @endif
</div>
