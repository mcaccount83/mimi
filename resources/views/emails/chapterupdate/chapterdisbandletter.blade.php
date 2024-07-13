@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

<p>This is to inform you that the MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has disbanded. If you believe this information is incorrect, then please contact your Primary Coordinator immediately</p>
<p>Attached is a copy of your official disband letter that will walk you through the steps to properly close your chapter.  If you have any questions at all, please do not hesitate to reach out to anyone on your Coordinator Team.</p>
<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['cc_fname'] }} {{ $mailData['cc_lname'] }}<br>
    {{ $mailData['cc_pos'] }}, Conference {{ $mailData['cc_conf'] }}<br>
    International MOMS Club</p>
@endcomponent
