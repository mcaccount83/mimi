@component('mail::message')
# Primary Coordinator Notification

Chapter Information for the MOMS Club of  {{$mailData['chapterNameUpd']}}, {{$mailData['chapterState']}} has been updated through the MOMS Information Management Interface.<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>

{!! $mailData['mailTablePrimary'] !!}


@endcomponent
