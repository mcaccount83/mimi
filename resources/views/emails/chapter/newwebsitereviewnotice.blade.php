@component('mail::message')
# Website Review Notification

<p>The MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} has updated their chapter
    website and they have requested to have it linked to the MOMS Club website.</p>
<br>
<p>The new website address is: {{$mailData['chapterWebsiteURL']}}.</p>
<br>
<p>Please review the site based on the website linking guidelines found here:
    <a href="https://momsclub.org/resources/website-linking-guidelines/">https://momsclub.org/resources/website-linking-guidelines/</a></p>
<br>
<p>Once the site has been reviewed and is ready to be linked, please update the Website Link Status to "Linked" in the Chapter's
    MIMI profile and it will automatically be added to our main website.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
@endcomponent
