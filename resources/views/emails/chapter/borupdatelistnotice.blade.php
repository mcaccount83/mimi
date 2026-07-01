@component('mail::message')
# ListAdmin Update Notification

Board member information for the MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has been updated through the MOMS Information Management Interface. Please update members of this chapter in any groups, forums, and mailing lists.

**MCL,**
MIMI Database Administrator

---

{!! $mailData['mailTableListAdmin'] !!}
@endcomponent
