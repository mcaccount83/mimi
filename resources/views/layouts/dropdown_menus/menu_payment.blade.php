<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition || ($coordinatorCondition && $conferenceCoordinatorCondition))
        <a class="dropdown-item" href="{{ route('payment.chapreregistration') }}">Re-Registration Payments</a>
        <a class="dropdown-item" href="{{ route('payment.chapdonations') }}">M2M & Sustaining Donations</a>
        @if ($coordinatorCondition && $conferenceCoordinatorCondition)
            <a class="dropdown-item" href="{{ route('payment.grantlist') }}">Grant Requests</a>
        @endif
    {{-- @elseif ($coordinatorCondition)
        <a class="dropdown-item" href="{{ route('payment.chapreregistration') }}">Re-Registration Payments</a>
        <a class="dropdown-item" href="{{ route('payment.chapdonations') }}">M2M & Sustaining Donations</a> --}}
    @elseif ($m2mCondition || $ITCondition)
        <a class="dropdown-item" href="{{ route('payment.chapreregistration', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}">International Re-Registration Payments</a>
        <a class="dropdown-item" href="{{ route('payment.chapdonations', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}">International M2M & Sustaining Donations</a>
        <a class="dropdown-item" href="{{ route('payment.grantlist', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}">International Grant Requests Report</a>
    @endif
</div>
