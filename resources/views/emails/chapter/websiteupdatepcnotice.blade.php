@component('mail::message')
# Primary Coordinator Notification

Website information for the MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}
has been updated through the MOMS Information Management Interface.

**MCL,**
MIMI Database Administrator

---

{!! $mailData['mailTablePrimary'] !!}
@endcomponent
