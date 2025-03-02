@component('mail::message')
# Primary Coordinator Notification

{{$mailData['pcName']}}!<br>
<br>
The MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has been added to MIMI and you have been assigned as the Primary Coordinator.<br>
<br>
Founder Information:<br>
{{$mailData['presName']}}<br>
{{$mailData['presEmail']}}<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
<br>
@endcomponent
