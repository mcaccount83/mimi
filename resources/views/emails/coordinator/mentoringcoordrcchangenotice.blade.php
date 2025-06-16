@component('mail::message')
# Mentoring Coordinator Notification

You have been reassigned as the Mentoring Coordinator for {{$mailData['cdName']}}, {{$mailData['cdPosition']}}.  You should be able to see them (and their chapters) in your MIMI profile.<br>
<br>
They have alrady been notified, but feel free to reach out to them directly as well.
<br>
{!! $mailData['mailTable'] !!}
<br>

<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
