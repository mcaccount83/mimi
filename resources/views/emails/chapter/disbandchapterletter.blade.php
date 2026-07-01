@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

This is to inform you that the MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has disbanded. If you believe this
information is incorrect, then please let us know immediately.

Attached is a copy of your official disband letter that will walk you through the steps to properly close your chapter. You will need to
complete the disbanding checklist, make your final re-registration payment and submit your final financial report all by logging into your MIMI account.

If you have any questions at all, please do not hesitate to reach out to anyone on your Coordinator Team.

**MCL**,
{{ $mailData['userName'] }}
{{ $mailData['userPosition'] }}
{{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}
International MOMS Club
@endcomponent
