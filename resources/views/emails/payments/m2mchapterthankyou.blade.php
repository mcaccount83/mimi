@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

<p>Thank you for your group’s contribution of <b>{{$mailData['m2mDonation']}}</b> to the International MOMS Club’s Mother-To-Mother Fund!</p>
<br>
<p>Your contribution will be added to the fund for use when a personal or natural disaster strikes MOMS Club members.  Please pass on to your
    members our congratulations for their generosity and compassion for their MOMS Club sisters.</p>
<br>
<p>Because of their farsightedness in contributing to the Mother-To-Mother Fund now, emergency assistance will be available should a disaster
    strike a MOMS Club family in the future.</p>
<br>
<p>Thank you again for helping!  Your members should be very proud of themselves!  I know we are very proud of all of you!</p>
<br>
<p><strong>MCL,</strong><br>
Mother-To-Mother Fund Committee</p>
@endcomponent
