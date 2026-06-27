@component('mail::message')
# Quarterly Financial Report Submitted

<p>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}, thank you for submitting your Quarterly Financial Report.</p>
<br>
{!! $mailData['mailTable'] !!}
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
@endcomponent
