@component('mail::message')
# Election Report Submitted & Activated Notification

<p>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} has submitted their Election Report.</p>
<br>
<p>Their new board members have automaticaly been activated and have access to MIMI.</p>
<br>
<p>Outgoing board members will still be able to log in and access the Financial Report.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
@endcomponent
