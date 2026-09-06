<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($inquiriesCondition || ($coordinatorCondition && $conferenceCoordinatorCondition))
        <a class="dropdown-item" href="{{ route('inquiries.inquiryapplication') }}">
            Inquiries Received List
            @if($pendingInquiryCount > 0)
                <span class="badge bg-danger badge-pill notification-badge ms-2">{{ $pendingInquiryCount }} Pending</span>
            @endif
        </a>
        <a class="dropdown-item" href="{{ route('chapters.chapinquiries', [\App\Enums\CheckboxFilterEnum::CONFERENCE_REGION => 'yes']) }}">Inquiries Active Chapter List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapinquirieszapped', [\App\Enums\CheckboxFilterEnum::CONFERENCE_REGION => 'yes']) }}">Inquiries Zapped Chapter List</a>
    @elseif ($inquiriesInternationalCondition || $ITCondition)
        <a class="dropdown-item" href="{{ route('inquiries.inquiryapplication', [\App\Enums\CheckboxFilterEnum::INTERNATIONALINQUIRIES => 'yes']) }}">Inquiries Received List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapinquiries', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}">Inquiries Active Chapter List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapinquirieszapped', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}">Inquiries Zapped Chapter List</a>
    @endif
</div>
