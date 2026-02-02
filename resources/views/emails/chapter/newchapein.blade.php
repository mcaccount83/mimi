@component('mail::message')

Your EIN has been assigned by the IRS: <b>{{ $mailData['chapterEIN'] }}</b>.  CONGRATULATIONS, you are officially a MOMS Club chapter!<br>
<br>
You will also be receiving a letter from the IRS in the mail that you should keep with your chapter records. When you get the letter, please scan/email a copy to us so we can upload it to MIMI where it will be accessible to your chapter board members as a backup in case it is ever needed.<br>

<br>
<strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club
@endcomponent







