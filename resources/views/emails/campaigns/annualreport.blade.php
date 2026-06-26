@component('mail::message')
<h1><center>{{$mailData['reportYearRange']}} Annual Reports</center></h1>
<br>
<p>As an outgoing board, your last responsibility is to complete the chapter's {{$mailData['reportYearRange']}}
    Annual Reports. The annual reports consist of:
</p>
<ul>
    <li>{{$mailData['boardReportName']}}</li>
    <li>{{$mailData['financialReportName']}}</li>
    <li>{{$mailData['irsFilingName']}}</li>
</ul>
<p>These reports can be accessed by logging into your MIMI account.</p>
<p><b>{{$mailData['boardReportName']}}</b><br>
    This report should be filled out as soon as your chapter has held its election but is due no later than June 30th.</p>
<p><b>{{$mailData['financialReportName']}}</b><br>
    When you have filled in all the answers, submit the report and save a copy in your chapter’s permanent files.
    The International MOMS Club does not keep copies of your reports long term. You need to be sure your chapter
    has a copy and keeps it for the life of your chapter, as this would be the information you would need if the IRS were to
    do an audit. The Financial Report and all required additional documents must be received by July 15th. NEW CHAPTERS who
    have not started meeting prior to June 30th, you do NOT need to fill out this report! </p>
<p><b>{{$mailData['irsFilingName']}}</b><br>
    990N will not be available to be filed until July 1st. Instructions will be posted in the Resources section of the
    website when they are available.</p>
<p><b>Some other important things to remember:</b></p>
<ul>
    <li>Any board member of your chapter may fill out the report. We recommend that the Treasurer and President work together
        but any board member may complete it. All the information needed to complete it should be found in your financial
        records, newsletters, and meeting minutes.</li>
    <li>Your report must be submitted no later than July 15th! It may be sent in earlier as long as you have included all of your
        financial information for the fiscal year of July 1, {{$mailData['reportYearStart']}} - June 30, {{$mailData['reportYearEnd']}}
        and all necessary supporting files.</li>
    <li>If you need help or extra time for ANY reason, contact your Primary Coordinator BEFORE July 15th. A chapter may be put
        on probation for a late report, and a late report may put your chapter at risk of losing its non-profit status for the
        year. The report is very easy to complete, so please make sure you send it in on time!</li>
    <li><span style="color: #dc3545;">All chapters should file their 990N directly with the IRS and not through a third party. The IRS does not
        charge a fee for 990N filings.</span></li>
</ul>
<p>If you have any questions about the Annual Report Process, please reach out and ask!</p>
<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club</p>
@endcomponent
