@component('mail::message')
# Retired Coordinator Admin Notice

The following coordinator has been reactivated in MIMI in {{ $mailData['userConfName'] }}.

{{ $mailData['cdName'] }}
{{ $mailData['cdEmail'] }}

Please reactivate the coordinator's momsclub.org email address.

**MCL,**
MIMI Database Administrator
@endcomponent
