@component('mail::message')
# Retired Coordinator Admin Notice

The following coordinator has been reactivated in MIMI in {{$mailData['userConfName']}}.<br>
<br>
{{$mailData['cdName']}}<br>
{{$mailData['cdEmail']}}<br>
<br>
Please reactivate the coordinator's momsclub.org email address.<br>
<br>
<strong>MCL</srong>,<br>
MIMI Database Administrator
<br>
@endcomponent
