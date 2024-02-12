@component('mail::message')
# Website Review Notification

The MOMS Club of {{ $mailData['chapter_name'] }}, {{ $mailData['chapter_state'] }} has updated their chapter website and they have requested to have it linked to the MOMS Club website.<br>
<br>
The new website address is: {{$mailData['ch_website_url']}}.<br>
<br>
Please review the site based on the website linking guidelines found here - https://momsclub.org/resources/website-linking-guidelines/<br>
<br>
Once the site has been reviewed and is ready to be linked, please update the Website Link Status to "Linked" in the Chapter's MIMI profile and it will automatically be added to our main website.<br>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent
