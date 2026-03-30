 @if ($userTypeId == \App\Enums\UserTypeEnum::COORD
            ? ($displayEOYTESTING || $displayEOYLIVE || $ITCondition)
            : $displayEOYLIVE)

   <div class="col-md-10 offset-md-1 text-center mb-2">
        <h4><u>Read carefully before starting!</u></h4>
        <div>All chapters must complete the {{ $reportYearRange}} End of Year Reports.  All reports are available through your chapter's MIMI profile login.</div>
    </div>

    <div class="col-md-12 text-center mb-3">
        <div>
            @if ($displayBoardRptLIVE)
                {{ $boardReportName }} - Due June 30th
            @else
                {{ $boardReportName }} - Available May 1st, Due June 30th
            @endif
        </div>
        <div>
        @if ($displayFinancialRptLIVE)
            {{ $financialReportName }} - Due July 15th
        @else
            {{ $financialReportName }} - Available June 1st, Due July 15th
        @endif
        </div>
        <div>
        @if ($displayEINInstructionsLIVE)
            {{ $irsFilingName }} - Due after July 1st
        @else
            {{ $irsFilingName }} - Available July 1st
        @endif
        </div>
    </div>

        <div class="col-md-10 offset-md-1 mt-1 mb-3">
            <h5><u><b>Board Report</b></u></h5>
            <div>The Board Election Report should be completed as soon as your chapter has held its elections and should include the information about your newly elected board.
                This will ensure they have access to all the tools they need to be successful in the upcoming year. Once submited, your report will be activated after July 1st and new board
                members will have full MIMI Access. Outgoing board members will have access to Financial Reports Only.</div>
        </div>

        <div class="col-md-10 offset-md-1 mt-1 mb-3">
            <h5><u><b>Financial Report</b></u></h5>
            <div>When you have filled in all the answers, submit the report and save a copy in your chapter's permanent files. The International MOMS Club is not responsible for keeping copies of your reports long term.
                You need to be sure your chapter has a copy and keeps it for the life of your chapter, as this would be the information you would need if the IRS were to do an audit.<br></div>
            <div class="mt-2"><b>Note:</b> New chapters who have not started meeting prior to June 30th, do NOT need to fill out this report!<br></div>
            <div class="mt-2">
                <a href="https://momsclub.org/elearning/courses/annual-financial-report-bank-reconciliation/" target="_blank">Step-by-Step Guide to Bank Reconciliation.</a><br>
            </div>
        </div>

        <div class="col-md-10 offset-md-1 mt-1 mb-3">
            <h5><u><b>990N IRS Filing</b></u></h5>
            <div>990N cannot be filed with the IRS before July 1st.  All chapters should file their 990N directly with the IRS and not through a third party. <i>The IRS does not charge a fee for 990N filings.</i>
                If you notice the dates on your filing are NOT July-June, do not continue filing, <b>STOP</b> and contact your coordinator team.<br></div>
            @if($userTypeId == \App\Enums\UserTypeEnum::COORD
                    ? ($displayEOYTESTING || $displayEOYLIVE || $ITCondition)
                    : $displayEINInstructionsLIVE)
                <div class="mt-2">
                    <a href="https://www.irs.gov/charities-non-profits/annual-electronic-filing-requirement-for-small-exempt-organizations-form-990-n-e-postcard" target="_blank">990N IRS Website Link to File</a>
                </div>
                <div>
                    @foreach($resources as $resourceItem)
                    @if ($resourceItem->name == '990N Filing Instructions')
                        <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">990N Filing Instructions</a><br>
                    @endif
                    @endforeach
                </div>
                <div>
                    @foreach($resources as $resourceItem)
                    @if ($resourceItem->name == '990N Filing FAQs')
                        <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">990N Filing FAQs</a><br>
                    @endif
                    @endforeach
                </div>
            @else
                <div class="text-danger mt-2">Filing links and instructions will be available on July 1st.</div>
            @endif
        </div>

        <div class="col-md-10 offset-md-1 mt-1 mb-3">
            <h5><u><b>Important things to remember</b></u></h5>
            <div>The fiscal year you are reporting for is <strong>July 1, {{ $reportYearStart }} - June 30, {{ $reportYearEnd }}</strong>.<br>
                The 990N tax filing associated with the fiscal year is <strong>{{ $reportYearStart }}.</strong></div>
            <div class="mt-2">Any board member of your chapter may fill out the report. We recommend that the Treasurer and President work together but any board member may complete it. All the information needed to
                complete it should be found in your financial records, newsletters, and meeting minutes.</div>
            <div class="mt-2">If you need help or extra time for ANY reason, contact your Primary Coordinator BEFORE July 15th. A chapter may be put on probation for a late report, and a late report may put your
                chapter at risk of losing its non-profit status for the year. The report is very easy to complete, so please make sure you send it in on time!</div>
        </div>

@else
    <h4> <span class="col-md-12 text-center text-danger mb-2">EOY Reports are not available at this time.</span></h4>
    <div class="col-md-12 text-center mb-3">
        <div>{{ $boardReportName }} - Available May 1st, Due June 30th</div>
        <div>{{ $financialReportName }} - Available June 1st, Due July 15th</div>
        <div>{{ $irsFilingName }} - Available July 1st</div>
    </div>
@endif
