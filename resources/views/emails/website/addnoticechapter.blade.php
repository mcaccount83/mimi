@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

Thank you for submitting your new website for linking.  The site has been reviewed and has been added to the main MOMS Club website.<br>
<br>
Your linked site is: {{$mailData['chapterWebsiteURL']}}.<br>
<br>
List of linked chapters can be found here: https://momsclub.org/chapters/chapter-links/<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
