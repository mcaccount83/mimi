@component('mail::message')
# Retired Coordinator Admin Notice

The following coordinator has retired: {{$mailData['coordName']}}, {{$mailData['email']}}, Conference {{$mailData['confNumber']}}.

Please deactivate the coordinator's momsclub.org email address and remove from any groups, forums and mailing lists.

**MCL**,<br>
MIMI Database Administrator
<br>
@endcomponent
