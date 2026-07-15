@component('mail::message')
# Retired Coordinator Admin Notice

The following coordinator has been marked as retired in MIMI in {{ $mailData['userConfName'] }}.

{{ $mailData['cdName'] }}
{{ $mailData['cdEmail'] }}

Please suspend the coordinator's momsclub.org email address.

**MCL,**<br>
MIMI Database Administrator
@endcomponent
