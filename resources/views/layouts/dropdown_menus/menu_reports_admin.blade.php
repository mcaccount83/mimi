<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition && $conferenceCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('adminreports.paymentlog') }}">Payment Log Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.rereg') }}">Re-Registration Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.inquiriesnotify') }}">Inquiries Notifications</a>
        <a class="dropdown-item" href="{{ route('adminreports.downloads') }}">Export Reports</a>
    @elseif ($ITCondition)
        <a class="dropdown-item" href="{{ route('adminreports.paymentlog', ['check5' => 'yes']) }}">International Payments Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.rereg', ['check5' => 'yes']) }}">International Re-Registration Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.inquiriesnotify', ['check5' => 'yes']) }}">International Inquiries Notifications</a>
    @endif
</div>
