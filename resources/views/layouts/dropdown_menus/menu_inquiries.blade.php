<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if (($regionalCoordinatorCondition) || ($inquiriesCondition) || ($ITCondition))
        <a class="dropdown-item" href="{{ route('inquiries.inquiryapplication') }}">Inquiries Received List</a>

    @endif
    @if (($inquiriesCondition) || ($regionalCoordinatorCondition))
        <a class="dropdown-item" href="{{ route('chapters.chapinquiries', ['check3' => 'yes']) }}">Inquiries Active Chapter List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapinquirieszapped', ['check3' => 'yes']) }}">Inquiries Zapped Chapter List</a>
    @elseif (($inquiriesInternationalCondition) || ($ITCondition))
        <a class="dropdown-item" href="{{ route('chapters.chapinquiries', ['check5' => 'yes']) }}">Inquiries Active Chapter List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapinquirieszapped', ['check5' => 'yes']) }}">Inquiries Zapped Chapter List</a>
    @endif
</div>

