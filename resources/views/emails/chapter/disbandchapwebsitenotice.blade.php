@component('mail::message')
# Website Removal Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} has disbanded.<br>
<br>
Please follow up with them to ensure they remove their online accounts.  Here is a list of their known online sites.<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Website: {{$mailData['chapterWebsiteURL']}}<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Online Discussion Group:<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Facebook:<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Twitter:<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Instatram:<br>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

