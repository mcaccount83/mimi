@component('mail::message')
# Primary Coordinator Notification

Chapter information for the MOMS Club of {{ $mailData['chapterNameUpd'] }}, {{ $mailData['chapterState'] }} has been updated through the MOMS Information Management Interface.

**MCL,**
MIMI Database Administrator

---

{!! $mailData['mailTablePrimary'] !!}
@endcomponent
