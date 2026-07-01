@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

Your chapter's annual re-registration payment for **{{ $mailData['reregMembers'] }} members** was
received on **{{ $mailData['reregPaid'] }}** and has been applied to your account.

Thank you for sending it in!

You can view/update your chapter details at any time by logging into MIMI at
[https://momsclub.org/mimi](https://momsclub.org/mimi).

**MCL,**
International MOMS Club
@endcomponent
