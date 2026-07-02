@component('mail::message')
# Assigned Reviewer Notification

{{ $mailData['userName'] }} has assigned you to review the financial report for {{ $mailData['chapterName'] }},
{{ $mailData['chapterState'] }}. After reviewing, assign to the next reviewer or mark as review complete.

**Message from {{ $mailData['userName'] }}:**
{{ $mailData['reviewerEmailMessage'] }}

The Financial Report PDF is attached and other documents that can be downloaded are listed below.

Submitted by: {{ $mailData['completedName'] }}, @mailto($mailData['completedEmail'])

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

**MCL,**<br>
MIMI Database Administrator
@endcomponent
