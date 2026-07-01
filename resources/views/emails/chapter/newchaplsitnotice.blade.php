@component('mail::message')
# ListAdmin New Chapter Add Notification

The following chapter has been added to MIMI:

MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}, Conference {{ $mailData['chapterConf'] }}.

Please add members of this chapter to any groups, forums and mailing lists.

**MCL,**
MIMI Database Administrator

---

{!! $mailData['mailTableNewChapter'] !!}
@endcomponent
