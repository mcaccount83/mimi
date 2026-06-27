@component('mail::message')
# Final Financial Report Submitted

<p>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} has submitted their Final Financial Report. Since the have disbanded, this is for information only and does
not need to be reviewed.</p>
<br>
<p>Submitted by: {{$mailData['completedName']}}, @mailto($mailData['completedEmail'])</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
@endcomponent
