@component('mail::message')
# Grant Request Received

<p>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}, thank you for submitting your Grant Request.</p>
<br>
<p>Your application will be processed by our M2M Committee, If you have any questions, please contact your Regional or Conference Coordinator.</p>
<br>
<p>A copy of your request is attached.  Please save/keep a copy for your records.</p>
<br>
<p><strong>MCL,</strong><br>
M2M Committee</p>
@endcomponent
