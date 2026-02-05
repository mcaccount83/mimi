@component('mail::message')
# New Grant Request Notification

A new grant rquest has been submitted by the MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}. Please review and pass along to the M2M Committee for review.<br>
<br>
Details are below as well as in MIMI.  If you need more information, please reach out to the board member submitting the request:<br>
{{$mailData['board_name']}}, {{$mailData['board_position']}}<br>
{{$mailData['board_email']}}<br>
{{$mailData['board_phone']}}<br>
<br>

<strong>MCL,</strong><br>
International MOMS Club

<br>
{!! $mailData['mailTable'] !!}
<br>

@endcomponent
