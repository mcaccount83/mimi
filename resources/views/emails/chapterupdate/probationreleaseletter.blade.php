@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

<p>This is to inform you that the probationary status for the MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has been lifed.</p>
<p>Attached is a copy of your official probation relase letter that you should keep wtih your chapter records.  If you have any questions at all, please do not hesitate to reach out to anyone on your Coordinator Team.</p>
<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['cc_fname'] }} {{ $mailData['cc_lname'] }}<br>
    {{ $mailData['cc_pos'] }}<br>
    {{ $mailData['cc_conf_name'] }}, {{ $mailData['cc_conf_desc'] }}<br>
    International MOMS Club</p>
@endcomponent