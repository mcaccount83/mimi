@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

This is to inform you that the probationary status for the MOMS Club
of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has been lifted.

Attached is a copy of your official probation release letter that you should keep with your
chapter records. If you have any questions at all, please do not hesitate to reach out to
anyone on your Coordinator Team.

**MCL**,<br>
{{ $mailData['userName'] }}<br>
{{ $mailData['userPosition'] }}<br>
{{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
International MOMS Club
@endcomponent
