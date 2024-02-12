@component('mail::message')
# Primary Coordinator Notification

You have been reassigned as the Primary Coordinator for The MOMS Club of {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}}.  You should be able to see them in your MIMI profile.<br>
<br>
They have alrady been notified, but feel free to reach out to them directly as well.<br>
<br>
President's Information:<br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$mailData['ch_pre_fname']}} {{$mailData['ch_pre_lname']}}<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$mailData['ch_pre_email']}}<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
