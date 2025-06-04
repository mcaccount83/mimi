@component('mail::message')
# New Coordinator Application Notification

A New Coordinator Application has been submitted for Conference {{ $mailData['conference_id'] }}. Please review the application information and contact the coordinator to get them started.<br>

<br>
{!! $mailData['mailTable'] !!}
<br>
<p>The New Chapter Application Fee is authorize only and must be retrieved in 30 days or the founder will have to resubmit their application. New Chapter must be approved and moved
    from PENDING to ACTIVE status before fee can be retrieved.
</p>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

