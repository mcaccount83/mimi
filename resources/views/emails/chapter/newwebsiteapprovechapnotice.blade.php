@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}

<p>Thank you for submitting your new website for linking.  The site has been reviewed and has been added
    to the main MOMS Club website.</p>
<p>Your linked site is: {{$mailData['chapterWebsiteURL']}}.</p>
<p>List of linked chapters can be found here:
    <a href="https://momsclub.org/chapters/chapter-links/">https://momsclub.org/chapters/chapter-links/</a></p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
