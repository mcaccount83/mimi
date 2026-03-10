<div class="col-md-12 text-center">
    <h4><u>Read carefully before starting!</u></h4>
    All chapters must complete the {{ $fiscalYearEOY}} End of Year Reports.  All reports are available through your chapter's MIMI profile login.<br>
    <br>
</div>
@if($displayEOYLIVE == false)
<div class="col-md-12 text-center text-danger">EOY Reports are not available at this time.</div><br>
<div class="col-md-12 text-center">{{ $boardReportName }} - Available May 1st<br>
        {{ $financialReportName }} - Available June 1st<br>
        {{ $irsFilingName }} - Available July 1st<br>
        <br>
    </div>
@endif
@if($displayEOYLIVE == true)
<div class="col-md-12 text-center">{{ $boardReportName }} - Due June 30th<br>
            {{ $financialReportName }} - Due July 15th<br>
            {{ $irsFilingName }} - Due after July 1st<br>
        <br>
    </div>
    <div class="col-md-12 text-center">
        <h5><u><b>Board Report</b></u></h5>
        @if ($displayBoardRptLIVE == false)
            <div class="text-danger">Board Report is not available at this time.  After September 30th, board members can make edits to their information from the chapter's MIMI profile.</div>
        @else
            <div>This report should be filled out as soon as your chapter has held its election but is due no later than June 30th.</div>
        @endif
        <br>
    </div>
    <div class="col-md-12 text-center">
        <h5><u><b>Financial Report</b></u></h5>
    @if ($displayFinancialRptLIVE == false)
        <div class="text-danger">Financial Reports are not available at this time.  They will be availabe on June 1st.</div>
    @else
        <div>When you have filled in all the answers, submit the report and save a copy in your chapter's permanent files. The International MOMS Club does not keep copies of your reports long term.
            You need to be sure your chapter has a copy and keeps it for the life of your chapter, as this would be the information you would need if the IRS were to do an audit. The Financial Report and all required additional documents are due no later than July 15th.
            <b>NEW CHAPTERS</b> who have not started meeting prior to June 30th, do NOT need to fill out this report!<br>
        <br>
            <a href="https://momsclub.org/elearning/courses/annual-financial-report-bank-reconciliation/" target="_blank">Step-by-Step Guide to Bank Reconciliation.</a><br>
    @endif
        <br>
    </div>
    <div class="col-md-12 text-center">
        <h5><u><b>990N IRS Filing</b></u></h5>
        @if($displayEINInstructionsLIVE == false)
            <div class="text-danger">990N Filing cannot be done before July 1st. Links and instructions will be available at that time.</div>
        @else
            <div>990N cannot be filed before July 1st.  All chapters should file their 990N directly with the IRS and not through a third party. <i>The IRS does not charge a fee for 990N filings.</i>  If you notice the dates on your filing are NOT July-June, do not continue filing, <b>STOP</b> and contact your coordinator team.<br>
            <br>
            <a href="https://www.irs.gov/charities-non-profits/annual-electronic-filing-requirement-for-small-exempt-organizations-form-990-n-e-postcard" target="_blank">990N IRS Website Link to File</a><br>
            @foreach($resources as $resourceItem)
            @if ($resourceItem->name == '990N Filing Instructions')
                <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">990N Filing Instructions</a><br>
            @endif
            @endforeach
            @foreach($resources as $resourceItem)
            @if ($resourceItem->name == '990N Filing FAQs')
                <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">990N Filing FAQs</a><br>
            @endif
            @endforeach
            <br>
            </div>
    @endif
    </div>
    <div class="col-md-12 text-center">
        <h5><u><b>Important things to remember</b></u></h5>

    The fiscal year you are reporting for is <strong>July 1, {{ $lastYearEOY }} - June 30, {{ $thisYearEOY }}</strong>.<br>
    The 990N tax filing associated with the fiscal year is <strong>{{ $lastYearEOY }}.</strong><br>
    <br>
    Any board member of your chapter may fill out the report. We recommend that the Treasurer and President work together but any board member may complete it. All the information needed to complete it should be found in your financial records, newsletters, and meeting minutes.<br>
    <br>
    If you need help or extra time for ANY reason, contact your Primary Coordinator BEFORE July 15th. A chapter may be put on probation for a late report, and a late report may put your chapter at risk of losing its non-profit status for the year. The report is very easy to complete, so please make sure you send it in on time!<br>
    <br>
</div>
@endif
