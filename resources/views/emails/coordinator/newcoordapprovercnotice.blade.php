@component('mail::message')
# New Coordinator Notification

<p>A New Coordinator has been approved and you have been assigned as their Mentoring Coordinator.</p>
<br>

{!! $mailData['mailTable'] !!}
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
@endcomponent

