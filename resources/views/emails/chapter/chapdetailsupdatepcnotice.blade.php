@component('mail::message')
# Primary Coordinator Notification

<p>Chapter Information for the MOMS Club of  {{$mailData['chapterNameUpd']}}, {{$mailData['chapterState']}} has been updated through the
    MOMS Information Management Interface.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>

{!! $mailData['mailTablePrimary'] !!}
<br>
@endcomponent
