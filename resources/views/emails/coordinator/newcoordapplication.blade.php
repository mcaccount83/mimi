@component('mail::message')
# New Coordinator Application Notification

<p>A New Coordinator Application has been submitted for Conference {{ $mailData['conference_id'] }}. Please review the application
    information and contact the coordinator to get them started.</p>

{!! $mailData['mailTable'] !!}
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
@endcomponent

