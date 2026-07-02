@component('mail::message')
**MOMS Club of {{ $mailData['chapterName'] }}:**

@php
    $message = $mailData['message'];
    $message = html_entity_decode($message, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $message = trim($message);
@endphp

{!! $message !!}

**MCL**,<br>
{{ $mailData['userName'] }}<br>
{{ $mailData['userPosition'] }}<br>
{{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
International MOMS Club
@endcomponent
