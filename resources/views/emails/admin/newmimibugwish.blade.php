@component('mail::message')
# New MIMI Bugs & Wishes Admin Notice

A new Bug or Wish has been added to the To Do list.
<br>
<strong>{{$mailData['taskNameNew']}}</strong>
{{$mailData['taskDetailsNew']}}
<br>
Reported by: {{$mailData['ReportedId']}} on {{ \Carbon\Carbon::parse($mailData['ReportedDate'])->format('m-d-Y') }}
<br>
**MCL**,<br>
MIMI Database Administrator
<br>
@endcomponent
