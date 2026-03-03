 <div class="text-center">
    @if ($cdDetails->active_status == 1 && $cdDetails->on_leave == 1)
        <span class="badge bg-warning text-dark fs-7">Coordinator is ON LEAVE</span><br>
        Leave Date: <span class="date-mask">{{ $cdDetails->leave_date }}</span>
    @else
        @if ($cdDetails->active_status == 1 && $cdDetails->on_leave != 1)
            <span class="badge bg-success fs-7">Coordinator is ACTIVE</span>
        @elseif ($cdDetails->active_status == 2)
            <span class="badge bg-warning text-dark fs-7">Coordinator is PENDING</span>
        @elseif ($cdDetails->active_status == 3)
            <span class="badge bg-warning text-dark fs-7">Coordinator was NOT APPROVED</span><br>
            Rejected Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
            {{ $cdDetails->reason_retired }}
        @elseif ($cdDetails->active_status == 0)
            <span class="badge bg-warning text-dark fs-7">Coordinator is RETIRED</span><br>
            Retired Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
            {{ $cdDetails->reason_retired }}
        @endif
    @endif
    <br>
 </div>
