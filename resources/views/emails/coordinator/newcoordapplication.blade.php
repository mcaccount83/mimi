@component('mail::message')
# New Coordinator Application Notification

A new Coordinator Application has been submitted for Conference {{ $mailData['conference_id'] }}. Please review
the application information and contact the coordinator to get them started.

{!! $mailData['mailTable'] !!}

**MCL,**
MIMI Database Administrator
@endcomponent

