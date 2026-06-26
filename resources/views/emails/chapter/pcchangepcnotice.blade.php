@component('mail::message')
# Primary Coordinator Notification

<p>You have been reassigned as the Primary Coordinator for The MOMS Club of
    {{$mailData['chapterName']}}, {{$mailData['chapterState']}}.  You should be able to see them in your
    MIMI profile.</p>
<p>They have alrady been notified, but feel free to reach out to them directly as well.</p>
{!! $mailData['mailTable'] !!}
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
