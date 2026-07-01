@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

This is to inform you that the MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has
been given a formal warning for excessive Party/Member Benefit expenses this fiscal year. If you believe this
information is incorrect, then please contact your Primary Coordinator immediately.

Attached is a copy of your official warning letter that you should keep in your chapter records. If you have
any questions at all, please do not hesitate to reach out to anyone on your Coordinator Team.

**MCL**,
{{ $mailData['userName'] }}
{{ $mailData['userPosition'] }}
{{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}
International MOMS Club
@endcomponent
