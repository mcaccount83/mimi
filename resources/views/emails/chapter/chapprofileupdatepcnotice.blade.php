@component('mail::message')
# Primary Coordinator Notification

<p>The MOMS Club of  {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has been updated through the MOMS Information Management Interface.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>

{!! $mailData['mailTablePrimary'] !!}
<br>
@endcomponent
