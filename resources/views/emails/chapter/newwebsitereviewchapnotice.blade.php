@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}

<p>
    Thank you for submitting your new website for linking.  The site will reviewed by one of our
    coordinators and we will let you know if any updates need to be made before the site can be linked.
</p>
<p>Your linked site is: <a href="{{$mailData['chapterWebsiteURL']}}">{{$mailData['chapterWebsiteURL']}}</a>.</p>
<p>List of linked chapters can be found here:
    <a href="https://momsclub.org/chapters/chapter-links/">https://momsclub.org/chapters/chapter-links/</a></p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
