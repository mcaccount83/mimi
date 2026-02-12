<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition && $conferenceCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('payment.chapreregistration') }}">Re-Registration Payments</a>
        <a class="dropdown-item" href="{{ route('payment.chapdonations') }}">M2M & Sustaining Donations</a>
        <a class="dropdown-item" href="{{ route('paymentreports.grantlist') }}">Grant Requests</a>
    @elseif ($coordinatorCondition)
        <a class="dropdown-item" href="{{ route('payment.chapreregistration') }}">Re-Registration Payments</a>
        <a class="dropdown-item" href="{{ route('payment.chapdonations') }}">M2M & Sustaining Donations</a>
    @elseif ($m2mCondition || $ITCondition)
        <a class="dropdown-item" href="{{ route('payment.chapreregistration', ['check5' => 'yes']) }}">International Re-Registration Payments</a>
        <a class="dropdown-item" href="{{ route('payment.chapdonations', ['check5' => 'yes']) }}">International M2M & Sustaining Donations</a>
        <a class="dropdown-item" href="{{ route('paymentreports.grantlist', ['check5' => 'yes']) }}">International Grant Requests Report</a>
    @endif
</div>
