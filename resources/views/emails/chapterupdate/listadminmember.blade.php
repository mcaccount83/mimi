@component('mail::message')
# ListAdmin Update Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} has been updated through the MOMS Information Management Interface. Please update members of this chapter in any groups, forums, and mailing lists.<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>

{!! $mailData['mailTableListAdmin'] !!}
<br>

@endcomponent

