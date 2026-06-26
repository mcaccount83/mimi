@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

<p>At this time, we have not received one or more of your chapter's End of Year Reports. They are
    now considered PAST DUE.</p>
<ul>
    @if($mailData['boardElectionReportReceived'] != '1')
        <li>{{$mailData['boardReportName']}}</li>
    @endif
    @if($mailData['financialReportReceived'] != '1')
        <li>{{$mailData['financialReportName']}}</li>
    @endif
    @if($mailData['990NSubmissionReceived'] != '1')
        <li>{{$mailData['irsFilingName']}}</li>
    @endif
    @if($mailData['einLetterCopyReceived'] == null)
        <li>Copy of EIN Letter</li>
    @endif
</ul>
<p>Please submit your report through MIMI (<a href="https://momsclub.org/mimi">https://momsclub.org/mimi</a>)
    as soon as possible. If you are having trouble submitting, have any questions, or need more time, please
    contact your Primary Coordinator.</p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
