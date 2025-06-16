@component('mail::message')
# Mentoring Coordinator Notification

Your have been assigned a new Mentoring Coordinator:
<br>
{!! $mailData['mailTableRC'] !!}
<br>
You should begin using her as your primary point of contact immediately.<br>
<br>
You can continue to view/update details about your Mentoring Coordinator and assigned Chapters at any time by logging into MIMI at https://momsclub.org/mimi.<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
