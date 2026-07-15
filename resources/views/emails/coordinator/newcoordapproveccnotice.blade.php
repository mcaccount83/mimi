@component('mail::message')
# New Coordinator CC Admin Notification

A new Coordinator has been approved for your Conference. Please create the following email address in Google GSuite. If you choose
to create an email address different than what is listed below, you will need to go back and update MIMI.
Otherwise, MIMI is already set up with the one listed here.

{!! $mailData['mailTable'] !!}

**MCL,**<br>
MIMI Database Administrator
@endcomponent
