<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition)
        <a class="dropdown-item" href="{{ route('chapters.chapreregistration') }}">Re-Registration Payments</a>
        <a class="dropdown-item" href="{{ route('chapreports.chaprptdonations') }}">M2M & Sustaining Donations</a>
    @endif
    @if ($m2mCondition || $adminReportCondition)
        <a class="dropdown-item" href="{{ route('international.intdonation') }}">International M2M & Sustaining Donations</a>
    @endif
</div>
