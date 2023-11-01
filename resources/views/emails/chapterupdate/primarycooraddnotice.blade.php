@component('mail::message')
# Primary Coordinator Notification

{{$mailData['cor_fname']}}!
<br>
The MOMS Club of {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}} has been added to MIMI and you have been assigned as the Primary Coordinator.
<br>
Founder Information:
{{$mailData['pfirst']}} {{$mailData['plast']}}
{{$mailData['pemail']}}
<br>
**MCL,**<br>
International MOMS Club
<br>
@endcomponent
