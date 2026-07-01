@component('mail::message')
Your EIN has been assigned by the IRS: **{{ $mailData['chapterEIN'] }}**. CONGRATULATIONS, you are officially a MOMS Club chapter!

You will also be receiving a letter from the IRS in the mail that you should keep with your chapter records. When you get the letter,
please scan/email a copy to us so we can upload it to MIMI where it will be accessible to your chapter board members as a backup in case it is ever needed.

**MCL**,
{{ $mailData['userName'] }}
{{ $mailData['userPosition'] }}
{{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}
International MOMS Club
@endcomponent







