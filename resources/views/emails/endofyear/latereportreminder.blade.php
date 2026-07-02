@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

At this time, we have not received one or more of your chapter's End of Year Reports. They are now considered PAST DUE.

@if($mailData['boardElectionReportReceived'] != '1')
- Board Election Report
@endif
@if($mailData['financialReportReceived'] != '1')
- Financial Report
@endif
@if($mailData['990NSubmissionReceived'] != '1')
- 990N Submission
@endif
@if($mailData['einLetterCopyReceived'] == null)
- Copy of EIN Letter
@endif

Please submit your report through MIMI (<a href="https://momsclub.org/mimi">https://momsclub.org/mimi</a>) as soon as possible. If you are having
trouble submitting, have any questions, or need more time, please contact your Primary Coordinator.

**MCL,**<br>
International MOMS Club
@endcomponent
