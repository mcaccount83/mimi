@component('mail::message')
# ListAdmin Update Notification

<p>Board member informationfor the MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has been updated through
    the MOMS Information Management Interface. Please update members of this chapter in any groups, forums, and mailing lists.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>

{!! $mailData['mailTableListAdmin'] !!}
<br>
@endcomponent
