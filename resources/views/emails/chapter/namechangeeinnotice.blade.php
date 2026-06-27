@component('mail::message')
# EIN Coordinator Notification

<p>The MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} in Conference {{ $mailData['chapterConf'] }} has updated thier name in MIMI.</[]>
<br>
<p>Their coordinator has requested that the IRS be notifed of the name change.</p>
<br>
<p>A copy of the change letter to be faxed to the IRS is attached.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>
@endcomponent
