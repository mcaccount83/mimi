@component('mail::message')
# Final Financial Report Submitted

{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}, thank you for submitting your Final Financial Report.<br>
<br>
A copy of your report is attached.  Please save/keep a copy for your records.<br>

<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent
