 <div class="row">
    <div class="col-auto fw-bold">Assigned Reviewer:</div>
    <div class="col text-end">
            @if($chFinancialReport->reviewer_id != null)
            {{ $chDetails->reportReviewer->first_name }} {{ $chDetails->reportReviewer->last_name }}
            @else
                No Reviewer Assigned
            @endif
    </div>
</div>
