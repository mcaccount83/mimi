@component('mail::message')
# New Chapter Notification

A New Chapter has been approved for Conference {{ $mailData['chapterConf'] }}. Please capture their payment and mail their MOMS Club Manual.
<br>
{!! $mailData['mailTable'] !!}

<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

