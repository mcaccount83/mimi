@component('mail::message')
# Financial Report Check-In Notification

{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has submitted their Financial Report. It is
ready to be reviewed. The Financial Report PDF is attached and other documents that can be downloaded are
listed below.

Submitted by: {{ $mailData['completedName'] }}, @mailto($mailData['completedEmail'])<br>

**Downloads Available:**

@if(!empty($mailData['rosterPath']))
- [Chapter Roster](https://drive.google.com/uc?export=download&id={{ $mailData['rosterPath'] }})
@else
- No Roster Attached
@endif
@if(!empty($mailData['statement1Path']))
- [Primary Bank Statement](https://drive.google.com/uc?export=download&id={{ $mailData['statement1Path'] }})
@else
- No Statement Attached
@endif
@if(!empty($mailData['statement2Path']))
- [Additional Bank Statement](https://drive.google.com/uc?export=download&id={{ $mailData['statement2Path'] }})
@endif
@if(!empty($mailData['irsPath']))
- [990N Confirmation File](https://drive.google.com/uc?export=download&id={{ $mailData['irsPath'] }})
@else
- No 990N File Attached
@endif

**Coordinators:**

- Primary Coordinator: {{ $mailData['pcName'] }}

**MCL,**<br>
MIMI Database Administrator
@endcomponent
