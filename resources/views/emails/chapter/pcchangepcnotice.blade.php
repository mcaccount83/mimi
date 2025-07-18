@component('mail::message')
# Primary Coordinator Notification

You have been reassigned as the Primary Coordinator for The MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}.  You should be able to see them in your MIMI profile.<br>
<br>
They have alrady been notified, but feel free to reach out to them directly as well.
<br>
{!! $mailData['mailTable'] !!}
<br>

<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
