@component('mail::message')
# New Chapter Notification

<p>A New Chapter has been approved for Conference {{ $mailData['chapterConf'] }}. Please capture their payment and mail their MOMS Club Manual.</p>
<br>

{!! $mailData['mailTable'] !!}
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
@endcomponent

