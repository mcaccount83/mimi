@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

Thank you for submitting your new website for linking. The site has been reviewed and has been added to the main MOMS Club website.

Your linked site is: {{ $mailData['chapterWebsiteURL'] }}.

List of linked chapters can be found here: [https://momsclub.org/chapters/chapter-links/](https://momsclub.org/chapters/chapter-links/)

**MCL,**
International MOMS Club
@endcomponent
