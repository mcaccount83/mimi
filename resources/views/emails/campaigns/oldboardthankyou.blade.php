@component('mail::message')
<h1><center>MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}</center></h1>
<b><center>{{$mailData['reportYearRange']}} Board - Thank You!</center></b><br>
<br>
<p>
We‘d like to take this time to thank you for serving on the board over the past year. It is always amazing to read and hear about all the
great ideas you have all come up with and the great service projects you have done in your local communities. You have all made a huge
difference, and our team wants to thank you for the time you have given to your chapter. If you are staying on for another year on the
board, we very much look forward to working with you again.
</p>
<p>
As you finish your year, please take a moment to consider becoming an International MOMS Club volunteer. We are still looking to fill
vacancies for the new year. Our volunteers are an integral part of MOMS Club and supporting mothers. Most of our volunteers are current
or former board members, who wish to still give, but in a different way. This is a great way to share the knowledge you’ve gained over
the year(s), learn new skills, meet other chapters, and take on new challenges. We also want to offer chapters a support team that they
have come to expect. Many chapters need our guidance, and we want to build a strong team to be there to help them. We’d love to have you
join our team, so please take a moment to think about it, and if you’d like more details or have questions feel free to contact any of
your coordinators or fill out the online application.
</p>
<p><center><a href="https://momsclub.org/coordinators/">https://momsclub.org/coordinators/</a></center></p>
<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['ccName'] }}<br>
    {{ $mailData['ccPosition'] }}<br>
    {{ $mailData['ccConfName'] }}, {{ $mailData['ccConfDescription'] }}<br>
    International MOMS Club</p>
@endcomponent
