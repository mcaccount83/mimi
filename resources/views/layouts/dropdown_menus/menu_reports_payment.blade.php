<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition && $conferenceCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('paymentreports.paymentlog') }}">Payment Log Report</a>
        <a class="dropdown-item" href="{{ route('paymentreports.rereg') }}">Re-Registration Report</a>
        <a class="dropdown-item" href="{{ route('paymentreports.grantlist') }}">Grant Requests Report</a>
        {{-- <a class="dropdown-item" href="{{ route('paymentreports.donationlog') }}">Donation Log Report</a> --}}
    @elseif ($m2mCondition || $ITCondition)
    @elseif ($ITCondition)
        <a class="dropdown-item" href="{{ route('paymentreports.paymentlog', ['check5' => 'yes']) }}">International Payments Report</a>
        <a class="dropdown-item" href="{{ route('paymentreports.rereg', ['check5' => 'yes']) }}">International Re-Registration Report</a>
    @endif
</div>
