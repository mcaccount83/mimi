@component('mail::message')

<p><b>{{ $mailData['inquiryFirstName'] }},</b></p>
<br>
<p>
    Thanks for your interest in MOMS Club!  You live in the boundaries of our <strong>{{ $mailData['chapterName'] }}</strong>
    chapter.  I have forwarded your inquiry to them and you should hear within the next couple of days.</p>
<p>
    If you don’t hear, please let me know and I’ll make sure they received your inquiry.  If you would
    like to contact them directly yourself, you can reach them at
    <a href="{{ $mailData['chapterInquiriesContact'] }}">{{ $mailData['chapterInquiriesContact'] }}</a>.</p>
<br>
<p><strong>MCL,</strong><br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club</p>
@endcomponent
