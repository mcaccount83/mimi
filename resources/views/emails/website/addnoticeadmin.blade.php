@component('mail::message')
# Website Link Notification

Thank you for completing the review on the website for the MOMS Club of {{ $mailData['chapter_name'] }}, {{ $mailData['chapter_state'] }}.

The new website address is: {{$mailData['ch_website_url']}}.

The site has been reviewed and has automatically be added to the main MOMS Club website.

**MCL,**<br>
MIMI
<br>
@endcomponent
