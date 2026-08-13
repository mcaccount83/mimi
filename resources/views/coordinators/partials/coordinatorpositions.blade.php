<div class="row">
    <div class="col-auto"><label>Supervising Coordinator:</label></div>
    <div class="col text-end">
        <a href="mailto:{{ $cdDetails->reportsTo?->email }}">{{ $ReportTo }} </a>
    </div>
    </div>
    <div class="row">
    <div class="col-auto"><label>Primary Position:</label></div>
    <div class="col text-end">
        {{ $displayPosition->long_title }}
    </div>
    </div>
<div class="row">
    <div class="col-auto"><label>MIMI Position: <a href="javascript:void(0);" onclick="showPositionInformation()" title="Show Position Information">
    <i class="bi bi-question-circle text-primary"></i></a></label></div>
    <div class="col text-end">{{ $mimiPosition?->long_title }}</span>
</div>
    </div>
    <div class="row">
    <div class="col-auto"><label>Secondary Positions:</label></div>
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
    <div class="col-auto"><label>MIMI Admin:</label></div>
    <div class="col text-end">
        {{ $cdAdminRole->admin_role }}
        </div>
    </div>
@endif
