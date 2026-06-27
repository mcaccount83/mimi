@component('mail::message')
# Financial Report Submitted

<p>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}, thank you for submitting your Financial Report.</p>
<p>Your Coordintor Team will review your report shortly and reach out with any questions they may have!</p>
<p>A copy of your report is attached.  Please save/keep a copy for your records.</p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent

