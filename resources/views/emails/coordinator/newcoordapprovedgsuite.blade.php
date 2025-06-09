@component('mail::message')
# New Coordinator GSuite Admin Notification

A New Coordinator has been approved. Please create the following email address in Google GSuite.  If you choose to create an email address
different than what is listed below, you will need to go back and update MIMI.  Otherwise, MIMI is already set up with the one listed here.
<br>
{!! $mailData['mailTable'] !!}
<br>

<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

