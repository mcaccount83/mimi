@component('mail::message')
<h1><center>{{$mailData['financialReportName']}}</center></h1>
<br>
<p>
    If you have not done so already, please complete the Financial Report for your chapter!  This report is
    available now and is due no later than July 15th.
</p>
<p>
    Along with your Financial Report you should also file your 990N with the IRS. The 990N cannot be filed before July 1st.
    Your 990N should be filed directly with the IRS and not through a third party. The IRS does not charge a fee for 990N filings.
    If you notice the dates on your filing are NOT July-June, do not continue filing, STOP and contact your coordinator team.
<p>
    After receiving your completed reports, your Coordiantor Team will review the report and
    reach out if they have any questions.
</p>
<p>
    The Financial Report as well as links to the 990N IRS Filing site (with instructions) can be accessed by logging into your MIMI
    account (<a href="https://momsclub.org/mimi">https://momsclub.org/mimi</a>) and navigating to the EOY
    Reports section in the left sidebar menu.
</p>
<p>If you have any questions about the Financial Report, please reach out and ask!</p>
<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club
</p>
@endcomponent
