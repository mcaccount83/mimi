<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition && $conferenceCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('paymentreports.paymentlog') }}">Payment Log Report</a>
        <a class="dropdown-item" href="{{ route('paymentreports.rereg') }}">Re-Registration Report</a>
        <a class="dropdown-item" href="{{ route('inquiries.inquiriesnotify') }}">Inquiries Notifications</a>
        <a class="dropdown-item" href="{{ route('resources.downloads') }}">Export Reports</a>
    @elseif ($ITCondition)
        <a class="dropdown-item" href="{{ route('paymentreports.paymentlog', ['check5' => 'yes']) }}">International Payments Report</a>
        <a class="dropdown-item" href="{{ route('paymentreports.rereg', ['check5' => 'yes']) }}">International Re-Registration Report</a>
        <a class="dropdown-item" href="{{ route('inquiries.inquiriesnotify', ['check5' => 'yes']) }}">International Inquiries Notifications</a>
    @endif
</div>
