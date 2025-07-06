@component('mail::message')
# New Coordinator Application Notification

A New Coordinator Application has been submitted for Conference {{ $mailData['conference_id'] }}. Please review the application information and contact the coordinator to get them started.<br>

<br>
{!! $mailData['mailTable'] !!}
<br>

<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

