@component('mail::message')
# Retired Coordinator Admin Notice

<p>The following coordinator has been marked as retired in MIMI in {{$mailData['userConfName']}}.</p>
<br>
<p>{{$mailData['cdName']}}<br>
{{$mailData['cdEmail']}}</p>
<br>
<p>Please deactivate the coordinator's momsclub.org email address and remove from any groups, forums and mailing lists.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>
@endcomponent
