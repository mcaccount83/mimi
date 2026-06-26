@component('mail::message')
<h1><center>{{$mailData['boardReportRange']}} Election Information</center></h1>
<br>
<p>
    With board elections fast approaching, now is the time to start preparing. If your chapter is new (started after June, {{$mailData['reportYearStart']}})
    you will not be having elections. Instead, your founding Board will continue until Spring, {{$mailData['boardReportEnd']}}. All other chapters, however, will be
    electing officers in May or June.
</p>
<p>There are several things you can do now to prepare your chapter for the elections, and to ensure a full slate of officers.</p>
<ul>
    <li>Review the Election Timetable Fact Sheet (attached).</li>
    <li>Complete the Election Process eLearning Training (access through your MIMI login).</li>
    <li>Re-evaluate officer workload and delegation options.</li>
    <li>Remind your chapter about elections.</li>
    <li>Seek qualified prospective candidates.</li>
    <li>ASK THEM to serve.</li>
    <li>Smile and stay positive when recruiting</li>
</ul>
<p>If you are struggling or have any questions about the election process, reach out to your Primary Coordinator for ideas and support!</p>
<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club</p>
@endcomponent
