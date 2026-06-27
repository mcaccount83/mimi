@component('mail::message')
# Mentoring Coordinator Notification

<p>You have been reassigned as the Mentoring Coordinator for {{$mailData['cdName']}}.  You should be able
    to see them (and their chapters) in your MIMI profile.</p>
<p>They have alrady been notified, but feel free to reach out to them directly as well.</p>
<br>

{!! $mailData['mailTable'] !!}
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
