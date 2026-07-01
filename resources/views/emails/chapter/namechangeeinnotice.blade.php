@component('mail::message')
# EIN Coordinator Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} in Conference {{ $mailData['chapterConf'] }} has updated
their name in MIMI.

Their coordinator has requested that the IRS be notified of the name change.

A copy of the change letter to be faxed to the IRS is attached.

**MCL,**
MIMI Database Administrator
@endcomponent
