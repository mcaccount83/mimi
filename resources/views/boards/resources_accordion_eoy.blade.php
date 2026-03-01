<h4><u>Read carefully before starting!</u></h4>
All chapters must complete the {{ $lastYear }} - {{ $currentYear }} End of Year Reports.  All reports are available through your chapter's MIMI profile login.<br>
<br>
@if($displayLIVE == false)
    <table>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td><span class="text-danger">EOY Reports are not available at this time.</span></td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td>{{ $boardReportName }} - Available May 1st</li></td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td>{{ $financialReportName }} - Available June 1st</li></td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td>{{ $irsFilingName }} - Available July 1st</li></td>
        </tr>
    </table>
@endif
@if($displayLIVE == true)
    <table>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td>{{ $boardReportName }} - Due June 30th</li></td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td>{{ $financialReportName }} - Due July 15th</li></td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td>{{ $irsFilingName }} - Due after July 1st</li></td>
        </tr>
    </table>
    <br>
    <strong><u>Board Report</u></strong><br>
    @if ($displayBoardRptLIVE == false)
        <span class="text-danger">Board Report is not available at this time.  After September 30th, board members can make edits to their information from the chapter's MIMI profile.</span><br>
    @else
        This report should be filled out as soon as your chapter has held its election but is due no later than June 30th.<br>
    @endif
    <br>
    <strong><u>Financial Report</u></strong><br>
    @if ($displayFinancialRptLIVE == false)
        <span class="text-danger">Financial Reports are not available at this time.  They will be availabe on June 1st.</span><br>
    @else
        When you have filled in all the answers, submit the report and save a copy in your chapter's permanent files. The International MOMS Club does not keep copies of your reports long term. You need to be sure your chapter has a copy and keeps it for the life of your chapter, as this would be the information you would need if the IRS were to do an audit. The Financial Report and all required additional documents are due no later than July 15th. <strong>NEW CHAPTERS</strong> who have not started meeting prior to June 30th, do NOT need to fill out this report!<br>
        <br>
        <table>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td><a href="https://momsclub.org/elearning/courses/annual-financial-report-bank-reconciliation/" target="_blank">Step-by-Step Guide to Bank Reconciliation.</a></td>
            </tr>
        </table>
    @endif
    <br>
    <strong><u>990N IRS Filing</u></strong><br>
    @if($displayEINInstructionsLIVE == false)
        <span class="text-danger">990N Filing cannot be done before July 1st. Links and instructions will be available at that time.</span><br>
    @else
        990N cannot be filed before July 1st.  All chapters should file their 990N directly with the IRS and not through a third party. <i>The IRS does not charge a fee for 990N filings.</i>  If you notice the dates on your filing are NOT July-June, do not continue filing, <strong>STOP</strong> and contact your coordinator team.<br>
        <br>
        <table>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td><a href="https://www.irs.gov/charities-non-profits/annual-electronic-filing-requirement-for-small-exempt-organizations-form-990-n-e-postcard" target="_blank">990N IRS Website Link to File</a></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;</td>
                @foreach($resources as $resourceItem)
                @if ($resourceItem->name == '990N Filing Instructions')
                    <td>
                        <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                            990N Filing Instructions
                        </a>
                    </td>
                @endif
                @endforeach
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;</td>
                @foreach($resources as $resourceItem)
                @if ($resourceItem->name == '990N Filing FAQs')
                    <td>
                        <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                            990N Filing FAQs
                        </a>
                    </td>
                @endif
                @endforeach
            </tr>
        </table>
    @endif
    <br>
    <strong><u>Important things to remember</u></strong><br>
    The fiscal year you are reporting for is <strong>July 1, {{ $lastYear }} - June 30, {{ $currentYear }}</strong>.<br>
    The 990N tax filing associated with the fiscal year is <strong>{{ $lastYear }}.</strong><br>
    <br>
    Any board member of your chapter may fill out the report. We recommend that the Treasurer and President work together but any board member may complete it. All the information needed to complete it should be found in your financial records, newsletters, and meeting minutes.<br>
    <br>
    If you need help or extra time for ANY reason, contact your Primary Coordinator BEFORE July 15th. A chapter may be put on probation for a late report, and a late report may put your chapter at risk of losing its non-profit status for the year. The report is very easy to complete, so please make sure you send it in on time!<br>
    <br>
    <br>
</div>
@endif
