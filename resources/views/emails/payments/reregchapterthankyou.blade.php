@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

Your chapter's annual re-registration payment for {{$mailData['chapterMembers']}} members was received on {{$mailData['chapterDate']}} and has been applied to your account.

Thank you for sending it in!

You can view/update your chpater details at any time by logging into MIMI at https://momsclub.org/mimi.

**MCL,**<br>
MIMI Database Administrator
<br>
@endcomponent
