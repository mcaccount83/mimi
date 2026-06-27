@component('mail::message')
# Mentoring Coordinator Notification

<p>Your have been assigned a new Mentoring Coordinator:</p>
<br>
{!! $mailData['mailTableRC'] !!}
<br>
<p>You should begin using her as your primary point of contact immediately.</p>
<p>You can continue to view details about your Mentoring Coordinator and assigned Chapters at any time
    by logging into MIMI at <a href="https://momsclub.org/mimi">https://momsclub.org/mimi</a>.</p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
