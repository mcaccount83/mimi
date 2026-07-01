@component('mail::message')
# New MIMI Bugs & Wishes Admin Notice

A new Bug or Wish has been added to the To Do list.

**{{ $mailData['taskNameNew'] }}**
{{ $mailData['taskDetailsNew'] }}

Reported by: {{ $mailData['ReportedId'] }} on {{ $mailData['ReportedDate'] }}

**MCL,**
MIMI Database Administrator
@endcomponent
