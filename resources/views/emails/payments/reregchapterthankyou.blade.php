@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

Your chapter's annual re-registration payment for <b>{{$mailData['reregMembers']}} members</b> was received on <b>{{$mailData['reregPaid']}}</b> and has been applied to your account.<br>
<br>
Thank you for sending it in!<br>
<br>
You can view/update your chapter details at any time by logging into MIMI at https://momsclub.org/mimi.<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
