@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

At this time, we have not received one or more of your chapter's End of Year Reports. They are now considered PAST DUE.<br>
<p>
    <ul>
        @if($mailData['boardElectionReportReceived'] != '1')
            <li>Board Election Report</li>
        @endif
        @if($mailData['financialReportReceived'] != '1')
            <li>Financial Report</li>
        @endif
        @if($mailData['990NSubmissionReceived'] != '1')
            <li>990N Submission</li>
        @endif
        @if($mailData['einLetterCopyReceived'] == null)
            <li>Copy of EIN Letter</li>
        @endif
    </ul>
</p>
<p>Please submit your report through MIMI (https://momsclub.org/mimi) as soon as possible. If you are having trouble submitting, have any questions, or need more time, please contact your Primary Coordinator.</p>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent








