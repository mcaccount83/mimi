@component('mail::message')

<p><b>MOMS Club of {{ $mailData['chapterName'] }}:</b></p>

{{-- @php
    $message = $mailData['message'];

    // Convert HTML formatting to text equivalents
    $message = str_replace(['<strong>', '<b>'], '**', $message);
    $message = str_replace(['</strong>', '</b>'], '**', $message);
    $message = str_replace(['<em>', '<i>'], '_', $message);
    $message = str_replace(['</em>', '</i>'], '_', $message);
    $message = str_replace('<u>', '', $message);
    $message = str_replace('</u>', '', $message);
    $message = str_replace('<br>', "\n", $message);
    $message = str_replace('<br/>', "\n", $message);
    $message = str_replace('<br />', "\n", $message);

    // Handle paragraphs
    $message = str_replace('<p>', '', $message);
    $message = str_replace('</p>', "\n\n", $message);

    // Handle lists
    $message = preg_replace('/<ol[^>]*>/', '', $message);
    $message = str_replace('</ol>', "\n", $message);
    $message = preg_replace('/<ul[^>]*>/', '', $message);
    $message = str_replace('</ul>', "\n", $message);
    $message = preg_replace('/<li[^>]*>/', '• ', $message);
    $message = str_replace('</li>', "\n", $message);

    // Clean up any remaining tags
    $message = strip_tags($message);

    // DECODE HTML ENTITIES (including &nbsp;) - This is the key addition
    $message = html_entity_decode($message, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Trim whitespace
    $message = trim($message);

    // Fix multiple line breaks
    $message = preg_replace('/\n{3,}/', "\n\n", $message);
@endphp --}}

@php
    $message = $mailData['message'];
    $message = html_entity_decode($message, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $message = trim($message);
@endphp

{{-- {!! nl2br(e($message)) !!} --}}
{!! $message !!}

<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club</p>
@endcomponent
