@component('mail::message')
# New MIMI Bugs & Wishes Admin Notice

A new Bug or Wish has been added to the To Do list.<br>
<br>
<strong>{{$mailData['taskNameNew']}}</strong><br>
{{$mailData['taskDetailsNew']}}<br>
<br>
Reported by: {{$mailData['ReportedId']}} on {{ $mailData['ReportedDate'] }}<br>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent
