@component('mail::message')
# Retired Coordinator Admin Notice

<p>The following coordinator has been reactivated in MIMI in {{$mailData['userConfName']}}.</p>
<br>
<p>{{$mailData['cdName']}}<br>
    {{$mailData['cdEmail']}}<br></p>
<br>
<p>Please reactivate the coordinator's momsclub.org email address.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>
@endcomponent
