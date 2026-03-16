    <div class="text-center">
        @if ($chDetails->active_status == 1 )
            <span class="badge bg-success fs-7">Chapter is ACTIVE</span>
            {{ $chapterStatus }}
        @elseif ($chDetails->active_status == 2)
            <span class="badge bg-warning text-dark fs-7">Chapter is PENDING</span><br>
            Application Date: @formatDate($chDetails->created_at)<br>
        @elseif ($chDetails->active_status == 3)
            <span class="badge bg-warning text-dark fs-7">Chapter was NOT APPROVED</span><br>
            Declined Date: @formatDate($chDetails->zap_date)<br>
            {{ $chDetails->disband_reason }}
        @elseif ($chDetails->active_status == 0)
            <span class="badge bg-danger fs-7">Chapter is NOT ACTIVE</span><br>
            Disband Date: <@formatDate($chDetails->zap_date)<br>
            {{ $chDetails->disband_reason }}
        @endif
        <br>
    </div>
