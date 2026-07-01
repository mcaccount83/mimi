@component('mail::message')
# Website Link Notification

Thank you for completing the review on the website for the MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}.

The new website address is: {{ $mailData['chapterWebsiteURL'] }}.

The site has been reviewed and has automatically been added to the main MOMS Club website.

**MCL,**
MIMI Database Administrator
@endcomponent
