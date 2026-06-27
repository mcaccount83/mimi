@component('mail::message')
# Election Report Submitted Notification

<p>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} has submitted their Election Report.</p>
<br>
<p>You can activate their new board members at any time to give them access to MIMI.</p>
<br>
<p>Outgoing board members will still be able to log in and access the Financial Report.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
@endcomponent

