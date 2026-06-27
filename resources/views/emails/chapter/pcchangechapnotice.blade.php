@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

<p>Your chapter has been assigned a new Priamry Coordinator:</p>
<br>
{!! $mailData['mailTablePC'] !!}
<br>
<p>You should begin using her as your primary point of contact immediately.</p>
<p>You can view/update your chapter details as well as see your current Coordinator list at any time by logging
    into MIMI at <a href="https://momsclub.org/mimi">https://momsclub.org/mimi</a>.</p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
