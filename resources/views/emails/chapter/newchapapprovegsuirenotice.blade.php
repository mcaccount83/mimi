@component('mail::message')
# New Chapter Notification

A New Chapter has been added for Conference {{ $mailData['chapterConf'] }}. Please create an email address in Google GSuite (a suggested email address is listed below). Once created, you'll need to go back and enter the new
email address into the chapter's MIMI profile as the "chapter email".
<br>
{!! $mailData['mailTableNewEmail'] !!}
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

