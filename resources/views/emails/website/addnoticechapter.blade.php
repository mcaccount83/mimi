@component('mail::message')
# MOMS Club of {{ $mailData['chapter_name'] }}, {{ $mailData['chapter_state'] }}

Thank you for submitting your new website for linking.  The site has been reviewed and has been added to the main MOMS Club website.<br>
<br>
Your linked site is: {{$mailData['ch_website_url']}}.<br>
<br>
List of linked chapters can be found here: https://momsclub.org/chapters/chapter-links/<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
