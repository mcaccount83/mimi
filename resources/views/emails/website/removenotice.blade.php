@component('mail::message')
# Website Removal Notification

The MOMS Club of {{ $mailData['chapter_name'] }}, {{ $mailData['chapter_state'] }} has disbanded.

Please follow up with them to ensure they remove their online accounts.  Here is a list of their known online sites.

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Website: {{$mailData['ch_website_url']}}
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Online Discussion Group:
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Facebook:
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Twitter:
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Instatram:

**MCL,**<br>
MIMI
<br>
@endcomponent

