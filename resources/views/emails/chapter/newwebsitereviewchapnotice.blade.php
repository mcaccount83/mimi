@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

Thank you for submitting your new website for linking. The site will be reviewed by one of our coordinators and we will let you know if any
updates need to be made before the site can be linked.

Your submitted site is: [{{ $mailData['chapterWebsiteURL'] }}]({{ $mailData['chapterWebsiteURL'] }}).

List of linked chapters can be found here: [https://momsclub.org/chapters/chapter-links/](https://momsclub.org/chapters/chapter-links/)

**MCL,**
International MOMS Club
@endcomponent
