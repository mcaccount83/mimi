@component('mail::message')

<p><b>{{ $mailData['chapterName'] }}:</b></p>
<p>{{ $mailData['message'] }}</p>

<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club</p>
@endcomponent
