@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

Your chapter's online annual re-registration payment for {{$mailData['members']}} members was processed on {{$mailData['datePaid']}} and has been applied to your account.<br>
<br>
Thank you for submtting it!<br>
<br>
You can view/update your chapter details at any time by logging into MIMI at https://momsclub.org/mimi.<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
