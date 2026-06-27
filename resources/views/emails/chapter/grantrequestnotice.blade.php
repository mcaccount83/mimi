@component('mail::message')
# New Grant Request Notification

<p>A new grant rquest has been submitted by the MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}. Please review and pass
    along to the M2M Committee for review.</p>
<br>
<p>Details are below as well as in MIMI.  If you need more information, please reach out to the board member submitting the request:<br>
    {{$mailData['board_name']}}, {{$mailData['board_position']}}<br>
    {{$mailData['board_email']}}<br>
    {{$mailData['board_phone']}}<br>
</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>

{!! $mailData['mailTable'] !!}
<br>
@endcomponent
