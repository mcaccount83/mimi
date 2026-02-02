@component('mail::message')

<b>MOMS Club of {{ $mailData['chapterName'] }}:</b>
<br>

@php
    $message = $mailData['message'];
    $message = html_entity_decode($message, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $message = trim($message);
@endphp

{!! $message !!}

<br>
<strong>MCL,</strong><br>
{{ $mailData['inqCoordName'] }}<br>
Inquiries Coordinator<br>
{{ $mailData['regionLongName'] }} Region<br>
{{ $mailData['conferenceDescription'] }} Conference<br>
International MOMS Club
@endcomponent
