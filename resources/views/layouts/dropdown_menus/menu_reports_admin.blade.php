<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition && $conferenceCoordinatorCondition)
        <a class="dropdown-item" href="{{ url(config('sentemails.routepath')) }}">Sent Mail Log</a>
        <a class="dropdown-item" href="{{ route('adminreports.emailcampaigns') }}">Email Campaigns</a>
        {{-- <a class="dropdown-item" href="{{ route('adminreports.maillog') }}">Mail Log</a> --}}
        <a class="dropdown-item" href="{{ route('adminreports.paymentlog') }}">Payment Log</a>
        <a class="dropdown-item" href="{{ route('adminreports.rereg') }}">Re-Registration Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.inquiriesnotify') }}">Inquiries Notifications</a>
        <a class="dropdown-item" href="{{ route('adminreports.inquiriesmap') }}">Inquiries Map Links</a>
        <a class="dropdown-item" href="{{ route('adminreports.downloads') }}">Export Reports</a>
    @elseif ($ITCondition)
        <a class="dropdown-item" href="{{ route('adminreports.paymentlog', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}">International Payments Log</a>
        <a class="dropdown-item" href="{{ route('adminreports.rereg', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}">International Re-Registration Report</a>
        <a class="dropdown-item" href="{{ route('adminreports.inquiriesnotify', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}">International Inquiries Notifications</a>
        <a class="dropdown-item" href="{{ route('adminreports.inquiriesmap', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}">International Inquiries Map Links</a>
    @endif
</div>
