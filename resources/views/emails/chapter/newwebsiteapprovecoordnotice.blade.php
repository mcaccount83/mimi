@component('mail::message')
# Website Link Notification

<p>Thank you for completing the review on the website for the MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}.</p>
<br>
<p>The new website address is: {{$mailData['chapterWebsiteURL']}}.</p>
<br>
<p>The site has been reviewed and has automatically been added to the main MOMS Club website.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>
@endcomponent
