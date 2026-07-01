@component('mail::message')
# ListAdmin unZapped Notification

The following chapter has been unZapped in MIMI: MOMS Club of {{ $mailData['chapterName'] }},
{{ $mailData['chapterState'] }}, Conference {{ $mailData['chapterConf'] }}.

Please re-add members of this chapter to any groups, forums and mailing lists.

**MCL,**
MIMI Database Administrator

---

{!! $mailData['mailTable'] !!}
@endcomponent
