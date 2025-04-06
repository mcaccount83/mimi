@component('mail::message')
# Primary Coordinator Notification

The MOMS Club of  {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has been updated through the MOMS Information Management Interface.<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>

{!! $mailData['mailTablePrimary'] !!}

<br>

@endcomponent
