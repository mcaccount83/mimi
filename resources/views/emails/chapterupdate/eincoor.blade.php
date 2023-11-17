@component('mail::message')
# EIN Coordinator Notification

The MOMS Club of {{ $mailData['chapterNameUpd'] }}, {{ $mailData['chapterStateUpd'] }} in Conference {{ $mailData['conference'] }} has updated thier name in MIMI.

Please follow up with the Conference Coordinator to make sure this is a chagne that needs to be reported to the IRS.

Previous Name: {{ $mailData['chapterNamePre'] }}
New Name: {{ $mailData['chapterNameUpd'] }}
Chapter EIN: {{ $mailData['einUpd'] }}
President Name: {{$mailData['chapfnameUpd']}} {{$mailData['chaplnameUpd']}}


**MCL**,<br>
MIMI Database Administrator
<br>
@endcomponent
