@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

This is to inform you that the MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has disbanded. If you believe this information is incorrect, then please let us know immediately<br>
<br>
Attached is a copy of your official disband letter that will walk you through the steps to properly close your chapter.  You will need to complete the disbanding checklist, make your final re-registration payment and submit your final financial report all by logging into your MIMI account.<br>
<br>
If you have any questions at all, please do not hesitate to reach out to anyone on your Coordinator Team.<br>
<br>
<strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club
@endcomponent
