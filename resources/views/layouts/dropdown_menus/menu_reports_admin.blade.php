<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition && $conferenceCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('adminreports.paymentlist') }}">Payment Log Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.reregdate') }}">Re-Registration Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.inquiriesnotify') }}">inquiries Notifications</a>
        {{-- <a class="dropdown-item" href="{{ route('adminreports.bugs') }}">MIMI Bugs Report</a> --}}
    @elseif ($ITCondition)
        <a class="dropdown-item" href="{{ route('adminreports.paymentlist', ['check5' => 'yes']) }}">International Payments Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.reregdate', ['check5' => 'yes']) }}">International Re-Registration Report</a>
    @endif
    @if ($ITCondition)
        <a class="dropdown-item" href="{{ route('adminreports.conferencelist') }}">Conference List</a>
        <a class="dropdown-item" href="{{ route('adminreports.regionlist') }}">Region List</a>
        <a class="dropdown-item" href="{{ route('adminreports.statelist') }}">State List</a>
    @endif
        <a class="dropdown-item" href="{{ route('adminreports.downloads') }}">Export Reports</a>
</div>
