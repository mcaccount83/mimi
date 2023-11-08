@component('mail::message')
# MOMS Club of {{ $mailData['chapter_name'] }}, {{ $mailData['chapter_state'] }}

Thank you for submitting your new website for linking.  The site has been reviewed and has been added to the main MOMS Club website.

Your linnked site is: {{$mailData['ch_website_url']}}.

List of linked chapters can be found here:

**MCL,**<br>
International MOMS Club
<br>
@endcomponent
<br>
@endcomponent
