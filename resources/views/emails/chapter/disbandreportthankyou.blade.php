@component('mail::message')
# Final Financial Report Submitted

<p>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}, thank you for submitting your Final Financial Report.</p>
<br>
<p>A copy of your report is attached.  Please save/keep a copy for your records.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
@endcomponent
