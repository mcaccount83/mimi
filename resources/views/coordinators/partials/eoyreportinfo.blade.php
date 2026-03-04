 <div class="row">
    <div class="col-auto fw-bold">New Board Submitted:</div>
    <div class="col text-end">
        {{ $chDetails->documentsEOY->new_board_submitted == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto fw-bold">New Board Activated:</div>
    <div class="col text-end">
        {{ $chDetails->documentsEOY->new_board_active == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto fw-bold">Financial Report Received</div>
    <div class="col text-end">
        {{ $chDetails->documentsEOY->financial_report_received == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto fw-bold">Financial Review Complete:</div>
    <div class="col text-end">
        {{ $chDetails->documentsEOY->financial_review_complete == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto fw-bold">Report Extension Given:</div>
    <div class="col text-end">
        {{ $chDetails->documentsEOY->report_extension == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto fw-bold">990N Verifed on irs.gov:</div>
    <div class="col text-end">
        {{ $chDetails->documentsEOY->irs_verified == 1 ? 'YES' : 'NO' }}
    </div>
</div>
