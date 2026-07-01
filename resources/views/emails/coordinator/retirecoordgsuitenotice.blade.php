@component('mail::message')
# Retired Coordinator Admin Notice

The following coordinator has been marked as retired in MIMI in {{ $mailData['userConfName'] }}.

{{ $mailData['cdName'] }}
{{ $mailData['cdEmail'] }}

Please deactivate the coordinator's momsclub.org email address and remove from any groups, forums and mailing lists.

**MCL,**
MIMI Database Administrator
@endcomponent
