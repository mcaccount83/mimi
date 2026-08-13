 <div class="row">
    <div class="col-auto"><label>Assigned Reviewer:</label></div>
    <div class="col text-end">
            @if($chFinancialReportReview->reviewer_id != null)
            {{ $chDetails->reportReviewer->first_name }} {{ $chDetails->reportReviewer->last_name }}
            @else
                No Reviewer Assigned
            @endif
    </div>
</div>
