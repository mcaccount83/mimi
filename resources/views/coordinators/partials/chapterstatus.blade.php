    <div class="text-center">
        @if ($chDetails->active_status == 1 )
            <span class="badge bg-success fs-7">Chapter is ACTIVE</span>
            {{ $chapterStatus }}
        @elseif ($chDetails->active_status == 2)
            <span class="badge bg-warning text-dark fs-7">Chapter is PENDING</span>
            Application Date: <span class="date-mask">{{ $chDetails->created_at }}</span><br>
        @elseif ($chDetails->active_status == 3)
            <span class="badge bg-warning text-dark fs-7">Chapter was NOT APPROVED</span><br>
            Declined Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
            {{ $chDetails->disband_reason }}
        @elseif ($chDetails->active_status == 0)
            <span class="badge bg-danger fs-7">Chapter is NOT ACTIVE</span><br>
            Disband Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
            {{ $chDetails->disband_reason }}
        @endif
        <br>
    </div>
