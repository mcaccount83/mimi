@component('mail::message')
# Quarterly Financial Report Submitted

{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}, thank you for submitting your Quarterly Financial Report.<br>

{!! $mailData['mailTable'] !!}

<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent
