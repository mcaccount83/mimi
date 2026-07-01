@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

<p>Your chapter's anniversary month is <b>{{$mailData['startMonth']}}</b>.</p>
<p>As of today we have not received your chapter’s re-registration fee. All re-registration fees are due annually on the month of your MOMS
    Club anniversary and it is now considered <b><u>PAST DUE</u></b>.</p>
<p>Below is information for how to calculate your payment as well as the different options available to submit payment.</p>
<p>If you have already submitted your payment, please let us know. Sometimes clerical errors are made and payments do not get applied correctly.</p>
<ul>
    <li>If you paid online, please forward the receipt you received via email.</li>
    <li>If you paid via check, please send a copy of your cleared check.</li>
    <li>If you paid via check, and it has not cleared yet, please provide us with an approximate mailing date.</li>
    <li>If there was an error, we’ll be sure to get it corrected as quickly as possible.</li>
    <li>If you have not submitted your payment, please follow the instructions below and include a $10 late fee when submitting.</li>
</ul>

@include('emails.partials.rereg-form')
<br>
<p>If payment is not received by the last day of <b>{{$mailData['dueMonth']}}</b> your chapter will be placed on probation.</p>
<p>If you have any questions at all, do not hesitate to ask.</p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
