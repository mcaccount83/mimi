@component('mail::message')
# Primary Coordinator Notification

You have been reassigned as the Primary Coordinator for the MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}. You should
be able to see them in your MIMI profile.

They have already been notified, but feel free to reach out to them directly as well.

{!! $mailData['mailTable'] !!}

**MCL,**
International MOMS Club
@endcomponent
