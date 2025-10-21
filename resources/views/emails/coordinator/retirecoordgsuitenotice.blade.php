@component('mail::message')
# Retired Coordinator Admin Notice

The following coordinator has been marked as retired in MIMI in {{$mailData['userConfName']}}.<br>
<br>
{{$mailData['cdName']}}<br>
{{$mailData['cdEmail']}}<br>
<br>
Please deactivate the coordinator's momsclub.org email address and remove from any groups, forums and mailing lists.<br>
<br>
<strong>MCL</srong>,<br>
MIMI Database Administrator
<br>
@endcomponent
