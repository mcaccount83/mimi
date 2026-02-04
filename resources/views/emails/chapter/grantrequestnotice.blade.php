@component('mail::message')
# New Grant Request Notification

A new grant rquest has been submitted by the MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}.<br>
<br>
Details are below as well as in MIMI.  If you need more information, please reach out to:<br>
{{$mailData['board_name']}}, {{$mailData['board_position']}}<br>
Email: {{$mailData['board_email']}}<br>
Phone: {{$mailData['board_phone']}}<br>
<br>
{!! $mailData['mailTable'] !!}
<br>

<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
