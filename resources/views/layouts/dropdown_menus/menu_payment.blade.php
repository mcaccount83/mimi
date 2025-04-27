<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition)
        <a class="dropdown-item" href="{{ route('chapters.chapreregistration') }}">Re-Registration Payments</a>
        <a class="dropdown-item" href="{{ route('chapreports.chaprptdonations') }}">M2M & Sustaining Donations</a>
    @endif
    @if ($userAdmin)
        <a class="dropdown-item" href="{{ route('international.intregistration') }}">International Re-Registration Payments</a>
    @endif
    @if ($m2mCondition || $userAdmin)
        <a class="dropdown-item" href="{{ route('international.intdonation') }}">International M2M & Sustaining Donations</a>
    @endif
</div>
