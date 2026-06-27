@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

<p>Your chapter has celebrated another year of offering support to the at-home mothers in your area!</p>
<p>This is the reminder that <b>{{$mailData['startMonth']}}</b> is your chapter's anniversary with the International MOMS Club and it is time to pay the
    chapter's re-registration fee, if you haven't done so already.</p>

@include('emails.partials.rereg-form')
<br>
<p>Thank you for your prompt renewal payment and/or sustaining chapter donation! If you have any questions, please do not hesitate to contact your chapter's Primary Coordinator.</p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
