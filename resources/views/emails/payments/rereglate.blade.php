@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

Your chapter's anniversary month is **{{ $mailData['startMonth'] }}**.

As of today we have not received your chapter's re-registration fee. All re-registration fees are due
annually on the month of your MOMS Club anniversary and it is now considered **PAST DUE**.

Below is information for how to calculate your payment as well as the different options available to
submit payment.

If you have already submitted your payment, please let us know. Sometimes clerical errors are made and
payments do not get applied correctly.

- If you paid online, please forward the receipt you received via email.
- If you paid via check, please send a copy of your cleared check.
- If you paid via check, and it has not cleared yet, please provide us with an approximate mailing date.
- If there was an error, we'll be sure to get it corrected as quickly as possible.
- If you have not submitted your payment, please follow the instructions below and include a $10 late fee
when submitting.

@include('emails.partials.rereg-form')

If payment is not received by the last day of **{{ $mailData['dueMonth'] }}** your chapter will be
placed on probation.

If you have any questions at all, do not hesitate to ask.

**MCL,**
International MOMS Club
@endcomponent
