 <div class="row">
    <div class="col-auto fw-bold">New Board Submitted:</div>
    <div class="col text-end">
        {{ $chEOYDocuments->new_board_submitted == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto fw-bold">New Board Activated:</div>
    <div class="col text-end">
        {{ $chEOYDocuments->new_board_active == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto fw-bold">Financial Report Received</div>
    <div class="col text-end">
        {{ $chEOYDocuments->financial_report_received == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto fw-bold">Financial Review Complete:</div>
    <div class="col text-end">
        {{ $chEOYDocuments->financial_review_complete == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto fw-bold">Report Extension Given:</div>
    <div class="col text-end">
        {{ $chEOYDocuments->report_extension == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto fw-bold">990N Verifed on irs.gov:</div>
    <div class="col text-end">
        {{ $chEOYDocuments->irs_verified == 1 ? 'YES' : 'NO' }}
    </div>
</div>
