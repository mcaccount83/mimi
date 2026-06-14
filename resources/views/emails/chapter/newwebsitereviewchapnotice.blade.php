@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}

Thank you for submitting your new website for linking.  The site will reviewed by one of our coordinators and
we will let you know if any updates need to be made before the site can be linked.<br>
<br>
Your linked site is: {{$mailData['chapterWebsiteURL']}}.<br>
<br>
List of linked chapters can be found here: https://momsclub.org/chapters/chapter-links/<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
