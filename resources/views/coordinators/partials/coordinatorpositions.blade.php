<div class="row">
    <div class="col-auto fw-bold">Supervising Coordinator:</div>
    <div class="col text-end">
        <a href="mailto:{{ $cdDetails->reportsTo?->email }}">{{ $ReportTo }} </a>
    </div>
    </div>
    <div class="row">
    <div class="col-auto fw-bold">Primary Position:</div>
    <div class="col text-end">
        {{ $displayPosition->long_title }}
    </div>
    </div>
<div class="row">
    <div class="col-auto fw-bold">MIMI Position: <a href="javascript:void(0);" onclick="showPositionInformation()" title="Show Position Information">
    <i class="bi bi-question-circle text-primary"></i></a></div>
    <div class="col text-end">{{ $mimiPosition?->long_title }}</span>
</div>
    </div>
    <div class="row">
    <div class="col-auto fw-bold">Secondary Positions:</div>
    <div class="col text-end">
        @forelse($cdDetails->secondaryPosition as $position)
            {{ $position->long_title }}@if(!$loop->last)<br>@endif
        @empty
            None
        @endforelse
    </div>
    </div>
    @if ($ITCondition)
<div class="row">
    <div class="col-auto fw-bold">MIMI Admin:</div>
    <div class="col text-end">
        {{ $cdAdminRole->admin_role }}
        </div>
    </div>
@endif
