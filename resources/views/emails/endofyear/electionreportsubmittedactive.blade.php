@component('mail::message')
# Election Report Submitted & Activated Notification

{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} has submitted their Election Report.<br>
<br>
Their new board members have automaticaly been activated and have access to MIMI.<br>
<br>
Outgoing board members will still be able to log in and access the Financial Report.<br>

<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent
