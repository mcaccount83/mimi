 <div class="row">
    <div class="col-auto"><label>New Board Submitted:</label></div>
    <div class="col text-end">
        {{ $chEOYDocuments->new_board_submitted == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto"><label>New Board Activated:</label></div>
    <div class="col text-end">
        {{ $chEOYDocuments->new_board_active == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto"><label>Financial Report Received</label></div>
    <div class="col text-end">
        {{ $chEOYDocuments->financial_report_received == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto"><label>Financial Review Complete:</label></div>
    <div class="col text-end">
        {{ $chEOYDocuments->financial_review_complete == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto"><label>Report Extension Given/Unsubmitted:</label></div>
    <div class="col text-end">
        {{ $chEOYDocuments->report_extension == 1 ? 'YES' : 'NO' }}
    </div>
</div>
<div class="row">
    <div class="col-auto"><label>990N Verifed on irs.gov:</label></div>
    <div class="col text-end">
        {{ $chIRSDocuments->irs_verified == 1 ? 'YES' : 'NO' }}
    </div>
</div>
