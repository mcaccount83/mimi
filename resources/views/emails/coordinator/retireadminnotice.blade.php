@component('mail::message')
# Retired Coordinator Admin Notice

The following coordinator has retired: {{$mailData['coordName']}}, {{$mailData['email']}}, Conference {{$mailData['confNumber']}}.<br>
<br>
Please deactivate the coordinator's momsclub.org email address and remove from any groups, forums and mailing lists.<br>
<br>
<strong>MCL</srong>,<br>
MIMI Database Administrator
<br>
@endcomponent
