@component('mail::message')
<h1><center>Happy Holidays!</center></h1>
<br>
<p>We hope you are enjoying the cooler weather and the holiday season!</p>
<p>
    Our coordinators will be taking off the following dates to spend time with their families over the
    holidays. All merchandise orders received during this time will be processed after coordinators return.
</p>
<p><center>
<b>{{$mailData['fallBreak']}}<br>
{{$mailData['winterBreak']}}<b>
</center></p>
<br>
<p>
    If you have an emergency, you can contact your {{ $mailData['userPosition'] }} ({{ $mailData['userName'] }}) directly at
    <a mailto="{{ $mailData['userEmail'] }}">{{ $mailData['userEmail'] }}</a>
    or you can always send a message to
    <a mailto="support@momsclub.org">support@momsclub.org</a>, and someone will contact you as soon as possible.
</p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
