@component('mail::message')
{{-- <center>
    <img src="https://momsclub.org/mimi/images/LOGO-W-MOMS-CLUB-old.jpg" alt="MC" style="width: 125px;">
</center>
<br> --}}
<p><b>{{ $mailData['chapterName'] }}:</b></p>
<p>{{ $mailData['message'] }}</p>

<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club</p>
@endcomponent
