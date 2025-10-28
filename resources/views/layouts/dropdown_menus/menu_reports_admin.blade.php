<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition && $conferenceCoordinatorCondition)
        <a class="dropdown-item" href="{{ route('adminreports.paymentlist') }}">Payment Log Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.reregdate') }}">Re-Registration Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.downloads') }}">Export All Reports</a>
        {{-- <a class="dropdown-item" href="{{ route('adminreports.bugs') }}">MIMI Bugs Report</a> --}}
    @elseif ($ITCondition)
        <a class="dropdown-item" href="{{ route('adminreports.paymentlist', ['check5' => 'yes']) }}">International Payments Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.reregdate', ['check5' => 'yes']) }}">International Re-Registration Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.downloads') }}">Export International Reports</a>
    @endif
</div>
