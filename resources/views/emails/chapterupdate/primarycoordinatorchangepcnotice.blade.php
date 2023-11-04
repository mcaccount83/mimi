@component('mail::message')
# Primary Coordinator Notification

You have been reassigned as the Primary Coordinator for The MOMS Club of {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}}.  You should be able to see them in your MIMI profile.

They have alrady been notified, but feel free to reach out to them directly as well.

President's Information:
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$mailData['ch_pre_fname']}} {{$mailData['ch_pre_lname']}}
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$mailData['ch_pre_email']}}
<br>
**MCL,**<br>
International MOMS Club
@endcomponent
