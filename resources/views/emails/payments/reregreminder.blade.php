@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

Your chapter has celebrated another year of offering support to the at-home mothers in your area!

This is the reminder that **{{ $mailData['startMonth'] }}** is your chapter's anniversary with the
International MOMS Club and it is time to pay the chapter's re-registration fee, if you haven't
done so already.

@include('emails.partials.rereg-form')

Thank you for your prompt renewal payment and/or sustaining chapter donation! If you have any
questions, please do not hesitate to contact your chapter's Primary Coordinator.

**MCL,**
International MOMS Club
@endcomponent
