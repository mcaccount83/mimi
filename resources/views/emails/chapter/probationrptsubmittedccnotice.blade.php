@component('mail::message')
# Quarterly Financial Report Submitted

{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}, thank you for submitting your Quarterly Financial Report.

{!! $mailData['mailTable'] !!}

**MCL,**<br>
MIMI Database Administrator
@endcomponent
