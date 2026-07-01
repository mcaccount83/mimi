@component('mail::message')
# Assigned Reviewer Notification

{{ $mailData['userName'] }} has assigned you to review the financial report for {{ $mailData['chapterName'] }},
{{ $mailData['chapterState'] }}. After reviewing, assign to the next reviewer or mark as review complete.

**Message from {{ $mailData['userName'] }}:**
{{ $mailData['reviewerEmailMessage'] }}

The Financial Report PDF is attached and other documents that can be downloaded are listed below.

Submitted by: {{ $mailData['completedName'] }}, @mailto($mailData['completedEmail'])

**Downloads Available:**

- @isset($mailData['rosterPath'])[Chapter Roster](https://drive.google.com/uc?export=download&id={{ $mailData['rosterPath'] }})@else No Roster Attached @endisset
- @isset($mailData['statement1Path'])[Primary Bank Statement](https://drive.google.com/uc?export=download&id={{ $mailData['statement1Path'] }})@else No Statement Attached @endisset
@isset($mailData['statement2Path'])
- [Additional Bank Statement](https://drive.google.com/uc?export=download&id={{ $mailData['statement2Path'] }})
@endisset
- @isset($mailData['irsPath'])[990N Confirmation File](https://drive.google.com/uc?export=download&id={{ $mailData['irsPath'] }})@else No 990N File Attached @endisset

**MCL,**
MIMI Database Administrator
@endcomponent
