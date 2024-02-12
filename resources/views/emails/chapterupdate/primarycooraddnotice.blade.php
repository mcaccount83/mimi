@component('mail::message')
# Primary Coordinator Notification

{{$mailData['cor_fname']}}!<br>
<br>
The MOMS Club of {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}} has been added to MIMI and you have been assigned as the Primary Coordinator.<br>
<br>
Founder Information:<br>
{{$mailData['pfirst']}} {{$mailData['plast']}}<br>
{{$mailData['pemail']}}<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
<br>
@endcomponent
