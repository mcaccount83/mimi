@component('mail::message')
# New MIMI Bugs & Wishes Admin Notice

<p>A new Bug or Wish has been added to the To Do list.</p>
<br>
<p><strong>{{$mailData['taskNameNew']}}</strong><br>
{{$mailData['taskDetailsNew']}}</p>
<br>
<p>Reported by: {{$mailData['ReportedId']}} on {{ $mailData['ReportedDate'] }}</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
@endcomponent
