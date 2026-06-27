@component('mail::message')
# New Chapter Primary Coordinator Notification

<p>{{$mailData['pcName']}}, the MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has been added to MIMI and you have been
    assigned as the Primary Coordinator.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>

{!! $mailData['mailTableNewChapter'] !!}
<br>
@endcomponent
