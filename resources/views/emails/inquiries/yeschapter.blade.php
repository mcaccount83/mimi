@component('mail::message')

<b>{{ $mailData['inquiryFirstName'] }},</b><br>
<br>
Thanks for your interest in MOMS Club!  You live in the boundaries of our <strong>{{ $mailData['chapterName'] }}</strong> chapter.  I have forwarded your inquiry to them and you should hear within the next couple of days.<br>
<br>
If you don’t hear, please let me know and I’ll make sure they received your inquiry.  If you would like to contact them directly yourself, you can reach them at <a href="{{ $mailData['chapterInquiriesContact'] }}">{{ $mailData['chapterInquiriesContact'] }}</a>.<br>
<br>
<strong>MCL,</strong><br>
{{ $mailData['inqCoordName'] }}<br>
Inquiries Coordinator<br>
{{ $mailData['regionLongName'] }} Region<br>
{{ $mailData['conferenceDescription'] }} Conference<br>
International MOMS Club
@endcomponent
