@component('mail::message')
<h1><center>{{$mailData['reportYearRange']}} End of Year Reports</center></h1>
<br>
<p>As an outgoing board, your last responsibility is to complete the chapter's {{$mailData['reportYearRange']}}
    End of Year Reports. All reports are available through your MIMI account (<a href="https://momsclub.org/mimi">https://momsclub.org/mimi</a>).
</p>
<p><center>{{$mailData['boardReportName']}} - Due June 30th<br>
    {{$mailData['financialReportName']}} - Due July 15th<br>
    {{$mailData['irsFilingName']}} - Available July 1st<br>
</center></p>
<br>
<p><b><u>{{$mailData['boardReportName']}}</u></b><br>
    The Board Election Report should be completed as soon as your chapter has held its elections and should include the information about your newly
    elected board. This will ensure they have access to all the tools they need to be successful in the upcoming year. Once submited, your report will be
    activated after July 1st and new board members will have full MIMI Access. Outgoing board members will have access to Financial Reports Only.</p>
<p><b><u>{{$mailData['financialReportName']}}</u></b><br>
    Any board member of your chapter may fill out the report. We recommend that the Treasurer and President work together
    but any board member may complete it. All the information needed to complete it should be found in your financial
    records, newsletters, and meeting minutes.</p>
<p><b><u>{{$mailData['irsFilingName']}}</u></b><br>
    990N cannot be filed with the IRS before July 1st. All chapters should file their 990N directly with the IRS and not through a third party.
    The IRS does not charge a fee for 990N filings.</p>
<p><b><u>Some other important things to remember:</u></b><br>
    The fiscal year you are reporting for is <b>July 1, {{$mailData['reportYearStart']}} - June 30, {{$mailData['reportYearEnd']}}</b>. The 990N tax filing
    associated with the fiscal year is <b>{{$mailData['reportYearEnd']}}</b>.</p>
<p>If you need help or extra time for ANY reason, contact your Primary Coordinator BEFORE July 15th. A chapter may be put
    on probation for a late report, and a late report may put your chapter at risk of losing its non-profit status for the
    year. The report is very easy to complete, so please make sure you send it in on time!</p>
<p> Be sure to save a copy of you report in your chapter’s permanent files. The International MOMS Club does not keep copies of your reports long term.
    You need to be sure your chapter has a copy and keeps it for the life of your chapter, as this would be the information you would need if the IRS
    were to do an audit.</p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
