@component('mail::message')
**{{ $mailData['inquiryFirstName'] }},**

Thanks for your interest in MOMS Club! You live in the boundaries of our **{{ $mailData['chapterName'] }}**
chapter. I have forwarded your inquiry to them and you should hear within the next couple of days.

If you don't hear, please let me know and I'll make sure they received your inquiry. If you would
like to contact them directly yourself, you can reach them at
[{{ $mailData['chapterInquiriesContact'] }}]({{ $mailData['chapterInquiriesContact'] }}).

**MCL,**<br>
{{ $mailData['userName'] }}<br>
{{ $mailData['userPosition'] }}<br>
{{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
International MOMS Club
@endcomponent
