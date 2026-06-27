@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

<p>Thank you for your group’s Sustaining Chapter donation of <b>{{$mailData['sustainingDonation']}}</b>!</p>
<p>Sustaining donations such as your chapter’s make it possible for us to extend the MOMS Club
    opportunity to more and more mothers, as well as maintain our high quality of support for the MOMS
    Club chapters, not just in your local area, but across the country and around the world.</p>
<p>Your chapter’s support of the International MOMS Club is both generous and most appreciated!</p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
