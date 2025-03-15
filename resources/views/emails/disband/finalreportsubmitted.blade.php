@component('mail::message')
# Final Financial Report Submitted

{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has submitted their Final Financial Report. Since the have disbanded, this is for information only and does
not need to be reviewed.<br>
<br>
Submitted by: {{$mailData['completedName']}}, <a href="mailto:{{$mailData['completedEmail']}}">{{$mailData['completedEmail']}}</a><br>
<br>

<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

@endcomponent

