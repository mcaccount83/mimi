@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

Your chapter has been assigned a new Primary Coordinator:

{!! $mailData['mailTablePC'] !!}

You should begin using her as your primary point of contact immediately.

You can view/update your chapter details as well as see your current Coordinator list at any time by logging into MIMI
at [https://momsclub.org/mimi](https://momsclub.org/mimi).

**MCL,**
International MOMS Club
@endcomponent
