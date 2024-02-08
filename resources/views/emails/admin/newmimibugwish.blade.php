@component('mail::message')
# New MIMI Bugs & Wishes Admin Notice

A new Bug or Wish has been added to the To Do list.

<h4>{{$mailData['taskName']}}</h4>
{{$mailData['taskDetails']}}

Reported by: {{$mailData['ReportedId']}} on {{ \Carbon\Carbon::parse($mailData['ReportedDate'])->format('m-d-Y') }}

**MCL**,<br>
MIMI Database Administrator
<br>
@endcomponent
