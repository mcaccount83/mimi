@component('mail::message')
# Mentoring Coordinator Notification

You have been reassigned as the Mentoring Coordinator for {{ $mailData['cdName'] }}. You should be able
to see them (and their chapters) in your MIMI profile.

They have already been notified, but feel free to reach out to them directly as well.

{!! $mailData['mailTable'] !!}

**MCL,**
International MOMS Club
@endcomponent
