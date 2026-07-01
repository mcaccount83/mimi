@component('mail::message')
# Final Financial Report Submitted

{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has submitted their Final Financial Report. Since they have disbanded, this is for information only and does not need to be reviewed.

Submitted by: {{ $mailData['completedName'] }}, @mailto($mailData['completedEmail'])

**MCL,**
MIMI Database Administrator
@endcomponent
