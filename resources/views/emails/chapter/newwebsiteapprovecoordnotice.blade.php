@component('mail::message')
# Website Link Notification

Thank you for completing the review on the website for the MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}.<br>
<br>
The new website address is: {{$mailData['chapterWebsiteURL']}}.<br>
<br>
The site has been reviewed and has automatically been added to the main MOMS Club website.<br>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
<br>
@endcomponent
