@component('mail::message')
# New Chapter Primary Coordinator Notification

{{$mailData['pcName']}}, the MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has been added to MIMI and you have been assigned as the Primary Coordinator.<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
<br>

{!! $mailData['mailTableNewChapter'] !!}

@endcomponent
