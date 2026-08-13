    <div class="row">
        <div class="col-auto"><label>Re-Registration Payment:</label></div>
        <div class="col text-end">
            @if ($chPayments->rereg_members)
                <b>{{ $chPayments->rereg_members }} Members</b> on <b>@formatDate($chPayments->rereg_date)</b>
            @else
                No Payment Recorded
            @endif
        </div>
    </div>
    @if ($chDetails->active_status == 1 )
    <div class="row">
        <div class="col-auto"><label>Re-Registration Dues:</label></div>
        <div class="col text-end">
            @if ($currentDate->gte($dueDate))
                @if ($chDetails->start_month_id == $currentMonth)
                    <span class="badge bg-success badge-inherit-size fw-bold">Due Now (@formatDate($renewalDate))</span>
                @else
                    <span class="badge bg-danger badge-inherit-size fw-bold">Overdue (@formatDate($renewalDate))</span>
                @endif
            @else
                Next Due on @formatDate($renewalDate)
            @endif
        </div>
    </div>
    @endif
