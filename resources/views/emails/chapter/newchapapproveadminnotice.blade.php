@component('mail::message')
# New Chapter Notification

A new chapter has been approved for Conference {{ $mailData['chapterConf'] }}. Please capture their payment and mail their MOMS Club Manual.

{!! $mailData['mailTable'] !!}

**MCL,**
MIMI Database Administrator
@endcomponent

