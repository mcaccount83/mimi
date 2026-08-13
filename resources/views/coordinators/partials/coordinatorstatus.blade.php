 <div class="text-center">
    @if ($cdDetails->active_status == 1 && $cdDetails->on_leave == 1)
        <span class="badge bg-warning badge-inherit-size text-dark fw-bold">Coordinator is ON LEAVE</span><br>
        Leave Date: @formatDate($cdDetails->leave_date)
    @else
        @if ($cdDetails->active_status == 1 && $cdDetails->on_leave != 1)
            <span class="badge bg-success badge-inherit-size fw-bold">Coordinator is ACTIVE</span>
        @elseif ($cdDetails->active_status == 2)
            <span class="badge bg-warning badge-inherit-size text-dark fw-bold">Coordinator is PENDING</span>
        @elseif ($cdDetails->active_status == 3)
            <span class="badge bg-warning badge-inherit-size text-dark fw-bold">Coordinator was NOT APPROVED</span><br>
            Rejected Date: @formatDate($cdDetails->zapped_date)<br>
            {{ $cdDetails->reason_retired }}
        @elseif ($cdDetails->active_status == 0)
            <span class="badge bg-warning badge-inherit-size text-dark fw-bold">Coordinator is RETIRED</span><br>
            Retired Date: @formatDate($cdDetails->zapped_date)<br>
            {{ $cdDetails->reason_retired }}
        @endif
    @endif
    <br>
 </div>
