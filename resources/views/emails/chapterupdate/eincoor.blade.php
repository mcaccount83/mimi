@component('mail::message')
# EIN Coordinator Notification

The MOMS Club of {{ $mailData['chapterNameUpd'] }}, {{ $mailData['chapterState'] }} in Conference {{ $mailData['chapterConf'] }} has updated thier name in MIMI.<br>
<br>
Please follow up with the Conference Coordinator to make sure this is a chagne that needs to be reported to the IRS.<br>
<br>
Previous Name: {{ $mailData['chapterName'] }}<br>
New Name: {{ $mailData['chapterNameUpd'] }}<br>
Chapter EIN: {{ $mailData['chapterEIN'] }}<br>
President Name: {{$mailData['presName']}}<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>
@endcomponent
