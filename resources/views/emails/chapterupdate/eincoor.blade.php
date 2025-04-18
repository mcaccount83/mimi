@component('mail::message')
# EIN Coordinator Notification

The MOMS Club of {{ $mailData['chapterNameUpd'] }}, {{ $mailData['chapterState'] }} in Conference {{ $mailData['chapterConf'] }} has updated thier name in MIMI.<br>
<br>
Please follow up with the Conference Coordinator to make sure this is a change that needs to be reported to the IRS.<br>
<br>
Information to send to IRS:<br>
<b>{{ $mailData['chapterEIN'] }}</b><br>
MOMS Club of {{ $mailData['chapterNameUpd'] }}, {{ $mailData['chapterState'] }}<br>
(formerly known as MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }})<br>
c/o {{$mailData['presName']}}<br>
{{$mailData['presAddress']}}<br>
{{$mailData['presCity']}}, {{$mailData['presState']}} {{$mailData['presZip']}}<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>
@endcomponent
