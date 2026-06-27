@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

<p>Your chapter's annual re-registration payment for <b>{{$mailData['reregMembers']}} members</b> was
    received on <b>{{$mailData['reregPaid']}}</b> and has been applied to your account.</p>
<p>Thank you for sending it in!</p>
<p>You can view/update your chapter details at any time by logging into MIMI at
    <a href="https://momsclub.org/mimi">https://momsclub.org/mimi</a></p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
