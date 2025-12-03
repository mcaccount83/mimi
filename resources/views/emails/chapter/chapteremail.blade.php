@component('mail::message')

<p><b>MOMS Club of {{ $mailData['chapterName'] }}:</b></p>

@php
    $message = $mailData['message'];
    $message = html_entity_decode($message, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $message = trim($message);
@endphp

{!! $message !!}

<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club</p>
@endcomponent
