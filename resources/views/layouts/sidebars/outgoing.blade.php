@php
    $isOutgoing = auth()->user()->type_id == \App\Enums\UserTypeEnum::OUTGOING;
    $isDisbanded = auth()->user()->type_id == \App\Enums\UserTypeEnum::DISBANDED;
@endphp

<!-- Disband Checklist - DISBANDED only -->
@if($isDisbanded && isset($chDetails))
<li class="nav-item">
    <a href="{{ route('board.editdisbandchecklist', ['id' => $chDetails->id]) }}"
       class="nav-link {{ $positionService->isActiveRoute(['board/disbandchecklist/*']) }}">
        <i class="nav-icon bi bi-list-check"></i>
        <p>Disband Checklist</p>
    </a>
</li>
@endif

<!-- Financial Report - OUTGOING and DISBANDED -->
@if(($isOutgoing || $isDisbanded) && isset($chDetails))
<li class="nav-item">
    <a href="{{ route('board.editfinancialreportfinal', ['id' => $chDetails->id]) }}"
       class="nav-link {{ $positionService->isActiveRoute(['board/financialreportfinal/*']) }}">
        <i class="nav-icon bi bi-file-earmark-bar-graph"></i>
        <p>Financial Report</p>
    </a>
</li>
@endif

<!-- Re-Registration - DISBANDED only -->
@if($isDisbanded && isset($chDetails))
<li class="nav-item">
    <a href="{{ route('board.editreregpayment', ['id' => $chDetails->id]) }}"
       class="nav-link {{ $positionService->isActiveRoute(['board/reregpayment/*']) }}">
        <i class="nav-icon bi bi-credit-card-fill"></i>
        <p>Re-Registration</p>
    </a>
</li>

<!-- Donations - DISBANDED only -->
<li class="nav-item">
    <a href="{{ route('board.editdonate', ['id' => $chDetails->id]) }}"
       class="nav-link {{ $positionService->isActiveRoute(['board/donation/*']) }}">
        <i class="nav-icon bi bi-currency-dollar"></i>
        <p>Donations</p>
    </a>
</li>

<!-- Documents - DISBANDED only -->
<li class="nav-item">
    <a href="{{ route('board.chapterprofile', ['id' => $chDetails->id]) }}"
       class="nav-link {{ $positionService->isActiveRoute(['board/profile/*']) }}">
        <i class="nav-icon bi bi-files"></i>
        <p>Documents</p>
    </a>
</li>
@endif
