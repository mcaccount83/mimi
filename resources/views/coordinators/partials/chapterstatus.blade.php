    <div class="text-center">
        @if ($chDetails->active_status == 1 )
            <span class="badge bg-success badge-inherit-size fw-bold">Chapter is ACTIVE</span>
            @if ($chDetails->status_id != \App\Enums\OperatingStatusEnum::OK)
                <br>{{ $chapterStatus }}
                {{-- @if ($chDetails->probation_id != \App\Enums\ProbationReasonEnum::OTHER)
                    <br>{{$probationReason}}
                @endif --}}
            @endif
        @elseif ($chDetails->active_status == 2)
            <span class="badge bg-warning text-dark badge-inherit-size fw-bold">Chapter is PENDING</span><br>
            Application Date: @formatDate($chDetails->created_at)<br>
        @elseif ($chDetails->active_status == 3)
            <span class="badge bg-warning text-dark badge-inherit-size fw-bold">Chapter was NOT APPROVED</span><br>
            Declined Date: @formatDate($chDetails->zap_date)<br>
            {{ $chDetails->disband_reason }}
        @elseif ($chDetails->active_status == 0)
            <span class="badge bg-danger badge-inherit-size fw-bold">Chapter is NOT ACTIVE</span><br>
            Disband Date: @formatDate($chDetails->zap_date)<br>
            {{ $chDetails->disband_reason }}
        @endif
        <br>
    </div>
