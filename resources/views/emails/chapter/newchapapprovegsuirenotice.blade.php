@component('mail::message')
# New Chapter Notification

A New Chapter has been approved for Conference {{ $mailData['chapterConf'] }}. Please create the following email address in Google GSuite. Once created, you'll need to go back and enter the new
email address into MIMI as the "chapter email" in their profile.
<br>
{!! $mailData['mailTableNewEmail'] !!}
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

