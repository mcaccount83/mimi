@component('mail::message')
# EIN Coordinator Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} in Conference {{ $mailData['chapterConf'] }} has updated thier name in MIMI.<br>
<br>
Please follow up with the Conference Coordinator to make sure this is a change that needs to be reported to the IRS.<br>
<br>
A copy of the change letter to be faxed to the IRS is attached.<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>
@endcomponent
